<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

 $routes->get('/', 'UsuariosController::procesarLogin');
 $routes->options('(:any)', 'UsuariosController::options');
$routes->get('/registro', 'UsuariosController::registro');
$routes->post('/login', 'UsuariosController::procesarLogin');
$routes->post('/usuarios/procesarRegistro', 'UsuariosController::procesarRegistro');
$routes->get('/logout', 'UsuariosController::logout');