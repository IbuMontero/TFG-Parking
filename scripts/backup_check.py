import os
import datetime

# Ruta del archivo de backup
ruta_backup = "/root/backup_parking.sql"

if os.path.exists(ruta_backup):
    fecha = datetime.datetime.fromtimestamp(os.path.getmtime(ruta_backup))
    print(f" Última copia encontrada: {fecha}")
else:
    print(" No se encontró el archivo de backup")
