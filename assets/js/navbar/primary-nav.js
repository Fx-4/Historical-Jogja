// assets/js/navbar/primary-nav.js
document.addEventListener('DOMContentLoaded', function() {
    const primaryNav = document.getElementById('primaryNav');
    let lastScroll = 0;
    let isScrolling = false;

    // Add active class to current page link
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-links a').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });

    // Scroll handler with requestAnimationFrame for better performance
    window.addEventListener('scroll', () => {
        if (!isScrolling) {
            window.requestAnimationFrame(() => {
                handleScroll();
                isScrolling = false;
            });
            isScrolling = true;
        }
    });

    function handleScroll() {
        const currentScroll = window.pageYOffset;
        
        // Determine scroll direction and distance
        if (currentScroll > lastScroll && currentScroll > 100) {
            // Scrolling down & past threshold
            primaryNav.classList.add('nav-hidden');
        } else {
            // Scrolling up or at top
            primaryNav.classList.remove('nav-hidden');
        }

        lastScroll = currentScroll;
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('.nav-links a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});