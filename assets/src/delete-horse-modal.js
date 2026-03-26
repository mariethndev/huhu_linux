document.addEventListener("DOMContentLoaded", () => {

  // je récupère tous les boutons supprimer
  document.querySelectorAll(".btn-delete-horse").forEach(btn => {

    // au clic sur un bouton
    btn.addEventListener("click", () => {

      // je récupère les éléments de la modale
      const nameEl = document.getElementById("deleteHorseName");
      const idEl   = document.getElementById("deleteHorseId");
      const modal  = document.getElementById("deleteModal");

      // si un élément manque j’arrête
      if (!nameEl || !idEl || !modal) return;

      // je mets le nom du cheval
      nameEl.textContent = btn.dataset.name;

      // je mets l’id dans le champ caché
      idEl.value = btn.dataset.id;

      // j’affiche la modale
      modal.classList.remove("hidden");
    });
  });

  // je récupère le bouton fermer
  const closeBtn = document.querySelector(".modal-close-delete");

  // je récupère le bouton annuler
  const cancelBtn = document.querySelector(".btn-cancel-delete");

  // je récupère la modale
  const modal = document.getElementById("deleteModal");

  // si j’ai le bouton fermer
  if (closeBtn && modal) {
    closeBtn.addEventListener("click", (e) => {

      // j’empêche le comportement par défaut
      e.preventDefault();

      // je cache la modale
      modal.classList.add("hidden");
    });
  }

  // si j’ai le bouton annuler
  if (cancelBtn && modal) {
    cancelBtn.addEventListener("click", (e) => {

      // j’empêche le comportement par défaut
      e.preventDefault();

      // je cache la modale
      modal.classList.add("hidden");
    });
  }

  // si je clique en dehors de la modale
  if (modal) {
    modal.addEventListener("click", (e) => {

      // si je clique sur le fond
      if (e.target.id === "deleteModal") {

        // je ferme la modale
        modal.classList.add("hidden");
      }
    });
  }

});