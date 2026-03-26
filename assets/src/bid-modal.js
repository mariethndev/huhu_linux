document.addEventListener("DOMContentLoaded", () => {

  // je récupère le bouton "faire une offre"
  const btn = document.querySelector(".btn-bid");

  // je récupère la modale
  const modal = document.getElementById("bidModal");

  // je vérifie que les deux existent
  if (btn && modal) {

    // j’ai défini un pas minimum pour les enchères
    const step = 50;

    // je stocke le prix actuel
    let base = 0;

    // au clic sur le bouton
    btn.addEventListener("click", (e) => {

      // j’empêche les conflits avec d’autres events
      e.stopPropagation(); 

      // je récupère le prix actuel
      base = Number(btn.dataset.price);

      // je mets le nom du cheval dans la modale
      document.getElementById("modalHorseName").textContent = btn.dataset.horseName;

      // j’affiche le prix actuel
      document.getElementById("modalCurrentPrice").textContent = base + " €";

      // j’envoie l’id du cheval dans le champ caché
      document.getElementById("modalHorseId").value = btn.dataset.horseId;

      // je calcule automatiquement la prochaine enchère
      document.getElementById("bidAmount").value = base + step;

      // je récupère l’image dans la modale
      const img = document.querySelector(".modal-image img");

      // si j’ai une image
      if (img && btn.dataset.image) {

        // je mets à jour la source
        img.src = btn.dataset.image;

        // je mets à jour le alt
        img.alt = btn.dataset.horseName;
      }

      // j’affiche la modale
      modal.classList.remove("hidden");
    });
  }

  // je récupère le bouton fermer
  const closeBtn = document.querySelector(".modal-close");

  // je vérifie que ça existe
  if (closeBtn && modal) {

    // au clic je ferme la modale
    closeBtn.addEventListener("click", () => {

      // je cache la modale
      modal.classList.add("hidden");
    });
  }
});