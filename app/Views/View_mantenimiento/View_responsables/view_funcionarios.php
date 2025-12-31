<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="horizontal">
<head>
  <!-- Required meta tags -->
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" type="image/png" href="<?= base_url('Img/plantillaImg/logo_CNS_header.png') ?>" />
  <link rel="stylesheet" href="<?= base_url('Css/plantillaCss/styles.css') ?>"/>
  <link rel="stylesheet" href="<?= base_url('Css/plantillaCss/dataTables.bootstrap5.min.css') ?>">
  <title><?= session()->get("configuracion")['conf_abrev_sistema'] ?? 'No encontrado' ?></title>
  
</head>
<body>
  <?= session()->get("view_bienvenida") ?? 'No encontrado'?>
  <div class="preloader">
    <img src="<?= base_url('Img/plantillaImg/logo_CNS_header.png') ?>" style="width: 5%;" alt="loader" class="lds-ripple img-fluid" />
  </div>
  
  <div id="main-wrapper">
    <div class="page-wrapper">
      <?= session()->get("view_modulos") ?? 'No encontrado' ?> <!--====  Start MENU BOOTSTRAP ====-->
      <?= session()->get("view_modulos_sidebar") ?? 'No encontrado' ?> <!--====  MENU BOOTSTRAP SIDEBAR ====-->
      <div class="body-wrapper">
        <div class="container-fluid">
          <!--  Header Start -->
          <header class="topbar sticky-top">
            <?= session()->get("view_cabecera_layout") ?? 'No encontrado' ?> <!-- ==== MENU LAYOUT CABECERA ==== -->
            <?= session()->get("view_cabecera") ?? 'No encontrado' ?> <!-- ===== CABECERA SUPERIOR ===== -->
          </header>

          <!--  Header End -->
          <div class="row">
          
          <div class="card">
            <div class="card-body">
              <h4 class="card-title mb-4 pb-2">LISTA DE RESPONSABLES POA</h4>
              <div class="table-responsive pb-4">
                <table id="all-student" class="table table-striped table-bordered border text-nowrap align-middle">
                  <thead>
                    <!-- start row -->
                    <tr>
                      <th>Profile</th>
                      <th>Sec.</th>
                      <th>Subject</th>
                      <th>D.O.B.</th>
                      <th>Phone</th>
                      <th>Email</th>
                      <th></th>
                    </tr>
                    <!-- end row -->
                  </thead>
                  <tbody>
                    <!-- start row -->
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-2.jpg" alt="spike-img" width="45" class="rounded-circle" />
                          </div>

                          <div>
                            <h6 class="mb-1">Sakyu Basu</h6>
                            <p class="fs-3 mb-0">Class: 2</p>
                          </div>
                        </div>
                      </td>
                      <td>A</td>
                      <td>English</td>
                      <td>25/05/2012</td>
                      <td>+ 123 9988568</td>
                      <td>kazifahim93@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="ti ti-eye fs-7"></i>
                        </a>
                      </td>
                    </tr>
                    <!-- end row -->
                    <!-- start row -->
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-2.jpg" alt="spike-img" width="45" class="rounded-circle" />
                          </div>

                          <div>
                            <h6 class="mb-1">Noah</h6>
                            <p class="fs-3 mb-0">Class: 12</p>
                          </div>
                        </div>
                      </td>
                      <td>B</td>
                      <td>Maths</td>
                      <td>12/12/2001</td>
                      <td>+ 123 9988568</td>
                      <td>davidzonar@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="bi bi-info-circle"></i>
                        </a>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-2.jpg" alt="spike-img" width="45" class="rounded-circle" />
                          </div>

                          <div>
                            <h6 class="mb-1">Noah</h6>
                            <p class="fs-3 mb-0">Class: 12</p>
                          </div>
                        </div>
                      </td>
                      <td>B</td>
                      <td>Maths</td>
                      <td>12/12/2001</td>
                      <td>+ 123 9988568</td>
                      <td>davidzonar@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="bi bi-info-circle"></i>
                        </a>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-2.jpg" alt="spike-img" width="45" class="rounded-circle" />
                          </div>

                          <div>
                            <h6 class="mb-1">Noah</h6>
                            <p class="fs-3 mb-0">Class: 12</p>
                          </div>
                        </div>
                      </td>
                      <td>B</td>
                      <td>Maths</td>
                      <td>12/12/2001</td>
                      <td>+ 123 9988568</td>
                      <td>davidzonar@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="bi bi-info-circle"></i>
                        </a>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-2.jpg" alt="spike-img" width="45" class="rounded-circle" />
                          </div>

                          <div>
                            <h6 class="mb-1">Noah</h6>
                            <p class="fs-3 mb-0">Class: 12</p>
                          </div>
                        </div>
                      </td>
                      <td>B</td>
                      <td>Maths</td>
                      <td>12/12/2001</td>
                      <td>+ 123 9988568</td>
                      <td>davidzonar@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="bi bi-info-circle"></i>
                        </a>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-2.jpg" alt="spike-img" width="45" class="rounded-circle" />
                          </div>

                          <div>
                            <h6 class="mb-1">Noah</h6>
                            <p class="fs-3 mb-0">Class: 12</p>
                          </div>
                        </div>
                      </td>
                      <td>B</td>
                      <td>Maths</td>
                      <td>12/12/2001</td>
                      <td>+ 123 9988568</td>
                      <td>davidzonar@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="bi bi-info-circle"></i>
                        </a>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-2.jpg" alt="spike-img" width="45" class="rounded-circle" />
                          </div>

                          <div>
                            <h6 class="mb-1">Noah</h6>
                            <p class="fs-3 mb-0">Class: 12</p>
                          </div>
                        </div>
                      </td>
                      <td>B</td>
                      <td>Maths</td>
                      <td>12/12/2001</td>
                      <td>+ 123 9988568</td>
                      <td>davidzonar@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="bi bi-info-circle"></i>
                        </a>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-2.jpg" alt="spike-img" width="45" class="rounded-circle" />
                          </div>

                          <div>
                            <h6 class="mb-1">Noah</h6>
                            <p class="fs-3 mb-0">Class: 12</p>
                          </div>
                        </div>
                      </td>
                      <td>B</td>
                      <td>Maths</td>
                      <td>12/12/2001</td>
                      <td>+ 123 9988568</td>
                      <td>davidzonar@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="bi bi-info-circle"></i>
                        </a>
                      </td>
                    </tr>
                    <!-- end row -->
                    <!-- start row -->
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-3.jpg" alt="spike-img" width="45" class="rounded-circle" />
                          </div>

                          <div>
                            <h6 class="mb-1">John Deo</h6>
                            <p class="fs-3 mb-0">Class: 5</p>
                          </div>
                        </div>
                      </td>
                      <td>B</td>
                      <td>Science</td>
                      <td>20/10/2007</td>
                      <td>+ 123 9988568</td>
                      <td>ronaldosingh007@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="ti ti-eye fs-7"></i>
                        </a>
                      </td>
                    </tr>
                    <!-- end row -->
                    <!-- start row -->
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-4.jpg" alt="spike-img" width="45" class="rounded-circle" />
                          </div>

                          <div>
                            <h6 class="mb-1">Oliver</h6>
                            <p class="fs-3 mb-0">Class: 5</p>
                          </div>
                        </div>
                      </td>
                      <td>c</td>
                      <td>English</td>
                      <td>26/01/2006</td>
                      <td>+ 123 9988568</td>
                      <td>jackdude224@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="ti ti-eye fs-7"></i>
                        </a>
                      </td>
                    </tr>
                    <!-- end row -->
                    <!-- start row -->
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-5.jpg" alt="spike-img" width="45" class="rounded-circle" />
                          </div>

                          <div>
                            <h6 class="mb-1">John Deo</h6>
                            <p class="fs-3 mb-0">Class: 12</p>
                          </div>
                        </div>
                      </td>
                      <td>c</td>
                      <td>Geography</td>
                      <td>02/10/2001</td>
                      <td>+ 123 9988568</td>
                      <td>patelaleis@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="ti ti-eye fs-7"></i>
                        </a>
                      </td>
                    </tr>
                    <!-- end row -->
                    <!-- start row -->
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-2.jpg" class="rounded-circle" width="45" alt="spike-img" />
                          </div>

                          <div>
                            <h6 class="mb-1">Mark J. Freeman</h6>
                            <p class="fs-3 mb-0">Class: 2</p>
                          </div>
                        </div>
                      </td>
                      <td>A</td>
                      <td>English</td>
                      <td>25/05/2012</td>
                      <td>+ 123 9988568</td>
                      <td>kazifahim93@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="ti ti-eye fs-7"></i>
                        </a>
                      </td>
                    </tr>
                    <!-- end row -->
                    <!-- start row -->
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-2.jpg" class="rounded-circle" width="45" alt="spike-img" />
                          </div>

                          <div>
                            <h6 class="mb-1">Inkyu Basu</h6>
                            <p class="fs-3 mb-0">Class: 12</p>
                          </div>
                        </div>
                      </td>
                      <td>B</td>
                      <td>Maths</td>
                      <td>12/12/2001</td>
                      <td>+ 123 9988568</td>
                      <td>davidzonar@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="ti ti-eye fs-7"></i>
                        </a>
                      </td>
                    </tr>
                    <!-- end row -->
                    <!-- start row -->
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-3.jpg" class="rounded-circle" width="45" alt="spike-img" />
                          </div>

                          <div>
                            <h6 class="mb-1">Kuu Dere</h6>
                            <p class="fs-3 mb-0">Class: 5</p>
                          </div>
                        </div>
                      </td>
                      <td>B</td>
                      <td>Science</td>
                      <td>20/10/2007</td>
                      <td>+ 123 9988568</td>
                      <td>ronaldosingh007@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="ti ti-eye fs-7"></i>
                        </a>
                      </td>
                    </tr>
                    <!-- end row -->
                    <!-- start row -->
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-3">
                            <img src="Img/user-4.jpg" class="rounded-circle" width="45" alt="spike-img" />
                          </div>

                          <div>
                            <h6 class="mb-1">Mai Waifu</h6>
                            <p class="fs-3 mb-0">Class: 10</p>
                          </div>
                        </div>
                      </td>
                      <td>c</td>
                      <td>English</td>
                      <td>26/01/2004</td>
                      <td>+ 123 9988568</td>
                      <td>markmaria223@gmail.com</td>
                      <td>
                        <a href="teacher-details.html" class="link-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                          <i class="ti ti-eye fs-7"></i>
                        </a>
                      </td>
                    </tr>
                    <!-- end row -->
                    <!-- start row -->
                   
                    <!-- end row -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
              
              <br>
              <?= $boton ?>
          </div>
        </div>
      </div>

    <!--  Menu izquierdo -->
    <?= session()->get("view_menu_izquierdo") ?? 'No encontrado' ?>
    <!--  Fin Menu izquierdo -->
    </div>
    <div class="dark-transparent sidebartoggler"></div>
  </div>
  <script src="<?= base_url('Js/PlantillaJs/vendor.min.js') ?>"></script>
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
  <script src="<?= base_url('Js/PlantillaJs/highlight.min.js') ?>"></script>
  <script>
  hljs.initHighlightingOnLoad();
  document.querySelectorAll("pre.code-view > code").forEach((codeBlock) => {
    codeBlock.textContent = codeBlock.innerHTML;
  });
</script>
  <script src="<?= base_url('Js/PlantillaJs/jquery.dataTables.min.js') ?>"></script>
  <script src="<?= base_url('Js/PlantillaJs/datatable.init.js') ?>"></script>
</body>

</html>