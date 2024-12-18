// Mobile Navigation Script
document.addEventListener('DOMContentLoaded', function() {
    // Get all required elements
    const burgerMenu = document.querySelector('.burger-menu');
    const mobileMenu = document.querySelector('.mobile-menu');
    const closeMenu = document.querySelector('.close-menu');
    const menuBackdrop = document.querySelector('.menu-backdrop');
    const menuLinks = document.querySelectorAll('.menu-link');

    // Toggle menu handler
    function toggleMenu() {
        if (mobileMenu) {
            mobileMenu.classList.toggle('active');
            burgerMenu.classList.toggle('menu-active');
            document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
        }
    }

    // Close menu handler
    function closeMenuHandler() {
        if (mobileMenu) {
            mobileMenu.classList.remove('active');
            burgerMenu.classList.remove('menu-active');
            document.body.style.overflow = '';
        }
    }

    // Event Listeners
    if (burgerMenu) {
        burgerMenu.addEventListener('click', function(e) {
            e.preventDefault();
            toggleMenu();
        });
    }

    if (closeMenu) {
        closeMenu.addEventListener('click', closeMenuHandler);
    }

    if (menuBackdrop) {
        menuBackdrop.addEventListener('click', closeMenuHandler);
    }

    // Close menu when clicking menu links
    menuLinks.forEach(link => {
        link.addEventListener('click', closeMenuHandler);
    });

    // Close menu on resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && mobileMenu && mobileMenu.classList.contains('active')) {
            closeMenuHandler();
        }
    });

    // ESC key to close menu
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
            closeMenuHandler();
        }
    });

    // Set active menu item based on current page
    const currentPath = window.location.pathname;
    menuLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath.split('/').pop()) {
            link.classList.add('active');
        }
    });
});