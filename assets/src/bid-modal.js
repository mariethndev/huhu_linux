document.addEventListener("DOMContentLoaded", function () {

  const bidButton = document.querySelector(".btn-bid"); 
  const modal = document.getElementById("bidModal"); // fenêtre modale d'enchère
  const closeBtn = document.querySelector(".modal-close"); // bouton fermer la modale
  const horseName = document.getElementById("modalHorseName"); // nom du cheval dans la modale
  const currentPrice = document.getElementById("modalCurrentPrice"); // prix actuel affiché
  const horseIdInput = document.getElementById("modalHorseId"); // champ caché pour l'id du cheval
  const bidAmountInput = document.getElementById("bidAmount"); // champ du montant de l'enchère
  const minusBtn = document.getElementById("bidMinus"); // bouton pour diminuer l'enchère
  const plusBtn = document.getElementById("bidPlus"); // bouton pour augmenter l'enchère
  const modalImg = document.querySelector(".modal-image img"); // image du cheval dans la modale

  if (!bidButton) return;
  let priceStep = 50;
  let basePrice = 0;

  bidButton.addEventListener("click", function () {

    const name = this.dataset.horseName;
    basePrice = parseFloat(this.dataset.price); 
    const id = this.dataset.horseId; 
    const image = this.dataset.image; 

    horseName.textContent = name;
    
    currentPrice.textContent = basePrice.toLocaleString() + " €";
    
    horseIdInput.value = id;

    bidAmountInput.value = basePrice + priceStep;

    if (modalImg && image) {
      modalImg.src = image;
      modalImg.alt = name;
    }

    modal.classList.remove("hidden");
  });

  closeBtn.addEventListener("click", function () {
    modal.classList.add("hidden");
  });

  minusBtn.addEventListener("click", function () {

    if (parseFloat(bidAmountInput.value) > basePrice + priceStep) {
      bidAmountInput.value = parseFloat(bidAmountInput.value) - priceStep;
    }
  });

  plusBtn.addEventListener("click", function () {

    bidAmountInput.value = parseFloat(bidAmountInput.value) + priceStep;
  });

});