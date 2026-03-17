// Attend que tout le HTML de la page soit chargé avant d'exécuter le script
document.addEventListener("DOMContentLoaded", function () {

    // Récupère l'input de type file qui permet de sélectionner une image
    const input = document.getElementById("horseImageInput");

    // Récupère l'élément <img> qui servira à afficher l'aperçu de l'image
    const preview = document.getElementById("horsePreview");

    // Si l'un des deux éléments n'existe pas dans la page, on arrête le script
    if (!input || !preview) return;

    // Écoute l'événement "change" qui se déclenche quand l'utilisateur sélectionne un fichier
    input.addEventListener("change", function () {

        // Récupère le premier fichier sélectionné dans l'input
        const file = this.files[0];

        // Si aucun fichier n'est sélectionné, on arrête la fonction
        if (!file) return;

        // Crée un objet FileReader pour lire le contenu du fichier
        const reader = new FileReader();

        // Fonction exécutée une fois que le fichier est complètement lu
        reader.onload = function (e) {

            // Définit la source de l'image preview avec les données du fichier
            // e.target.result contient l'image convertie en DataURL (base64)
            preview.src = e.target.result;
        };

        // Lance la lecture du fichier et le convertit en DataURL
        // Ce format permet d'afficher directement l'image dans un <img>
        reader.readAsDataURL(file);

    });

});