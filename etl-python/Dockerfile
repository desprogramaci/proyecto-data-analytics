FROM python:3.11

# Instalar cron
RUN apt-get update && apt-get install -y cron

WORKDIR /app

COPY requirements.txt requirements.txt
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

# Copiar el cronjob
COPY cronjob /etc/cron.d/etl-cron

# Dar permisos
RUN chmod 0644 /etc/cron.d/etl-cron

# Registrar el cronjob
RUN crontab /etc/cron.d/etl-cron

# Crear archivo de logs
RUN touch /var/log/cron.log

# Comando final: iniciar cron y dejar el contenedor vivo
CMD python etl.py && cron && tail -f /var/log/cron.log