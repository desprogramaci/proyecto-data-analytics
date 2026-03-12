import csv
import random
from datetime import datetime, timedelta
from pathlib import Path

# Ruta donde se guardará el CSV
OUTPUT_FILE = Path(__file__).resolve().parent.parent / "raw" / "ventas_grandes.csv"


productos = [
    "Laptop Pro", "Laptop Air", "Smartphone X", "Smartphone Mini", "Tablet Plus",
    "Auriculares Bluetooth", "Teclado Mecánico", "Mouse Gamer", "Monitor 27",
    "Monitor 32", "Impresora Laser", "Cámara HD", "Drone Mini", "Smartwatch",
    "Router WiFi", "SSD 1TB", "HDD 2TB", "Memoria RAM 16GB", "Silla Gamer",
    "Escritorio Oficina", "Lámpara LED", "Cargador USB-C", "PowerBank 20k",
    "Altavoz Bluetooth", "Microfono Pro", "Webcam FullHD", "Proyector HD",
    "TV 55", "TV 65", "Barra de Sonido", "Refrigerador Mini", "Ventilador Turbo",
    "Aspiradora Robot", "Cafetera Automática", "Batidora Pro", "Tostadora",
    "Plancha Vapor", "Zapatillas Running", "Mochila Travel", "Casco Bicicleta",
    "Guantes Gym", "Pelota Fitness", "Colchoneta Yoga", "Set Mancuernas",
    "Reloj Deportivo", "Gafas Sol", "Chaqueta Invierno", "Pantalón Trekking"
]

categorias = [
    "Electrónica", "Accesorios", "Hogar", "Deportes",
    "Oficina", "Audio", "Video", "Moda"
]

proveedores = [
    "TechCorp", "LogiTech", "MegaStore", "Distribuciones Europa",
    "SportWorld", "HomePlus", "FashionCorp", "ElectroMax",
    "GlobalTech", "ProGear"
]

paises = ["España", "Francia", "Alemania", "Italia", "Portugal"]

canales = ["Online", "Tienda", "Distribuidor"]

monedas = {
    "España": "EUR",
    "Francia": "EUR",
    "Alemania": "EUR",
    "Italia": "EUR",
    "Portugal": "EUR"
}

# ============================
# ESTACIONALIDAD REALISTA
# ============================

def factor_estacionalidad(fecha):
    mes = fecha.month

    # Rebajas de enero
    if mes == 1:
        return 1.30

    # Semana Santa (marzo/abril)
    if mes in [3, 4]:
        return 1.10

    # Verano: deportes sube, electrónica baja
    if mes in [6, 7, 8]:
        return 0.85

    # Vuelta al cole (septiembre)
    if mes == 9:
        return 1.15

    # Black Friday (noviembre)
    if mes == 11:
        return 1.50

    # Navidad (diciembre)
    if mes == 12:
        return 1.40

    # Meses normales
    return 1.0


def generar_fecha():
    inicio = datetime(2024, 1, 1)
    fin = datetime(2024, 12, 31)
    delta = fin - inicio
    return inicio + timedelta(days=random.randint(0, delta.days))


with open(OUTPUT_FILE, "w", newline="", encoding="utf-8") as file:
    writer = csv.writer(file)
    writer.writerow([
        "producto","categoria","proveedor","cantidad","precio_unitario",
        "descuento","impuesto","fecha","canal_venta","pais","moneda"
    ])

    for _ in range(5000):
        producto = random.choice(productos)
        categoria = random.choice(categorias)
        proveedor = random.choice(proveedores)
        cantidad = random.randint(1, 5)

        # Precio base
        precio = round(random.uniform(10, 1500), 2)

        # Fecha con estacionalidad
        fecha_dt = generar_fecha()
        factor = factor_estacionalidad(fecha_dt)

        # Ajuste estacional del precio
        precio = round(precio * factor, 2)

        descuento = round(random.uniform(0, precio * 0.15), 2)
        impuesto = round(precio * 0.21, 2)

        fecha = fecha_dt.strftime("%Y-%m-%d")
        pais = random.choice(paises)
        canal = random.choice(canales)
        moneda = monedas[pais]

        writer.writerow([
            producto, categoria, proveedor, cantidad, precio,
            descuento, impuesto, fecha, canal, pais, moneda
        ])

print("CSV generado con estacionalidad:", OUTPUT_FILE)
