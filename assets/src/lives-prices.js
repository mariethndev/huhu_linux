// j’attends que la page soit chargée
document.addEventListener("DOMContentLoaded", () => {

  // je récupère les éléments
  const priceEl = document.querySelector(".live-price");
  const msg = document.getElementById("bidMessage");
  const btn = document.querySelector(".btn-bid");

  // si le prix existe pas j’arrête
  if (!priceEl) return;

  // je récupère l’id du cheval
  const horseId = Number(priceEl.dataset.id);

  // si l’id est invalide j’affiche une erreur
  if (!horseId) {
    msg.textContent = "Erreur ID";
    console.error("ID invalide :", priceEl.dataset.id);
    return;
  }

  // j’ai une clé pour le localStorage
  const key = "price_" + horseId;

  // je récupère le prix sauvegardé
  const saved = localStorage.getItem(key);
  if (saved) priceEl.textContent = saved + " €";

  // fonction pour mettre à jour le prix
  function update() {

    // je prépare les données à envoyer
    const formData = new FormData();
    formData.append("horse_id", horseId);

    // j’envoie la requête au serveur
    fetch("/huhu/huhu_linux/controller/get_price.php", {
      method: "POST",
      body: formData
    })
    .then(response => {

      // je vérifie la réponse
      if (!response.ok) throw new Error("HTTP " + response.status);

      return response.json();
    })
    .then(data => {

      // j’affiche les données pour debug
      console.log("DATA:", data); 

      // si erreur côté serveur
      if (!data.success) {
        msg.textContent = data.error || "Erreur";
        console.error("Erreur PHP :", data);
        return;
      }

      // je convertis le prix
      const price = Number(data.price);

      // si prix invalide j’arrête
      if (isNaN(price)) {
        console.error("Prix invalide :", data.price);
        return;
      }

      // si je suis en tête
      if (data.current_user && data.last_bidder == data.current_user) {
        msg.textContent = "Vous êtes en tête";
        btn.disabled = true;
      } 
      // si j’ai été dépassé
      else if (data.has_bid) {
        msg.textContent = "Dépassé !";
        btn.disabled = false;
      } 
      // sinon j’ai pas encore enchéri
      else {
        msg.textContent = "Faites une offre";
        btn.disabled = false;
      }

      // je mets à jour le prix affiché
      priceEl.textContent = price + " €";

      // je sauvegarde le prix
      localStorage.setItem(key, price);
    })
    .catch((err) => {

      // erreur réseau
      console.error("Fetch error :", err);

      msg.textContent = "Erreur connexion";
    });
  }

  // je mets à jour toutes les 3 secondes
  setInterval(update, 3000);

  // je lance une première mise à jour
  update();
});