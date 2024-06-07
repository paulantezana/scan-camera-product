let pValidator;

document.addEventListener("DOMContentLoaded", () => {
  pValidator = dynamicLoadFormValidate(pValidator, 'loginForm');

  // Submit form
  const loginForm = document.getElementById('loginForm');
  loginForm.addEventListener('submit', e => {
    e.preventDefault();
    loginFormSubmit(loginForm);
  });
});

function loginFormSubmit(elementForm) {
  if (!pValidator.validate()) {
    dynamicFormSubmitInvalidMessage();
    return;
  }
  SnLoadingState(true, 'jsAction', 'loginFormSubmit');

  let userData = {};
  userData.email = elementForm.email.value;
  userData.password = elementForm.password.value;

  RequestApi.fetch('/user/loginValidate', {
    method: "POST",
    body: userData,
  })
    .then((res) => {
      if (res.success) {
        if (location.href === location.origin + URL_PATH + '/user/login') {
          location.href = location.origin + URL_PATH + '/admin';
        } else {
          location.reload();
        }

        dynamicClearForm(pValidator, 'loginForm');
      } else {
        dynamicResponseErrorModalMessage(res);
      }
    })
    .finally((e) => {
      SnLoadingState(false, 'jsAction', 'loginFormSubmit');
    });
}
