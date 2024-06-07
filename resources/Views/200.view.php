<div class="Result">
  <!-- <img src="<?= URL_PATH ?>/images/403.svg" alt="403" style="max-height: 25rem; margin: auto;"> -->
  <?php if (isset($parameter['message']) && $parameter['message'] != '') : ?>
    <p class="Result-description">
    <div class="SnAlert warning">
      <span class="SnAlert-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
      <div class="SnAlert-content">
        <strong>VALIDACIÓN</strong>
        <div><?= $parameter['message'] ?></div>
      </div>
    </div>
    </p>
  <?php else : ?>
    <p class="Result-description">VALIDACIÓN</p>
  <?php endif; ?>
  <a href="<?= URL_PATH ?>/" class="SnBtn primary radio"><i class="fas fa-home SnMr-2"></i>Volver al Inicio</a>
</div>
