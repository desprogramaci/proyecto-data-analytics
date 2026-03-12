📊 Proyecto de Data Analytics con ETL en Python, PostgreSQL, Laravel 12, Docker y Metabase
Este proyecto implementa un pipeline completo de datos utilizando:

- **Laravel** como aplicación web y API
- **Python** para el ETL
- **PostgreSQL** como base de datos central
- **Docker Compose** para orquestación
- **Metabase** para dashboards
- **Adminer** para administración de la base de datos
- **Generación automática de datos con estacionalidad realista**

El objetivo es simular un entorno real de analítica empresarial con un flujo profesional:

Código
Generación de datos → ETL → PostgreSQL → Dashboards en Metabase

🚀 **Características principales**:

✔ **Laravel (analytics-app)**
- API para exponer datos procesados
- Panel web para visualización
- Integración directa con PostgreSQL
- Arquitectura MVC limpia

✔ **ETL profesional en Python**
-Limpieza de datos
-Validación de columnas
-Conversión de tipos
-Generación de IDs únicos
-Cálculo automático de totales
-Manejo de errores
-Logs detallados
-Procesamiento incremental

✔ **Generación automática de datos realistas**
-Incluye estacionalidad:
-Rebajas de enero
-Semana Santa
-Caída en verano
-Vuelta al cole
-Black Friday
-Navidad

✔ **Base de datos PostgreSQL en Docker**
-Persistencia con volúmenes
-Configuración automática
-Acceso desde Metabase y Adminer

✔ **Dashboards en Metabase**
-Conexión directa a PostgreSQL
-Visualizaciones dinámicas
-KPIs y tendencias

✔ **Adminer para administración rápida**
-Consultas SQL
-Exploración de tablas
-Gestión de datos

🐳 **Arquitectura del proyecto**

proyecto-data-analytics/
│
├── analytics-app/          # Aplicación Laravel
│   ├── app/
│   ├── routes/
│   ├── resources/
│   └── ...
│
├── etl-python/
│   ├── etl.py              # Pipeline principal
│   ├── requirements.txt
│   ├── Dockerfile
│   │
│   ├── raw/                # Datos sin procesar (entrada)
│   ├── processed/          # Datos procesados
│   ├── logs/               # Logs del ETL
│   ├── errors/             # Archivos con errores
│   │
│   └── scripts/
│       └── generar_ventas.py   # Generador de datos con estacionalidad
│
├── docker-compose.yml
└── metabase-data/          # Persistencia de Metabase


🧱 **Servicios incluidos (Docker Compose)**
-postgres → Base de datos
-etl → Ejecuta el pipeline automáticamente
-metabase → Dashboards
-adminer → UI para PostgreSQL

▶️ **Cómo ejecutar el proyecto**
-Desde la raíz del proyecto:
bash
docker compose up --build

**Esto hará:**
-Levantar PostgreSQL
-Levantar Laravel
-Levantar Metabase
-Levantar Adminer
-Ejecutar el ETL automáticamente
-Generar datos con estacionalidad
-Cargar los datos en PostgreSQL

🗄 **Acceso a los servicios**

🔹 **Laravel**
http://localhost:8000

🔹 **Metabase**
http://localhost:3000

🔹 **Adminer**
http://localhost:8080

**Credenciales:**
-Servidor: postgres
-Usuario: postgres
-Contraseña: postgres
-Base de datos: analytics

📦 **ETL: Flujo completo**
-Ejecuta generar_ventas.py
-Crea ventas_grandes.csv en raw/
-Valida columnas y tipos
-Limpia strings
-Genera id_transaccion
-Calcula total
-Inserta en PostgreSQL
-Mueve archivo a processed/

📈 **Dashboards en Metabase**
-Una vez cargados los datos, puedes crear dashboards como:
-Ventas por mes
-Ventas por categoría
-Ventas por país
-Tendencias estacionales
-Top productos
-Comparativa por canal de venta

🧪 **Comandos útiles**
-Ver logs del ETL
bash
docker logs etl_python

-Reiniciar solo el ETL
bash
docker compose restart etl

-Acceder a PostgreSQL desde el contenedor
bash
docker exec -it postgres_data psql -U postgres -d analytics

📝 **Licencia**
-Este proyecto es de uso libre para aprendizaje, análisis y demostración.

🙌 **Autor**
-Yonti Testa 
-Proyecto de Data Engineering con ETL + Docker + Metabase