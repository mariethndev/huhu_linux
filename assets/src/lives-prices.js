document.addEventListener("DOMContentLoaded", () => {
  const livePriceSpan = document.querySelector(".live-price");
  if (!livePriceSpan) return;

  const horseId = livePriceSpan.dataset.id;
  let lastPrice = parseFloat(livePriceSpan.textContent.replace(/\s|€/g, "")) || 0;

  function updatePrice() {
    fetch(`/huhu/huhu_linux/controller/get_price.php?id=${horseId}`)
      .then(res => res.text())
      .then(price => {
        const currentPrice = parseFloat(price);
        if (!isNaN(currentPrice)) {
          if (currentPrice > lastPrice) {
            const bidMsg = document.getElementById("bidMessage");
            if (bidMsg) {
              bidMsg.textContent = "Vous venez d'être dépassé ! Surenchérissez !";
            }
            lastPrice = currentPrice;
          } else {
            const bidMsg = document.getElementById("bidMessage");
            if (bidMsg) bidMsg.textContent = "Vous êtes en tête sur ce lot !";
          }
          livePriceSpan.textContent = currentPrice.toLocaleString("fr-FR") + " €";
        }
      })
      .catch(err => console.error("Erreur fetch prix :", err));
  }

  setInterval(updatePrice, 3000);
  updatePrice();
});