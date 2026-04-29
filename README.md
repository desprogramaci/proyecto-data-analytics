# 📊 Data Analytics Ecosystem: ETL, BI & Web App

Este proyecto implementa un pipeline end-to-end de datos diseñado para simular un entorno corporativo de analítica real. Automatiza todo el flujo: desde la generación de datos sintéticos con lógica de negocio (estacionalidad) hasta la visualización en dashboards profesionales.

---
## 🛠️ Stack Tecnológico

| Componente | Tecnología | Rol en el Proyecto |
| :--- | :--- | :--- |
| **Backend / API** | **Laravel 12** | Aplicación de consumo y exposición de datos procesados. |
| **Data Engine** | **Python 3.11** | Motor de ETL, limpieza, validación y generación de datos. |
| **Base de Datos** | **PostgreSQL 16** | Almacén de datos central (Data Warehouse). |
| **Visualización** | **Metabase** | Plataforma de Business Intelligence y Dashboards. |
| **Infraestructura** | **Docker Compose** | Orquestación de microservicios y persistencia. |
| **Administración** | **Adminer** | Cliente ligero para gestión rápida de la DB. |

---

## 🏗️ Arquitectura y Flujo de Datos

El sistema opera bajo un flujo profesional de ingeniería de datos:

1.  **Generación:** Script Python crea datasets `.csv` en la carpeta `/raw` con estacionalidad (Black Friday, Navidad, etc.).
2.  **Procesamiento (ETL):** Limpieza, validación de tipos, cálculo de totales y manejo de errores.
3.  **Carga:** Inserción de datos transformados en **PostgreSQL**.
4.  **Visualización:** Consumo de métricas vía **Metabase** y exposición de datos vía **Laravel API**.

### Estructura del Repositorio

proyecto-data-analytics/
├── analytics-app/         # Aplicación Laravel (MVC)
├── etl-python/            # Pipeline de Ingeniería de Datos
│   ├── raw/               # Landing zone (Archivos de entrada)
│   ├── processed/         # Datos procesados con éxito
│   ├── logs/              # Trazabilidad del sistema
│   ├── errors/            # Archivos con fallos de validación
│   ├── scripts/           # Generador de ventas con estacionalidad
│   └── etl.py             # Script core del pipeline
├── docker-compose.yml     # Orquestación de servicios
└── metabase-data/         # Persistencia de Dashboards

🚀 Despliegue Rápido
Solo necesitas tener instalado Docker y Docker Desktop.

  1 Clonar el proyecto:
      git clone [https://github.com/tu-usuario/tu-repositorio.git](https://github.com/tu-usuario/tu-repositorio.git)
      cd tu-repositorio
        Bash
        git clone [https://github.com/tu-usuario/tu-repositorio.git](https://github.com/tu-usuario/tu-repositorio.git)
        cd tu-repositorio
  
  2 Levantar el ecosistema:
      Bash
      docker compose up --build -d
    Este comando levantará todos los servicios y ejecutará el ETL automáticamente por primera vez.

🔗 Puntos de Acceso y Credenciales
    Servicio	      URL	      Credenciales (Default)
    🌐 Laravel       App	      http://localhost:8000	N/A
    📈 Metabase	      http:      //localhost:3000	Configurar al primer inicio
    🗄️ Adminer	      http:      //localhost:8080	Server: postgres
    
    Nota sobre la DB: El nombre de la base de datos es analytics y la contraseña es postgres.

⚙️ Características del Pipeline (ETL)
    -El módulo de Python implementa reglas de robustez industrial:
    -Validación Estricta: Limpieza de strings y conversión de tipos de datos.
    -Lógica de Estacionalidad: Los datos reflejan tendencias reales (Picos en Black Friday, caídas en verano).
    -Manejo de Errores: Los registros corruptos se mueven a la carpeta /errors para auditoría.
    -Procesamiento Incremental: Evita la duplicidad de datos en cargas sucesivas.

🧪 Comandos Útiles
    1 Monitorear el proceso ETL:
        Bash
        docker logs -f etl_python
      
    2 Reiniciar el ciclo de carga de datos:
        Bash
        docker compose restart etl
        
    3 Acceder a la terminal de la Base de Datos:
        Bash
        docker exec -it postgres_data psql -U postgres -d analytics

  📝 Licencia
      Este proyecto es de código abierto y está disponible bajo la Licencia MIT. Ideal para fines educativos y   demostraciones técnicas.
      
  🙌 Autor
      Yonti Testa - Data Engineering & Fullstack Developer Project

      
