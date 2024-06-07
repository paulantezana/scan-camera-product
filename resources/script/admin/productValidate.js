let html5QrCode;
document.addEventListener("DOMContentLoaded", () => {
  html5QrCode = new Html5Qrcode("qrReader");
});

function startCamera() {
  SnModal.open('cameraModal');

  try {
    html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 250, height: 250 } }, onScanSuccess, onScanError);
  } catch (error) {
    console.log(error, 'ERRRRRRRRRRRRRRRRRR');
  }
}

function reStartCamera() {
  startCamera();
  SnModal.close('validateModal');
}

function qrSearchByCodeStart() {
  const decodedText = document.getElementById('qrSearchByCode').value;
  validateByPassengerId(decodedText);
}

function stopCamera() {
  let cameraStatus = html5QrCode.getState();

  if (cameraStatus === 2) {
    html5QrCode.stop().then((ignore) => {
      // END
    }).catch((err) => {
      SnMessage.danger({ content: err });
    })
  }

  SnModal.close('cameraModal');
}

let lastResult = '';
function onScanSuccess(decodedText, decodedResult) {
  // Validate is not some result
  if (decodedText == lastResult) {
    return;
  }
  lastResult = decodedText;

  // Start validate barcode
  // if (decodedText.length !== 5) {
  //   SnMessage.warning({ content: 'Código de barra desconocido' });
  //   return;
  // }

  validateByCodeId(decodedText);
}

function onScanError(decodedText, decodedResult){
  // console.log({decodedText, decodedResult});
}

function validateByCodeId(code) {
  SnLoadingState(true, 'jsAction');
  RequestApi.fetch('/admin/product/verified', {
    method: 'POST',
    body: { code },
  }).then((res) => {
    if (res.success) {
      SnMessage.success({ content: res.message });
      // drawResult(res);
    } else {
      dynamicResponseErrorModalMessage(res);
    }
  }).finally(() => {
    SnLoadingState(false, 'jsAction');
  })
}

function drawResult(res) {
  // const validateModalBody = document.getElementById('validateModalBody');

  // SnModal.open('validateModal');
  // if (res.success) {
  //   let passenger = res.result;
  //   if (passenger.standby_time > 0) {
  //     validateModalBody.innerHTML = `<div style="text-align: center;">
  //                                                       <div style="color: var(--green-6); font-size: 6rem;"><i class="fa-solid fa-circle-check"></i></div>
  //                                                       <div style="font-size: 1.5rem; font-weight: bold">VÁLIDO</div>
  //                                                       <div class="SnTag success">Quedan ${passenger.standby_time} horas</div>
  //                                                       <div>${passenger.full_name}</div>
  //                                                       <div>${passenger.itinerary_id_departure_date} ${passenger.itinerary_id_departure_time}</div>
  //                                                       <div>${passenger.geo_location_origin_last_geo_name} - ${passenger.geo_location_des_last_geo_name}</div>
  //                                                   </div>`;
  //   } else {
  //     validateModalBody.innerHTML = `<div style="text-align: center;">
  //                                                       <div style="color: var(--yellow-6); font-size: 6rem;"><i class="fa-solid fa-info"></i></div>
  //                                                       <div style="font-size: 1.5rem; font-weight: bold">EXPIRADO</div>
  //                                                       <div>${passenger.full_name}</div>
  //                                                       <div>${passenger.itinerary_id_departure_date} ${passenger.itinerary_id_departure_time}</div>
  //                                                       <div>${passenger.geo_location_origin_last_geo_name} - ${passenger.geo_location_des_last_geo_name}</div>
  //                                                   </div>`;
  //   }
  // } else {
  //   validateModalBody.innerHTML = `<div style="text-align: center;">
  //                                                   <div style="color: var(--red-6); font-size: 6rem;"><i class="fa-solid fa-circle-exclamation"></i></div>
  //                                                   <div>${res.message}</div>
  //                                               </div>`;
  // }

  // stopCamera();
  // lastResult = '';
}
