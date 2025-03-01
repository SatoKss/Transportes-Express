
Crear un nuevo proyecto o seleccionar uno existente.

Ir a APIs y Servicios > Biblioteca y habilitar:

Maps JavaScript API

Geocoding API

Directions API

Service Usage API

Ir a la sección de Credenciales y generar una nueva API Key.

Copiar la API Key y agregarla en public/index.php:

<script src="https://maps.googleapis.com/maps/api/js?key=TU_API_KEY&libraries=places"></script>

Nota: Reemplazar TU_API_KEY con la API Key generada.

Obtener las Credenciales de Google Sheets y Configurar la API

Ir a Google Cloud Console.

Crear un nuevo proyecto o seleccionar uno existente.

Ir a APIs y Servicios > Biblioteca y habilitar:

Google Sheets API

Google Drive API

Ir a la sección de Credenciales y generar una cuenta de servicio.

Descargar el archivo JSON de credenciales y guardarlo en config/

Renombra el archivo JSON asi credenciales.json.

Compartir el acceso a la hoja de Google Sheets con el correo de la cuenta de servicio.



5 Instalar Dependencias

Si Composer no está instalado, descárgalo desde getcomposer.org e instálalo.

Luego, abrir la consola de comandos (CMD), navegar a la carpeta del proyecto y ejecutar:

cd C:/xampp/htdocs/transportes
composer install

6️ Acceder a la Aplicación en el Navegador

Abrir XAMPP y asegurarse de que Apache y MySQL estén corriendo.

Abrir el navegador e ingresar la siguiente URL:

http://localhost/transportes/public/index.php

 Funcionalidades Implementadas

 Gestión de Viajes (CRUD): Crear, editar y eliminar viajes con AJAX.
 Visualización de Rutas: Mapa interactivo con Google Maps API.
 Filtros Avanzados: Buscar por ejecutivo, fecha y comuna.
 Reportes SQL Dinámicos: Generación de informes en la interfaz.
 Exportación a Google Sheets: Datos en tiempo real con formato condicional.
 Interfaz Mejorada: Diseño responsivo con Bootstrap.

 ¿Por qué estas funcionalidades?

AJAX: Para mejorar la experiencia sin recargar la página.

Google Maps API: Para mostrar rutas en tiempo real.

Google Sheets API: Para exportar reportes y analizarlos externamente.

Filtros avanzados: Para una búsqueda eficiente dentro de los registros.