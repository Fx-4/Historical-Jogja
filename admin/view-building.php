<?php
session_start();
require_once '../config/db-connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Check if building ID is provided
if (!isset($_GET['id'])) {
    header("Location: list-building.php");
    exit;
}

$building_id = intval($_GET['id']);

// Database connection
$db = new Database();
$conn = $db->connect();

// Get building details
$sql = "SELECT * FROM historical_buildings WHERE building_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $building_id);
$stmt->execute();
$building = $stmt->get_result()->fetch_assoc();

if (!$building) {
    header("Location: list-building.php");
    exit;
}

// Get gallery images
$sql_gallery = "SELECT * FROM gallery WHERE building_id = ?";
$stmt_gallery = $conn->prepare($sql_gallery);
$stmt_gallery->bind_param("i", $building_id);
$stmt_gallery->execute();
$gallery = $stmt_gallery->get_result();

// Get location data
$sql_location = "SELECT * FROM tourist_map WHERE building_id = ?";
$stmt_location = $conn->prepare($sql_location);
$stmt_location->bind_param("i", $building_id);
$stmt_location->execute();
$location = $stmt_location->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Bangunan - <?php echo htmlspecialchars($building['building_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/view-building-styles.css">
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
                <a href="list-building.php" class="nav-link active">List Bangunan</a>
                <a href="gallery.php" class="nav-link">Galeri</a>
                <a href="tourist-map.php" class="nav-link">Peta Wisata</a>
                <a href="messages.php" class="nav-link">Pesan</a>
                <a href="logout.php" class="nav-link logout">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-wrapper">
            <!-- Back Button and Actions -->
            <div class="header-actions">
                <a href="list-building.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <div class="action-buttons">
                    <a href="edit-building.php?id=<?php echo $building_id; ?>" class="btn btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button onclick="confirmDelete(<?php echo $building_id; ?>)" class="btn btn-delete">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>

            <!-- Building Details -->
            <div class="building-details">
                <div class="main-image-section">
                    <img src="../uploads/main/<?php echo htmlspecialchars($building['main_image']); ?>" 
                         alt="<?php echo htmlspecialchars($building['building_name']); ?>"
                         class="main-image">
                </div>

                <div class="detail-sections">
                    <!-- Basic Information -->
                    <section class="detail-section">
                        <h2>Informasi Dasar</h2>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Nama Bangunan:</label>
                                <span><?php echo htmlspecialchars($building['building_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Kategori:</label>
                                <span><?php echo htmlspecialchars($building['category']); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Tahun Pembangunan:</label>
                                <span><?php echo htmlspecialchars($building['construction_year']); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Status:</label>
                                <span class="status-badge <?php echo isset($building['status']) && $building['status'] === 'aktif' ? 'status-active' : 'status-inactive'; ?>">
    <?php echo isset($building['status']) ? ucfirst($building['status']) : 'Tidak Ada Status'; ?>
</span>
                            </div>
                        </div>
                    </section>

                    <!-- Historical Information -->
                    <section class="detail-section">
                        <h2>Informasi Sejarah</h2>
                        <div class="info-text">
                            <h3>Sejarah Lengkap</h3>
                            <p><?php echo nl2br(htmlspecialchars($building['complete_history'])); ?></p>
                            
                            <h3>Tokoh Penting</h3>
                            <p><?php echo nl2br(htmlspecialchars($building['important_figures'])); ?></p>
                            
                            <h3>Nilai Budaya</h3>
                            <p><?php echo nl2br(htmlspecialchars($building['cultural_value'])); ?></p>
                        </div>
                    </section>

                    <!-- Practical Information -->
                    <section class="detail-section">
                        <h2>Informasi Praktis</h2>
                        <div class="info-grid">
                            <div class="info-item full-width">
                                <label>Alamat:</label>
                                <span><?php echo nl2br(htmlspecialchars($building['address'])); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Jam Operasional:</label>
                                <span><?php echo nl2br(htmlspecialchars($building['open_hours'])); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Harga Tiket:</label>
                                <span><?php echo htmlspecialchars($building['ticket_price']); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Kontak:</label>
                                <span><?php echo htmlspecialchars($building['contact']); ?></span>
                            </div>
                        </div>
                    </section>

                    <!-- Gallery -->
                    <section class="detail-section">
                        <h2>Galeri Foto</h2>
                        <div class="gallery-grid">
                            <?php while ($image = $gallery->fetch_assoc()): ?>
                            <div class="gallery-item">
                                <img src="../uploads/gallery/<?php echo htmlspecialchars($image['image_path']); ?>"
                                     alt="Gallery image"
                                     onclick="showImage(this.src)">
                                <?php if ($image['caption']): ?>
                                <div class="image-caption"><?php echo htmlspecialchars($image['caption']); ?></div>
                                <?php endif; ?>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </section>

                    <!-- Location Map -->
                    <?php if ($location && isset($location['latitude']) && isset($location['longitude'])): ?>
                    <section class="detail-section">
                        <h2>Peta Lokasi</h2>
                        <div class="map-container">
                            <iframe
                                width="100%"
                                height="450"
                                frameborder="0"
                                style="border:0"
                                src="https://maps.google.com/maps?q=<?php echo $location['latitude']; ?>,<?php echo $location['longitude']; ?>&hl=id&z=14&output=embed">
                            </iframe>
                        </div>
                        <?php if(isset($location['description']) && !empty($location['description'])): ?>
                        <div class="location-description">
                            <p><?php echo htmlspecialchars($location['description']); ?></p>
                        </div>
                        <?php endif; ?>
                    </section>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <span class="modal-close">&times;</span>
        <img id="modalImage" class="modal-content" src="" alt="Enlarged image">
    </div>

    <script>
        // Image modal functionality
        function showImage(src) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = "block";
            modalImg.src = src;
        }

        const modal = document.getElementById('imageModal');
        const closeBtn = document.getElementsByClassName('modal-close')[0];

        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        modal.onclick = function(e) {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        }

        // Delete confirmation
        function confirmDelete(buildingId) {
            if (confirm('Apakah Anda yakin ingin menghapus bangunan ini?')) {
                window.location.href = `delete-building.php?id=${buildingId}`;
            }
        }
    </script>
</body>
</html>
