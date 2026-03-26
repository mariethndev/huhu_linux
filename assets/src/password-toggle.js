// j’attends que la page soit chargée
document.addEventListener('DOMContentLoaded', function () {

    // je récupère tous les boutons toggle password
    const toggleButtons = document.querySelectorAll('.password-toggle');

    // si aucun bouton j’arrête
    if (!toggleButtons.length) return;

    // je boucle sur chaque bouton
    toggleButtons.forEach(button => {

        // je récupère l’input juste avant le bouton
        const input = button.previousElementSibling;

        // si pas d’input ou pas un password j’arrête
        if (!input || input.type !== 'password') return;

        // au clic sur le bouton
        button.addEventListener('click', function () {

            // je vérifie si le mot de passe est caché
            const isHidden = input.type === 'password';

            // je change le type pour afficher ou cacher
            input.type = isHidden ? 'text' : 'password';

            // je change l’emoji du bouton
            button.textContent = isHidden ? '🙊' : '🙈';
        });
    });
});