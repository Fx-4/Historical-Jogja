<?php
session_start();
require_once '../config/db-connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Database connection
$db = new Database();
$conn = $db->connect();

// Get all buildings with their locations
$sql = "SELECT hb.building_id, hb.building_name, hb.category, hb.status, 
               hb.main_image, hb.address, tm.latitude, tm.longitude, 
               tm.location_name, tm.description
        FROM historical_buildings hb
        LEFT JOIN tourist_map tm ON hb.building_id = tm.building_id
        WHERE tm.latitude IS NOT NULL AND tm.longitude IS NOT NULL
        ORDER BY hb.building_name";

$result = $conn->query($sql);
$buildings = [];
while ($row = $result->fetch_assoc()) {
    $buildings[] = $row;
}

// Get unique categories for filter
$sql_categories = "SELECT DISTINCT category FROM historical_buildings ORDER BY category";
$categories = $conn->query($sql_categories);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Wisata - Historical Buildings</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/tourist-map-styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <a href="#" class="nav-brand">Historical Jogja Admin</a>
            </div>
            <div class="nav-links">
                <a href="admin-dashboard.php" class="nav-link">Input Bangunan</a>
                <a href="list-building.php" class="nav-link">List Bangunan</a>
                <a href="gallery.php" class="nav-link">Galeri</a>
                <a href="tourist-map.php" class="nav-link active">Peta Wisata</a>
                <a href="messages.php" class="nav-link">Pesan</a>
                <a href="logout.php" class="nav-link logout">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
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
                                 class="building-image">
                            <div class="building-info">
                                <h3 class="building-name"><?php echo htmlspecialchars($building['building_name']); ?></h3>
                                <p class="building-category"><?php echo ucfirst(htmlspecialchars($building['category'])); ?></p>
                                <p class="building-address"><?php echo htmlspecialchars($building['address']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>

        <!-- Map Container -->
        <div class="map-container">
            <div id="map"></div>
        </div>

        <!-- Building Detail Modal -->
        <div id="buildingModal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <div id="buildingDetail"></div>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize map
        const map = L.map('map').setView([-7.7956, 110.3695], 13); // Centered on Yogyakarta

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Store all markers
        const markers = {};
        const buildings = <?php echo json_encode($buildings); ?>;

        // Add markers for each building
        buildings.forEach(building => {
            const marker = L.marker([building.latitude, building.longitude])
                .bindPopup(`
                    <div class="marker-popup">
                        <h3>${building.building_name}</h3>
                        <p>${building.location_name}</p>
                        <button onclick="showBuildingDetail(${building.building_id})">
                            Lihat Detail
                        </button>
                    </div>
                `);
            
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

        // Building detail modal
        function showBuildingDetail(buildingId) {
            const building = buildings.find(b => b.building_id == buildingId);
            if (building) {
                document.getElementById('buildingDetail').innerHTML = `
                    <div class="building-detail">
                        <img src="../uploads/main/${building.main_image}" 
                             alt="${building.building_name}"
                             class="detail-image">
                        <h2>${building.building_name}</h2>
                        <p class="category">${ucfirst(building.category)}</p>
                        <p class="address">${building.address}</p>
                        <p class="description">${building.description || 'Tidak ada deskripsi'}</p>
                        <a href="view-building.php?id=${building.building_id}" 
                           class="view-more-btn">Lihat Detail Lengkap</a>
                    </div>
                `;
                document.getElementById('buildingModal').style.display = 'block';
            }
        }

        // Helper function
        function ucfirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // Close modal
        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('buildingModal').style.display = 'none';
        });

        window.addEventListener('click', function(e) {
            const modal = document.getElementById('buildingModal');
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });

        // Building detail template
function getBuildingDetailTemplate(building) {
    return `
        <div class="building-detail">
            <div class="detail-header">
                <div class="detail-image-container">
                    <img src="../uploads/main/${building.main_image}" 
                         alt="${building.building_name}"
                         class="detail-image">
                    <span class="detail-status">${building.status === 'aktif' ? 'Aktif' : 'Tidak Aktif'}</span>
                </div>
                <div class="detail-title">
                    <h2>${building.building_name}</h2>
                    <span class="detail-category">${ucfirst(building.category)}</span>
                </div>
            </div>

            <div class="detail-info">
                <div class="info-item">
                    <span class="info-label">Lokasi</span>
                    <span class="info-value">${building.location_name}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Alamat</span>
                    <span class="info-value">${building.address}</span>
                </div>
                ${building.contact ? `
                <div class="info-item">
                    <span class="info-label">Kontak</span>
                    <span class="info-value">${building.contact}</span>
                </div>
                ` : ''}
                ${building.open_hours ? `
                <div class="info-item">
                    <span class="info-label">Jam Operasional</span>
                    <span class="info-value">${building.open_hours}</span>
                </div>
                ` : ''}
            </div>

            ${building.description ? `
            <div class="detail-description">
                <h3>Deskripsi</h3>
                <p>${building.description}</p>
            </div>
            ` : ''}

            <div class="detail-actions">
                <a href="view-building.php?id=${building.building_id}" 
                   class="action-button primary">
                    <i class="fas fa-eye"></i>
                    Lihat Detail Lengkap
                </a>
                <a href="edit-building.php?id=${building.building_id}" 
                   class="action-button secondary">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>
            </div>
        </div>
    `;
}

// Marker popup template
function getMarkerPopupTemplate(building) {
    return `
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
                    Lihat Detail
                </button>
                <a href="view-building.php?id=${building.building_id}" 
                   class="popup-button secondary">
                    Info Lengkap
                </a>
            </div>
        </div>
    `;
}

// Initialize map with custom controls
function initializeMap() {
    const map = L.map('map').setView([-7.7956, 110.3695], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add custom controls
    const controlsDiv = L.control({position: 'topright'});
    controlsDiv.onAdd = function(map) {
        const div = L.DomUtil.create('div', 'map-controls');
        div.innerHTML = `
            <button class="map-control-button" onclick="map.zoomIn()">
                <i class="fas fa-plus"></i>
            </button>
            <button class="map-control-button" onclick="map.zoomOut()">
                <i class="fas fa-minus"></i>
            </button>
            <button class="map-control-button" onclick="resetMapView()">
                <i class="fas fa-home"></i>
            </button>
        `;
        return div;
    };
    controlsDiv.addTo(map);

    return map;
}

// Helper functions
function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function resetMapView() {
    map.setView([-7.7956, 110.3695], 13);
}
    </script>
</body>
</html>
