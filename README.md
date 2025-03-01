
1. Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

XAMPP (para Apache y MySQL)

PHP 8+

Composer (para gestionar dependencias de PHP)

Cuenta de Google Cloud (para obtener credenciales de la API de Google Sheets y Google Maps)

 2. Configuración de la API de Google Maps

1️ Crear un nuevo proyecto en Google Cloud

Ir a Google Cloud Console.

Crear un nuevo proyecto o seleccionar uno existente.

2️ Habilitar las siguientes APIs en el proyecto

Maps JavaScript API

Geocoding API

Directions API

Service Usage API

3️ Generar una API Key

Ir a la sección Credenciales y generar una nueva API Key.

Copiar la API Key y agregarla en public/index.php:

<script src="https://maps.googleapis.com/maps/api/js?key=TU_API_KEY&libraries=places"></script>

Nota: Reemplazar TU_API_KEY con la clave generada.

 3. Configuración de la API de Google Sheets

1️ Crear un nuevo proyecto en Google Cloud

Ir a Google Cloud Console.

Crear un nuevo proyecto o seleccionar uno existente.

2️ Habilitar las siguientes APIs en el proyecto

Google Sheets API

Google Drive API

3️ Obtener Credenciales y Configurar la API

Ir a la sección Credenciales y generar una cuenta de servicio.

Descargar el archivo JSON de credenciales y guardarlo en config/.

Renombrar el archivo a credenciales.json.

Compartir el acceso a la hoja de Google Sheets con el correo de la cuenta de servicio.

 Recuerda agregar el ID de la hoja de Google Sheets en exportar_reportes_google_sheets.php en la siguiente línea de código para que la exportación funcione correctamente:

$spreadsheetId = "Tu_id_aqui";

 4. Instalación de Dependencias

Si Composer no está instalado, descárgalo desde getcomposer.org e instálalo.

Luego, abre la consola de comandos (CMD), navega a la carpeta del proyecto y ejecuta:

cd C:/xampp/htdocs/Transportes-Express
composer install

Esto instalará todas las dependencias necesarias, incluyendo la API de Google Sheets.

 5. Acceder a la Aplicación en el Navegador

1️ Iniciar Apache y MySQL en XAMPP

Abre XAMPP y asegúrate de que Apache y MySQL estén corriendo.

2️ Abrir la aplicación en el navegador

http://localhost/Transportes-Express/public/index.php

 6. Funcionalidades Implementadas

✅ Gestión de Viajes (CRUD): Crear, editar y eliminar viajes con AJAX.
✅ Visualización de Rutas: Mapa interactivo con Google Maps API.
✅ Filtros Avanzados: Buscar por ejecutivo, fecha y comuna.
✅ Reportes SQL Dinámicos: Generación de informes en la interfaz.
✅ Exportación a Google Sheets: Datos en tiempo real con formato condicional.
✅ Interfaz Mejorada: Diseño responsivo con Bootstrap.

7. ¿Por qué estas funcionalidades?

🔹 AJAX: Para mejorar la experiencia sin recargar la página.
🔹 Google Maps API: Para mostrar rutas en tiempo real.
🔹 Google Sheets API: Para exportar reportes y analizarlos externamente.
🔹 Filtros avanzados: Para una búsqueda eficiente dentro de los registros.
