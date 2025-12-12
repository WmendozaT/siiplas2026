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

$routes->get('dashboard', 'CDashboard\Dashboard::dashboard_admin');

