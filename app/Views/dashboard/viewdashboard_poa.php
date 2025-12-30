<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="horizontal">

<head>
  <!-- Required meta tags -->
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Favicon icon-->

  <link rel="shortcut icon" type="image/png" href="<?= base_url('Img/plantillaImg/logo_CNS_header.png') ?>" />

  <!-- Core Css -->
  <link rel="stylesheet" href="<?= base_url('Css/plantillaCss/styles.css') ?>"/>

  <title><?= session()->get('dat_conf["conf_gestion"]') ?></title>
  <!-- jvectormap  -->
  <link rel="stylesheet" href="<?= base_url('Css/plantillaCss/jvectormap.css') ?>">
</head>

<body>
  <!-- Toast -->
  <div class="toast toast-onload align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body hstack align-items-start gap-6">
      <i class="ti ti-alert-circle fs-6"></i>
      <div>
        <h5 class="text-white fs-3 mb-1">Bienvenidos</h5>
        <h6 class="text-white fs-2 mb-0">Sistema PoaWeb-CNS</h6>
      </div>
      <button type="button" class="btn-close btn-close-white fs-2 m-0 ms-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
  <!-- Preloader -->
  <div class="preloader">
    <img src="<?= base_url('Img/plantillaImg/logo_CNS_header.png') ?>" style="width: 5%;" alt="loader" class="lds-ripple img-fluid" />
  </div>
  <div id="main-wrapper">




    <!--========== Sidebar Start MENU BOOTSTRAP ================-->
    <aside class="left-sidebar with-vertical">
      <!-- ---------------------------------- -->
      <!-- Start Vertical Layout Sidebar -->
      <!-- ---------------------------------- -->
      <div class="brand-logo d-flex align-items-center justify-content-between">
        <a href="index.html" class="text-nowrap logo-img">
          PoaWeb - CNS
        </a>
        <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
          <i class="ti ti-x"></i>
        </a>
      </div>

      <div class="scroll-sidebar" data-simplebar>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
          <ul id="sidebarnav" class="mb-0">

            <!-- ============================= -->
            <!-- Home -->
            <!-- ============================= -->
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-bold-duotone" class="nav-small-cap-icon fs-5"></iconify-icon>
              <span class="hide-menu">HOME</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link sidebar-link primary-hover-bg" href="" id="get-url" aria-expanded="false">
                <span class="aside-icon p-2 bg-primary-subtle rounded-1">
                  <iconify-icon icon="solar:screencast-2-line-duotone" class="fs-6"></iconify-icon>
                </span>
                <span class="hide-menu ps-1"><?= session()->get('gestion') ?></span>
              </a>
            </li>

            <!-- ============================= -->
            <!-- PEI -->
            <!-- ============================= -->
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-bold-duotone" class="nav-small-cap-icon fs-5"></iconify-icon>
              <span class="hide-menu">PEI</span>
            </li>

                <li class="sidebar-item">
                  <a class="sidebar-link secondary-hover-bg" href="app-notes.html" aria-expanded="false">
                    <span class="aside-icon p-2 bg-secondary-subtle rounded-1">
                      <iconify-icon icon="solar:notification-unread-lines-line-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu ps-1">Objetivos Estrategicos</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link success-hover-bg" href="app-contact.html" aria-expanded="false">
                    <span class="aside-icon p-2 bg-success-subtle rounded-1">
                      <iconify-icon icon="solar:notification-unread-lines-line-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu ps-1">Form SPO N° 1 (ACP)</span>
                  </a>
                </li>

            <!-- ============================= -->
            <!-- PROGRAMACION POA -->
            <!-- ============================= -->
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-bold-duotone" class="nav-small-cap-icon fs-5"></iconify-icon>
              <span class="hide-menu">PROGRAMACIÓN POA</span>
            </li>

                <li class="sidebar-item">
                  <a class="sidebar-link indigo-hover-bg" href="page-pricing.html" aria-expanded="false">
                    <span class="aside-icon p-2 bg-indigo-subtle rounded-1">
                      <iconify-icon icon="solar:document-add-line-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu ps-1">AnteProyecto</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link info-hover-bg" href="page-faq.html" aria-expanded="false">
                    <span class="aside-icon p-2 bg-info-subtle rounded-1">
                      <iconify-icon icon="solar:file-text-line-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu ps-1">Poa Aprobado</span>
                  </a>
                </li>

            <!-- ============================= -->
            <!-- MODIFICACION POA -->
            <!-- ============================= -->
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-bold-duotone" class="nav-small-cap-icon fs-5"></iconify-icon>
              <span class="hide-menu">MODIFICACIÓN POA</span>
            </li>

                <li class="sidebar-item">
                  <a class="sidebar-link sidebar-link indigo-hover-bg" href="classes.html" aria-expanded="false">
                    <span class="aside-icon p-2 bg-indigo-subtle rounded-1">
                      <iconify-icon icon="solar:document-add-line-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu ps-1">Poa Aprobado</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link sidebar-link info-hover-bg" href="attendance.html" aria-expanded="false">
                    <span class="aside-icon p-2 bg-info-subtle rounded-1">
                      <iconify-icon icon="solar:document-add-line-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu ps-1">Techo Presupuestario</span>
                  </a>
                </li>

            <!-- ============================= -->
            <!-- EVALUACION POA -->
            <!-- ============================= -->
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-bold-duotone" class="nav-small-cap-icon fs-5"></iconify-icon>
              <span class="hide-menu">EVALUACIÓN POA</span>
            </li>

                <li class="sidebar-item">
                  <a class="sidebar-link sidebar-link info-hover-bg" href="attendance.html" aria-expanded="false">
                    <span class="aside-icon p-2 bg-info-subtle rounded-1">
                      <iconify-icon icon="solar:document-text-line-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu ps-1">Eval. Form SPO N° 1</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link sidebar-link info-hover-bg" href="attendance.html" aria-expanded="false">
                    <span class="aside-icon p-2 bg-info-subtle rounded-1">
                      <iconify-icon icon="solar:document-text-line-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu ps-1">Eval. Form SPO N° 2</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link sidebar-link info-hover-bg" href="attendance.html" aria-expanded="false">
                    <span class="aside-icon p-2 bg-info-subtle rounded-1">
                      <iconify-icon icon="solar:document-text-line-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu ps-1">Eval. Form SPO N° 4</span>
                  </a>
                </li> 

            <!-- ============================= -->
            <!-- CERTIFICACION POA -->
            <!-- ============================= -->
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-bold-duotone" class="nav-small-cap-icon fs-5"></iconify-icon>
              <span class="hide-menu">CERTIFICACIÓN POA</span>
            </li>

                <li class="sidebar-item">
                  <a class="sidebar-link sidebar-link info-hover-bg" href="attendance.html" aria-expanded="false">
                    <span class="aside-icon p-2 bg-info-subtle rounded-1">
                      <iconify-icon icon="solar:book-2-line-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu ps-1">Form SPO N° 5</span>
                  </a>
                </li> 

            <!-- ============================= -->
            <!-- Mantenimiento -->
            <!-- ============================= -->
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-bold-duotone" class="nav-small-cap-icon fs-5"></iconify-icon>
              <span class="hide-menu">MANTENIMIENTO</span>
            </li>

                <li class="sidebar-item">
                  <a class="sidebar-link sidebar-link info-hover-bg" href="attendance.html" aria-expanded="false">
                    <span class="aside-icon p-2 bg-info-subtle rounded-1">
                      <iconify-icon icon="solar:document-text-line-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu ps-1">Responsables</span>
                  </a>
                </li> 

                <li class="sidebar-item">
                  <a class="sidebar-link has-arrow indigo-hover-bg" href="javascript:void(0)" aria-expanded="false">
                    <span class="aside-icon p-2 bg-indigo-subtle rounded-1">
                      <iconify-icon icon="solar:tablet-line-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu ps-1">Configuración Sistema</span>
                  </a>
                  <ul aria-expanded="false" class="collapse first-level">
                    <li class="sidebar-item">
                      <a href="table-basic.html" class="sidebar-link">
                        <span class="sidebar-icon"></span>
                        <span class="hide-menu">Basic Table</span>
                      </a>
                    </li>

                    <li class="sidebar-item">
                      <a href="table-dark-basic.html" class="sidebar-link">
                        <span class="sidebar-icon"></span>
                        <span class="hide-menu">Dark Basic Table</span>
                      </a>
                    </li>

                    <li class="sidebar-item">
                      <a href="table-sizing.html" class="sidebar-link">
                        <span class="sidebar-icon"></span>
                        <span class="hide-menu">Sizing Table</span>
                      </a>
                    </li>

                    <li class="sidebar-item">
                      <a href="table-layout-coloured.html" class="sidebar-link">
                        <span class="sidebar-icon"></span>
                        <span class="hide-menu">Coloured Table</span>
                      </a>
                    </li>
                  </ul>
                </li>
          </ul>
        </nav>
        <!-- End Sidebar navigation -->
      </div>

      <div class=" fixed-profile mx-3 mt-3">
        <div class="card bg-primary-subtle mb-0 shadow-none">
          <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between gap-3">
              <div class="d-flex align-items-center gap-3">
                <img src="<?= base_url('Img/plantillaImg/user-1.jpg') ?>" width="45" height="45" class="img-fluid rounded-circle" alt="spike-img" />
                <div>
                  <h5 class="mb-1">Wilmer Mendoza</h5>
                  <p class="mb-0">Administrador</p>
                </div>
              </div>
              <a href="authentication-login.html" class="position-relative" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Logout">
                <iconify-icon icon="solar:logout-line-duotone" class="fs-8"></iconify-icon>
              </a>
            </div>
          </div>
        </div>
      </div>

    </aside>
    <!--==================== FIN MENU BOOTSTRAP =========================-->









    <!--  Sidebar End -->
    <div class="page-wrapper">




      <!--==================  SEGUNDO MENU ======================-->
      <aside class="left-sidebar with-horizontal">
        <!-- Sidebar scroll-->
        <div>
          <!-- Sidebar navigation-->
          <nav id="sidebarnavh" class="sidebar-nav scroll-sidebar container-fluid">
            <ul id="sidebarnav">
              <!-- ============================= -->
              <!-- Home -->
              <!-- ============================= -->
              <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu">Home</span>
              </li>
              <!-- =================== -->
              <!-- Dashboard -->
              <!-- =================== -->
              <li class="sidebar-item">
                <a class="sidebar-link sidebar-link primary-hover-bg" href="index.html" aria-expanded="false">
                  <iconify-icon icon="solar:atom-line-duotone" class="fs-6 aside-icon"></iconify-icon>
                  <span class="hide-menu ps-1">Dashboard</span>
                </a>
              </li>

              <!-- ============================= -->
              <!-- PEI -->
              <!-- ============================= -->
              <li class="sidebar-item">
                <a class="sidebar-link has-arrow indigo-hover-bg" href="javascript:void(0)" aria-expanded="false">
                  <iconify-icon icon="solar:archive-broken" class="fs-6 aside-icon"></iconify-icon>
                  <span class="hide-menu">Pei - CNS</span>
                </a>
                <ul aria-expanded="false" class="collapse first-level">
                  <li class="sidebar-item">
                    <a href="app-calendar.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu">Mision / Vision</span>
                    </a>
                  </li>
                  <li class="sidebar-item">
                    <a href="app-kanban.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu">Objetivos Estrategicos</span>
                    </a>
                  </li>
                  <li class="sidebar-item">
                    <a href="app-chat.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu">Form SPO N° 1 (ACP)</span>
                    </a>
                  </li>
                </ul>
              </li>

              <!-- ============================= -->
              <!-- PROGRAMACION POA -->
              <!-- ============================= -->
              <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu">Programación</span>
              </li>
              <li class="sidebar-item">
                <a class="sidebar-link has-arrow warning-hover-bg" href="javascript:void(0)" aria-expanded="false">
                  <iconify-icon icon="solar:document-text-line-duotone" class="fs-6 aside-icon"></iconify-icon>
                  <span class="hide-menu ps-1">Programación</span>
                </a>
                <ul aria-expanded="false" class="collapse first-level">
                  <li class="sidebar-item">
                    <a href="frontend-landingpage.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu">AnteProyecto POA</span>
                    </a>
                  </li>
                  <li class="sidebar-item">
                    <a href="frontend-aboutpage.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu">Poa Aprobado</span>
                    </a>
                  </li>
                </ul>
              </li>
              <!-- ============================= -->

              <!-- MODIFICACION POA -->
              <!-- ============================= -->
              <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu">Modificación</span>
              </li>
              <li class="sidebar-item">
                <a class="sidebar-link has-arrow primary-hover-bg" href="javascript:void(0)" aria-expanded="false">
                  <iconify-icon icon="solar:file-text-line-duotone" class="fs-6 aside-icon"></iconify-icon>
                  <span class="hide-menu ps-1">Modificación</span>
                </a>
                <ul aria-expanded="false" class="collapse first-level">
                  <!-- Teachers -->
                  <li class="sidebar-item">
                    <a href="all-teacher.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu text-truncate">All Teachers</span>
                    </a>
                  </li>
                  <li class="sidebar-item">
                    <a href="teacher-details.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu text-truncate"> Teachers Details</span>
                    </a>
                  </li>
                </ul>
              </li>
              <!-- ============================= -->

              <!-- EVALUACION POA -->
              <!-- ============================= -->
              <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu">Evaluación</span>
              </li>
              <li class="sidebar-item">
                <a class="sidebar-link has-arrow success-hover-bg" href="javascript:void(0)" aria-expanded="false">
                  <iconify-icon icon="solar:book-2-line-duotone" class="fs-6 aside-icon"></iconify-icon>
                  <span class="hide-menu ps-1">Evaluación</span>
                </a>
                <ul aria-expanded="false" class="collapse first-level">
                  <!-- Teachers -->
                  <li class="sidebar-item">
                    <a href="all-teacher.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu text-truncate">Eval 1</span>
                    </a>
                  </li>
                  <li class="sidebar-item">
                    <a href="teacher-details.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu text-truncate"> Reporte Evaluacion</span>
                    </a>
                  </li>
                </ul>
              </li>
              <!-- ============================= -->

              <!-- CERTIFICACION POA -->
              <!-- ============================= -->
              <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu">Certificación</span>
              </li>
              <li class="sidebar-item">
                <a class="sidebar-link has-arrow warning-hover-bg" href="javascript:void(0)" aria-expanded="false">
                  <iconify-icon icon="solar:bedside-table-2-line-duotone" class="fs-6 aside-icon"></iconify-icon>
                  <span class="hide-menu ps-1">Certificación</span>
                </a>
                <ul aria-expanded="false" class="collapse first-level">
                  <li class="sidebar-item">
                    <a href="table-basic.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu text-truncate">Poa</span>
                    </a>
                  </li>
                  <li class="sidebar-item">
                    <a href="table-basic.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu text-truncate">Cert 2</span>
                    </a>
                  </li>
                </ul>
              </li>

              <!-- ============================= -->
              <!-- MANTENIMIENTO -->
              <!-- ============================= -->
              <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="hide-menu">Mantenimiento</span>
              </li>
              <li class="sidebar-item">
                <a class="sidebar-link has-arrow info-hover-bg" href="javascript:void(0)" aria-expanded="false">
                  <iconify-icon icon="solar:lock-keyhole-line-duotone" class="fs-6 aside-icon"></iconify-icon>
                  <span class="hide-menu ps-1">Mantenimiento</span>
                </a>
                <ul aria-expanded="false" class="collapse first-level">
                  <li class="sidebar-item">
                    <a href="authentication-error.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu text-truncate">Configuración</span>
                    </a>
                  </li>

                  <li class="sidebar-item">
                    <a href="authentication-login.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu text-truncate">Responsables</span>
                    </a>
                  </li>

                  <li class="sidebar-item">
                    <a href="authentication-login2.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu text-truncate">Partidas</span>
                    </a>
                  </li>

                  <li class="sidebar-item">
                    <a href="authentication-register.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu text-truncate">Programas</span>
                    </a>
                  </li>
                  <!-- datatable -->
                  <li class="sidebar-item">
                    <a href="authentication-register2.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu text-truncate">Estructura Organizacional</span>
                    </a>
                  </li>

                  <li class="sidebar-item">
                    <a href="authentication-forgot-password.html" class="sidebar-link">
                      <span class="sidebar-icon"></span>
                      <span class="hide-menu text-truncate">Techo Presupuestario</span>
                    </a>
                  </li>

                </ul>
              </li>

            </ul>
        </div>
      </aside>
      <!--========================== FIN SEGUNDO MENU ===========================-->



      <div class="body-wrapper">
        <div class="container-fluid">
          <!--  Header Start -->
          <header class="topbar sticky-top">
            

            <!-- ================== MENU BOOSTRAP CABECERA ====================== -->
            <div class="with-vertical">
              <!-- Start Vertical Layout Header -->
              <!-- ---------------------------------- -->
              <nav class="navbar navbar-expand-lg p-0">
                <ul class="navbar-nav">
                  <li class="nav-item nav-icon-hover-bg rounded-circle">
                    <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                      <iconify-icon icon="solar:list-bold-duotone" class="fs-7"></iconify-icon>
                    </a>
                  </li>
                </ul>


                <div class="d-block d-lg-none py-3" style="color:white">
                  <img src="<?= base_url('Img/plantillaImg/logo_CNS_header.png') ?>" class="dark-logo" width="40" alt="spike-img" />&nbsp;&nbsp;<b>PoaWeb-CNS - Departamento Nacional de Planificación</b>
                </div>


                <a class="navbar-toggler p-0 border-0" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="p-2">
                    <i class="ti ti-dots fs-7"></i>
                  </span>
                </a>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                  <div class="d-flex align-items-center justify-content-between">
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">

                      <li class="nav-item dropdown">
                        <a class="nav-link position-relative ms-6" href="javascript:void(0)" id="drop1" aria-expanded="false">
                          <div class="d-flex align-items-center flex-shrink-0">
                            <div class="user-profile me-sm-3 me-2">
                              <img src="<?= base_url('Img/plantillaImg/user-1.jpg') ?>" width="40" class="rounded-circle" alt="spike-img">
                            </div>
                            <span class="d-sm-none d-block"><iconify-icon icon="solar:alt-arrow-down-line-duotone"></iconify-icon></span>

                            <div class="d-none d-sm-block">
                              <h6 class="fs-4 mb-1 profile-name" >
                                Wilmer Mendoza
                              </h6>
                              <p class="fs-3 lh-base mb-0 profile-subtext">
                                Administrador
                              </p>
                            </div>
                          </div>
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                          <div class="profile-dropdown position-relative" data-simplebar>
                            <div class="d-flex align-items-center justify-content-between pt-3 px-7">
                              <h3 class="mb-0 fs-5">PerfiL Usuario</h3>
                            </div>

                            <div class="d-flex align-items-center mx-7 py-9 border-bottom">
                              <img src="<?= base_url('Img/plantillaImg/user-1.jpg') ?>" alt="user" width="90" class="rounded-circle" />
                              <div class="ms-4">
                                <h4 class="mb-0 fs-5 fw-normal" >Wilmer Mendoza</h4>
                                <span class="text-muted" style="color:white;">Administrador</span>
                                <p class="text-muted mb-0 mt-1 d-flex align-items-center">
                                  <iconify-icon icon="solar:mailbox-line-duotone" class="fs-4 me-1"></iconify-icon>
                                  info@spike.com
                                </p>
                              </div>
                            </div>
                            <div class="py-6 px-7 mb-1">
                              <a href="authentication-login.html" class="btn btn-primary w-100">Cerrar Sesión</a>
                            </div>
                          </div>
                        </div>
                      </li>
                      <!-- ------------------------------- -->
                      <!-- end profile Dropdown -->
                      <!-- ------------------------------- -->
                    </ul>
                  </div>
                </div>
              </nav>
            </div>
            <!-- ================== FIN MENU BOOSTRAP CABECERA ====================== -->



            <!-- ================== CABECERA SUPERIOR ====================== -->
            <div class="app-header with-horizontal">
              <nav class="navbar navbar-expand-xl container-fluid p-0">
                <ul class="navbar-nav">
                  <li class="nav-item d-none d-xl-block">
                    <a href="index.html" class="text-nowrap nav-link" style="color:#ffffff; font-size: 25px;">
                      <img src="<?= base_url('Img/plantillaImg/logo_CNS_header.png') ?>" class="dark-logo" width="35" alt="spike-img"/>&nbsp;&nbsp;<b>PoaWeb-CNS</b>
                    </a>
                  </li>
                </ul>
                <a class="navbar-toggler p-0 border-0" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="p-2">
                    <i class="ti ti-dots fs-7"></i>
                  </span>
                </a>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                  <div class="d-flex align-items-center justify-content-between">
                    <a href="javascript:void(0)" class="nav-link d-flex d-lg-none align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar" aria-controls="offcanvasWithBothOptions">
                      <div class="nav-icon-hover-bg rounded-circle ">
                        <i class="ti ti-align-justified fs-7"></i>
                      </div>
                    </a>


                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                      <li class="nav-item dropdown">
                        <a class="nav-link position-relative ms-6" href="javascript:void(0)" id="drop1" aria-expanded="false">
                          <div class="d-flex align-items-center flex-shrink-0">
                            <div class="user-profile me-sm-3 me-2">
                              <img src="<?= base_url('Img/plantillaImg/user-1.jpg') ?>" width="40" class="rounded-circle" alt="spike-img">
                            </div>
                            <span class="d-sm-none d-block"><iconify-icon icon="solar:alt-arrow-down-line-duotone"></iconify-icon></span>

                            <div class="d-none d-sm-block">
                              <h6 class="fs-4 mb-1 profile-name" style="color:white;">
                                Wilmer Mendoza
                              </h6>
                              <p class="fs-3 lh-base mb-0 profile-subtext" style="color:white;">
                                Administrador 
                              </p>
                            </div>
                          </div>
                        </a>

                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                          <div class="profile-dropdown position-relative" data-simplebar>
                            <div class="d-flex align-items-center justify-content-between pt-3 px-7">
                              <h3 class="mb-0 fs-5">Perfil Usuario</h3>

                            </div>

                            <div class="d-flex align-items-center mx-7 py-9 border-bottom">
                              <img src="<?= base_url('Img/plantillaImg/user-1.jpg') ?>" alt="user" width="90" class="rounded-circle" />
                              <div class="ms-4">
                                <h4 class="mb-0 fs-5 fw-normal">Wilmer Mendoza</h4>
                                <span class="text-muted">Administrador</span>
                                <p class="text-muted mb-0 mt-1 d-flex align-items-center">
                                  <iconify-icon icon="solar:mailbox-line-duotone" class="fs-4 me-1"></iconify-icon>
                                  wilmer.mendoza@cns.gob
                                </p>
                              </div>
                            </div>

                            <div class="py-6 px-7 mb-1">
                              <a href="authentication-login.html" class="btn btn-primary w-100">Cerrar Sesión</a>
                            </div>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </nav>
            </div>
            <!-- ================== FIN CABECERA SUPERIOR ====================== -->


          </header>





          <!--  Header End -->
          <div class="row">
           <?= session()->get('dat_conf["conf_gestion"]') ?>
          </div>







      </div>
    </div>


    <!--  Menu izquierdo -->
    <button class="btn btn-primary p-3 rounded-circle d-flex align-items-center justify-content-center customizer-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
      <i class="icon ti ti-settings fs-7"></i>
    </button>

    <div class="offcanvas customizer offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
      <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
        <h4 class="offcanvas-title fw-semibold" id="offcanvasExampleLabel">
          DESCARGAS
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body h-n80" data-simplebar>
        <h6 class="fw-semibold fs-4 mb-2">Archivos</h6>
            aqui va el listado de archivos
      </div>
    </div>
    <!--  Fin Menu izquierdo -->


  </div>
  <div class="dark-transparent sidebartoggler"></div>
  </div>

  <script src="Js/vendor.min.js"></script>
  <!-- Import Js Files -->
  <script src="<?= base_url('Js/PlantillaJs/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('Js/PlantillaJs/simplebar.min.js') ?>"></script>
  <script src="<?= base_url('Js/PlantillaJs/app.horizontal.init.js') ?>"></script>
  <script src="<?= base_url('Js/PlantillaJs/theme.js') ?>"></script>
  <script src="<?= base_url('Js/PlantillaJs/app.min.js') ?>"></script>
  <script src="<?= base_url('Js/PlantillaJs/sidebarmenu.js') ?>"></script>
  <script src="<?= base_url('Js/PlantillaJs/feather.min.js') ?>"></script>

  <!-- solar icons -->
  <script src="<?= base_url('Js/PlantillaJs/iconify-icon.min.js') ?>"></script>

  <!-- highlight.js (code view) -->
  <script src="<?= base_url('Js/PlantillaJs/Js/highlight.min.js') ?>"></script>
  <script>
  hljs.initHighlightingOnLoad();


  document.querySelectorAll("pre.code-view > code").forEach((codeBlock) => {
    codeBlock.textContent = codeBlock.innerHTML;
  });
</script>
  <script src="<?= base_url('Js/PlantillaJs/Js/jquery-jvectormap.min.js') ?>"></script>
  <script src="<?= base_url('Js/PlantillaJs/apexcharts.min.js') ?>"></script>
  <script src="<?= base_url('Js/PlantillaJs/query-jvectormap-us-aea-en.js') ?>"></script>
  <script src="<?= base_url('Js/PlantillaJs/Js/dashboard.js') ?>"></script>
</body>

</html>