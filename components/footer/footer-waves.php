<?php
// components/footer/footer-waves.php

class FooterWaves {
    private $svgHeight = 150;
    private $baseColor = '#914e18';
    
    public function __construct($options = []) {
        if (isset($options['height'])) {
            $this->svgHeight = $options['height'];
        }
        if (isset($options['color'])) {
            $this->baseColor = $options['color'];
        }
    }
    
    public function render() {
        // Add necessary CSS
        echo '<link rel="stylesheet" href="../assets/css/footer-waves.css">';
        
        // Container element
        echo '<div class="footer-waves">';
        
        // Include the SVG content
        include 'footer-waves-svg.php';
        
        // Add overlay gradient
        echo '<div class="wave-overlay"></div>';
        
        echo '</div>';
    }
}

// Usage example:
/*
$waves = new FooterWaves([
    'height' => 150,
    'color' => '#914e18'
]);
$waves->render();
*/
?>