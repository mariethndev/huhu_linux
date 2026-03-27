document.addEventListener("DOMContentLoaded", () => {

  // Attend que toute la page HTML soit chargée avant d’exécuter le script
  const btn = document.querySelector(".btn-bid");
  // Récupère le bouton "faire une offre"
  const modal = document.getElementById("bidModal");
  // Récupère la modale (la popup)
  const closeBtn = document.querySelector(".modal-close");
  // Récupère le bouton pour fermer la modale
  const plusBtn = document.getElementById("bidPlus");
  const minusBtn = document.getElementById("bidMinus");
  // Récupère les boutons pour augmenter et diminuer le prix
  const bidInput = document.getElementById("bidAmount");
  // Récupère le champ où l’utilisateur entre le montant de l’enchère
  const step = 50;

  // Définit le pas minimum pour les enchères, ici 50€
  let base = 0;

  // Variable qui stocke le prix actuel du cheval

  if (btn && modal) {

    // Vérifie que le bouton et la modale existent avant d’ajouter un événement
    btn.addEventListener("click", (e) => {

      e.preventDefault();
      base = Number(btn.dataset.price);

      // Récupère le prix actuel du cheval depuis les attributs data du bouton
      document.getElementById("modalHorseName").textContent = btn.dataset.horseName;

      // Insère le nom du cheval dans la modale
      document.getElementById("modalCurrentPrice").textContent = base + " €";

      // Affiche le prix actuel dans la modale
      document.getElementById("modalHorseId").value = btn.dataset.horseId;

      // Place l’identifiant du cheval dans le champ caché du formulaire
      bidInput.value = base + step;

      // Initialise le montant de l’enchère à prix actuel + 50€
      const img = document.querySelector(".modal-image img");

      // Récupère l’image présente dans la modale

      if (img && btn.dataset.image) {
        // Vérifie que l’image existe et qu’une source est disponible
        img.src = btn.dataset.image;
        // Met à jour la source de l’image
        img.alt = btn.dataset.horseName;
        // Met à jour le texte alternatif de l’image
      }

      modal.classList.remove("hidden");
      // Affiche la modale en retirant la classe hidden
    });
  }

  if (closeBtn && modal) {

    // Vérifie que le bouton de fermeture existe
    closeBtn.addEventListener("click", () => {

      // Au clic sur la croix, on ferme la modale
      modal.classList.add("hidden");

      // Cache la modale en ajoutant la classe hidden
    });
  }

  if (plusBtn && bidInput) {

    // Vérifie que le bouton + et le champ existent
    plusBtn.addEventListener("click", () => {

      // Au clic sur +, on augmente le prix
      let value = Number(bidInput.value) || 0;

      // Récupère la valeur actuelle du champ
      bidInput.value = value + step;

      // Ajoute 50€ au montant
    });
  }

  if (minusBtn && bidInput) {

    // Vérifie que le bouton - et le champ existent
    minusBtn.addEventListener("click", () => {

      // Au clic sur -, on diminue le prix
      let value = Number(bidInput.value) || 0;

      // Récupère la valeur actuelle du champ

      if (value > base + step) {

        // Vérifie que le montant ne descend pas sous le minimum autorisé
        bidInput.value = value - step;

        // Retire 50€ au montant
      }
    });
  }

});