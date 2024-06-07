<div class="SnContent">
  <form action="" class="SnForm" novalidate id="productForm">
    <input type="hidden" class="SnForm-control" id="productId" value="<?= $parameter['id'] ?? '0' ?>">
    <div class="SnForm-item required">
      <label for="productCode" class="SnForm-label">Código</label>
      <div class="SnControl-wrapper">
        <i class="fas fa-laptop-code SnControl-prefix"></i>
        <input type="text" class="SnForm-control SnControl" name="productCode" id="productCode" required value="<?= $parameter['product']['code'] ?? '' ?>" />
      </div>
    </div>
    <div class="SnForm-item required">
      <label for="productDescription" class="SnForm-label">Descripción</label>
      <div class="SnControl-wrapper">
        <i class="fas fa-laptop-code SnControl-prefix"></i>
        <input type="text" class="SnForm-control SnControl" name="productDescription" id="productDescription" required value="<?= $parameter['product']['description'] ?? '' ?>" />
      </div>
    </div>
    <button type="submit" id="productFormSubmit" class="SnBtn lg primary radio block"><i class="fa-solid fa-floppy-disk SnMr-2"></i>Guardar</button>
  </form>
</div>

<script src="<?= URL_PATH ?>/build/script/admin/productForm.js?v=<?= APP_VERSION ?>"></script>
