<?php
require_once '../config/db-connect.php';

// Database connection
$db = new Database();
$conn = $db->connect();

// Get all active buildings with their locations 
$sql = "SELECT hb.building_id, hb.building_name, hb.category, 
               hb.main_image, hb.address, tm.latitude, tm.longitude, 
               tm.location_name, tm.description
        FROM historical_buildings hb
        LEFT JOIN tourist_map tm ON hb.building_id = tm.building_id
        WHERE tm.latitude IS NOT NULL 
        AND tm.longitude IS NOT NULL
        AND hb.status = 'aktif'
        ORDER BY hb.building_name";

$result = $conn->query($sql);
$buildings = [];
while ($row = $result->fetch_assoc()) {
    $buildings[] = $row;
}

// Get unique categories for filter
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
    <title>Peta Wisata - Historical Jogja</title>
    
    <!-- Preload -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com">
    
    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <link rel="stylesheet" href="../assets/css/petawisata.css">
    <link rel="stylesheet" href="../assets/css/navbar/primary-nav.css">
    <link rel="stylesheet" href="../assets/css/navbar/mobile-nav.css">
    <link rel="stylesheet" href="<?php echo __DIR__ . '/../assets/css/footer-waves.css'; ?>">
</head>

<body>
    <?php include_once '../components/navbar/PrimaryNav.php'; ?>
    <?php include_once '../components/navbar/MobileNav.php'; ?>

    <main class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">Jelajahi Jogja Bersejarah</h1>
                <p class="hero-subtitle">Temukan lokasi bangunan bersejarah di Yogyakarta dengan mudah</p>
            </div>
            <div class="hero-pattern"></div>
        </section>

        <!-- Map Section -->
        <section class="map-section">
            <div class="map-container">
                <!-- Sidebar -->
                <aside class="map-sidebar">
                    <div class="sidebar-content">
                        <!-- Search & Filter -->
                        <div class="search-filter">
                            <div class="search-box">
                                <input type="text" 
                                       id="searchBuilding" 
                                       placeholder="Cari bangunan bersejarah..."
                                       class="search-input">
                            </div>
                            <div class="filter-box">
                                <select id="categoryFilter" class="filter-select">
                                    <option value="">Semua Kategori</option>
                                    <?php while ($category = $categories->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($category['category']); ?>">
                                            <?php echo ucfirst(htmlspecialchars($category['category'])); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Building List -->
                        <div class="building-list">
                            <?php foreach ($buildings as $building): ?>
                                <div class="building-item" 
                                     data-id="<?php echo $building['building_id']; ?>"
                                     data-category="<?php echo htmlspecialchars($building['category']); ?>">
                                    <img src="../uploads/main/<?php echo htmlspecialchars($building['main_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($building['building_name']); ?>"
                                         class="building-image"
                                         loading="lazy">
                                    <div class="building-info">
                                        <h3 class="building-name"><?php echo htmlspecialchars($building['building_name']); ?></h3>
                                        <p class="building-category"><?php echo ucfirst(htmlspecialchars($building['category'])); ?></p>
                                        <p class="building-address">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?php echo htmlspecialchars($building['address']); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </aside>

                <!-- Map Display -->
                <div class="map-wrapper">
                    <div id="map" class="map-display"></div>
                </div>
            </div>
        </section>

        <!-- Building Detail Modal -->
        <div id="buildingModal" class="modal">
            <div class="modal-content">
                <button class="close-modal" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
                <div id="buildingDetail"></div>
            </div>
        </div>
    </main>
    
<!-- At the end of petawisata.php, before closing body tag -->
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

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>

    <!-- Navigation Scripts -->
    <script src="../assets/js/navbar/primary-nav.js" defer></script>
    <script src="../assets/js/navbar/mobile-nav.js" defer></script>

    <!-- Map Initialization -->
    <script>
        // Initialize map
        const map = L.map('map', {
            center: [-7.7956, 110.3695], // Yogyakarta center
            zoom: 13,
            zoomControl: false // We'll add custom zoom control
        });

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Add zoom control to top-right
        L.control.zoom({
            position: 'topright'
        }).addTo(map);

        // Store markers
        const markers = {};
        const buildings = <?php echo json_encode($buildings); ?>;

        // Custom icon
        const customIcon = L.icon({
            iconUrl: '../assets/images/marker.png',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        // Add markers for each building
        buildings.forEach(building => {
            const marker = L.marker([building.latitude, building.longitude], {
                icon: customIcon
            }).bindPopup(`
                <div class="popup-content">
                    <img src="../uploads/main/${building.main_image}" 
                         alt="${building.building_name}"
                         class="popup-image">
                    <h3 class="popup-title">${building.building_name}</h3>
                    <span class="popup-category">${ucfirst(building.category)}</span>
                    <p class="popup-address">
                        <i class="fas fa-map-marker-alt"></i>
                        ${building.address}
                    </p>
                    <div class="popup-actions">
                        <button onclick="showBuildingDetail(${building.building_id})" 
                                class="popup-button primary">
                            <i class="fas fa-info-circle"></i>
                            Lihat Detail
                        </button>
                        <a href="building-detail.php?id=${building.building_id}" 
                           class="popup-button secondary">
                            <i class="fas fa-external-link-alt"></i>
                            Info Lengkap
                        </a>
                    </div>
                </div>
            `, {
                maxWidth: 300,
                className: 'custom-popup'
            });
            
            markers[building.building_id] = marker;
            marker.addTo(map);
        });

        // Search functionality
        document.getElementById('searchBuilding').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            filterBuildings(searchTerm, document.getElementById('categoryFilter').value);
        });

        // Category filter
        document.getElementById('categoryFilter').addEventListener('change', function(e) {
            const searchTerm = document.getElementById('searchBuilding').value.toLowerCase();
            filterBuildings(searchTerm, e.target.value);
        });

        // Filter buildings function
        function filterBuildings(search, category) {
            document.querySelectorAll('.building-item').forEach(item => {
                const name = item.querySelector('.building-name').textContent.toLowerCase();
                const itemCategory = item.dataset.category;
                
                const matchesSearch = name.includes(search);
                const matchesCategory = !category || itemCategory === category;
                
                item.style.display = matchesSearch && matchesCategory ? 'flex' : 'none';
                
                // Show/hide markers
                const marker = markers[item.dataset.id];
                if (matchesSearch && matchesCategory) {
                    marker.addTo(map);
                } else {
                    marker.remove();
                }
            });
        }

        // Building list item click handler
        document.querySelectorAll('.building-item').forEach(item => {
            item.addEventListener('click', function() {
                const id = this.dataset.id;
                const building = buildings.find(b => b.building_id === id);
                if (building) {
                    map.setView([building.latitude, building.longitude], 16);
                    markers[id].openPopup();
                }
            });
        });

        // Show building detail
        function showBuildingDetail(buildingId) {
            const building = buildings.find(b => b.building_id == buildingId);
            if (building) {
                document.getElementById('buildingDetail').innerHTML = `
                    <div class="building-detail">
                        <div class="detail-image-container">
                            <img src="../uploads/main/${building.main_image}" 
                                 alt="${building.building_name}"
                                 class="detail-image">
                            <span class="detail-category">${ucfirst(building.category)}</span>
                        </div>
                        <div class="detail-content">
                            <h2 class="detail-title">${building.building_name}</h2>
                            <p class="detail-address">
                                <i class="fas fa-map-marker-alt"></i>
                                ${building.address}
                            </p>
                            <div class="detail-description">
                                ${building.description || 'Tidak ada deskripsi tersedia.'}
                            </div>
                            <a href="building-detail.php?id=${building.building_id}" 
                               class="detail-link">
                                <i class="fas fa-external-link-alt"></i>
                                Lihat Informasi Lengkap
                            </a>
                        </div>
                    </div>
                `;
                document.getElementById('buildingModal').classList.add('active');
            }
        }

        // Helper function
        function ucfirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // Close modal handlers
        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('buildingModal').classList.remove('active');
        });

        window.addEventListener('click', function(e) {
            const modal = document.getElementById('buildingModal');
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });

        // Escape key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('buildingModal').classList.remove('active');
            }
        });

            // Handle map centering from URL parameters
    window.addEventListener('load', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const lat = urlParams.get('lat');
        const lng = urlParams.get('lng');
        const zoom = urlParams.get('zoom');
        const shouldCenter = urlParams.get('center');

        if (lat && lng && shouldCenter === 'true') {
            map.setView([parseFloat(lat), parseFloat(lng)], zoom ? parseInt(zoom) : 16);
            
            // Find and open the corresponding marker's popup
            Object.values(markers).forEach(marker => {
                const markerLatLng = marker.getLatLng();
                if (markerLatLng.lat === parseFloat(lat) && markerLatLng.lng === parseFloat(lng)) {
                    marker.openPopup();
                }
            });

            // Scroll to map if there's a hash
            if (window.location.hash === '#map') {
                document.querySelector('.map-wrapper').scrollIntoView({ 
                    behavior: 'smooth'
                });
            }
        }
    });
    </script>
</body>
</html>