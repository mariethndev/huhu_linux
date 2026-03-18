document.addEventListener("DOMContentLoaded", () => {

  document.querySelectorAll(".btn-delete-horse").forEach(btn => {
    btn.addEventListener("click", () => {
      const nameEl = document.getElementById("deleteHorseName");
      const idEl   = document.getElementById("deleteHorseId");
      const modal  = document.getElementById("deleteModal");

      if (!nameEl || !idEl || !modal) return;

      nameEl.textContent = btn.dataset.name;
      idEl.value = btn.dataset.id;
      modal.classList.remove("hidden");
    });
  });

  const closeBtn = document.querySelector(".modal-close-delete");
  const cancelBtn = document.querySelector(".btn-cancel-delete");
  const modal = document.getElementById("deleteModal");

  if (closeBtn && modal) {
    closeBtn.addEventListener("click", (e) => {
      e.preventDefault();
      modal.classList.add("hidden");
    });
  }

  if (cancelBtn && modal) {
    cancelBtn.addEventListener("click", (e) => {
      e.preventDefault();
      modal.classList.add("hidden");
    });
  }

  if (modal) {
    modal.addEventListener("click", (e) => {
      if (e.target.id === "deleteModal") {
        modal.classList.add("hidden");
      }
    });
  }

});