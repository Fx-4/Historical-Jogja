<?php
session_start();
require_once '../config/db-connect.php';

define('SITE_URL', 'http://localhost/Historical%20Jogja');

// Initialize database connection
$db = new Database();
$conn = $db->connect();

// Function to set secure cookies
function setHistoricalCookie($name, $value, $expiry = 30) {
    setcookie("historical_jogja_" . $name, $value, [
        'expires' => time() + (86400 * $expiry),
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

// Track visit count
$visit_count = isset($_COOKIE['historical_jogja_visits']) ? 
    intval($_COOKIE['historical_jogja_visits']) + 1 : 1;
setHistoricalCookie('visits', $visit_count);

// Get latest buildings for hero section
$sql_latest = "SELECT building_id, building_name, main_image, category 
               FROM historical_buildings 
               WHERE status = 'aktif' 
               ORDER BY created_at DESC 
               LIMIT 5";
$latest_buildings = $conn->query($sql_latest);

// Get featured buildings for showcase
$sql_featured = "SELECT hb.building_id, hb.building_name, hb.main_image, 
                        hb.category, hb.complete_history,
                        COUNT(DISTINCT g.gallery_id) as gallery_count
                 FROM historical_buildings hb
                 LEFT JOIN gallery g ON hb.building_id = g.building_id
                 WHERE hb.status = 'aktif'
                 GROUP BY hb.building_id
                 ORDER BY gallery_count DESC
                 LIMIT 6";
$featured_buildings = $conn->query($sql_featured);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historical Jogja - Jelajahi Warisan Sejarah Yogyakarta</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Jelajahi warisan sejarah dan budaya Yogyakarta melalui koleksi bangunan bersejarah, galeri foto, dan informasi lengkap tentang destinasi wisata sejarah">
    <meta name="keywords" content="yogyakarta, sejarah, wisata sejarah, bangunan bersejarah, warisan budaya, heritage">
    <meta name="author" content="Historical Jogja Team">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Historical Jogja - Warisan Sejarah Yogyakarta">
    <meta property="og:description" content="Jelajahi warisan sejarah dan budaya Yogyakarta melalui koleksi lengkap bangunan bersejarah">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.jpg">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <link rel="apple-touch-icon" href="../assets/images/apple-touch-icon.png">
    
    <!-- Preload Resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/beranda.css">
    <link rel="stylesheet" href="../assets/css/navbar/primary-nav.css">
    <link rel="stylesheet" href="../assets/css/navbar/mobile-nav.css">
    <link rel="stylesheet" href="../assets/css/components/footer.css">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "TouristAttraction",
        "name": "Historical Jogja",
        "description": "Portal wisata sejarah dan budaya Yogyakarta",
        "url": "<?php echo SITE_URL; ?>",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Yogyakarta",
            "addressRegion": "DIY",
            "addressCountry": "ID"
        }
    }
    </script>
</head>

<body>

<?php include_once '../components/navbar/PrimaryNav.php'; ?>
<?php include_once '../components/navbar/MobileNav.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-background">
            <div class="hero-overlay"></div>
            <!-- Dynamic background slider -->
            <div class="hero-slider">
                <?php while($building = $latest_buildings->fetch_assoc()): ?>
                    <div class="slide" style="background-image: url('../uploads/main/<?php echo htmlspecialchars($building['main_image']); ?>')">
                        <div class="slide-content">
                            <h2><?php echo htmlspecialchars($building['building_name']); ?></h2>
                            <span class="slide-category">
                                <?php echo ucfirst(htmlspecialchars($building['category'])); ?>
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">Jelajahi Warisan Sejarah Yogyakarta</h1>
                <p class="hero-subtitle">Temukan keindahan dan kisah di balik bangunan bersejarah yang menjadi saksi perjalanan Kota Yogyakarta</p>
                
                <div class="hero-cta">
                    <a href="bbsejarah.php" class="cta-button primary">
                        <i class="fas fa-landmark"></i>
                        Mulai Eksplorasi
                    </a>
                    <a href="petawisata.php" class="cta-button secondary">
                        <i class="fas fa-map-marked-alt"></i>
                        Lihat Peta
                    </a>
                </div>
            </div>

            <div class="hero-features">
                <div class="feature-card">
                    <i class="fas fa-camera-retro"></i>
                    <h3>Galeri Foto</h3>
                    <p>Koleksi foto bangunan bersejarah berkualitas tinggi</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-book-open"></i>
                    <h3>Informasi Lengkap</h3>
                    <p>Detail sejarah dan informasi setiap bangunan</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-route"></i>
                    <h3>Panduan Wisata</h3>
                    <p>Rute dan panduan mengunjungi lokasi sejarah</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-puzzle-piece"></i>
                    <h3>Kuis Interaktif</h3>
                    <p>Uji pengetahuanmu tentang sejarah Yogyakarta</p>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-number">50+</span>
                <span class="stat-label">Bangunan Bersejarah</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">1000+</span>
                <span class="stat-label">Foto Galeri</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">10K+</span>
                <span class="stat-label">Pengunjung</span>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="scroll-indicator">
            <span class="scroll-text">Scroll untuk eksplorasi</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Featured Buildings Section -->
    <section class="featured-section">
        <div class="section-header">
            <h2 class="section-title">Bangunan Bersejarah Unggulan</h2>
            <p class="section-subtitle">Temukan cerita menarik di balik bangunan bersejarah paling populer di Yogyakarta</p>
        </div>

        <div class="featured-grid">
            <?php 
            $count = 0;
            while($building = $featured_buildings->fetch_assoc()): 
                $count++;
                $excerpt = substr(strip_tags($building['complete_history']), 0, 120) . '...';
                $size_class = $count === 1 ? 'featured-large' : 'featured-normal';
            ?>
                <article class="featured-card <?php echo $size_class; ?>">
                    <div class="card-image">
                        <img src="../uploads/main/<?php echo htmlspecialchars($building['main_image']); ?>"
                             alt="<?php echo htmlspecialchars($building['building_name']); ?>"
                             loading="lazy">
                        <div class="card-overlay">
                            <span class="building-category">
                                <?php echo ucfirst(htmlspecialchars($building['category'])); ?>
                            </span>
                            <div class="overlay-content">
                                <h3 class="building-name">
                                    <?php echo htmlspecialchars($building['building_name']); ?>
                                </h3>
                                <p class="building-excerpt"><?php echo $excerpt; ?></p>
                            </div>
                            <div class="card-actions">
                                <a href="building-detail.php?id=<?php echo $building['building_id']; ?>" 
                                   class="btn-detail">
                                    <i class="fas fa-info-circle"></i>
                                    Lihat Detail
                                </a>
                                <?php if($building['gallery_count'] > 0): ?>
                                    <button class="btn-gallery" 
                                            onclick="viewGallery(<?php echo $building['building_id']; ?>)">
                                        <i class="fas fa-images"></i>
                                        Galeri (<?php echo $building['gallery_count']; ?>)
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="section-footer">
            <a href="bbsejarah.php" class="btn-explore">
                Lihat Semua Bangunan
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="featured-interactive">
    <div class="interactive-content">
        <h3>Mulai Petualangan Sejarahmu</h3>
        <p>Gunakan peta interaktif untuk menemukan bangunan bersejarah di sekitarmu</p>
        <div class="interactive-actions">
            <a href="petawisata.php" class="btn-map">
                <i class="fas fa-map-marked-alt"></i>
                Buka Peta
            </a>
            <a href="quis.php" class="btn-quiz">
                <i class="fas fa-puzzle-piece"></i>
                Ikuti Kuis
            </a>
        </div>
    </div>
    <div class="location-preview">
        <div id="preview-map" class="preview-map">
            <!-- Peta Preview Container -->
            <div class="preview-map-container">
                <!-- Jalan-jalan pada peta -->
                <div class="map-roads">
                    <div class="road-horizontal"></div>
                    <div class="road-vertical"></div>
                </div>
                
                <!-- Markers Lokasi -->
                <div class="location-markers">
                    <div class="marker" style="top: 30%; left: 40%">
                        <div class="marker-dot"></div>
                        <div class="marker-pulse"></div>
                    </div>
                    <div class="marker" style="top: 60%; left: 70%">
                        <div class="marker-dot"></div>
                        <div class="marker-pulse"></div>
                    </div>
                    <div class="marker" style="top: 45%; left: 55%">
                        <div class="marker-dot"></div>
                        <div class="marker-pulse"></div>
                    </div>
                </div>

                <!-- Compass Rose -->
                <div class="compass">
                    <div class="compass-arrow">N</div>
                </div>

                <!-- Scale Bar -->
                <div class="scale-bar">
                    <div class="scale-line"></div>
                    <span>500 m</span>
                </div>
            </div>

            <!-- Hover Overlay -->
            <div class="preview-overlay">
                <div class="overlay-content">
                    <h4>Jelajahi Lokasi Bersejarah</h4>
                    <p>Klik untuk melihat peta lengkap</p>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Categories & Quick Access Section -->
    <section class="categories-section">
        <div class="section-header">
            <h2 class="section-title">Jelajahi Kategori</h2>
            <p class="section-subtitle">Temukan bangunan bersejarah berdasarkan kategori yang kamu minati</p>
        </div>

        <div class="categories-grid">
            <a href="bbsejarah.php?category=candi" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-gopuram"></i>
                </div>
                <h3>Candi</h3>
                <p>Bangunan suci peninggalan Hindu-Buddha</p>
                <span class="arrow-link">
                    <i class="fas fa-arrow-right"></i>
                </span>
            </a>

            <a href="bbsejarah.php?category=istana" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-landmark"></i>
                </div>
                <h3>Istana</h3>
                <p>Kompleks bangunan kerajaan & keraton</p>
                <span class="arrow-link">
                    <i class="fas fa-arrow-right"></i>
                </span>
            </a>

            <a href="bbsejarah.php?category=benteng" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-fort-awesome"></i>
                </div>
                <h3>Benteng</h3>
                <p>Bangunan pertahanan bersejarah</p>
                <span class="arrow-link">
                    <i class="fas fa-arrow-right"></i>
                </span>
            </a>

            <a href="bbsejarah.php?category=masjid" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-mosque"></i>
                </div>
                <h3>Masjid</h3>
                <p>Masjid bersejarah & bernilai budaya</p>
                <span class="arrow-link">
                    <i class="fas fa-arrow-right"></i>
                </span>
            </a>

            <a href="bbsejarah.php?category=museum" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-museum"></i>
                </div>
                <h3>Museum</h3>
                <p>Museum & galeri seni sejarah</p>
                <span class="arrow-link">
                    <i class="fas fa-arrow-right"></i>
                </span>
            </a>

            <a href="bbsejarah.php?category=monumen" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-monument"></i>
                </div>
                <h3>Monumen</h3>
                <p>Tugu & monumen bersejarah</p>
                <span class="arrow-link">
                    <i class="fas fa-arrow-right"></i>
                </span>
            </a>
        </div>

        <!-- Quick Access Features -->
        <div class="quick-access">
            <div class="feature-box timeline-feature">
                <div class="feature-icon">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
                <div class="feature-content">
                    <h3>Timeline Sejarah</h3>
                    <p>Jelajahi perjalanan waktu bangunan bersejarah Yogyakarta dari masa ke masa</p>
                    <a href="timeline.php" class="feature-link">
                        Lihat Timeline
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="feature-box gallery-feature">
                <div class="feature-icon">
                    <i class="fas fa-images"></i>
                </div>
                <div class="feature-content">
                    <h3>Galeri Foto</h3>
                    <p>Koleksi foto eksklusif bangunan bersejarah dari berbagai sudut dan era</p>
                    <a href="gallery.php" class="feature-link">
                        Lihat Galeri
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="feature-box quiz-feature">
                <div class="feature-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="feature-content">
                    <h3>Kuis Sejarah</h3>
                    <p>Uji pengetahuanmu tentang sejarah dan warisan budaya Yogyakarta</p>
                    <a href="kuis.php" class="feature-link">
                        Mulai Kuis
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Tips & Panduan Section -->
    <section class="tips-section">
        <div class="section-header">
            <h2 class="section-title">Tips & Panduan Wisata</h2>
            <p class="section-subtitle">Panduan lengkap untuk mengeksplorasi bangunan bersejarah di Yogyakarta</p>
        </div>

        <div class="tips-grid">
            <!-- Photography Tips -->
            <div class="tips-card">
                <div class="tips-icon">
                    <i class="fas fa-camera-retro"></i>
                </div>
                <h3>Tips Fotografi</h3>
                <ul class="tips-list">
                    <li>
                        <i class="fas fa-check"></i>
                        Waktu terbaik untuk foto: pagi (6-8) atau sore (15-17)
                    </li>
                    <li>
                        <i class="fas fa-check"></i>
                        Gunakan mode landscape untuk arsitektur
                    </li>
                    <li>
                        <i class="fas fa-check"></i>
                        Perhatikan detail ornamen & ukiran
                    </li>
                </ul>
            </div>

            <!-- Visiting Tips -->
            <div class="tips-card">
                <div class="tips-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Waktu Kunjungan</h3>
                <ul class="tips-list">
                    <li>
                        <i class="fas fa-check"></i>
                        Weekday: lebih sepi & nyaman
                    </li>
                    <li>
                        <i class="fas fa-check"></i>
                        Buka: 08.00 - 16.00 WIB
                    </li>
                    <li>
                        <i class="fas fa-check"></i>
                        Hindari musim liburan panjang
                    </li>
                </ul>
            </div>

            <!-- Cultural Tips -->
            <div class="tips-card">
                <div class="tips-icon">
                    <i class="fas fa-hands"></i>
                </div>
                <h3>Etika Berkunjung</h3>
                <ul class="tips-list">
                    <li>
                        <i class="fas fa-check"></i>
                        Berpakaian sopan & rapi
                    </li>
                    <li>
                        <i class="fas fa-check"></i>
                        Jaga volume suara
                    </li>
                    <li>
                        <i class="fas fa-check"></i>
                        Hormati area sakral/suci
                    </li>
                </ul>
            </div>
        </div>

        <!-- Travel Guide -->
        <div class="travel-guide">
            <div class="guide-content">
                <h3>Peta & Rute Wisata</h3>
                <p>Temukan rute terbaik untuk mengunjungi bangunan bersejarah dengan peta interaktif kami</p>
                <div class="guide-features">
                    <div class="feature">
                        <i class="fas fa-route"></i>
                        <span>Rute Optimal</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>Lokasi Akurat</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-info-circle"></i>
                        <span>Info Lengkap</span>
                    </div>
                </div>
                <a href="petawisata.php" class="guide-cta">
                    Buka Peta Wisata
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="guide-image">
                <div class="map-preview">
                    <!-- Container untuk preview peta -->
                    <div id="preview-map" class="preview-map-container"></div>
                    
                    <!-- Overlay dengan text -->
                    <div class="map-preview-overlay">
                        <p class="preview-title">Peta Wisata Interaktif</p>
                        <p class="preview-subtitle">Klik untuk melihat peta lengkap</p>
                    </div>
                </div>
            </div>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
            <div class="newsletter-content">
                <div class="newsletter-text">
                    <h2>Jangan Lewatkan Update Terbaru</h2>
                    <p>Dapatkan informasi terbaru tentang sejarah dan budaya Yogyakarta langsung di inbox Anda</p>
                </div>
                <a href="kontak.php" class="newsletter-button">
                    Hubungi Kami
                    <i class="fas fa-paper-plane"></i>
                </a>
            </div>
        </section>

        <!-- Footer -->
        <footer class="site-footer">
        <!-- Wave Animation -->
        <div class="footer-waves-wrapper">
            <?php 
            $svgPath = __DIR__ . '/../components/footer/footer-waves-svg.php';
            if (file_exists($svgPath)) {
                include $svgPath;
            }
            ?>
        </div>

            <div class="footer-content">
                <div class="footer-grid">
                    <!-- About Section -->
                    <div class="footer-section">
                        <h3 class="footer-title">Historical Jogja</h3>
                        <p class="footer-desc">Portal wisata sejarah dan budaya yang memperkenalkan warisan bersejarah Yogyakarta melalui teknologi digital yang informatif dan interaktif.</p>
                        <div class="footer-social">
                            <a href="#" class="social-link" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-link" aria-label="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-link" aria-label="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="footer-section">
                        <h3 class="footer-title">Tautan Cepat</h3>
                        <ul class="footer-links">
                            <li><a href="beranda.php">Beranda</a></li>
                            <li><a href="bbsejarah.php">Bangunan Bersejarah</a></li>
                            <li><a href="gallery.php">Galeri</a></li>
                            <li><a href="timeline.php">Timeline</a></li>
                            <li><a href="kuis.php">Kuis</a></li>
                            <li><a href="petawisata.php">Peta</a></li>
                            <li><a href="kontak.php">Kontak</a></li>
                        </ul>
                    </div>

                    <!-- Contact Info -->
                    <div class="footer-section">
                        <h3 class="footer-title">Kontak</h3>
                        <ul class="footer-contact">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Jl. Malioboro No. 123<br>Yogyakarta, Indonesia</span>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <span>+62 274 123456</span>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <span>info@historicaljogja.com</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Footer Bottom -->
                <div class="footer-bottom">
                    <p>&copy; <?php echo date('Y'); ?> Historical Jogja. All rights reserved.</p>
                </div>
            </div>
        </footer>

               <!-- Navigation Scripts -->
    <script src="../assets/js/navbar/primary-nav.js" defer></script>
    <script src="../assets/js/navbar/mobile-nav.js" defer></script>

        <!-- Scroll to Top Button -->
        <button id="scrollToTop" class="scroll-top" aria-label="Scroll to top">
            <i class="fas fa-chevron-up"></i>
        </button>

        <!-- Scripts -->
        <script src="../assets/js/main.js" defer></script>

        </style>

<!-- Tambahkan JavaScript untuk menambah marker dan interaktivitas -->
<script>
class PreviewMap {
    constructor(containerId, locations) {
        this.container = document.getElementById(containerId);
        this.locations = locations || [
            { 
                top: '30%', 
                left: '40%',
                name: 'Keraton Yogyakarta',
                type: 'istana'
            },
            { 
                top: '60%', 
                left: '70%',
                name: 'Benteng Vredeburg',
                type: 'benteng'
            },
            { 
                top: '45%', 
                left: '55%',
                name: 'Tugu Yogyakarta',
                type: 'monumen'
            }
        ];
        this.init();
    }

    init() {
        this.addMarkers();
        this.addControls();
        this.addCompass();
        this.addScaleBar();
        this.addEventListeners();
        this.startMarkerAnimation();
    }

    addMarkers() {
        this.locations.forEach(location => {
            const marker = document.createElement('div');
            marker.className = 'marker';
            marker.style.top = location.top;
            marker.style.left = location.left;

            // Tambah dot dan pulse effect
            const dot = document.createElement('div');
            dot.className = 'marker-dot';
            
            const pulse = document.createElement('div');
            pulse.className = 'marker-pulse';

            // Tambah tooltip
            const tooltip = document.createElement('div');
            tooltip.className = 'marker-tooltip';
            tooltip.innerHTML = `
                <strong>${location.name}</strong>
                <span class="location-type">${location.type}</span>
            `;

            marker.appendChild(dot);
            marker.appendChild(pulse);
            marker.appendChild(tooltip);
            
            // Hover effect untuk tooltip
            marker.addEventListener('mouseenter', () => {
                tooltip.style.opacity = '1';
                tooltip.style.transform = 'translateY(-10px)';
            });

            marker.addEventListener('mouseleave', () => {
                tooltip.style.opacity = '0';
                tooltip.style.transform = 'translateY(0)';
            });

            this.container.appendChild(marker);
        });
    }

    addControls() {
        const controls = document.createElement('div');
        controls.className = 'map-controls';

        // Zoom controls
        const zoomControls = ['in', 'out'].map(type => {
            const button = document.createElement('button');
            button.className = `zoom-${type}`;
            button.innerHTML = `<i class="fas fa-${type === 'in' ? 'plus' : 'minus'}"></i>`;
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                this.handleZoom(type === 'in' ? 1.1 : 0.9);
            });
            return button;
        });

        // Reset view button
        const resetButton = document.createElement('button');
        resetButton.className = 'reset-view';
        resetButton.innerHTML = '<i class="fas fa-home"></i>';
        resetButton.addEventListener('click', (e) => {
            e.stopPropagation();
            this.resetView();
        });

        controls.append(...zoomControls, resetButton);
        this.container.appendChild(controls);
    }

    addCompass() {
        const compass = document.createElement('div');
        compass.className = 'compass';
        compass.innerHTML = `
            <div class="compass-ring"></div>
            <div class="compass-arrow">N</div>
        `;
        this.container.appendChild(compass);

        // Rotating compass animation
        let rotation = 0;
        setInterval(() => {
            rotation = (rotation + 1) % 360;
            compass.style.transform = `rotate(${rotation}deg)`;
        }, 50);
    }

    addScaleBar() {
        const scaleBar = document.createElement('div');
        scaleBar.className = 'scale-bar';
        scaleBar.innerHTML = `
            <div class="scale-line"></div>
            <span>500 m</span>
        `;
        this.container.appendChild(scaleBar);
    }

    handleZoom(factor) {
        const markers = this.container.querySelectorAll('.marker');
        markers.forEach(marker => {
            const currentScale = marker.style.transform ? 
                parseFloat(marker.style.transform.replace('scale(', '')) : 1;
            marker.style.transform = `scale(${currentScale * factor})`;
        });
    }

    resetView() {
        const markers = this.container.querySelectorAll('.marker');
        markers.forEach(marker => {
            marker.style.transform = 'scale(1)';
        });
    }

    startMarkerAnimation() {
        const markers = this.container.querySelectorAll('.marker');
        markers.forEach((marker, index) => {
            marker.style.animationDelay = `${index * 0.2}s`;
        });
    }

    addEventListeners() {
        // Click untuk navigasi
        this.container.addEventListener('click', () => {
            this.navigateToFullMap();
        });

        // Hover effect untuk preview
        this.container.addEventListener('mouseenter', () => {
            this.container.classList.add('preview-hover');
        });

        this.container.addEventListener('mouseleave', () => {
            this.container.classList.remove('preview-hover');
        });

        // Mouse move effect untuk parallax
        this.container.addEventListener('mousemove', (e) => {
            this.handleParallax(e);
        });
    }

    handleParallax(e) {
        const { left, top, width, height } = this.container.getBoundingClientRect();
        const x = (e.clientX - left) / width - 0.5;
        const y = (e.clientY - top) / height - 0.5;

        const markers = this.container.querySelectorAll('.marker');
        markers.forEach(marker => {
            const speed = 20;
            const xPos = x * speed;
            const yPos = y * speed;
            marker.style.transform = `translate(${xPos}px, ${yPos}px)`;
        });
    }

    navigateToFullMap() {
        window.location.href = 'petawisata.php';
    }
}

// Inisialisasi ketika DOM sudah siap
document.addEventListener('DOMContentLoaded', function() {
    const previewMap = new PreviewMap('preview-map');
});
</script>

    </body>
</html>