// navigator.serviceWorker.register(URL_PATH + '/build/script/helpers/sw.js');
const mimeTypesMap = {
  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'Excel (xlsx)',
  'application/vnd.ms-excel': 'Excel (xls)',
  'application/pdf': 'PDF',
  'application/msword': 'Word (doc)',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'Word (docx)',
  'image/jpeg': 'Imagen JPEG',
  'image/jpg': 'Imagen JPG',
  'image/png': 'Imagen PNG',
  'text/plain': 'Texto plano',
  'image/gif': 'Imagen GIF',
};

function getFriendlyMimeTypes(mimeTypes) {
  return mimeTypes.map(type => mimeTypesMap[type] || type);
}

class RequestApi {
  static setHeaders(options) {
    if (!(options.body instanceof FormData)) {
      options.headers = {
        Accept: "application/json",
        "Content-Type": "application/json; charset=utf-8",
        ...options.headers,
      };
      options.body = JSON.stringify(options.body);
    } else {
      options.headers = {
        Accept: "application/json",
        ...options.headers,
      };
    }
    return options;
  }

  static fetch(path, options = {}) {
    NProgress.start();
    const newOptions = RequestApi.setHeaders(options);

    return fetch(URL_PATH + path, newOptions)
      .then((response) => {
        return response.json();
      })
      .catch(e => {
        SnMessage.danger({ content: `ERROR FATAL JSON: ${e}` });
        return e;
      })
      .finally(e => {
        NProgress.done();
      });
  }

  static fetchOut(path, options = {}) {
    NProgress.start();
    const newOptions = RequestApi.setHeaders(options);

    return fetch(path, newOptions)
      .then((response) => {
        return response.json();
      })
      .catch(e => {
        SnMessage.danger({ content: `ERROR FATAL JSON: ${e}` });
        return e;
      })
      .finally(e => {
        NProgress.done();
      });
  }
}

const TableToExcel = (
  tableHtml,
  sheetName = "Sheet 1",
  fileName = "report"
) => {
  const template =
    '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>';
  const base64 = function (s) {
    return window.btoa(decodeURIComponent(encodeURIComponent(s)));
  };
  const format = function (s, c) {
    return s.replace(/{(\w+)}/g, function (m, p) {
      return c[p];
    });
  };
  const s2ab = (s) => {
    let buf = new ArrayBuffer(s.length);
    let view = new Uint8Array(buf);
    for (let i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xff;
    return buf;
  };

  const ctx = { worksheet: sheetName, table: tableHtml };

  const blob = new Blob([s2ab(atob(base64(format(template, ctx))))], {
    type: "",
  });

  let link = document.createElement("a");
  link.download = fileName + ".xls";

  link.href = URL.createObjectURL(blob);
  link.click();
};


// ====================================================================================
// N U M    T O     W O R D
// ====================================================================================


// Validate
function validateFile(file, fileTypes, maxSizeKb) {
  const friendlyFileTypes = getFriendlyMimeTypes(fileTypes);

  if (!fileTypes.includes(file.type)) {
    const friendlyType = mimeTypesMap[file.type] || file.type;

    SnModal.danger({
      title: "Archivo no soportado",
      content: `Parece que has seleccionado un tipo de archivo que no podemos aceptar (${friendlyType}). Por favor, elige un archivo de tipo: ${friendlyFileTypes.join(', ')}.`
    });
    return false;
  }

  const fileSizeKb = file.size / 1024;
  const fileSizeMb = fileSizeKb / 1024;
  const maxSizeMb = maxSizeKb / 1024;

  if (fileSizeKb > maxSizeKb) {
    const sizeMessage = fileSizeKb > 1024 ?
      `${fileSizeMb.toFixed(2)} MB (máximo permitido: ${maxSizeMb.toFixed(2)} MB)` :
      `${fileSizeKb.toFixed(2)} KB (máximo permitido: ${maxSizeKb} KB)`;

    SnModal.danger({
      title: "Archivo demasiado grande",
      content: `El archivo que has seleccionado es demasiado grande. Tamaño: ${sizeMessage}.`
    });
    return false;
  }

  return true;
}


// ====================================================================================
// D Y N A M I C     H E L P E R S
// ====================================================================================
function dynamicFormSubmitInvalidMessage() {
  SnModal.warning({
    title: "ALERTA USUARIO",
    content: 'Verifica si los capos del formulario estén correctamente validados',
    okClassNames: 'warning'
  });
}

function dynamicResponseErrorModalMessage(res) {
  const errorType = res.errorType || 'danger';
  const okClassNames = res.errorType || 'primary';

  if (errorType === 'warning') {
    res.message = res.message.split('|').map(message => `<div>${message}</div>`).join('');
  }

  SnModal.confirm({
    confirm: false,
    title: res.title || '',
    content: res.message,
    type: errorType,
    okClassNames,
  });
}

function dynamicClearForm(pValidator, formId, inputFocusId, clearHtml = "") {
  // Reset form
  const currentForm = document.getElementById(formId);
  if (currentForm) {
    currentForm.reset();
  }

  // Clear Html
  const htmlContent = document.getElementById(clearHtml);
  if (htmlContent) {
    htmlContent.innerHTML = '';
  }

  // Reset Validator
  pValidator.reset();

  // Set focus
  const inputFocus = document.getElementById(inputFocusId);
  if (inputFocus) {
    setTimeout(() => {
      inputFocus.focus();
    }, 500)
  }
}

function dynamicLoadFormValidate(pValidator, formId) {
  if (pValidator) {
    pValidator.destroy();
  }
  return new Pristine(document.getElementById(formId));
}

function dynamicTableToExcel(id, title) {
  let dataTable = document.getElementById(id);
  if (dataTable) {
    TableToExcel(dataTable.outerHTML, 'Hoja 1', title);
  }
}

function dynamicTableToPdf(id, title = 'Document', scanStyles = true) {
  printJS({
    printable: id,
    type: 'html',
    documentTitle: title,
    scanStyles: scanStyles,
    css: URL_PATH + '/build/css/print.css'
  })
}

function dynamicViewKeyboardListeners(screenName, menuData = []) {
  document.addEventListener('keydown', (e) => {
    if (!e.key) {
      return;
    }

    const eventKeyName = e.key.toUpperCase();

    // match
    menuData.forEach(item => {
      if ((item.position === 'TOOLBAR' || item.position === 'FOOTER') && item.keyboard_key.length > 0) {
        const keySplit = item.keyboard_key.toUpperCase().split('+');
        const eventFunctionName = (item.event_name_prefix.length > 0 ? item.event_name_prefix : screenName) + item.event_name;

        if (keySplit.length > 1) { // Combination
          const preKey = keySplit[0];
          const comKey = keySplit[1];
          if ((e.ctrlKey && preKey === 'CTRL' && eventKeyName === comKey) || (e.altKey && preKey === 'ALT' && eventKeyName === comKey)) {
            e.preventDefault();
            eval(`${eventFunctionName}`)(screenName, item.screen_id_controller);
          }
        } else { // Only key
          if (eventKeyName === item.keyboard_key.toUpperCase() && e.ctrlKey === false && e.altKey === false && e.shiftKey === false) {
            e.preventDefault();
            eval(`${eventFunctionName}`)(screenName, item.screen_id_controller);
          }
        }
      }
    });
  });
}

function dynamicResponseSuccess(updated, message, onOk, onCancel) {
  SnModal.confirm({
    type: 'success',
    title: 'OPERACIÓN EXITOSA',
    content: message,
    okText: !updated ? 'Crear nuevo' : 'Refrescar',
    okClassNames: 'primary radio',
    cancelClassNames: 'radio',
    cancelText: 'Listar',
    onOk(message) {
      if (typeof onOk === "function") {
        onOk(message);
      }
    },
    onCancel(message) {
      if (typeof onCancel === "function") {
        onCancel(message);
      }
    }
  });
}


// ====================================================================================
// C O M P O N E N T S
// ====================================================================================
let SnLiveList = options => {
  let tElementNodes = document.querySelectorAll(options.elem);

  tElementNodes.forEach(tElementNode => {
    let ul = document.createElement('ul');
    ul.classList.add('SnLiveList');

    const itemOnClick = (e, item) => {
      options.onSelect(e, item);
      ul.remove();
    }

    const renderContainer = () => {
      let parentNode = tElementNode.parentNode;
      if (!parentNode.querySelector('.SnLiveList')) {
        parentNode.appendChild(ul);
      }

      setPositionContainer();
    }

    const setPositionContainer = () => {
      let tElementNodeInfo = tElementNode.getBoundingClientRect();
      ul.style.top = tElementNodeInfo.height + 'px';
      ul.style.width = tElementNodeInfo.width + 'px';
    }

    const dataPaint = data => {
      if (tElementNode.value.length === 0 || data.length === 0) {
        ul.remove();
        return;
      }

      if (typeof data === 'object') {
        ul.innerHTML = '';
        data.forEach((item, index) => {
          const liEle = document.createElement('li');
          liEle.setAttribute('data-type', 'data');
          liEle.setAttribute('data-index', index);
          liEle.classList.add('SnLiveList-item');
          liEle.innerHTML = item.text;

          // Append child
          ul.appendChild(liEle);

          // Listener
          liEle.addEventListener('click', (e) => {
            e.preventDefault();
            itemOnClick(e, item);
          });
        });

        renderContainer();
      } else if (typeof data === 'string') {
        ul.innerHTML = `<li data-type="alert" class="SnLiveList-item alert">${data}</li>`;
        renderContainer();
      }
    }

    tElementNode.addEventListener('input', function (e) {
      e.preventDefault();
      if (options.data) {
        if (typeof options.data === 'function') {
          options.data(tElementNode.value, dataPaint);
        }
      }
    });
  });
};

function appLeftPanelClose() {
  const drawTableInfo = document.getElementById('drawTableInfo');
  if (drawTableInfo) {
    drawTableInfo.classList.remove('active');
  }
}

function appLeftPanelToggle() {
  const drawTableInfo = document.getElementById('drawTableInfo');
  if (drawTableInfo) {
    drawTableInfo.classList.toggle('active');
  }
}

function appLeftPanelGetContent(screenName, selectRows) {
  if (selectRows.length === 0) {
    return;
  }

  let dynamicWhere = {}
  dynamicWhere.id = selectRows[0];

  SnLoadingState(true, 'jsAction');
  RequestApi.fetch("/admin/appSidebar/getByScreenName", {
    method: "POST",
    body: { screenName, dynamicWhere },
  })
    .then((res) => {
      if (res.success) {
        const drawTableInfoContent = document.getElementById('drawTableInfoContent');

        if (drawTableInfoContent) {
          drawTableInfoContent.innerHTML = '';
          const resResult = res.result;

          resResult.forEach(sidebar => {
            let options = '';
            sidebar.options.forEach(opt => {
              const valueElement = opt.url_ref.length > 0 ? `<a href="${opt.url_ref.replaceAll('{{URL_PATH}}', URL_PATH)}" target="${opt.url_ref.replaceAll('{{URL_PATH}}', URL_PATH)}" rel="noopener noreferrer">${opt.value}</a>` : opt.value;
              options += `<li style="display: flex; justify-content: space-between; align-items: center;"><span>${opt.description}</span><span>${valueElement}</span></li>`
            });
            drawTableInfoContent.insertAdjacentHTML('beforeend', `<h3 class="LeftPanel-title">${sidebar.title}</h3><ul class="SnList">${options}</ul>`);
          });
        }
      } else {
        dynamicResponseErrorModalMessage(res);
      }
    })
    .finally((e) => {
      SnLoadingState(false, 'jsAction');
    });
}

function navigatorGoTo(path, params, target = '') {
  const getParams = new URLSearchParams(params).toString();

  const linkELe = document.createElement('a');
  linkELe.setAttribute('href', URL_PATH + path + (getParams.length > 0 ? ('?' + getParams) : ''));
  linkELe.setAttribute('target', target);

  linkELe.click();
}

const copyToClipboard = str => {
  if (navigator && navigator.clipboard && navigator.clipboard.writeText)
    return navigator.clipboard.writeText(str);
  return Promise.reject('The Clipboard API is not available.');
};

const dateDDMMMYYYToStdFormat = (date, lang = 'es') => {
  const monthsShort = 'ene_feb_mar_abr_may_jun_jul_ago_sep_oct_nov_dic'.split('_')
  const dateParts = date.split('-');
  const day = dateParts[0];
  const month = monthsShort.findIndex(item => item === dateParts[1].toLowerCase()) + 1;
  const year = dateParts[2];

  const monthFill = ('00' + month).slice(-2);

  return `${year}-${monthFill}-${day}`;
}

async function snTableFetchData(url, body) {
  const response = await RequestApi.fetch(url, { method: "POST", body: body });

  if (!response.success) {
    SnModal.confirm({
      confirm: false,
      title: response.title,
      content: response.message,
      type: response.errorType,
    });

    return [];
  }

  return response.result;
}

function snTableAuditColumnRender() {
  const render = (item) => {
    const title = 'CREACIÓN DEL REGISTRO\n' +
      'Usuario: ' + item?.created_user + '\n' +
      'Fecha: ' + dayjs(item?.created_at).format('dddd, MMMM D, YYYY h:mm A') + '\n\n' +
      'ULTIMA ACTUALIZACIÓN\n' +
      'Usuario: ' + item?.updated_user + '\n' +
      'Fecha: ' + dayjs(item?.updated_at).format('dddd, MMMM D, YYYY h:mm A') + '\n';

    return `<i class="fa-regular fa-eye" title="${title}" style="cursor: pointer"></i>`;
  }

  return [
    {
      title: 'Crea úl. usuario',
      field: 'created_user',
      filterable: true,
      sortable: true,
      visible: false,
    },
    {
      title: 'Crea úl. fecha',
      field: 'created_at',
      filterable: true,
      sortable: true,
      visible: false,
      // customRender: (item) => {
      //   return !!item?.created_at ? dayjs(item?.created_at).format('dddd, MMMM D, YYYY h:mm A') : ''
      // }
    },
    {
      title: 'Modifica úl. usuario',
      field: 'updated_user',
      filterable: true,
      sortable: true,
      visible: false,
    },
    {
      title: 'Modifica úl. fecha',
      field: 'updated_at',
      filterable: true,
      sortable: true,
      visible: false,
      // customRender: (item) => {
      //   return !!item?.updated_at ? dayjs(item?.updated_at).format('dddd, MMMM D, YYYY h:mm A') : ''
      // }
    },
  ]
}

function formatNumber(number, presition = 2) {
  if (isNaN(number)) {
    return 0;
  }

  return parseFloat(number).toLocaleString('es-US', {
    style: 'decimal', minimumFractionDigits: presition, maximumFractionDigits: presition
  });
}


function fixedNumber(number, presition = 2) {
  if (isNaN(number)) {
    return 0;
  }

  return parseFloat(parseFloat(number).toFixed(presition))
}
