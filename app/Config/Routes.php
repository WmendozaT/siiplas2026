<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');
$routes->setDefaultController('User');
$routes->get('/', 'User::index');
//$routes->get('admin/proy/list_proy', 'User::listProy', ['as' => 'admin_proy_listado']);
$routes->get('admin/proy/list_proy', 'User::listProy');
