<?php
session_start();
// Tambahkan ini di bagian atas file setelah session_start()
function setHistoricalCookie($name, $value, $expiry = 30) {
    setcookie("historical_jogja_" . $name, $value, [
        'expires' => time() + (86400 * $expiry), // 30 days default
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

// Cek dan set cookies untuk preferensi user
$saved_category = isset($_COOKIE['historical_jogja_category']) ? $_COOKIE['historical_jogja_category'] : '';
$saved_sort = isset($_COOKIE['historical_jogja_sort']) ? $_COOKIE['historical_jogja_sort'] : 'newest';
$last_visited = isset($_COOKIE['historical_jogja_last_visited']) ? $_COOKIE['historical_jogja_last_visited'] : '';

// Gunakan preferensi yang disimpan jika tidak ada filter baru
$category = isset($_GET['category']) ? $_GET['category'] : $saved_category;
$sort = isset($_GET['sort']) ? $_GET['sort'] : $saved_sort;

// Set cookies baru ketika ada perubahan filter
if (isset($_GET['category'])) {
    setHistoricalCookie('category', $_GET['category']);
}

if (isset($_GET['sort'])) {
    setHistoricalCookie('sort', $_GET['sort']);
}

// Track last visited building
if (isset($_GET['id'])) {
    setHistoricalCookie('last_visited', $_GET['id']);
}

// Track visit count
$visit_count = isset($_COOKIE['historical_jogja_visits']) ? 
    intval($_COOKIE['historical_jogja_visits']) + 1 : 1;
setHistoricalCookie('visits', $visit_count);


require_once '../config/db-connect.php';

// Database connection
$db = new Database();
$conn = $db->connect();

// Pagination settings
$items_per_page = 9;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $items_per_page;

// Get filters
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $conn->real_escape_string($_GET['sort']) : 'newest';

// Build the query
$where_clauses = ["status = 'aktif'"]; // Only show active buildings
$params = [];
$types = '';

if ($category) {
    $where_clauses[] = "category = ?";
    $params[] = $category;
    $types .= 's';
}

if ($search) {
    $where_clauses[] = "(building_name LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param]);
    $types .= 'ss';
}

$where_sql = implode(" AND ", $where_clauses);

// Sort options
$sort_options = [
    'newest' => 'created_at DESC',
    'oldest' => 'created_at ASC',
    'name_asc' => 'building_name ASC',
    'name_desc' => 'building_name DESC'
];

$sort_sql = $sort_options[$sort] ?? 'created_at DESC';

// Get total records for pagination
$count_sql = "SELECT COUNT(*) as total FROM historical_buildings WHERE $where_sql";
$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_records = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $items_per_page);

// Get buildings
$sql = "SELECT b.*, 
               COUNT(DISTINCT g.gallery_id) as gallery_count,
               t.latitude, t.longitude
        FROM historical_buildings b
        LEFT JOIN gallery g ON b.building_id = g.building_id
        LEFT JOIN tourist_map t ON b.building_id = t.building_id
        WHERE $where_sql
        GROUP BY b.building_id
        ORDER BY $sort_sql 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types . "ii", ...array_merge($params, [$items_per_page, $offset]));
$stmt->execute();
$buildings = $stmt->get_result();

// Get categories for filter
$sql_categories = "SELECT DISTINCT category FROM historical_buildings WHERE status = 'aktif' ORDER BY category";
$categories = $conn->query($sql_categories);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bangunan Bersejarah - Historical Jogja</title>
    
    <!-- Preload -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/bbsejarah.css">
    <link rel="stylesheet" href="../assets/css/navbar/primary-nav.css">
    <link rel="stylesheet" href="../assets/css/navbar/mobile-nav.css">
</head>

<body>
    <?php include_once '../components/navbar/PrimaryNav.php'; ?>
    <?php include_once '../components/navbar/MobileNav.php'; ?>

    <main class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">Bangunan Bersejarah Yogyakarta</h1>
                <p class="hero-subtitle">Jelajahi warisan sejarah dan budaya yang memperkaya kota Yogyakarta</p>
            </div>
            <div class="hero-pattern"></div>
        </section>

        <!-- Filters Section -->
        <section class="filters-section">
            <div class="filters-container">
                <div class="search-box">
                    <input type="text" 
                           id="searchInput" 
                           placeholder="Cari bangunan bersejarah..."
                           value="<?php echo htmlspecialchars($search); ?>">
                    <button type="button" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div class="filter-controls">
                    <div class="category-filter">
                        <select id="categoryFilter">
                            <option value="">Semua Kategori</option>
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($cat['category']); ?>"
                                        <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                                    <?php echo ucfirst(htmlspecialchars($cat['category'])); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="sort-filter">
                        <select id="sortFilter">
                            <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Terbaru</option>
                            <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Terlama</option>
                            <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Nama A-Z</option>
                            <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Nama Z-A</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- Buildings Grid -->
        <section class="buildings-section">
            <div class="buildings-grid">
                <?php while ($building = $buildings->fetch_assoc()): ?>
                    <article class="building-card" data-category="<?php echo htmlspecialchars($building['category']); ?>">
                        <div class="building-image">
                            <img src="../uploads/main/<?php echo htmlspecialchars($building['main_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($building['building_name']); ?>"
                                 loading="lazy">
                            <div class="image-overlay">
                                <span class="category-badge">
                                    <?php echo ucfirst(htmlspecialchars($building['category'])); ?>
                                </span>
                                <div class="overlay-actions">
                                    <?php if ($building['gallery_count'] > 0): ?>
                                        <button class="gallery-button" title="Lihat Galeri">
                                            <i class="fas fa-images"></i>
                                            <span><?php echo $building['gallery_count']; ?></span>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($building['latitude'] && $building['longitude']): ?>
                                        <button class="map-button" 
                                                title="Lihat di Peta"
                                                data-lat="<?php echo $building['latitude']; ?>"
                                                data-lng="<?php echo $building['longitude']; ?>">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="building-info">
                            <h2 class="building-name">
                                <a href="building-detail.php?id=<?php echo $building['building_id']; ?>">
                                    <?php echo htmlspecialchars($building['building_name']); ?>
                                </a>
                            </h2>
                            <p class="building-excerpt">
                                <?php 
                                    $excerpt = strip_tags($building['complete_history']);
                                    echo strlen($excerpt) > 150 ? 
                                         substr($excerpt, 0, 150) . '...' : 
                                         $excerpt;
                                ?>
                            </p>
                            <div class="building-meta">
                                <span class="construction-year">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo htmlspecialchars($building['construction_year']); ?>
                                </span>
                                <a href="building-detail.php?id=<?php echo $building['building_id']; ?>" 
                                   class="read-more">
                                    Baca Selengkapnya
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo ($page - 1); ?>&category=<?php echo urlencode($category); ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>" 
                           class="page-link prev">
                            <i class="fas fa-chevron-left"></i>
                            Sebelumnya
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&category=<?php echo urlencode($category); ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>" 
                           class="page-link <?php echo $page == $i ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo ($page + 1); ?>&category=<?php echo urlencode($category); ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>" 
                           class="page-link next">
                            Selanjutnya
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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

    <!-- Scripts -->

        <!-- Navigation Scripts -->
        <script src="../assets/js/navbar/primary-nav.js" defer></script>
    <script src="../assets/js/navbar/mobile-nav.js" defer></script>
    
    <script>
        // Handle filters
        document.querySelectorAll('#categoryFilter, #sortFilter').forEach(select => {
            select.addEventListener('change', function() {
                applyFilters();
            });
        });

        document.querySelector('.search-button').addEventListener('click', function() {
            applyFilters();
        });

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        function applyFilters() {
            const category = document.getElementById('categoryFilter').value;
            const sort = document.getElementById('sortFilter').value;
            const search = document.getElementById('searchInput').value;
            
            window.location.href = `?category=${encodeURIComponent(category)}&sort=${encodeURIComponent(sort)}&search=${encodeURIComponent(search)}`;
        }

        // Image loading animation
        document.querySelectorAll('.building-image img').forEach(img => {
            img.addEventListener('load', function() {
                this.classList.add('loaded');
            });
        });

        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }

        // Gallery button click handler
        document.querySelectorAll('.gallery-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const buildingId = this.closest('.building-card').dataset.id;
                window.location.href = `gallery.php?building=${buildingId}`;
            });
        });

        // Map button click handler
        document.querySelectorAll('.map-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const lat = this.dataset.lat;
                const lng = this.dataset.lng;
                // Add zoom parameter and center=true to trigger map centering
                window.location.href = `petawisata.php?lat=${lat}&lng=${lng}&zoom=17&center=true#map`;
            });
        });

        // Smooth scroll for pagination
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                window.location.href = href;
                
                const buildingsSection = document.querySelector('.buildings-section');
                buildingsSection.scrollIntoView({ behavior: 'smooth' });
            });
        });

        // Animation on scroll
        function revealOnScroll() {
            const cards = document.querySelectorAll('.building-card');
            const windowHeight = window.innerHeight;

            cards.forEach(card => {
                const cardTop = card.getBoundingClientRect().top;
                if (cardTop < windowHeight - 50) {
                    card.classList.add('visible');
                }
            });
        }

        window.addEventListener('scroll', revealOnScroll);
        window.addEventListener('load', revealOnScroll);

        // Enhanced hover effects
        document.querySelectorAll('.building-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.querySelector('.image-overlay').style.opacity = '1';
            });

            card.addEventListener('mouseleave', function() {
                this.querySelector('.image-overlay').style.opacity = '0';
            });
        });

        // Mobile menu handling
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');

        if (mobileMenuToggle && mobileMenu) {
            mobileMenuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('active');
                document.body.classList.toggle('menu-open');
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!mobileMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                    mobileMenu.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            });
        }

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 768) {
                    mobileMenu?.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            }, 250);
        });

        // Add loading state to buttons
        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', function() {
                if (!this.classList.contains('no-loading')) {
                    this.classList.add('loading');
                    setTimeout(() => {
                        this.classList.remove('loading');
                    }, 1000);
                }
            });
        });
    </script>
            <script>
        // Fungsi untuk mendapatkan nilai cookie
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; historical_jogja_${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }

        // Event listener untuk filter dan sort
        document.addEventListener('DOMContentLoaded', function() {
            // Restore saved filters
            const savedCategory = getCookie('category');
            const savedSort = getCookie('sort');
            
            if (savedCategory) {
                document.getElementById('categoryFilter').value = savedCategory;
            }
            
            if (savedSort) {
                document.getElementById('sortFilter').value = savedSort;
            }

            // Track last viewed position
            const lastScrollPos = getCookie('scroll_position');
            if (lastScrollPos) {
                window.scrollTo(0, parseInt(lastScrollPos));
            }

            // Save scroll position before leaving page
            window.addEventListener('beforeunload', function() {
                setHistoricalCookie('scroll_position', window.scrollY);
            });

            // Track time spent on page
            let startTime = Date.now();
            window.addEventListener('beforeunload', function() {
                const timeSpent = Math.round((Date.now() - startTime) / 1000);
                setHistoricalCookie('time_spent', timeSpent);
            });
        });

        // Enhance the existing filter application function
        function applyFilters() {
            const category = document.getElementById('categoryFilter').value;
            const sort = document.getElementById('sortFilter').value;
            const search = document.getElementById('searchInput').value;
            
            // Save preferences to cookies
            setHistoricalCookie('category', category);
            setHistoricalCookie('sort', sort);
            if (search) {
                setHistoricalCookie('last_search', search);
            }
            
            // Redirect with filters
            window.location.href = `?category=${encodeURIComponent(category)}&sort=${encodeURIComponent(sort)}&search=${encodeURIComponent(search)}`;
        }

        // Fungsi untuk set cookie dengan pengaturan keamanan
        function setHistoricalCookie(name, value) {
            const secure = window.location.protocol === 'https:' ? 'Secure;' : '';
            document.cookie = `historical_jogja_${name}=${value};path=/;max-age=2592000;SameSite=Strict;${secure}`;
        }
        </script>
</body>
</html>