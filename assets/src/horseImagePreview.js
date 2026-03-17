document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("horseImageInput");
    const preview = document.getElementById("horsePreview");
    if (!input || !preview) return;

    input.addEventListener("change", function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
        };

        reader.readAsDataURL(file);

    });

});