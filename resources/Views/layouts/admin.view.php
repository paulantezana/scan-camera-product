<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $parameter['title'] ?? 'Home' ?> | <?= $parameter['app']['commercial_reason'] ?? $_ENV['APP_NAME'] ?></title>
  <meta name="description" content="<?= $_ENV['APP_DESCRIPTION'] ?>">
  <link rel="shortcut icon" href="<?= URL_PATH ?>/images/icon/144.png">

  <?php // require_once(__DIR__ . '/manifest.partial.php') ?>

  <script src="<?= URL_PATH ?>/build/script/helpers/theme.js?v=<?= APP_VERSION ?>"></script>

  <link rel="stylesheet" href="<?= URL_PATH ?>/build/css/admin.css?v=<?= APP_VERSION ?>">
  <link rel="stylesheet" href="<?= URL_PATH ?>/build/css/nprogress.css?v=<?= APP_VERSION ?>">
  <link rel="stylesheet" href="<?= URL_PATH ?>/libraries/css/fontawesome.css?v=<?= APP_VERSION ?>">
  <link rel="stylesheet" href="<?= URL_PATH ?>/build/css/slimselect.css?v=<?= APP_VERSION ?>">
  <!-- <link rel="stylesheet" href="<?= URL_PATH ?>/libraries/css/datepicker.min.css?v=<?= APP_VERSION ?>"> -->

  <script>
    var URL_PATH = '<?= URL_PATH ?>';
    var STORAGE_PUBLIC_URL = '<?= $_ENV['STORAGE_PUBLIC_URL'] ?>';
  </script>
  <script src="<?= URL_PATH ?>/libraries/js/sedna.js?v=<?= APP_VERSION ?>"></script>
  <script src="<?= URL_PATH ?>/libraries/js/pristine.min.js?v=<?= APP_VERSION ?>"></script>
  <script src="<?= URL_PATH ?>/libraries/js/nprogress.js?v=<?= APP_VERSION ?>"></script>
  <script src="<?= URL_PATH ?>/build/script/helpers/common.js?v=<?= APP_VERSION ?>"></script>

  <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
</head>

<body>
  <div class="AdminLayout" id="AdminLayout">
    <div class="AdminLayout-header">
      <header class="Header">
        <div class="Header-left">
          <div id="AsideMenu-toggle"><i class="fas fa-bars"></i></div>
          <a href="<?= URL_PATH ?>/admin" class="Header-branding" id="brandingLink">
            <img src="<?= URL_PATH . '/images/icon/144.png' ?>" id="headerCompanyLogo" class="Header-img">
          </a>
          <span class="Header-title">Admin</span>
        </div>
        <div class="Header-center"></div>
        <div class="Header-right UserMenu">
          <div class="SnDropdown">
            <div class="SnDropdown-toggle SnAvatar jsGlobalUserAvatar SnMl-2 SnMr-2">
              <div class="SnAvatar-text"><i class="fas fa-user"></i></div>
            </div>
            <div class="SnDropdown-list no-closable">
              <ul class="SnList menu profile">
                <li class="SnList-item SnMt-2 SnMb-2">
                  <a href="<?= URL_PATH ?>/user/update">
                    <div class="SnAvatar SnMr-3 jsGlobalUserAvatar">
                      <div class="SnAvatar-text"><i class="fas fa-user"></i></div>
                    </div>
                    <div>
                      <div class="UserMenu-title"><strong><?= $_SESSION[SESS_USER]['user_name'] ?></strong></div>
                      <div class="UserMenu-description"><?= $_SESSION[SESS_USER]['user_name'] ?></div>
                    </div>
                  </a>
                </li>
                <li class="SnList-item"><a href="<?= URL_PATH ?>/user/update"><i class="fas fa-user SnMr-2"></i>Perfil</a></li>
                <!-- <li><a href="<?= URL_PATH ?>/"><i class="fa-solid fa-globe SnMr-2"></i>Sitio web público</a></li> -->
                <li class="SnList-item"><a href="#" class="flex"><i class="fa-solid fa-moon SnMr-2"></i>Dark mode <span class="SnMl-2"><input type="checkbox" class="switch" id="snTheme"></span> </a></li>
                <li class="SnList-item"><a href="<?= URL_PATH ?>/user/logout"><i class="fas fa-sign-out-alt SnMr-2"></i>Cerrar sesión</a></li>
              </ul>
            </div>
          </div>

        </div>
      </header>
    </div>
    <div class="AdminLayout-asideLeft" id="adminLayoutAside">
      <div id="AsideMenu-wrapper" class="AsideMenu-wrapper">
        <div class="AsideMenu-container">
          <div class="AsideHeader">
            <div class="Branding">
              <a href="<?= URL_PATH ?>/admin" class="Branding-link" id="brandingLink">
                <img src="<?= URL_PATH . '/images/icon/144.png' ?>" id="headerCompanyLogo" class="Branding-img">
                <span class="Branding-name">app</span>
              </a>
            </div>
          </div>
          <ul class="AsideMenu" id="AsideMenu">
            <li>
              <a href="<?= URL_PATH ?>/admin/product/validate"><i class="fa-solid fa-check-double AsideMenu-icon"></i><span>Validar</span> </a>
            </li>
            <li>
              <a href="<?= URL_PATH ?>/admin/product"><i class="fa-solid fa-boxes-stacked AsideMenu-icon"></i><span>Listar Productos</span> </a>
            </li>
          </ul>
          <div class="AsideFooter"></div>
        </div>
      </div>
    </div>
    <div class="AdminLayout-body">
      <div class="AdminLayout-body-main" id="adminLayoutBodyMain">
        <?php echo $content ?>
      </div>
      <div class="AdminLayout-body-panelRight"></div>
    </div>
  </div>

  <script src="<?= URL_PATH ?>/build/script/admin/adminLayout.js?v=<?= APP_VERSION ?>"></script>
</body>

</html>
