document.addEventListener("DOMContentLoaded", () => {
  const livePriceSpan = document.querySelector(".live-price");
  const bidMsg = document.getElementById("bidMessage");
  const bidButton = document.querySelector(".btn-bid");

  if (!livePriceSpan) return;

  // ✅ message au chargement
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
        console.log(data); // ✅ OK ici

        if (!data.success) {
          if (bidMsg) {
            bidMsg.textContent = "Erreur récupération enchère";
          }
          return;
        }

        const currentPrice = parseFloat(data.price);
        const lastBidderId = data.last_bidder;
        const currentUserId = data.current_user;

        if (!isNaN(currentPrice)) {
          // 🔴 déjà dernier enchérisseur
          if (
            lastBidderId !== null &&
            String(lastBidderId) === String(currentUserId)
          ) {
            if (bidMsg) {
              bidMsg.textContent =
                "Vous êtes déjà le dernier enchérisseur. Attendez une nouvelle mise avant de surenchérir.";
            }
            if (bidButton) bidButton.disabled = true;
          }

          // 🟠 dépassé
          else if (currentPrice > lastPrice) {
            if (bidMsg) {
              bidMsg.textContent =
                "Vous venez d'être dépassé ! Surenchérissez !";
            }
            if (bidButton) bidButton.disabled = false;
            lastPrice = currentPrice;
          }

          // 🟢 en tête
          else {
            if (bidMsg) {
              bidMsg.textContent = "Vous êtes en tête sur ce lot !";
            }
            if (bidButton) bidButton.disabled = false;
          }

          // 💰 update prix
          livePriceSpan.textContent =
            currentPrice.toLocaleString("fr-FR") + " €";
        }
      })
      .catch((err) => {
        console.error("Erreur fetch prix :", err);
        if (bidMsg) {
          bidMsg.textContent = "Erreur serveur";
        }
      });
  }

  // ⏱️ refresh
  setInterval(updatePrice, 3000);

  // ⚡ premier appel
  updatePrice();
});