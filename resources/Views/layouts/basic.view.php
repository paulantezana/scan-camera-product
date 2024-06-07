<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $_ENV['APP_NAME'] ?></title>
  <meta name="description" content="<?= $_ENV['APP_DESCRIPTION'] ?>">
  <link rel="shortcut icon" href="<?= URL_PATH ?>/images/icon/144.png">

  <?php require_once(__DIR__ . '/manifest.partial.php') ?>

  <script src="<?= URL_PATH ?>/build/script/helpers/theme.js?v=<?= APP_VERSION ?>"></script>

  <link rel="stylesheet" href="<?= URL_PATH ?>/build/css/site.css?v=<?= APP_VERSION ?>">
  <link rel="stylesheet" href="<?= URL_PATH ?>/build/css/nprogress.css?v=<?= APP_VERSION ?>">
  <link rel="stylesheet" href="<?= URL_PATH ?>/libraries/css/fontawesome.css?v=<?= APP_VERSION ?>">

  <script>
    var URL_PATH = '<?= URL_PATH ?>';
  </script>
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
</head>

<body itemscope itemtype="http://schema.org/WebPage">
  <div class="SiteLayout" id="SiteLayout">
    <div class="SiteLayout-header"></div>
    <div class="SiteLayout-main">
      <?= $content; ?>
    </div>
    <div class="SiteLayout-footer">
      <div class="MainContainer">
        <a href="<?= $_ENV['APP_AUTHOR_WEB'] ?>" class="copyright" target="_blank">Copyright © <?= date('Y') ?> <?= $_ENV['APP_AUTHOR'] ?></a>
      </div>
    </div>
  </div>
</body>

</html>
