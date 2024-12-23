// main.js
document.addEventListener('DOMContentLoaded', function() {
    // Hero Slider
    const initHeroSlider = () => {
        const slides = document.querySelectorAll('.slide');
        let currentSlide = 0;
        const slideInterval = 5000;

        const showSlide = (index) => {
            slides.forEach(slide => slide.classList.remove('active'));
            slides[index].classList.add('active');
        };

        const nextSlide = () => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        };

        // Initialize first slide
        showSlide(0);

        // Auto advance slides
        setInterval(nextSlide, slideInterval);
    };

    // Scroll to Top Button
    const initScrollToTop = () => {
        const scrollBtn = document.getElementById('scrollToTop');
        const scrollThreshold = 300;

        const toggleScrollButton = () => {
            if (window.scrollY > scrollThreshold) {
                scrollBtn.classList.add('visible');
            } else {
                scrollBtn.classList.remove('visible');
            }
        };

        window.addEventListener('scroll', () => {
            requestAnimationFrame(toggleScrollButton);
        });

        scrollBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    };

    // Intersection Observer for Animations
    const initScrollAnimations = () => {
        const observerOptions = {
            threshold: 0.2,
            rootMargin: '50px'
        };

        const animateOnScroll = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    animateOnScroll.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe elements that should animate on scroll
        document.querySelectorAll('.featured-card, .category-card, .tips-card')
            .forEach(element => animateOnScroll.observe(element));
    };

    // Smooth Scroll for Anchor Links
    const initSmoothScroll = () => {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    };

    // Lazy Loading Images
    const initLazyLoading = () => {
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
            });
        } else {
            // Fallback for browsers that don't support lazy loading
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lozad.js/1.16.0/lozad.min.js';
            script.async = true;
            script.onload = () => {
                const observer = lozad();
                observer.observe();
            };
            document.body.appendChild(script);
        }
    };

    // Header Scroll Effect
    const initHeaderScroll = () => {
        const header = document.querySelector('.navbar');
        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.scrollY;
            if (currentScroll > lastScroll && currentScroll > 100) {
                header.classList.add('nav-hidden');
            } else {
                header.classList.remove('nav-hidden');
            }
            lastScroll = currentScroll;
        });
    };

    // Mobile Menu Toggle
    const initMobileMenu = () => {
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        const overlay = document.querySelector('.mobile-overlay');

        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('active');
                document.body.classList.toggle('menu-open');
            });

            if (overlay) {
                overlay.addEventListener('click', () => {
                    mobileMenu.classList.remove('active');
                    document.body.classList.remove('menu-open');
                });
            }

            // Close menu on window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    mobileMenu.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            });
        }
    };

    // Performance Monitoring
    const initPerformanceMonitoring = () => {
        if ('performance' in window) {
            window.addEventListener('load', () => {
                let perfData = window.performance.timing;
                let pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
                console.log(`Page Load Time: ${pageLoadTime}ms`);
            });
        }
    };

    // Initialize all features
    initHeroSlider();
    initScrollToTop();
    initScrollAnimations();
    initSmoothScroll();
    initLazyLoading();
    initHeaderScroll();
    initMobileMenu();
    initPerformanceMonitoring();
});