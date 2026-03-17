document.addEventListener('DOMContentLoaded', function () {
// DOMCONTENTLOADED est déclenché lorsque le document 
// HTML a été complètement chargé et analysé

// On sélectionne tous les boutons de bascule de mot de passe
    const toggleButtons = document.querySelectorAll('.password-toggle');

    // Si aucun bouton n'est trouvé, on arrête l'exécution
    if (!toggleButtons.length) return;

    // Pour chaque bouton, on ajoute un écouteur 
    // d'événement de clic
    toggleButtons.forEach(button => {

    // On trouve l'input de mot de passe associé
    // previousElementSibling est utilisé pour trouver l'élément
    // juste avant le bouton dans le DOM
        const input = button.previousElementSibling;

        // Si l'input n'existe pas ou n'est pas de type 'password',
        //  on ignore ce bouton
        // return est utilisé pour sortir de la
        //  fonction actuelle et passer à la suivante
        if (!input || input.type !== 'password') return;

        // On ajoute un écouteur d'événement de clic au bouton
        button.addEventListener('click', function () {
            // On vérifie si le type de l'input est 
            // actuellement 'password'
            const isHidden = input.type === 'password';

            // On bascule le type de l'input entre 'text' 
            // et 'password'
            input.type = isHidden ? 'text' : 'password';

            // On change le texte du bouton en fonction 
            // de l'état actuel
            button.textContent = isHidden ? '🙊' : '🙈';
        });
    });
});
