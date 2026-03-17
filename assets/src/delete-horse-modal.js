// Ce script gère l'ouverture et la fermeture de la modale de suppression d'un cheval
document.addEventListener("DOMContentLoaded", function () {
  const deleteButtons = document.querySelectorAll(".btn-delete-horse");
  const modal = document.getElementById("deleteModal");
  const closeBtn = document.querySelector(".modal-close-delete");
  const cancelBtn = document.querySelector(".btn-cancel-delete");
  const horseName = document.getElementById("deleteHorseName");
  const horseIdInput = document.getElementById("deleteHorseId");
 
  // deleteButtons est une NodeList de tous les boutons de suppression de cheval
  // forEach permet de parcourir chaque bouton et d'ajouter un écouteur d'événement
  // Lorsque l'utilisateur clique sur un bouton de suppression,
  // la modale s'ouvre et affiche le nom du cheval à supprimer
  // ainsi que son ID dans un champ caché pour le traitement du formulaire
  // this.dataset.name et this.dataset.id font référence aux attributs data-name et data-id
  // du bouton cliqué, qui contiennent respectivement le nom et l'ID du cheval à supprimer
  // modal.classList.remove("hidden") rend la modale visible en supprimant la classe "hidden"
  // qui est utilisée pour cacher la modale par défaut
  
  deleteButtons.forEach((button) => {
    button.addEventListener("click", function () {
      horseName.textContent = this.dataset.name;
      horseIdInput.value = this.dataset.id;

      modal.classList.remove("hidden");
    });
  });

  // closeModal est une fonction qui ajoute la classe "hidden" à la modale,
  // la rendant ainsi invisible

  function closeModal() {
    modal.classList.add("hidden");
  }

// closeBtn est le bouton de fermeture (X) dans la modale
// Si le bouton existe (vérification pour éviter les erreurs si l'élément n'est pas trouvé),
// on ajoute un écouteur d'événement qui appelle la fonction closeModal() lorsque le bouton est cliqué
// e.preventDefault() empêche le comportement par défaut du bouton (qui pourrait être de soumettre un formulaire ou de suivre un lien)
//  closeModal() est appelé pour fermer la modale lorsque le bouton de fermeture est cliqué

  if (closeBtn) {
    closeBtn.addEventListener("click", function (e) {
      e.preventDefault();
      closeModal();
    });
  }

// Bouton Annuler
// cancelBtn est le bouton "Annuler" dans la modale
// Si le bouton existe, on ajoute un écouteur d'événement qui appelle la fonction closeModal() lorsque le bouton est cliqué
// e.preventDefault() empêche le comportement par défaut du bouton (qui pourrait être de soumettre un formulaire ou de suivre un lien)
// closeModal() est appelé pour fermer la modale lorsque le bouton "Annuler" est cliqué

  if (cancelBtn) {
    cancelBtn.addEventListener("click", function (e) {
      e.preventDefault(); // IMPORTANT
      closeModal();
    });
  }

  // cliquer en dehors de la carte pour fermer la modale

  // modal est l'élément de la modale elle-même
// Lorsque l'utilisateur clique n'importe où sur la modale,
// on vérifie si le clic a eu lieu sur la modale elle-même (et pas à l'intérieur de la carte ou d'autres éléments)
// Si e.target === modal, 
// cela signifie que l'utilisateur a cliqué en dehors de la carte, 
// sur la zone de la modale
// Dans ce cas, on appelle closeModal() pour fermer la modale

  modal.addEventListener("click", function (e) {
    if (e.target === modal) {
      closeModal();
    }
  });
});
