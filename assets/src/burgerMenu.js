document.addEventListener("DOMContentLoaded", () => {

  const burger = document.getElementById('burger');
  const menu = document.getElementById('mobileMenu');
  const closeMenu = document.getElementById('closeMenu');

  // ouverture menu
  if (burger && menu) {
    burger.addEventListener('click', () => {
      menu.classList.toggle('active');
    });
  }

  // fermeture menu
  if (closeMenu && menu) {
    closeMenu.addEventListener('click', () => {
      menu.classList.remove('active');
    });
  }

});