let pValidator;function productNewList(t,o){window.location.href=URL_PATH+"/admin/"+o}function productSubmit(t){if(pValidator.validate()){SnLoadingState(!0,"jsAction","productFormSubmit");var o={};o.id=t.productId.value,o.code=t.productCode.value,o.description=t.productDescription.value;const d=o.id&&!["","0"].includes(o.id.toString());RequestApi.fetch("/admin/product/"+(d?"update":"create"),{method:"POST",body:o}).then(t=>{t.success?dynamicResponseSuccess(d,t.message,()=>{d?window.location.href=URL_PATH+"/admin/product/edit/"+t.result.id:dynamicClearForm(pValidator,"productForm","productDescription")},()=>window.location.href=URL_PATH+"/admin/product"):dynamicResponseErrorModalMessage(t)}).finally(t=>{SnLoadingState(!1,"jsAction","productFormSubmit")})}else dynamicFormSubmitInvalidMessage()}document.addEventListener("DOMContentLoaded",()=>{pValidator=dynamicLoadFormValidate(pValidator,"productForm");const o=document.getElementById("productForm");o.addEventListener("submit",t=>{t.preventDefault(),productSubmit(o)})});