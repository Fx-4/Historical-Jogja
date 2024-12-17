function resizeNav() {
    // Set the nav height to fill the window
    $("#nav-fullscreen").css({"height": window.innerHeight});
    
    // Set the circle radius to the length of the window diagonal from the right side
    var radius = Math.sqrt(Math.pow(window.innerHeight, 2) + Math.pow(window.innerWidth, 2));
    var diameter = radius * 2;
    $("#nav-overlay").width(diameter);
    $("#nav-overlay").height(diameter);
    $("#nav-overlay").css({
        "margin-top": -radius,
        "margin-right": -radius
    });
}

$(document).ready(function() {
    // Hide navbar on scroll down, show on scroll up
    let lastScrollTop = 0;
    $(window).scroll(function() {
        let st = $(this).scrollTop();
        if (st > lastScrollTop && st > 100) {
            // Scroll down
            $('.navbar').addClass('navbar-hidden');
        } else {
            // Scroll up
            $('.navbar').removeClass('navbar-hidden');
        }
        lastScrollTop = st;
    });

    // Toggle navigation
    $("#nav-toggle").click(function() {
        $("#nav-toggle, #nav-overlay, #nav-fullscreen").toggleClass("open");
        
        // Toggle body scroll
        if($("#nav-fullscreen").hasClass("open")) {
            $("body").css("overflow", "hidden");
            $('.navbar').addClass('navbar-hidden'); // Hide navbar when menu is open
        } else {
            $("body").css("overflow", "");
            $('.navbar').removeClass('navbar-hidden');
        }
    });
    
    // Close navigation when clicking a link
    $("#nav-fullscreen a").click(function() {
        $("#nav-toggle, #nav-overlay, #nav-fullscreen").removeClass("open");
        $("body").css("overflow", "");
    });

    // Handle window resize
    $(window).resize(resizeNav);
    resizeNav();

    // Add scroll animation for links
    $("#nav-fullscreen a").click(function(e) {
        let target = $(this).attr('href');
        if(target !== '#' && !target.startsWith('http')) {
            e.preventDefault();
            $("#nav-toggle").click(); // Close menu
            setTimeout(() => {
                window.location = target;
            }, 500);
        }
    });
});

// Close navigation on escape key
$(document).keyup(function(e) {
    if (e.key === "Escape") {
        $("#nav-toggle, #nav-overlay, #nav-fullscreen").removeClass("open");
        $("body").css("overflow", "");
    }
});

