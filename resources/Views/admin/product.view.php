<div class="SnToolbar">
  <div class="SnToolbar-left">
    <ul class="SnToolbar-menu" style="display: flex;">
      <li>
        <div class="SnToolbar-menu-btn  jsAction" id="maintenanceTableReloadBtn" onclick="maintenanceTableReload('product', 'product')" title="Refrescar (F5)"><i class="fas fa-redo-alt SnMr-2"></i> Refrescar </div>
      </li>
      <li>
        <div class="SnToolbar-menu-btn  jsAction" id="maintenanceTableNewBtn" onclick="maintenanceTableNew('product', 'product')"><i class="fa-solid fa-plus SnMr-2"></i>Nuevo </div>
      </li>
    </ul>
  </div>
</div>
<div class="SnContent">
  <div id="drawProductTable"></div>
</div>

<script src="<?= URL_PATH ?>/build/script/admin/product.js"></script>
