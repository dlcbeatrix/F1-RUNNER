document.addEventListener('DOMContentLoaded', () => {

    const msgBox = document.querySelector('.msgBox');

    if (msgBox) {
        setTimeout(() => {
            msgBox.classList.add('fadeOut');

            setTimeout(() => {
                msgBox.remove();
            }, 500);

        }, 3000);
    }
});