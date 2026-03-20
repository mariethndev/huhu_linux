document.addEventListener("DOMContentLoaded", () => {

  const btn = document.querySelector(".btn-bid");
  const modal = document.getElementById("bidModal");

  if (btn && modal) {

    const step = 50;
    let base = 0;

    btn.addEventListener("click", (e) => {
      e.stopPropagation(); 

      base = Number(btn.dataset.price);

      document.getElementById("modalHorseName").textContent = btn.dataset.horseName;
      document.getElementById("modalCurrentPrice").textContent = base + " €";
      document.getElementById("modalHorseId").value = btn.dataset.horseId;
      document.getElementById("bidAmount").value = base + step;

      const img = document.querySelector(".modal-image img");

      if (img && btn.dataset.image) {
        img.src = btn.dataset.image;
        img.alt = btn.dataset.horseName;
      }

      modal.classList.remove("hidden");
    });
  }

  const closeBtn = document.querySelector(".modal-close");
  if (closeBtn && modal) {
    closeBtn.addEventListener("click", () => {
      modal.classList.add("hidden");
    });
  }

});