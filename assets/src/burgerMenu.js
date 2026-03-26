// je récupère les éléments du menu mobile
const burger = document.getElementById('burger');
const menu = document.getElementById('mobileMenu');
const closeMenu = document.getElementById('closeMenu');

// si un élément manque j’arrête pour éviter les erreurs
if (!burger || !menu || !closeMenu) return;

// j’ajoute un événement au clic sur le bouton burger
// ça permet d’ouvrir ou fermer le menu
burger.addEventListener('click', () => {
    menu.classList.toggle('active');
});

// j’ai un bouton pour fermer le menu (la croix)
// au clic je cache le menu
closeMenu.addEventListener('click', () => {
    menu.classList.remove('active');
});