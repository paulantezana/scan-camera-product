<div class="SnToolbar">
  <div class="SnToolbar-left">
    <ul class="SnToolbar-menu" style="display: flex;">
      <li>
        <div class="SnToolbar-menu-btn jsAction" id="maintenanceTableReloadBtn" onclick="maintenanceTableReload('product', 'product')" title="Refrescar (F5)"><i class="fas fa-redo-alt SnMr-2"></i> Refrescar </div>
      </li>
      <li>
        <div class="SnToolbar-menu-btnn jsAction" id="maintenanceTableNewBtn" onclick="maintenanceTableNew('product', 'product')"><i class="fa-solid fa-plus SnMr-2"></i>Nuevo </div>
      </li>
      <li>
        <div class="SnToolbar-menu-btn jsAction" id="maintenanceTableImportBtn" onclick="maintenanceTableImport('product', 'product')"><i class="fa-solid fa-upload SnMr-2"></i>Importar</div>
      </li>
    </ul>
  </div>
</div>
<div class="SnContent">
  <div id="drawProductTable"></div>
</div>

<script src="<?= URL_PATH ?>/build/script/admin/product.js?v=<?= APP_VERSION ?>"></script>

<div class="SnModal-wrapper" data-modal="productImportModal" data-maskclose="false">
  <div class="SnModal">
    <div class="SnModal-close" data-modalclose="productImportModal">
      <i class="fas fa-times"></i>
    </div>
    <div class="SnModal-header"><i class="fas fa-upload SnMr-2"></i> Importar</div>
    <div class="SnModal-body">
      <p><a href="<?= URL_PATH ?>/files/product-template.xlsx">Descarga la plantilla</a> y abre en Excel para ver el formato con todos los campos aceptados.</p>
      <div class="SnDivider"></div>
      <form action="" id="productImportModalForm" novalidate>
        <div class="SnForm-item inner required">
          <label for="productImportFile" class="SnForm-label">Archivo</label>
          <div class="SnControl-wrapper">
            <i class="fas fa-file-excel SnControl-prefix"></i>
            <input type="file" class="SnForm-control sm SnControl" id="productImportFile" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
          </div>
        </div>
        <button type="submit" class="SnBtn primary block lg" id="productImportModalFormSubmit"><i class="fas fa-cloud-upload-alt SnMr-2"></i> Subir</button>
      </form>
    </div>
  </div>
</div>
