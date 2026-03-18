document.addEventListener("DOMContentLoaded", () => {

  const livePriceSpan = document.querySelector(".live-price");
  const bidMsg = document.getElementById("bidMessage");
  const bidButton = document.querySelector(".btn-bid");

  if (!livePriceSpan) return;

   if (bidMsg) {
    bidMsg.textContent = "Chargement des enchères...";
  }

  const horseId = livePriceSpan.dataset.id;

  let lastPrice =
    parseFloat(livePriceSpan.textContent.replace(/\s|€/g, "")) || 0;

  function updatePrice() {
    fetch(`/huhu/huhu_linux/controller/get_price.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ horse_id: horseId }),
    })
      .then((res) => res.json())
      .then((data) => {

        if (!data.success) {
          if (bidMsg) {
            bidMsg.textContent = "Erreur récupération enchère";
          }
          return;
        }

        const currentPrice = parseFloat(data.price);
        const lastBidderId = data.last_bidder;
        const currentUserId = data.current_user;
        const hasBid = data.has_bid;

        if (!isNaN(currentPrice)) {

           if (
            lastBidderId !== null &&
            String(lastBidderId) === String(currentUserId)
          ) {
            if (bidMsg) {
              bidMsg.textContent = "Vous êtes en tête sur ce lot !";
            }
            if (bidButton) bidButton.disabled = true;
          }

           else if (hasBid === true) {
            if (bidMsg) {
              bidMsg.textContent = "Vous avez été dépassé ! Surenchérissez !";
            }
            if (bidButton) bidButton.disabled = false;
          }

           else {
            if (bidMsg) {
              bidMsg.textContent = "Faites votre première offre !";
            }
            if (bidButton) bidButton.disabled = false;
          }

           livePriceSpan.textContent =
            currentPrice.toLocaleString("fr-FR") + " €";

          lastPrice = currentPrice;
        }
      })
      .catch((err) => {
        console.error("Erreur fetch prix :", err);
        if (bidMsg) {
          bidMsg.textContent = "Erreur serveur";
        }
      });
  }

   setInterval(updatePrice, 3000);

   updatePrice();

});