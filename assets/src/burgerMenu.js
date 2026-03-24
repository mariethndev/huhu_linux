const burger = document.getElementById('burger');
const menu = document.getElementById('mobileMenu');
const closeMenu = document.getElementById('closeMenu');

burger.addEventListener('click', () => {
    menu.classList.toggle('active');
});

closeMenu.addEventListener('click', () => {
    menu.classList.remove('active');
});