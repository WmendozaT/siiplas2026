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
$routes->get('mnt/responsables', 'CMantenimiento\CResponsables::lista_responsables'); /// lista de Responsables POA
$routes->get('mnt/nuevo_responsable', 'CMantenimiento\CResponsables::new_responsables'); /// Form Add Responsable POA
$routes->get('mnt/update_responsable/(:num)', 'CMantenimiento\CResponsables::update_responsable/$1'); /// form Update Responsable POA
$routes->post('mnt/get_reg_nal_add', 'CMantenimiento\CResponsables::get_reg_nal_add'); /// add
$routes->post('mnt/get_reg_nal', 'CMantenimiento\CResponsables::get_reg_nal'); /// update
$routes->post('mnt/get_dist_add', 'CMantenimiento\CResponsables::get_distritales_add'); /// get Add Distritales segun la regional
$routes->post('mnt/get_dist', 'CMantenimiento\CResponsables::get_distritales'); /// get Update Distritales segun la regional
$routes->post('mnt/add_resp', 'CMantenimiento\CResponsables::Add_resp'); /// Valida Add Responsable
$routes->post('mnt/update_resp', 'CMantenimiento\CResponsables::Update_resp'); /// Valida Update Responsable
$routes->post('mnt/verif_usuario', 'CMantenimiento\CResponsables::verif_usuario'); /// Verifica La duplicidad de Usuario
$routes->post('mnt/delete_responsable', 'CMantenimiento\CResponsables::delete_responsable'); /// Elimina Usuario
$routes->get('mnt/exportar_responsablePoa', 'CMantenimiento\CResponsables::exportar_responsables'); /// Exportar Responsable POA

$routes->post('mnt/update_permisos_responsable', 'CMantenimiento\CResponsables::update_permisos_responsable'); /// Permisos para el Usuario


$routes->get('mnt/resp_seguimientopoa', 'CMantenimiento\CResponsables::lista_responsables_seguimientopoa'); /// lista de Responsables-Seguimiento POA
$routes->get('mnt/nuevo_reponsable_seguimientopoa', 'CMantenimiento\CResponsables::new_responsables_segpoa'); /// form Add Responsable POA-Seguimiento POA
$routes->post('mnt/get_aper_seg', 'CMantenimiento\CResponsables::get_aper_add'); /// get Apertura segun la regional-Seguimiento POA
$routes->post('mnt/get_uresp_seg', 'CMantenimiento\CResponsables::get_uresp_add'); /// get Apertura segun la regional-Seguimiento POA
$routes->post('mnt/add_segpoa', 'CMantenimiento\CResponsables::Add_segpoa'); /// Valida Add Responsable-Seguimiento POA
$routes->get('mnt/form_update_segpoa/(:num)', 'CMantenimiento\CResponsables::form_update_segpoa/$1'); /// Form Update Responsable-Seguimiento POA
$routes->post('mnt/update_respspoa', 'CMantenimiento\CResponsables::Update_respspoa'); /// Valida Update Responsable

$routes->post('mnt/Pdf_responsables', 'CMantenimiento\CResponsables_Pdf::Pdf_lista_responsables'); /// Pdf Responsables en base64

//// Para firma digital
$routes->get('mnt/Pdf_responsables_sfirma', 'CMantenimiento\CResponsables_Pdf::Pdf_lista_responsables_para_firmar'); /// Pdf Responsables en base64

//// Mantenimiento - configuracion Sistema
$routes->get('mnt/ConfiguracionSistema', 'CMantenimiento\CConfiguracion::Menu_configuracion'); /// View Configuracion
$routes->post('mnt/update_conf', 'CMantenimiento\CConfiguracion::Update_configuracion'); /// Valida Update Responsable
$routes->post('mnt/update_estado_modulos', 'CMantenimiento\CConfiguracion::update_estado_modulos'); /// Estados para modulo disponibles
$routes->post('mnt/aperturas', 'CMantenimiento\CConfiguracion::valida_aperturas'); /// Valida Aperturas Programaticas