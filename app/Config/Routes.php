<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');
/*$routes->setDefaultController('User');
$routes->get('/', 'User::index');
//$routes->get('admin/proy/list_proy', 'User::listProy', ['as' => 'admin_proy_listado']);
$routes->get('admin/proy/list_proy', 'User::listProy');*/





// app/Config/Routes.php
$routes->setDefaultController('User');
$routes->get('login', 'User::index');
$routes->post('login/auth', 'User::loginAction');
$routes->get('logout', 'User::logout');

// Añade una ruta de ejemplo para después del login
/*$routes->get('dashboard', function(){
    if (!session()->get('isLoggedIn')) {
        return redirect()->to(base_url('login'));
    }
    return view('dashboard_view'); // Necesitarás crear esta vista simple
});*/