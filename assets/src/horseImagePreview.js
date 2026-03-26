document.addEventListener("DOMContentLoaded", () => {

  // je récupère l'input file pour l'image
  const input = document.getElementById("horseImageInput");

  // si l'input existe pas j’arrête
  if (!input) return;

  // quand je change le fichier
  input.addEventListener("change", (e) => {

    // j’ai mis un log pour vérifier que ça marche
    console.log("preview ok");
  });

});