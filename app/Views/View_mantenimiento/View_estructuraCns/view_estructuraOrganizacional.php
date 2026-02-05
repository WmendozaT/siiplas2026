<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="horizontal">
<head>
  <!-- Required meta tags -->
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" type="image/png" href="<?= base_url('Img/plantillaImg/logo_CNS_header.png') ?>" />
  <link rel="stylesheet" href="<?= base_url('Css/plantillaCss/styles.css') ?>"/>
  
  <title><?= session()->get("configuracion")['conf_abrev_sistema'] ?? 'No encontrado' ?></title>
  <link rel="stylesheet" href="<?= base_url('Css/plantillaCss/jquery-jvectormap.css') ?>">

  <meta name="csrf-token-name" content="<?= csrf_token() ?>">
  <meta name="csrf-token-value" content="<?= csrf_hash() ?>">
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
            <?= $formulario; ?>
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
  <script src=<?= base_url('Js/Mantenimiento/JsEstructuraOrganizacional.js') ?>></script>
  <script src=<?= base_url('Js/Inactividad.js') ?>></script>
</body>

</html>