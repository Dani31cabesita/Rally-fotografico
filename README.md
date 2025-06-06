#  Manual de Instalación

Este proyecto fue desarrollado sobre una máquina virtual Debian 12 con Apache, PHP y MariaDB. La gestión de la base de datos se realizó mediante **Adminer**.

A continuación, se detallan los pasos necesarios para clonar, configurar e instalar el proyecto correctamente en un entorno similar.

---

## 1. Clonar el repositorio desde GitHub

Abre una terminal y ejecuta el siguiente comando para clonar el repositorio:

```bash
git clone https://github.com/Dani31cabesita/Rally-fotografico
```

## 2. Configurar Apache (Servidor Web)

Asegúrate de tener Apache instalado. Si no lo tienes, puedes instalarlo con:

```bash
sudo apt update
sudo apt install apache2
```

Después, copia los archivos del proyecto al directorio raíz de Apache:

```bash
sudo cp -r repositorio/* /var/www/html/
```

Establece los permisos correctos:

```bash
sudo chown -R www-data:www-data /var/www/html/
```

Reinicia Apache para aplicar los cambios:

```bash
sudo systemctl restart apache2
```

---

## 3. Instalar PHP y extensiones necesarias

Instala PHP y las extensiones necesarias:

```bash
sudo apt install php php-mysql php-xml php-mbstring
```

Puedes comprobar que PHP funciona creando un archivo llamado `info.php` en `/var/www/html/` con el siguiente contenido:

```php
<?php phpinfo(); ?>
```

---

## 4. Importar la base de datos con Adminer

1. Asegúrate de que MariaDB esté corriendo:

```bash
sudo systemctl start mariadb
```

2. Accede a Adminer desde tu navegador:

```
http://<IP_DE_LA_MAQUINA>:8080
```

3. Completa los campos del formulario de acceso:
   - **Sistema de base de datos**: MySQL  
   - **Servidor**: `localhost` o la IP de tu máquina (ej: `192.168.1.50`)  
   - **Usuario**: `root` (u otro usuario válido)  
   - **Contraseña**: la que hayas configurado  
   - **Base de datos**: crea una nueva (ejemplo: `rally_fotos`)

4. Una vez dentro, selecciona la base de datos y usa la opción "Importar" para subir el archivo `.sql` incluido en el proyecto (ej: `database/rally_fotos.sql`).

---

## 5. Configurar la conexión a la base de datos

Edita el archivo de conexión en el backend (por ejemplo `config/conexion.php`) con los datos correctos de acceso a tu base de datos:

```php
$host = "localhost";
$db = "rally_fotos";
$user = "root";
$pass = "tu_contraseña";
```

---

## 6. Probar la aplicación

Abre tu navegador y accede a:

```
http://localhost
```

O si estás en red local, desde otro dispositivo:

```
http://<IP_DE_LA_MAQUINA>
```

Deberías ver la página principal del proyecto funcionando correctamente.

---

## Consejos adicionales

- Si no puedes acceder desde otro dispositivo, asegúrate de que el puerto 80 esté abierto:
  ```bash
  sudo ufw allow 80/tcp
  ```

- Si Adminer no se carga, verifica que `adminer.php` esté ubicado en `/var/www/html/`.

- Puedes editar tu archivo `/etc/hosts` en otras máquinas para apuntar a la IP local si quieres usar un nombre de dominio personalizado.
