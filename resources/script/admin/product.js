let maintenanceTable;
let pProductImportValidator;

document.addEventListener("DOMContentLoaded", () => {
  pProductImportValidator = dynamicLoadFormValidate(pProductImportValidator, 'productImportModalForm');

  maintenanceTable = new SnTable({
    elementId: 'drawProductTable',
    entity: 'product',
    data: (body) => snTableFetchData('/admin/product/table', body),
    actions: [],
    // actions: [
    //   {
    //     "id": "9",
    //     "title": "Modificar",
    //     "description": "Modificar",
    //     "icon": "fas fa-pen",
    //     "event_name": "Edit",
    //     "parent_id": "0",
    //     "sort_order": "6",
    //     "position": "TABLE",
    //     "shape": "ICON",
    //     "color": "",
    //     "keyboard_key": "F4",
    //     "state": "1",
    //     "updated_at": null,
    //     "created_at": null,
    //     "created_user": "",
    //     "updated_user": "",
    //     "screen_id_controller": "product"
    //   }
    // ],
    selectable: false,
    paramKeys: ['id'],
    columns: [
      {
        title: 'Código',
        field: 'code',
        filterable: true,
        sortable: true,
      },
      {
        title: 'Descripción',
        field: 'description',
        filterable: true,
        sortable: true,
      },
      {
        title: 'Verificado',
        field: 'verified',
        filterable: true,
        sortable: true,
      },
      {
        title: 'Verificado fecha',
        field: 'verified_date',
        filterable: true,
        sortable: true,
      },
    ],
  });

  // ---------------------------------------------------------------------------------
  // File submit
  const productImportModalForm = document.getElementById('productImportModalForm');
  productImportModalForm.addEventListener('submit', function (e) {
    e.preventDefault();
    productImportSubmit(productImportModalForm);
  })
});

function maintenanceTableReload() {
  maintenanceTable.getData();
}

function maintenanceTableNew(screen, controller) {
  window.location.href = URL_PATH + '/admin/' + controller + '/form';
}

function maintenanceTableImport(screen, controller){
  SnModal.open('productImportModal');
}


// ---------------------------------------------------------------------------------
// Import
function productImportSubmit(elementForm) {
  if (!pProductImportValidator.validate()) {
    dynamicFormSubmitInvalidMessage('No se especificó ningún archivo');
    return;
  }

  let element = document.getElementById('productImportFile');
  if (element == null) {
    return;
  }

  if (element.files === undefined) {
    SnModal.danger({ title: "ALERTA USUARIO", content: 'Por favor, selecciona al menos un archivo para continuar.' });
    return;
  }

  let excelFile = element.files[0];

  if (excelFile == undefined || excelFile == null) {
    SnModal.danger({ title: "ALERTA USUARIO", content: 'Por favor, selecciona al menos un archivo para continuar.' });
    return;
  }


  if (!(validateFile(excelFile, ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'], 3000))) {
    return;
  }

  let data = new FormData();
  data.append('excelFile', excelFile);

  SnLoadingState(true, 'jsAction', 'productImportModalFormSubmit');
  RequestApi.fetch("/admin/product/import", {
    method: "POST",
    body: data,
  })
    .then((res) => {
      if (res.success) {
        SnModal.success({ title: "PROCESO COMPLETO", content: res.message });
        SnModal.close('productImportModal');
        maintenanceTable.getData();
      } else {
        dynamicResponseErrorModalMessage(res);
      }
      elementForm.reset();
    })
    .finally((e) => {
      SnLoadingState(false, 'jsAction', 'productImportModalFormSubmit');
    });
}
