let pValidator;

document.addEventListener("DOMContentLoaded", () => {
  pValidator = dynamicLoadFormValidate(pValidator, 'productForm');

  // Submit form
  const productForm = document.getElementById('productForm');
  productForm.addEventListener('submit', e => {
    e.preventDefault();
    productSubmit(productForm);
  });
});

function productNewList(screen, controller) {
  window.location.href = URL_PATH + '/admin/' + controller;
}

function productSubmit(elementForm) {
  if (!pValidator.validate()) {
    dynamicFormSubmitInvalidMessage();
    return;
  }
  SnLoadingState(true, 'jsAction', 'productFormSubmit');

  let productSendData = {};
  productSendData.id = elementForm.productId.value;
  productSendData.code = elementForm.productCode.value;
  productSendData.description = elementForm.productDescription.value;

  const updated = productSendData.id && !['', '0'].includes(productSendData.id.toString());

  RequestApi.fetch('/admin/product/' + (!updated ? 'create' : 'update'), {
    method: "POST",
    body: productSendData,
  })
    .then((res) => {
      if (res.success) {
        dynamicResponseSuccess(
          updated,
          res.message,
          () => {
            if (updated) {
              window.location.href = URL_PATH + '/admin/product/edit/' + res.result.id;
            } else {
              dynamicClearForm(pValidator, 'productForm', 'productDescription');
            }
          },
          () => window.location.href = URL_PATH + `/admin/product`
        );
      } else {
        dynamicResponseErrorModalMessage(res);
      }
    })
    .finally((e) => {
      SnLoadingState(false, 'jsAction', 'productFormSubmit');
    });
}
