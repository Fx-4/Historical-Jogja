<?php
require_once '../config/db-connect.php';

// Inisialisasi database
$db = new Database();
$conn = $db->connect();

// Ambil semua data bangunan yang sudah dipublish
$sql = "SELECT building_id, building_name, category, main_image, status, created_at 
        FROM historical_buildings 
        WHERE draft_status = 'published' 
        ORDER BY created_at DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Bangunan - Historical Jogja Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-preview-dashboard.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-left">
            <a href="#" class="nav-brand">Historical Jogja Admin</a>
            <button id="menuToggle" class="menu-toggle">
                <span></span>
            </button>
        </div>
        <div class="nav-links">
            <!-- Bangunan Bersejarah Management -->
            <a href="admin-dashboard.php" class="nav-link">Input Bangunan</a>
            <a href="list-building.php" class="nav-link">List Bangunan</a>
            <a href="gallery.php" class="nav-link">Galeri</a>
            <a href="tourist-map.php" class="nav-link">Peta Wisata</a>
            <a href="messages.php" class="nav-link">Pesan</a>
            <a href="adminlogin.php" class="nav-link logout">Logout</a>
        </div>
    </div>
</nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-wrapper">
            <header class="dashboard-header">
                <h1>Preview Bangunan Bersejarah</h1>
                <div class="header-actions">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Cari bangunan...">
                        <button class="search-btn">🔍</button>
                    </div>
                    <select id="categoryFilter" class="filter-select">
                        <option value="">Semua Kategori</option>
                        <option value="candi">Candi</option>
                        <option value="istana">Istana</option>
                        <option value="benteng">Benteng</option>
                        <option value="monumen">Monumen</option>
                        <option value="masjid">Masjid</option>
                        <option value="museum">Museum</option>
                        <option value="makam">Makam</option>
                    </select>
                </div>
            </header>

            <div class="buildings-grid">
                <?php 
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                ?>
                <div class="building-card">
                    <div class="building-image">
                        <img src="../<?php echo htmlspecialchars($row['main_image']); ?>" 
                             alt="<?php echo htmlspecialchars($row['building_name']); ?>">
                        <span class="building-status <?php echo $row['status'] == 'aktif' ? 'active' : 'inactive'; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </div>
                    <div class="building-info">
                        <h3><?php echo htmlspecialchars($row['building_name']); ?></h3>
                        <span class="building-category">
                            <?php echo ucfirst($row['category']); ?>
                        </span>
                        <div class="building-meta">
                            <span class="building-date">
                                <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                    <div class="building-actions">
                        <a href="view-building.php?id=<?php echo $row['building_id']; ?>" 
                           class="btn btn-view">Lihat</a>
                        <a href="edit-building.php?id=<?php echo $row['building_id']; ?>" 
                           class="btn btn-edit">Edit</a>
                        <button class="btn btn-delete" 
                                onclick="deleteBuilding(<?php echo $row['building_id']; ?>)">
                            Hapus
                        </button>
                    </div>
                </div>
                <?php 
                    }
                } else {
                ?>
                <div class="no-data">
                    <div class="no-data-message">
                        <span>🏛️</span>
                        <h3>Belum ada bangunan</h3>
                        <p>Mulai tambahkan bangunan bersejarah pertama Anda</p>
                        <a href="admin-dashboard.php" class="btn btn-primary">Tambah Bangunan</a>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.building-card');
            
            cards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const category = card.querySelector('.building-category').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || category.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Category filter
        document.getElementById('categoryFilter').addEventListener('change', function(e) {
            const category = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.building-card');
            
            cards.forEach(card => {
                const cardCategory = card.querySelector('.building-category')
                                      .textContent.toLowerCase();
                
                if (!category || cardCategory === category) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Delete confirmation
        function deleteBuilding(id) {
            if (confirm('Apakah Anda yakin ingin menghapus bangunan ini?')) {
                window.location.href = `delete-building.php?id=${id}`;
            }
        }
    </script>
</body>
</html>