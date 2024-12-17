document.addEventListener('DOMContentLoaded', function() {
    function resizeNav() {
        // Calculate the size needed for the overlay circle
        const radius = Math.sqrt(Math.pow(window.innerHeight, 2) + Math.pow(window.innerWidth, 2));
        const diameter = radius * 2;
        
        const overlay = document.getElementById('nav-overlay');
        overlay.style.width = `${diameter}px`;
        overlay.style.height = `${diameter}px`;
        overlay.style.marginTop = `-${radius}px`;
        overlay.style.marginLeft = `-${radius}px`;
    }

    const navToggle = document.getElementById('nav-toggle');
    const navOverlay = document.getElementById('nav-overlay');
    const navFullscreen = document.getElementById('nav-fullscreen');
    
    navToggle.addEventListener('click', function() {
        this.classList.toggle('open');
        navOverlay.classList.toggle('open');
        navFullscreen.classList.toggle('open');
        
        // Toggle body scroll
        document.body.style.overflow = this.classList.contains('open') ? 'hidden' : '';
    });

    // Initialize overlay size and update on window resize
    window.addEventListener('resize', resizeNav);
    resizeNav();
});