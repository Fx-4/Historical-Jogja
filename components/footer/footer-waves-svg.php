<?php
// Save as: components/footer/footer-waves-svg.php
?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" style="transform: scaleY(-1.03);">
    <!-- Define gradient patterns -->
    <defs>
        <linearGradient id="wave-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:#914e18;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#b36420;stop-opacity:1" />
        </linearGradient>
        
        <!-- Define the wave path pattern -->
        <path id="wave-path" 
              d="M0,160 C320,300,420,300,740,160 C1060,20,1120,20,1440,160 V320 H0 Z">
            <!-- Add smooth wave animation -->
            <animate 
                attributeName="d" 
                values="M0,160 C320,300,420,300,740,160 C1060,20,1120,20,1440,160 V320 H0 Z;
                        M0,160 C320,20,420,20,740,160 C1060,300,1120,300,1440,160 V320 H0 Z;
                        M0,160 C320,300,420,300,740,160 C1060,20,1120,20,1440,160 V320 H0 Z"
                dur="20s"
                repeatCount="indefinite"/>
        </path>
    </defs>

    <!-- Create multiple wave layers -->
    <g class="waves" transform="scaleY(-1)">
        <!-- Back wave -->
        <use href="#wave-path" 
             fill="url(#wave-gradient)" 
             opacity="0.3"
             transform="translate(1, -15)">
        </use>
        
        <!-- Middle wave -->
        <use href="#wave-path" 
             fill="url(#wave-gradient)" 
             opacity="0.6"
             transform="translate(0, -10)">
        </use>
        
        <!-- Front wave -->
        <use href="#wave-path" 
             fill="url(#wave-gradient)" 
             opacity="0.9"
             transform="translate(0, -5)">
        </use>
    </g>
</svg>