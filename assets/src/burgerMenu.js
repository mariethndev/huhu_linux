// récupère les éléments du DOM
const burger = document.getElementById('burger');       // bouton burger (icône menu)
const mobileMenu = document.getElementById('mobileMenu'); // menu mobile qui s'ouvre
const overlay = document.getElementById('menuOverlay'); // fond sombre derrière le menu
const closeMenu = document.getElementById('closeMenu'); // bouton pour fermer le menu

// burger menu toggle
// toggle permet d'ajouter ou de supprimer
// la classe 'active' à l'élément mobileMenu et overlay
// cela permet d'afficher ou cacher le menu mobile
burger.addEventListener('click', () => {
  mobileMenu.classList.toggle('active'); // affiche ou cache le menu
  overlay.classList.toggle('active');    // affiche ou cache l'overlay
});

// fermeture du menu si on clique sur l'overlay
// (le fond sombre autour du menu)
overlay.addEventListener('click', () => {
  mobileMenu.classList.remove('active'); // retire la classe active → cache le menu
  overlay.classList.remove('active');    // retire la classe active → cache l'overlay
});

// fermeture du menu si on clique sur le bouton "close"
closeMenu.addEventListener('click', () => {
  mobileMenu.classList.remove('active'); // cache le menu mobile
  overlay.classList.remove('active');    // cache l'overlay
});