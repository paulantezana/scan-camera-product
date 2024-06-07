document.addEventListener("DOMContentLoaded", () => {
  SnMenu({
    menuId: "AsideMenu",
    toggleButtonID: "AsideMenu-toggle",
    toggleClass: "AsideMenu-is-show",
    contextId: "AdminLayout",
    parentClose: true,
    menuCloseID: "AsideMenu-wrapper",
    iconClassDown: 'fas fa-chevron-down',
    iconClassUp: 'fas fa-chevron-up',
  });
});
