const burger = document.getElementById('burger');
const menu = document.getElementById('mobileMenu');
const overlay = document.getElementById('menuOverlay');

burger.onclick = () => {
  menu.classList.toggle('active');
  overlay.classList.toggle('active');
};

overlay.onclick = () => {
  menu.classList.remove('active');
  overlay.classList.remove('active');
};