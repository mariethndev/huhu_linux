const burger = document.getElementById('burger');      
const mobileMenu = document.getElementById('mobileMenu'); 
const overlay = document.getElementById('menuOverlay');
const closeMenu = document.getElementById('closeMenu'); 

burger.addEventListener('click', () => {
  mobileMenu.classList.toggle('active');
  overlay.classList.toggle('active');   
});
overlay.addEventListener('click', () => {
  mobileMenu.classList.remove('active');
  overlay.classList.remove('active');   
});

closeMenu.addEventListener('click', () => {
  mobileMenu.classList.remove('active'); 
  overlay.classList.remove('active');   
});