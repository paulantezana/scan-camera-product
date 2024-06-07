let maintenanceTable;

document.addEventListener("DOMContentLoaded", () => {
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
});

function maintenanceTableReload() {
  maintenanceTable.getData();
}

function maintenanceTableNew(screen, controller) {
  window.location.href = URL_PATH + '/admin/' + controller + '/form';
}
