document.addEventListener("DOMContentLoaded", (event) => {
    const header = document.getElementById('header_top');

    window.addEventListener('scroll', () => {
        header.classList.toggle('is-scrolled', window.scrollY > 30);
    });
});
