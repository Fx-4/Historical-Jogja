<?php
session_start();
require_once '../config/db-connect.php';

// Check if building ID is provided
if (!isset($_GET['id'])) {
    header("Location: bbsejarah.php");
    exit;
}

$building_id = intval($_GET['id']);

// Database connection
$db = new Database();
$conn = $db->connect();

// Get building details with location and gallery count
$sql = "SELECT b.*, 
               COUNT(DISTINCT g.gallery_id) as gallery_count,
               t.latitude, t.longitude, t.location_name, t.description as location_description
        FROM historical_buildings b
        LEFT JOIN gallery g ON b.building_id = g.building_id
        LEFT JOIN tourist_map t ON b.building_id = t.building_id
        WHERE b.building_id = ?
        GROUP BY b.building_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $building_id);
$stmt->execute();
$building = $stmt->get_result()->fetch_assoc();

if (!$building) {
    header("Location: bbsejarah.php");
    exit;
}

// Set cookie for last visited building
setcookie("historical_jogja_last_visited", $building_id, [
    'expires' => time() + (86400 * 30),
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Get gallery images
$sql_gallery = "SELECT * FROM gallery WHERE building_id = ? LIMIT 6";
$stmt_gallery = $conn->prepare($sql_gallery);
$stmt_gallery->bind_param("i", $building_id);
$stmt_gallery->execute();
$gallery = $stmt_gallery->get_result();

// Get related buildings (same category)
$sql_related = "SELECT building_id, building_name, main_image, category 
                FROM historical_buildings 
                WHERE category = ? 
                AND building_id != ? 
                AND status = 'aktif'
                LIMIT 3";
$stmt_related = $conn->prepare($sql_related);
$stmt_related->bind_param("si", $building['category'], $building_id);
$stmt_related->execute();
$related_buildings = $stmt_related->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candi Prambanan - Historical Jogja</title>
    
    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://unpkg.com">
    
    <!-- Font preloading -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" as="style">
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="Candi Prambanan, yang dibangun pada tahun 850 Masehi, adalah hasil karya Rakai Pikatan dari Wangsa Sanjaya sebagai penghormatan kepada agama Hindu. Kompleks ini">
    <meta property="og:title" content="Candi Prambanan - Historical Jogja">
    <meta property="og:description" content="Candi Prambanan, yang dibangun pada tahun 850 Masehi, adalah hasil karya Rakai Pikatan dari Wangsa Sanjaya sebagai penghormatan kepada agama Hindu. Kompleks ini">
    <meta property="og:image" content="../uploads/main/candi/676384e6b5b60_WIN_20230806_15_43_28_Pro.jpg">
    
    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="../assets/css/building-detail.css">
    <link rel="stylesheet" href="../assets/css/navbar/primary-nav.css">
    <link rel="stylesheet" href="../assets/css/navbar/mobile-nav.css">

    <script>
// Convert PHP gallery data to JavaScript
window.galleryData = <?php 
    $galleryData = [];
    if ($gallery) {
        while ($image = $gallery->fetch_assoc()) {
            $galleryData[] = [
                'src' => '../uploads/gallery/' . htmlspecialchars($image['image_path']),
                'caption' => htmlspecialchars($image['caption']),
                'height' => 250 // Default height, dapat disesuaikan
            ];
        }
        echo json_encode($galleryData);
    } else {
        echo '[]';
    }
?>;
</script>

</head>

<body>
    <?php include_once '../components/navbar/PrimaryNav.php'; ?>
    <?php include_once '../components/navbar/MobileNav.php'; ?>

    <main class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-image">
                <img src="../uploads/main/<?php echo htmlspecialchars($building['main_image']); ?>" 
                     alt="<?php echo htmlspecialchars($building['building_name']); ?>">
                <div class="hero-overlay"></div>
            </div>
            
            <div class="hero-content">
                <nav class="breadcrumb">
                    <a href="beranda.php">Beranda</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="bbsejarah.php">Bangunan Bersejarah</a>
                    <i class="fas fa-chevron-right"></i>
                    <span><?php echo htmlspecialchars($building['building_name']); ?></span>
                </nav>
                
                <h1 class="building-title"><?php echo htmlspecialchars($building['building_name']); ?></h1>
                
                <div class="building-meta">
                    <span class="category">
                        <i class="fas fa-landmark"></i>
                        <?php echo ucfirst(htmlspecialchars($building['category'])); ?>
                    </span>
                    <span class="year">
                        <i class="fas fa-calendar-alt"></i>
                        <?php echo htmlspecialchars($building['construction_year']); ?>
                    </span>
                </div>
            </div>
        </section>

        <!-- Content Section -->
        <section class="content-section">
            <div class="content-wrapper">
                <!-- Main Content -->
                <div class="main-column">
                    <!-- Complete History -->
                    <article class="content-block history-block">
                        <h2>Sejarah Lengkap</h2>
                        <div class="content-text">
                            <?php echo nl2br(htmlspecialchars($building['complete_history'])); ?>
                        </div>
                    </article>

                    <!-- Important Figures -->
                    <?php if ($building['important_figures']): ?>
                    <article class="content-block figures-block">
                        <h2>Tokoh Penting</h2>
                        <div class="content-text">
                            <?php echo nl2br(htmlspecialchars($building['important_figures'])); ?>
                        </div>
                    </article>
                    <?php endif; ?>

                    <!-- Cultural Value -->
                    <?php if ($building['cultural_value']): ?>
                    <article class="content-block cultural-block">
                        <h2>Nilai Budaya</h2>
                        <div class="content-text">
                            <?php echo nl2br(htmlspecialchars($building['cultural_value'])); ?>
                        </div>
                    </article>
                    <?php endif; ?>

                    <!-- Gallery Preview -->
                    <?php if ($gallery->num_rows > 0): ?>
                    <article class="content-block gallery-block">
                        <div class="block-header">
                            <h2>Galeri Foto</h2>
                            <?php if ($gallery->num_rows > 6): ?>
                                <a href="gallery.php?building=<?php echo $building_id; ?>" class="view-all">
                                    Lihat Semua <i class="fas fa-arrow-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="gallery-grid">
                            <?php while ($image = $gallery->fetch_assoc()): ?>
                                <div class="gallery-item">
                                    <img src="../uploads/gallery/<?php echo htmlspecialchars($image['image_path']); ?>"
                                         alt="<?php echo htmlspecialchars($image['caption']); ?>"
                                         loading="lazy"
                                         onclick="openGallery(this.src)">
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </article>
                    <?php endif; ?>

<!-- Location Map -->
<?php if ($building['latitude'] && $building['longitude']): ?>
<article class="content-block map-block">
    <h2>Lokasi</h2>
    <div id="map" class="location-map"></div>
    <div class="map-actions">
        <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $building['latitude']; ?>,<?php echo $building['longitude']; ?>" 
           class="google-maps-button"
           target="_blank"
           rel="noopener noreferrer">
            <i class="fas fa-directions"></i>
            Buka di Google Maps
        </a>
        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($building['building_name']); ?>" 
           class="search-maps-button"
           target="_blank"
           rel="noopener noreferrer">
            <i class="fas fa-search-location"></i>
            Cari di Google Maps
        </a>
    </div>
    <?php if ($building['location_description']): ?>
        <p class="location-description">
            <?php echo nl2br(htmlspecialchars($building['location_description'])); ?>
        </p>
    <?php endif; ?>
</article>
<?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Quick Info -->
                    <div class="sidebar-block info-block">
                        <h3>Informasi Praktis</h3>
                        <ul class="info-list">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Alamat</strong>
                                    <p><?php echo nl2br(htmlspecialchars($building['address'])); ?></p>
                                </div>
                            </li>
                            <?php if ($building['open_hours']): ?>
                            <li>
                                <i class="fas fa-clock"></i>
                                <div>
                                    <strong>Jam Operasional</strong>
                                    <p><?php echo nl2br(htmlspecialchars($building['open_hours'])); ?></p>
                                </div>
                            </li>
                            <?php endif; ?>
                            <?php if ($building['ticket_price']): ?>
                            <li>
                                <i class="fas fa-ticket-alt"></i>
                                <div>
                                    <strong>Harga Tiket</strong>
                                    <p><?php echo htmlspecialchars($building['ticket_price']); ?></p>
                                </div>
                            </li>
                            <?php endif; ?>
                            <?php if ($building['contact']): ?>
                            <li>
                                <i class="fas fa-phone"></i>
                                <div>
                                    <strong>Kontak</strong>
                                    <p><?php echo htmlspecialchars($building['contact']); ?></p>
                                </div>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Share Buttons -->
                    <div class="sidebar-block share-block">
                        <h3>Bagikan</h3>
                        <div class="share-buttons">
                            <button onclick="share('facebook')" class="share-button facebook">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button onclick="share('twitter')" class="share-button twitter">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button onclick="share('whatsapp')" class="share-button whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button onclick="share('copy')" class="share-button copy">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Related Buildings -->
                    <?php if ($related_buildings->num_rows > 0): ?>
                    <div class="sidebar-block related-block">
                        <h3>Bangunan Terkait</h3>
                        <div class="related-buildings">
                            <?php while ($related = $related_buildings->fetch_assoc()): ?>
                                <a href="building-detail.php?id=<?php echo $related['building_id']; ?>" 
                                   class="related-item">
                                    <img src="../uploads/main/<?php echo htmlspecialchars($related['main_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($related['building_name']); ?>"
                                         loading="lazy">
                                    <div class="related-info">
                                        <h4><?php echo htmlspecialchars($related['building_name']); ?></h4>
                                        <span class="related-category">
                                            <?php echo ucfirst(htmlspecialchars($related['category'])); ?>
                                        </span>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </aside>
            </div>
        </section>
    </main>

<!-- Footer -->
<footer class="site-footer">
    <!-- Wave Animation -->
    <div class="footer-waves-wrapper">
    <?php 
    $svgPath = __DIR__ . '/../components/footer/footer-waves-svg.php';
    if (file_exists($svgPath)) {
        include $svgPath;
    } else {
        echo "<!-- SVG file not found at: $svgPath -->";
    }
    ?>
    </div>

    <!-- Footer Content -->
    <div class="footer-content">
        <div class="footer-grid">
            <!-- Company Info Section -->
            <div class="footer-section">
                <h3 class="footer-title">Historical Jogja</h3>
                <p class="footer-desc">Melestarikan Warisan Sejarah Yogyakarta melalui teknologi digital yang inovatif.</p>
                <div class="footer-social">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <!-- Quick Links Section -->
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

            <!-- Contact Section -->
            <div class="footer-section">
                <h3 class="footer-title">Kontak</h3>
                <ul class="footer-contact">
                    <li><i class="fas fa-map-marker-alt"></i> Jl. Malioboro No. 123, Yogyakarta</li>
                    <li><i class="fas fa-phone"></i> +62 274 123456</li>
                    <li><i class="fas fa-envelope"></i> info@historicaljogja.com</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Historical Jogja. All rights reserved.</p>
    </div>
</footer>

    <!-- Gallery Modal -->
    <div id="galleryModal" class="modal">
        <button class="modal-close">&times;</button>
        <img id="modalImage" src="" alt="Gallery image">
    </div>

    <!-- Scripts -->

       <!-- Navigation Scripts -->
    <script src="../assets/js/navbar/primary-nav.js" defer></script>
    <script src="../assets/js/navbar/mobile-nav.js" defer></script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize map if coordinates exist
        <?php if ($building['latitude'] && $building['longitude']): ?>
        const map = L.map('map').setView([<?php echo $building['latitude']; ?>, <?php echo $building['longitude']; ?>], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.marker([<?php echo $building['latitude']; ?>, <?php echo $building['longitude']; ?>])
            .addTo(map)
            .bindPopup("<?php echo htmlspecialchars($building['building_name']); ?>");
        <?php endif; ?>

        // Gallery modal
        function openGallery(src) {
            const modal = document.getElementById('galleryModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = "flex";
            modalImg.src = src;
        }

        document.querySelector('.modal-close').addEventListener('click', function() {
            document.getElementById('galleryModal').style.display = "none";
        });

        // Share functionality
        function share(platform) {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            
            switch(platform) {
                case 'facebook':
                    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`);
                    break;
                case 'twitter':
                    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`);
                    break;
                case 'whatsapp':window.open(`https://wa.me/?text=${title} ${url}`);
                    break;
                case 'copy':
                    navigator.clipboard.writeText(window.location.href)
                        .then(() => {
                            showToast('Link berhasil disalin!');
                        })
                        .catch(() => {
                            showToast('Gagal menyalin link', 'error');
                        });
                    break;
            }
        }

        // Toast notification
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('show');
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 300);
                }, 2000);
            }, 100);
        }

        // Lazy loading for images
        document.addEventListener('DOMContentLoaded', function() {
            const lazyImages = document.querySelectorAll('img[loading="lazy"]');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src || img.src;
                            imageObserver.unobserve(img);
                        }
                    });
                });

                lazyImages.forEach(img => imageObserver.observe(img));
            }
        });

        // Smooth scroll for navigation
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

        // Track reading progress
        window.addEventListener('scroll', function() {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            
            if (document.querySelector('.progress-bar')) {
                document.querySelector('.progress-bar').style.width = scrolled + '%';
            }
        });
    </script>
</body>
</html>