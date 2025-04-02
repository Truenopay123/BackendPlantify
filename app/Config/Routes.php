<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'UsuariosController::procesarLogin');

// Habilitar CORS para todas las rutas con OPTIONS
$routes->options('(:any)', 'UsuariosController::options');

// Rutas para autenticación (UsuariosController)
$routes->post('/login', 'UsuariosController::procesarLogin'); // Iniciar sesión
$routes->post('/logout', 'UsuariosController::logout');      // Cerrar sesión

// Rutas para gestión de usuarios (UsuariosController)
$routes->get('/usuarios', 'UsuariosController::index');      // Obtener todos los usuarios
$routes->post('/usuarios', 'UsuariosController::create');    // Crear un nuevo usuario
$routes->post('/usuarios/update', 'UsuariosController::updatePost');
$routes->get('/operadores', 'UsuariosController::operadores'); // Obtener todos los usuarios operadores

// Rutas para sensores (SensoresController)
$routes->post('/sensores/guardar', 'SensoresController::guardar'); // Guardar datos de sensores
$routes->get('/sensores/historial', 'SensoresController::historial'); // Obtener historial de sensores
$routes->get('/sensores/reporte', 'SensoresController::reporte');

// Rutas para notificaciones
$routes->post('/notificaciones', 'UsuariosController::notificaciones'); // Guardar notificaciones desde Firebase
$routes->get('/notificaciones/historial', 'NotificacionesController::historial'); // Obtener historial de notificaciones
$routes->get('/notificaciones/reporte', 'NotificacionesController::reporte'); // Obtener reporte