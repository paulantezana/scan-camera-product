<style>
  .cameraOn1 {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 10rem !important;
    height: 10rem !important;
    border-radius: 100%;

    border-style: dashed;

    font-size: 2rem;
  }

  [data-modal="cameraModal"] {
    .SnModal {
      border-radius: 0;
      border: 0;
      background: none;
    }
  }

  /* #qrReader {
    width: 100vw;
    height: 100vh;
  } */
</style>


<div style="height: 100%; display: flex; align-items: center; justify-content: center;">
  <div class="SnBtn lg cameraOn1" onclick="startCamera()">
    <i class="fa-solid fa-camera"></i>
  </div>
</div>

<div class="SnModal-wrapper" data-modal="cameraModal" data-maskclose="false">
  <div class="SnModal">
    <div class="SnModal-close" onclick="stopCamera()">
      <i class="fas fa-times"></i>
    </div>
    <div class="SnModal-header" style="color: transparent;"><i class="fa-solid fa-camera"></i></div>
    <div class="SnModal-body">
      <div id="qrReader"></div>
      <p style="text-align: center;">Enfoca el código QR dentro del recuadro</p>
    </div>
  </div>
</div>

<div class="SnModal-wrapper" data-modal="validateModal" data-maskclose="false">
  <div class="SnModal" style="max-width: 90vw">
    <!-- <div class="SnModal-close" data-modalclose="validateModal">
            <i class="fas fa-times"></i>
        </div> -->
    <!-- <div class="SnModal-header"><i class="fas fa-file-pdf SnMr-2"></i><span id="validateModalTitle">VALIDA</span></div> -->
    <div class="SnModal-body">
      <div id="validateModalBody"></div>
      <div style="display: flex; justify-content: center; align-items: center; height: 100px;"><button class="SnBtn lg" type="button" style="border-style: dashed;" onclick="reStartCamera()"><i class="fa-solid fa-camera SnMr-2"></i>Camara.</button></div>
    </div>
  </div>
</div>

<script src="<?= URL_PATH ?>/libraries/js/html5-qrcode.min.js"></script>

<script src="<?= URL_PATH ?>/build/script/admin/productValidate.js?v=<?= APP_VERSION ?>"></script>
