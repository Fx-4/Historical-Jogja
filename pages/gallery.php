<?php
session_start();
require_once '../config/db-connect.php';

// Cookie management helper functions
function setGalleryCookie($name, $value, $expiry = 30) {
    setcookie("historical_jogja_gallery_" . $name, $value, [
        'expires' => time() + (86400 * $expiry),
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

function getGalleryCookie($name) {
    return isset($_COOKIE["historical_jogja_gallery_" . $name]) ? 
           $_COOKIE["historical_jogja_gallery_" . $name] : null;
}

// Get saved preferences from cookies
$saved_category = getGalleryCookie('category') ?? 'all';
$saved_view = getGalleryCookie('view') ?? 'grid';
$saved_sort = getGalleryCookie('sort') ?? 'newest';
$last_viewed = getGalleryCookie('last_viewed');

// Handle current preferences
$category = $_GET['category'] ?? $saved_category;
$view = $_GET['view'] ?? $saved_view;
$sort = $_GET['sort'] ?? $saved_sort;
$search = $_GET['search'] ?? '';

// Save new preferences
if (isset($_GET['category'])) setGalleryCookie('category', $category);
if (isset($_GET['view'])) setGalleryCookie('view', $view);
if (isset($_GET['sort'])) setGalleryCookie('sort', $sort);

// Track visits
$visit_count = isset($_COOKIE['historical_jogja_gallery_visits']) ? 
    intval($_COOKIE['historical_jogja_gallery_visits']) + 1 : 1;
setGalleryCookie('visits', $visit_count);

// Database connection
$db = new Database();
$conn = $db->connect();

// Build query conditions
$where_clauses = ["hb.status = 'aktif'"]; // Only show active buildings
$params = [];
$types = '';

if ($category !== 'all') {
    $where_clauses[] = "hb.category = ?";
    $params[] = $category;
    $types .= 's';
}

if ($search) {
    $where_clauses[] = "(hb.building_name LIKE ? OR g.caption LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param]);
    $types .= 'ss';
}

$where_sql = implode(" AND ", $where_clauses);

// Sort options
$sort_options = [
    'newest' => 'hb.created_at DESC',
    'oldest' => 'hb.created_at ASC',
    'building_asc' => 'hb.building_name ASC',
    'building_desc' => 'hb.building_name DESC'
];

$sort_sql = $sort_options[$sort] ?? 'hb.created_at DESC';

// Main query to get buildings with galleries
$sql = "SELECT DISTINCT hb.building_id, hb.building_name, hb.category, 
        hb.status, hb.main_image, hb.address, hb.construction_year,
        hb.created_at
        FROM historical_buildings hb
        WHERE $where_sql 
        GROUP BY hb.category, hb.building_id
        ORDER BY $sort_sql";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Group images by category
$images_by_category = [];
while ($row = $result->fetch_assoc()) {
    $images_by_category[$row['category']][] = $row;
}

// Get categories for filter
$sql_categories = "SELECT DISTINCT category 
                  FROM historical_buildings 
                  WHERE status = 'aktif' 
                  ORDER BY category";
$categories = $conn->query($sql_categories);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Bangunan Bersejarah - Historical Jogja</title>
    
    <!-- Preload -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/gallery.css">
    <link rel="stylesheet" href="../assets/css/navbar/primary-nav.css">
    <link rel="stylesheet" href="../assets/css/navbar/mobile-nav.css">

    <!-- Meta tags -->
    <meta name="description" content="Jelajahi koleksi foto bangunan bersejarah di Yogyakarta melalui galeri interaktif kami.">
    <meta name="keywords" content="galeri, foto, bangunan bersejarah, yogyakarta, wisata sejarah">
</head>

<body>
    <!-- Include Navigation -->
    <?php include_once '../components/navbar/PrimaryNav.php'; ?>
    <?php include_once '../components/navbar/MobileNav.php'; ?>

    <main class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">Galeri Bangunan Bersejarah</h1>
                <p class="hero-subtitle">Jelajahi keindahan arsitektur bersejarah Yogyakarta melalui koleksi foto kami</p>
            </div>
            <div class="hero-pattern"></div>
        </section>

        <!-- Filter & Search Section -->
        <section class="filters-section">
            <div class="filters-container">
                <div class="search-box">
                    <input type="text" 
                           id="searchInput" 
                           placeholder="Cari gambar atau bangunan..."
                           value="<?php echo htmlspecialchars($search); ?>">
                    <button type="button" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div class="filter-group">
                    <select id="categoryFilter" class="category-filter">
                        <option value="all">Semua Kategori</option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($cat['category']); ?>"
                                    <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                                <?php echo ucfirst(htmlspecialchars($cat['category'])); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <select id="sortFilter" class="sort-filter">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Terbaru</option>
                        <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Terlama</option>
                        <option value="building_asc" <?php echo $sort === 'building_asc' ? 'selected' : ''; ?>>Nama A-Z</option>
                        <option value="building_desc" <?php echo $sort === 'building_desc' ? 'selected' : ''; ?>>Nama Z-A</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- Gallery Section -->
        <section class="gallery-section">
            <?php if(empty($images_by_category)): ?>
                <div class="empty-state">
                    <i class="fas fa-images"></i>
                    <p>Belum ada foto yang tersedia untuk kategori ini.</p>
                </div>
            <?php else: ?>
                <?php foreach($images_by_category as $cat => $buildings): ?>
                    <div class="category-section">
                        <h2 class="category-title"><?php echo ucfirst(htmlspecialchars($cat)); ?></h2>
                        <div class="gallery-grid">
                            <?php foreach($buildings as $building): ?>
                                <div class="gallery-card">
                                    <div class="gallery-card-image">
                                    <img data-src="../uploads/main/<?php echo htmlspecialchars($building['main_image']); ?>"
                                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                        alt="<?php echo htmlspecialchars($building['building_name']); ?>">
                                        <div class="gallery-overlay">
                                            <div class="gallery-tag">
                                                <?php echo ucfirst(htmlspecialchars($cat)); ?>
                                            </div>
                                            <div class="overlay-actions">
                                            <a href="view-gallery.php?id=<?php echo $building['building_id']; ?>" 
                                            class="btn-gallery">
                                                <i class="fas fa-images"></i>
                                                Lihat Galeri
                                            </a>
                                                <a href="building-detail.php?id=<?php echo $building['building_id']; ?>" 
                                                   class="btn-building">
                                                    <i class="fas fa-building"></i> Detail Bangunan
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="gallery-card-content">
                                        <h3 class="gallery-title">
                                            <?php echo htmlspecialchars($building['building_name']); ?>
                                        </h3>
                                        <div class="gallery-meta">
                                            <span>
                                                <i class="far fa-calendar"></i>
                                                <?php echo htmlspecialchars($building['construction_year']); ?>
                                            </span>
                                            <span>
                                                <i class="fas fa-map-marker-alt"></i>
                                                <?php 
                                                    $address = explode(',', $building['address']);
                                                    echo htmlspecialchars(trim($address[0]));
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <!-- Gallery Modal -->
        <div id="galleryModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Galeri Foto</h3>
                    <button class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="modal-gallery-grid" id="modalGalleryGrid">
                        <!-- Images will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <div id="imagePreviewModal" class="modal">
            <div class="modal-preview-content">
                <button class="modal-close" onclick="closeImagePreview()">&times;</button>
                <img id="previewImage" src="" alt="">
                <p id="previewCaption" class="image-caption"></p>
            </div>
        </div>

        <!-- Image View Modal -->
        <div id="imageModal" class="modal">
            <div class="modal-content">
                <button class="modal-close" aria-label="Tutup modal">&times;</button>
                <img id="modalImage" class="modal-image" src="" alt="">
                <div class="modal-info">
                    <h3 id="modalBuilding" class="modal-building"></h3>
                    <p id="modalCaption" class="modal-caption"></p>
                </div>
            </div>
        </div>
    </main>

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

<!-- Script harus dimuat setelah HTML modal -->
<script src="../assets/js/gallery.js"></script>
        </body>
        </html>