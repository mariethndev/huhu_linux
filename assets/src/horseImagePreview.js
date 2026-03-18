document.addEventListener("DOMContentLoaded", () => {

  const input = document.getElementById("horseImageInput");

  if (!input) return;

  input.addEventListener("change", (e) => {
    console.log("preview ok");
  });

});