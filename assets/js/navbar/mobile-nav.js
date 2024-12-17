// Mobile Navigation Script
document.addEventListener('DOMContentLoaded', function() {
    const burgerMenu = document.querySelector('.burger-menu');
    const mobileMenu = document.getElementById('mobileMenu');
    const closeMenu = document.querySelector('.close-menu');
    const menuBackdrop = document.querySelector('.menu-backdrop');
    const menuLinks = document.querySelectorAll('.menu-link');

    // Toggle menu
    burgerMenu?.addEventListener('click', function() {
        this.classList.add('active');
        mobileMenu.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    // Close menu function
    function closeMenuHandler() {
        burgerMenu?.classList.remove('active');
        mobileMenu.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Add event listeners for closing
    closeMenu?.addEventListener('click', closeMenuHandler);
    menuBackdrop?.addEventListener('click', closeMenuHandler);
    menuLinks.forEach(link => link.addEventListener('click', closeMenuHandler));

    // Close menu on window resize if screen becomes large
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && mobileMenu.classList.contains('active')) {
            closeMenuHandler();
        }
    });

    // Set active menu item based on current page
    const currentPage = window.location.pathname.split('/').pop();
    menuLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
});