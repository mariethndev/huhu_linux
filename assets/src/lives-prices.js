// Attend que le DOM soit complètement chargé
document.addEventListener("DOMContentLoaded", () => {

  // Récupère les éléments HTML nécessaires
  const priceEl = document.querySelector(".live-price");
  const msg = document.getElementById("bidMessage");
  const btn = document.querySelector(".btn-bid");

  // Si l'élément prix n'existe pas → on stoppe
  if (!priceEl) return;

  // Récupère l'id du cheval depuis l'attribut data-id
  const horseId = Number(priceEl.dataset.id);

  // Vérifie que l'id est valide
  if (!horseId) {
    msg.textContent = "Erreur ID";
    console.error("ID invalide :", priceEl.dataset.id);
    return;
  }

  // Clé utilisée pour stocker le prix en local (localStorage)
  const key = "price_" + horseId;

  // Récupère le dernier prix sauvegardé dans le navigateur
  const saved = localStorage.getItem(key);
  if (saved) priceEl.textContent = saved + " €";

  // Fonction qui met à jour le prix en temps réel
  function update() {

    // Création d'un FormData pour envoyer l'id au serveur
    const formData = new FormData();
    formData.append("horse_id", horseId);

    // Requête vers le serveur pour récupérer le prix actuel
    fetch("/huhu/huhu_linux/controller/get_price.php", {
      method: "POST",
      body: formData
    })
    .then(response => {
      // Vérifie si la réponse HTTP est correcte
      if (!response.ok) throw new Error("HTTP " + response.status);
      return response.json();
    })
    .then(data => {

      // Affiche les données reçues dans la console (debug)
      console.log("DATA:", data); 

      // Si le serveur renvoie une erreur
      if (!data.success) {
        msg.textContent = data.error || "Erreur";
        console.error("Erreur PHP :", data);
        return;
      }

      // Convertit le prix en nombre
      const price = Number(data.price);

      // Vérifie que le prix est valide
      if (isNaN(price)) {
        console.error("Prix invalide :", data.price);
        return;
      }

      // Vérifie si l'utilisateur est actuellement le meilleur enchérisseur
      if (data.current_user && data.last_bidder == data.current_user) {
        msg.textContent = "Vous êtes en tête";
        btn.disabled = true;
      } 
      // L'utilisateur a déjà enchéri mais a été dépassé
      else if (data.has_bid) {
        msg.textContent = "Dépassé !";
        btn.disabled = false;
      } 
      // L'utilisateur n'a pas encore enchéri
      else {
        msg.textContent = "Faites une offre";
        btn.disabled = false;
      }

      // Met à jour le prix affiché
      priceEl.textContent = price + " €";

      // Sauvegarde le prix dans le localStorage
      localStorage.setItem(key, price);
    })
    .catch((err) => {
      // Gestion des erreurs de connexion
      console.error("Fetch error :", err);
      msg.textContent = "Erreur connexion";
    });
  }

  // Met à jour le prix toutes les 3 secondes
  setInterval(update, 3000);

  // Lance une première mise à jour immédiatement
  update();
});