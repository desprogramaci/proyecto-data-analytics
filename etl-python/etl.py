import subprocess
from pathlib import Path

RAW_DIR = Path(__file__).resolve().parent / "raw"
generador = Path(__file__).resolve().parent / "scripts" / "generar_ventas.py"

print("Generando dataset de ventas...")
subprocess.run(["python", str(generador)], check=True)
print("Dataset generado en RAW.")

import os
import shutil
import uuid
import logging
from datetime import datetime
from pathlib import Path

import pandas as pd
import psycopg2
import requests


# =========================
# CONFIGURACIÓN GENERAL
# =========================

BASE_DIR = Path(__file__).resolve().parent
RAW_DIR = BASE_DIR / "raw"
PROCESSED_DIR = BASE_DIR / "processed"
ERROR_DIR = BASE_DIR / "errors"
LOGS_DIR = BASE_DIR / "logs"

LOGS_DIR.mkdir(exist_ok=True)
PROCESSED_DIR.mkdir(exist_ok=True)
ERROR_DIR.mkdir(exist_ok=True)

LOG_FILE = LOGS_DIR / "etl.log"
ERROR_LOG_FILE = LOGS_DIR / "etl_errors.log"

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[
        logging.FileHandler(LOG_FILE),
        logging.StreamHandler()
    ]
)

error_logger = logging.getLogger("errors")
error_handler = logging.FileHandler(ERROR_LOG_FILE)
error_handler.setLevel(logging.ERROR)
error_logger.addHandler(error_handler)


# =========================
# CONFIG DB
# =========================

DB_CONFIG = {
    "host": "postgres",
    "database": "analytics",
    "user": "yonti",
    "password": "secreto"
}


# =========================
# COLUMNAS OBLIGATORIAS
# =========================

REQUIRED_COLUMNS = [
    "producto",
    "categoria",
    "proveedor",
    "cantidad",
    "precio_unitario",
    "descuento",
    "impuesto",
    "fecha",
    "canal_venta",
    "pais",
    "moneda",
]

# Columnas que puede traer el archivo
OPTIONAL_COLUMNS = [
    "id_transaccion",
    "total",
]


# =========================
# FUNCIONES DE UTILIDAD
# =========================

def log_info(msg: str):
    logging.info(msg)


def log_error(msg: str):
    logging.error(msg)
    error_logger.error(msg)


def move_file(src: Path, dest_dir: Path):
    dest_dir.mkdir(exist_ok=True)
    dest = dest_dir / src.name
    shutil.move(str(src), str(dest))


# =========================
# LECTURA DE ARCHIVOS
# =========================

def load_csv(path: Path) -> pd.DataFrame:
    log_info(f"Leyendo CSV: {path.name}")
    return pd.read_csv(path)


def load_excel(path: Path) -> pd.DataFrame:
    log_info(f"Leyendo Excel: {path.name}")
    return pd.read_excel(path)


def load_json(path: Path) -> pd.DataFrame:
    log_info(f"Leyendo JSON: {path.name}")
    return pd.read_json(path)


def load_file(path: Path) -> pd.DataFrame:
    ext = path.suffix.lower()
    if ext == ".csv":
        return load_csv(path)
    elif ext in [".xls", ".xlsx"]:
        return load_excel(path)
    elif ext == ".json":
        return load_json(path)
    else:
        raise ValueError(f"Extensión no soportada: {ext}")


# =========================
# VALIDACIÓN
# =========================

def validate_columns(df: pd.DataFrame, file_name: str):
    missing = [col for col in REQUIRED_COLUMNS if col not in df.columns]
    if missing:
        raise ValueError(
            f"El archivo {file_name} no tiene las columnas obligatorias: {missing}"
        )


def validate_types_and_values(df: pd.DataFrame, file_name: str):
    # Cantidad, precio, descuento, impuesto deben ser numéricos
    numeric_cols = ["cantidad", "precio_unitario", "descuento", "impuesto"]
    for col in numeric_cols:
        if not pd.api.types.is_numeric_dtype(df[col]):
            try:
                df[col] = pd.to_numeric(df[col], errors="raise")
            except Exception:
                raise ValueError(f"Columna {col} en {file_name} no es numérica")

        if (df[col] < 0).any():
            raise ValueError(f"Columna {col} en {file_name} tiene valores negativos")

    # Fecha debe ser convertible a datetime
    try:
        df["fecha"] = pd.to_datetime(df["fecha"], errors="raise")
    except Exception:
        raise ValueError(f"Columna fecha en {file_name} tiene valores inválidos")


# =========================
# LIMPIEZA Y TRANSFORMACIÓN
# =========================

def clean_strings(df: pd.DataFrame):
    string_cols = [
        "producto",
        "categoria",
        "proveedor",
        "canal_venta",
        "pais",
        "moneda",
    ]
    for col in string_cols:
        df[col] = df[col].astype(str).str.strip()


def generate_id_if_missing(df: pd.DataFrame):
    if "id_transaccion" not in df.columns:
        df["id_transaccion"] = None

    df["id_transaccion"] = df["id_transaccion"].apply(
        lambda x: x if pd.notnull(x) and str(x).strip() != "" else str(uuid.uuid4())
    )


def calculate_total(df: pd.DataFrame):
    # total = (precio_unitario - descuento + impuesto) * cantidad
    df["total"] = (df["precio_unitario"] - df["descuento"] + df["impuesto"]) * df["cantidad"]


def transform_data(df: pd.DataFrame, file_name: str) -> pd.DataFrame:
    log_info(f"Limpiando y transformando datos de {file_name}")
    clean_strings(df)
    validate_types_and_values(df, file_name)
    generate_id_if_missing(df)
    calculate_total(df)
    return df


# =========================
# CARGA A POSTGRES
# =========================

def get_connection():
    return psycopg2.connect(
        host=DB_CONFIG["host"],
        database=DB_CONFIG["database"],
        user=DB_CONFIG["user"],
        password=DB_CONFIG["password"],
    )


def load_to_postgres(df: pd.DataFrame, file_name: str):
    log_info(f"Cargando datos de {file_name} en PostgreSQL...")
    conn = get_connection()
    cursor = conn.cursor()

    for _, row in df.iterrows():
        try:
            cursor.execute(
                """
                INSERT INTO ventas (
                    id_transaccion,
                    producto,
                    categoria,
                    proveedor,
                    cantidad,
                    precio_unitario,
                    descuento,
                    impuesto,
                    total,
                    fecha,
                    canal_venta,
                    pais,
                    moneda,
                    created_at,
                    updated_at
                )
                VALUES (
                    %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW()
                )
                ON CONFLICT (id_transaccion)
                DO UPDATE SET
                    producto = EXCLUDED.producto,
                    categoria = EXCLUDED.categoria,
                    proveedor = EXCLUDED.proveedor,
                    cantidad = EXCLUDED.cantidad,
                    precio_unitario = EXCLUDED.precio_unitario,
                    descuento = EXCLUDED.descuento,
                    impuesto = EXCLUDED.impuesto,
                    total = EXCLUDED.total,
                    fecha = EXCLUDED.fecha,
                    canal_venta = EXCLUDED.canal_venta,
                    pais = EXCLUDED.pais,
                    moneda = EXCLUDED.moneda,
                    updated_at = NOW();
                """,
                (
                    row["id_transaccion"],
                    row["producto"],
                    row["categoria"],
                    row["proveedor"],
                    int(row["cantidad"]),
                    float(row["precio_unitario"]),
                    float(row["descuento"]),
                    float(row["impuesto"]),
                    float(row["total"]),
                    row["fecha"],
                    row["canal_venta"],
                    row["pais"],
                    row["moneda"],
                ),
            )
        except Exception as e:
            log_error(f"Error insertando fila en {file_name}: {e}")

    conn.commit()
    cursor.close()
    conn.close()
    log_info(f"Datos de {file_name} cargados correctamente en PostgreSQL.")


# =========================
# ORQUESTACIÓN
# =========================

def process_file(path: Path):
    file_name = path.name
    log_info(f"Procesando archivo: {file_name}")

    try:
        df = load_file(path)
        validate_columns(df, file_name)
        df = transform_data(df, file_name)
        load_to_postgres(df, file_name)
        move_file(path, PROCESSED_DIR)
        log_info(f"Archivo {file_name} procesado y movido a 'processed/'.")
    except Exception as e:
        log_error(f"Error procesando archivo {file_name}: {e}")
        move_file(path, ERROR_DIR)
        log_info(f"Archivo {file_name} movido a 'errors/'.")


def process_all_files():
    log_info("===== INICIO EJECUCIÓN ETL =====")
    files = list(RAW_DIR.glob("*"))

    if not files:
        log_info("No hay archivos nuevos en 'raw/'. Nada que procesar.")
        return

    for f in files:
        if f.is_file():
            process_file(f)

    log_info("===== FIN EJECUCIÓN ETL =====")


if __name__ == "__main__":
    process_all_files()
    print("ETL ejecutado sin duplicados y con múltiples fuentes.")
