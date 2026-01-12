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
$routes->get('mnt/responsables', 'CMantenimiento\CResponsables::lista_responsables');
$routes->get('mnt/update_responsable/(:num)', 'CMantenimiento\CResponsables::update_responsable/$1');
$routes->post('mnt/get_reg_nal', 'CMantenimiento\CResponsables::get_reg_nal');
$routes->post('mnt/get_dist', 'CMantenimiento\CResponsables::get_distritales'); /// get Distritales segun la regional
$routes->post('mnt/update_resp', 'CMantenimiento\CResponsables::Update_resp'); /// Valida Update Responsable

$routes->get('mnt/Pdf_responsables', 'CMantenimiento\CResponsables_Pdf::Pdf_lista_responsables'); /// Pdf Responsables en base64
$routes->get('mnt/Pdf_responsables_sfirma', 'CMantenimiento\CResponsables_Pdf::Pdf_lista_responsables_para_firmar'); /// Pdf Responsables en base64
