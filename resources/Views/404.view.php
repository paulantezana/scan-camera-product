<div class="Result">
    <img src="<?= URL_PATH ?>/images/404.svg" alt="404" style="max-height: 25rem; margin: auto;">
    <?php if (isset($parameter['message']) && $parameter['message'] != '') : ?>
        <p class="Result-description"><?= $parameter['message'] ?></p>
    <?php else: ?>
        <p class="Result-description">Lo sentimos, la página que visitaste no existe</p>
    <?php endif; ?>
    <a href="<?= URL_PATH ?>/" class="SnBtn primary radio"><i class="fas fa-home SnMr-2"></i>Volver al Inicio</a>
</div>