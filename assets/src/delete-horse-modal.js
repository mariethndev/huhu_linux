document.addEventListener("DOMContentLoaded", function () {
  const deleteButtons = document.querySelectorAll(".btn-delete-horse");
  const modal = document.getElementById("deleteModal");
  const closeBtn = document.querySelector(".modal-close-delete");
  const cancelBtn = document.querySelector(".btn-cancel-delete");
  const horseName = document.getElementById("deleteHorseName");
  const horseIdInput = document.getElementById("deleteHorseId");
 
  
  deleteButtons.forEach((button) => {
    button.addEventListener("click", function () {
      horseName.textContent = this.dataset.name;
      horseIdInput.value = this.dataset.id;

      modal.classList.remove("hidden");
    });
  });

  function closeModal() {
    modal.classList.add("hidden");
  }

  if (closeBtn) {
    closeBtn.addEventListener("click", function (e) {
      e.preventDefault();
      closeModal();
    });
  }

  if (cancelBtn) {
    cancelBtn.addEventListener("click", function (e) {
      e.preventDefault();
      closeModal();
    });
  }

  modal.addEventListener("click", function (e) {
    if (e.target === modal) {
      closeModal();
    }
  });
});
