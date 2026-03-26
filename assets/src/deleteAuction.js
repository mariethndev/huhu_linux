// je récupère tous les boutons supprimer enchère
document.querySelectorAll(".btn-delete-auction").forEach(btn => {

    // au clic sur un bouton
    btn.addEventListener("click", () => {

        // je mets l’id dans le champ caché
        document.getElementById("deleteAuctionId").value = btn.dataset.id;

        // je mets le nom dans la modale
        document.getElementById("deleteAuctionName").textContent = btn.dataset.name;

        // j’affiche la modale
        document.getElementById("deleteModal").classList.remove("hidden");
    });
});

// je récupère le bouton annuler
const cancelBtn = document.querySelector(".btn-cancel-delete");

// je récupère la modale
const modal = document.getElementById("deleteModal");

// si les éléments existent
if (cancelBtn && modal) {

    // au clic je ferme la modale
    cancelBtn.addEventListener("click", () => {

        // je cache la modale
        modal.classList.add("hidden");
    });
}