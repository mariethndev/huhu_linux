document.querySelectorAll(".btn-delete-auction").forEach(btn => {
    btn.addEventListener("click", () => {
        document.getElementById("deleteAuctionId").value = btn.dataset.id;
        document.getElementById("deleteAuctionName").textContent = btn.dataset.name;
        document.getElementById("deleteModal").classList.remove("hidden");
    });
});

document.querySelector(".btn-cancel-delete").addEventListener("click", () => {
    document.getElementById("deleteModal").classList.add("hidden");
});