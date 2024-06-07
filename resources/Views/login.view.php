<style>
  .SiteLayout-header {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
  }
</style>
<div class="Login">
  <div class="Login-banner"></div>
  <div class="Login-body">
    <div class="Login-content">
      <h1 class="Login-title">Iniciar sesión</h1>
      <p class="Login-desc">Bienvenido a <?= $_ENV['APP_NAME'] ?></p>

      <form action="" class="SnForm" novalidate id="loginForm">
        <div class="SnForm-item required">
          <label for="email" class="SnForm-label">Nombre de usuario</label>
          <div class="SnControl-wrapper">
            <i class="far fa-user SnControl-prefix"></i>
            <input type="text" class="SnForm-control SnControl" required id="email" placeholder="Nombre de usuario">
          </div>
        </div>
        <div class="SnForm-item required">
          <label for="password" class="SnForm-label">Contraseña</label>
          <div class="SnControl-wrapper">
            <i class="fas fa-key SnControl-prefix"></i>
            <input type="password" class="SnForm-control SnControl" required id="password" placeholder="Contraseña">
            <span class="SnControl-suffix far fa-eye togglePassword"></span>
          </div>
        </div>
        <button type="submit" class="SnBtn block primary lg radio" id="loginFormSubmit"><i class="fas fa-sign-in-alt SnMr-2"></i>Iniciar sesión</button>
        <!-- <p style="text-align: center">
          <a href="<?= URL_PATH ?>/user/forgot"> ¿Olvido su contraseña?</a>
        </p> -->
      </form>
    </div>
  </div>
</div>

<script src="<?= URL_PATH ?>/build/script/site/login.js?v=<?= APP_VERSION ?>"></script>

<div class="SnModal-wrapper" data-modal="userPostLoginModal" data-maskclose="false">
  <div class="SnModal">
    <div class="SnModal-header"><i class="fa-solid fa-building SnMr-2"></i>Empresa</div>
    <div class="SnModal-body">
      <form action="" id="userPostLoginModalForm">
        <div class="SnForm-item inner required">
          <label for="userPostLoginCompanyd" class="SnForm-label">Empresa</label>
          <select class="SnForm-control" id="userPostLoginCompanyd" required></select>
        </div>
        <button type="submit" class="SnBtn primary lg block jsUserAction" id="userPostLoginModalFormSubmit"><i class="fa-solid fa-angles-right SnMr-2"></i>Continuar</button>
      </form>
    </div>
  </div>
</div>
