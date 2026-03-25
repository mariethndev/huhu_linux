// Je récupère les éléments HTML par leur id
const burger = document.getElementById('burger');
const menu = document.getElementById('mobileMenu');
const closeMenu = document.getElementById('closeMenu');

// J'ajoute un événement au clic sur le bouton burger pour afficher/masquer le menu
burger.addEventListener('click', () => {
    menu.classList.toggle('active');
});

// closeMenu correspond au bouton de fermeture (croix)
// Au clic, je cache le menu
closeMenu.addEventListener('click', () => {
    menu.classList.remove('active');
});