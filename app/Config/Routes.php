<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// app/Config/Routes.php
$routes->setDefaultController('User');
$routes->get('/', 'User::index');
$routes->get('login', 'User::index');
$routes->post('login/auth', 'User::loginAction');
$routes->get('logout', 'User::logout');

$routes->post('User/get_captcha', 'User::get_captcha');

//// recuperar contraseñas
$routes->get('password', 'User::user_password');
$routes->post('valida_psw', 'User::ValidaPws');

//// archivos adjuntos
$routes->get('documents', 'User::list_documentos');


/// Dashboard
$routes->get('dashboard', 'CDashboard\Dashboard::dashboard_admin');


/// Mantenimiento
$routes->get('mnt/responsables', 'CMantenimiento\Responsables::lista_responsables');

