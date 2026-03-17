document.addEventListener('DOMContentLoaded', function () {
    const toggleButtons = document.querySelectorAll('.password-toggle');

    if (!toggleButtons.length) return;

    toggleButtons.forEach(button => {

        const input = button.previousElementSibling;
        if (!input || input.type !== 'password') return;

        button.addEventListener('click', function () {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.textContent = isHidden ? '🙊' : '🙈';
        });
    });
});
