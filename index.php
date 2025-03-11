<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ---------------------------------------------------------------
// CHECK PHP VERSION
// ---------------------------------------------------------------
$minPhpVersion = '8.1'; // Asegúrate de que este número corresponda a la versión mínima que quieres.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;
    exit(1);
}

// ---------------------------------------------------------------
// SET THE CURRENT DIRECTORY
// ---------------------------------------------------------------
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

// ---------------------------------------------------------------
// BOOTSTRAP THE APPLICATION
// ---------------------------------------------------------------

// Cargar el archivo de configuración de rutas
require FCPATH . '/app/Config/Paths.php';  // Asegúrate de que esta ruta esté correcta

$paths = new Config\Paths(); // Crear la instancia de Paths

// Cargar el archivo de bootstrap del framework CodeIgniter
require $paths->systemDirectory . '/Boot.php';

// Iniciar la aplicación CodeIgniter
exit(CodeIgniter\Boot::bootWeb($paths));
