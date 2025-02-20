<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'UsuarioController::index');

$routes->get('usuarios', 'UsuarioController::index');


