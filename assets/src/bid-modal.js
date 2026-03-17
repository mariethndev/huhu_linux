// Attend que tout le HTML soit chargé avant d'exécuter le script
document.addEventListener("DOMContentLoaded", function () {

  // Récupère les éléments nécessaires dans le DOM
  const bidButton = document.querySelector(".btn-bid"); // bouton pour enchérir
  const modal = document.getElementById("bidModal"); // fenêtre modale d'enchère
  const closeBtn = document.querySelector(".modal-close"); // bouton fermer la modale
  const horseName = document.getElementById("modalHorseName"); // nom du cheval dans la modale
  const currentPrice = document.getElementById("modalCurrentPrice"); // prix actuel affiché
  const horseIdInput = document.getElementById("modalHorseId"); // champ caché pour l'id du cheval
  const bidAmountInput = document.getElementById("bidAmount"); // champ du montant de l'enchère
  const minusBtn = document.getElementById("bidMinus"); // bouton pour diminuer l'enchère
  const plusBtn = document.getElementById("bidPlus"); // bouton pour augmenter l'enchère
  const modalImg = document.querySelector(".modal-image img"); // image du cheval dans la modale

  // Si le bouton d'enchère n'existe pas sur la page, on arrête le script
  if (!bidButton) return;

  // montant minimum d'augmentation d'une enchère
  let priceStep = 50;

  // prix actuel du cheval
  let basePrice = 0;

  // Quand l'utilisateur clique sur le bouton "enchérir"
  bidButton.addEventListener("click", function () {

    // récupère les données stockées dans les attributs data-*
    const name = this.dataset.horseName; // nom du cheval
    basePrice = parseFloat(this.dataset.price); // prix actuel converti en nombre
    const id = this.dataset.horseId; // id du cheval
    const image = this.dataset.image; // image du cheval

    // remplit les informations dans la modale
    horseName.textContent = name;
    // permet de renvoyer une chaîne de caractères 
    // représentant un nombre en tenant compte de la locale
    currentPrice.textContent = basePrice.toLocaleString() + " €"; // formatte le prix
    
    horseIdInput.value = id;

    // montant proposé = prix actuel + incrément minimum
    bidAmountInput.value = basePrice + priceStep;

    // met à jour l'image dans la modale si elle existe
    if (modalImg && image) {
      modalImg.src = image;
      modalImg.alt = name;
    }

    // affiche la modale (on retire la classe hidden)
    modal.classList.remove("hidden");
  });

  // ferme la modale si on clique sur le bouton fermer
  closeBtn.addEventListener("click", function () {
    modal.classList.add("hidden");
  });

  // bouton pour diminuer l'enchère
  minusBtn.addEventListener("click", function () {

    // empêche de descendre en dessous de l'enchère minimale
    // parseFloat() est une fonction JavaScript qui transforme une chaîne 
    // de caractères (texte) en nombre décimal.
    if (parseFloat(bidAmountInput.value) > basePrice + priceStep) {
      bidAmountInput.value = parseFloat(bidAmountInput.value) - priceStep;
    }
  });

  // bouton pour augmenter l'enchère
  plusBtn.addEventListener("click", function () {

    // ajoute l'incrément au montant actuel
    bidAmountInput.value = parseFloat(bidAmountInput.value) + priceStep;
  });

});