<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $_ENV['APP_NAME'] ?></title>
  <meta name="description" content="<?= $_ENV['APP_DESCRIPTION'] ?>">
  <link rel="shortcut icon" href="<?= URL_PATH ?>/images/icon/144.png">

  <?php // require_once(__DIR__ . '/manifest.partial.php') ?>

  <script src="<?= URL_PATH ?>/build/script/helpers/theme.js?v=<?= APP_VERSION ?>"></script>

  <link rel="stylesheet" href="<?= URL_PATH ?>/build/css/site.css?v=<?= APP_VERSION ?>">
  <link rel="stylesheet" href="<?= URL_PATH ?>/build/css/nprogress.css?v=<?= APP_VERSION ?>">
  <link rel="stylesheet" href="<?= URL_PATH ?>/libraries/css/fontawesome.css?v=<?= APP_VERSION ?>">

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

<body itemscope itemtype="http://schema.org/WebPage">
  <div class="SiteLayout" id="SiteLayout">
    <div class="SiteLayout-header ">
      <header class="SiteHeader MainContainer" itemscope itemtype="http://schema.org/WPHeader">
        <div class="SiteHeader-left"></div>
        <div class="SiteHeader-right"></div>
      </header>
    </div>
    <div class="SiteLayout-main">
      <?= $content; ?>
    </div>
    <div class="SiteLayout-footer">

    </div>
  </div>
  <script src="<?= URL_PATH ?>/build/script/site/siteLayout.js?v=<?= APP_VERSION ?>"></script>
</body>

</html>
