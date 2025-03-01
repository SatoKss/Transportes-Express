Guía para Probar la Página Web

Este proyecto es un Panel de Administración de Viajes que permite gestionar registros de viajes, visualizar rutas en Google Maps, generar reportes SQL y exportarlos a Google Sheets. A continuación, se detallan los pasos para probar la aplicación correctamente.

Pasos para Probar la Aplicación

1️⃣ Instalar XAMPP (Si no está instalado)

Si XAMPP no está instalado, descárgalo desde apachefriends.org e instálalo.

2️⃣ Clonar el Repositorio en la Carpeta Correcta

Abrir la consola de comandos (CMD) en Windows.

Navegar a la carpeta htdocs de XAMPP:

cd C:/xampp/htdocs/

Clonar el repositorio dentro de htdocs:

git clone https://github.com/tu-usuario/proyecto-viajes.git transportes

3️⃣ Configurar la Base de Datos en phpMyAdmin con XAMPP

Iniciar XAMPP y asegurarse de que Apache y MySQL estén corriendo.

Abrir phpMyAdmin desde http://localhost/phpmyadmin/.

Crear una nueva base de datos llamada transportes.

Importar el archivo transportes.sql:

Ir a la pestaña Importar en phpMyAdmin.

Seleccionar el archivo transportes.sql.

Hacer clic en Ejecutar para cargar los datos.

4️⃣ Configurar el Proyecto

En src/db.php, actualizar los datos de conexión a la base de datos si es necesario:

$host = "localhost";
$user = "root"; // Usuario por defecto en XAMPP
$pass = ""; // Sin contraseña por defecto
$db = "transportes";

Configurar la API Key de Google Maps en public/index.php:

<script src="https://maps.googleapis.com/maps/api/js?key=TU_API_KEY&libraries=places"></script>

Nota: Reemplazar TU_API_KEY con una API Key válida de Google Maps.

5️⃣ Instalar Dependencias

Si Composer no está instalado, descárgalo desde getcomposer.org e instálalo.

Luego, abrir la consola de comandos (CMD), navegar a la carpeta del proyecto y ejecutar:

cd C:/xampp/htdocs/transportes
composer install

6️⃣ Acceder a la Aplicación en el Navegador

Abrir XAMPP y asegurarse de que Apache y MySQL estén corriendo.

Abrir el navegador e ingresar la siguiente URL:

http://localhost/transportes/public/index.php

 Funcionalidades Implementadas

✅ Gestión de Viajes (CRUD): Crear, editar y eliminar viajes con AJAX.
✅ Visualización de Rutas: Mapa interactivo con Google Maps API.
✅ Filtros Avanzados: Buscar por ejecutivo, fecha y comuna.
✅ Reportes SQL Dinámicos: Generación de informes en la interfaz.
✅ Exportación a Google Sheets: Datos en tiempo real con formato condicional.
✅ Interfaz Mejorada: Diseño responsivo con Bootstrap.

 ¿Por qué estas funcionalidades?

AJAX: Para mejorar la experiencia sin recargar la página.

Google Maps API: Para mostrar rutas en tiempo real.

Google Sheets API: Para exportar reportes y analizarlos externamente.

Filtros avanzados: Para una búsqueda eficiente dentro de los registros.



