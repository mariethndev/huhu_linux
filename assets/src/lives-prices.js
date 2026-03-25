document.addEventListener("DOMContentLoaded", () => {

  const priceEl = document.querySelector(".live-price");
  const msg = document.getElementById("bidMessage");
  const btn = document.querySelector(".btn-bid");

  if (!priceEl) return;

  const horseId = Number(priceEl.dataset.id);
  if (!horseId) {
    msg.textContent = "Erreur ID";
    return;
  }

  const key = "price_" + horseId;

  const saved = localStorage.getItem(key);
  if (saved) priceEl.textContent = saved + " €";

  function update() {

   fetch("/huhu/huhu_linux/controller/get_price.php?horse_id=" + horseId)
      .then(response => {
        if (!response.ok) throw new Error("HTTP");
        return response.json();
      })
      .then(data => {

        if (!data.success) {
          msg.textContent = "Erreur";
          return;
        }

        const price = Number(data.price);
        if (isNaN(price)) return;

        if (data.current_user && data.last_bidder == data.current_user) {
          msg.textContent = "Vous êtes en tête";
          btn.disabled = true;
        } 
        else if (data.has_bid) {
          msg.textContent = "Dépassé !";
          btn.disabled = false;
        } 
        else {
          msg.textContent = "Faites une offre";
          btn.disabled = false;
        }

        priceEl.textContent = price + " €";
        localStorage.setItem(key, price);
      })
      .catch(() => {
        msg.textContent = "Erreur connexion";
      });
  }

  setInterval(update, 3000);
  update();
});

/*
La fonction update permet de récupérer en temps réel les informations de l’enchère depuis le serveur.

Elle envoie une requête HTTP avec fetch vers le fichier get_price.php en passant l’identifiant du cheval dans l’URL.
Cette requête est asynchrone, ce qui signifie qu’elle s’exécute sans recharger la page.

Lorsque la réponse est reçue, elle est d’abord vérifiée avec response.ok afin de s’assurer que la requête HTTP s’est bien déroulée.
Si ce n’est pas le cas, une erreur est déclenchée.

Ensuite, la réponse est convertie en JSON avec response.json(), ce qui permet de récupérer les données envoyées par le serveur.

Dans le bloc suivant, les données sont analysées. Si data.success est false, cela signifie qu’il y a eu un problème côté serveur,
et un message “Erreur” est affiché à l’utilisateur.

Si la réponse est valide, le prix est récupéré avec data.price et converti en nombre.
Une vérification avec isNaN permet d’éviter d’afficher une valeur incorrecte.

Le script applique ensuite une logique en fonction de la situation de l’utilisateur.
Si l’utilisateur connecté est le dernier enchérisseur, il est en tête, un message est affiché et le bouton est désactivé.
Si l’utilisateur a déjà enchéri mais qu’il a été dépassé, un message “Dépassé !” est affiché et le bouton reste actif.
Sinon, l’utilisateur n’a jamais enchéri et un message l’invite à faire une offre.

Le prix est ensuite mis à jour dans l’interface avec priceEl.textContent et sauvegardé dans le localStorage
afin de conserver la dernière valeur connue.

En cas d’erreur lors de la requête fetch, le bloc catch permet d’afficher un message “Erreur connexion”.

La fonction update est appelée immédiatement une première fois pour afficher les données actuelles,
puis elle est exécutée automatiquement toutes les trois secondes grâce à setInterval,
ce qui permet de simuler une mise à jour en temps réel de l’enchère.
*/