<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login SIIPLAS2026</title>
    <link href="<?= base_url('Css/LoginCss/css/style.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('Css/LoginCss/css/custom.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('Css/LoginCss/css/toastr.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('Css/LoginCss/css/tooltipster.bundle.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('Css/LoginCss/css/tooltipster-sideTip-punk.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('Css/LoginCss/css/otp-siat.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('Css/LoginCss/css/login-siat.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('Css/LoginCss/css/all.min.css') ?>" rel="stylesheet" >

</head>
<body class="">
<div class="">
    <div id="kc-header" class="hidden">
        <div id="kc-header-wrapper" class=""><div></div></div>
    </div>
    <div class="">
        <header class="">
            <h1 id="kc-page-title"></h1>
        </header>
      <div id="kc-content">
        <?= $formulario; ?>
      </div>

    </div>
  </div>
  
    <script src=<?= base_url('Js/Index/jquery.min.js') ?>></script>
    <script src=<?= base_url('Js/Index/lg.js') ?>></script>
</body>

</body>
</html>