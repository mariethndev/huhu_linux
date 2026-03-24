document.addEventListener("DOMContentLoaded", () => {

  const priceEl = document.querySelector(".live-price");
  const msg = document.getElementById("bidMessage");
  const btn = document.querySelector(".btn-bid");

  if (!priceEl) return;

  const horseId = priceEl.dataset.id;
  const key = "price_" + horseId;

  const saved = localStorage.getItem(key);
  if (saved) priceEl.textContent = saved + " €";

  function update() {
    fetch("/huhu/huhu_linux/controller/get_price.php", {
      method: "POST",
      body: JSON.stringify({ horse_id: horseId })
    })
      .then(r => r.json())
      .then(data => {

        if (!data.success) {
          msg.textContent = "Erreur";
          return;
        }

        const price = Number(data.price);

        if (data.last_bidder == data.current_user) {
          msg.textContent = "Vous êtes en tête";
          btn.disabled = true;
        } else if (data.has_bid) {
          msg.textContent = "Dépassé !";
          btn.disabled = false;
        } else {
          msg.textContent = "Faites une offre";
          btn.disabled = false;
        }

        priceEl.textContent = price + " €";
        localStorage.setItem(key, price);
      });
  }

  setInterval(update, 3000);
  update();
});