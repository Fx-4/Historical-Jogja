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

// Get location data
$sql_location = "SELECT * FROM tourist_map WHERE building_id = ?";
$stmt_location = $conn->prepare($sql_location);
$stmt_location->bind_param("i", $building_id);
$stmt_location->execute();
$location = $stmt_location->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Start transaction
        $conn->begin_transaction();

        // Handle main image upload if new image is provided
        $mainImagePath = $building['main_image']; // Keep existing image by default
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === 0) {
            $mainImage = $_FILES['main_image'];
            // Delete old image
            if ($building['main_image'] && file_exists("../uploads/main/".$building['main_image'])) {
                unlink("../uploads/main/".$building['main_image']);
            }
            $mainImagePath = uploadImage($mainImage, '../uploads/main/');
        }

        // Update building information
        $sql = "UPDATE historical_buildings SET 
                building_name = ?,
                category = ?,
                construction_year = ?,
                status = ?,
                complete_history = ?,
                main_image = ?,
                important_figures = ?,
                cultural_value = ?,
                address = ?,
                open_hours = ?,
                ticket_price = ?,
                contact = ?,
                updated_at = NOW()
                WHERE building_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssssi",
            $_POST['building_name'],
            $_POST['category'],
            $_POST['construction_year'],
            $_POST['status'],
            $_POST['complete_history'],
            $mainImagePath,
            $_POST['important_figures'],
            $_POST['cultural_value'],
            $_POST['address'],
            $_POST['open_hours'],
            $_POST['ticket_price'],
            $_POST['contact'],
            $building_id
        );
        $stmt->execute();

        // Update location information
        if ($location) {
            $sql_location = "UPDATE tourist_map SET 
                            location_name = ?,
                            latitude = ?,
                            longitude = ?,
                            description = ?
                            WHERE building_id = ?";
        } else {
            $sql_location = "INSERT INTO tourist_map 
                            (location_name, latitude, longitude, description, building_id) 
                            VALUES (?, ?, ?, ?, ?)";
        }

        $stmt_location = $conn->prepare($sql_location);
        $stmt_location->bind_param("sddsi",
            $_POST['location_name'],
            $_POST['latitude'],
            $_POST['longitude'],
            $_POST['description'],
            $building_id
        );
        $stmt_location->execute();

        // Commit transaction
        $conn->commit();

        $_SESSION['success_message'] = "Data bangunan berhasil diperbarui!";
        header("Location: view-building.php?id=" . $building_id);
        exit;

    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $_SESSION['error_message'] = "Terjadi kesalahan: " . $e->getMessage();
    }
}

// Helper function to handle file uploads
function uploadImage($file, $targetDir) {
    $fileName = uniqid() . '_' . basename($file['name']);
    $targetPath = $targetDir . $fileName;
    
    // Check file size (max 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new Exception("File terlalu besar (maksimal 2MB)");
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/png'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Format file tidak didukung");
    }
    
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception("Gagal mengupload file");
    }
    
    return $fileName;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bangunan - <?php echo htmlspecialchars($building['building_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/edit-building-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Leaflet CSS dan JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
            <!-- Back Button -->
            <div class="header-actions">
                <a href="view-building.php?id=<?php echo $building_id; ?>" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="edit-form">
                <h1>Edit Bangunan Bersejarah</h1>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php 
                            echo $_SESSION['error_message'];
                            unset($_SESSION['error_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <!-- Main Image Section -->
                    <section class="form-section">
                        <h2>Foto Utama</h2>
                        <div class="current-image">
                            <img src="../uploads/main/<?php echo htmlspecialchars($building['main_image']); ?>" 
                                 alt="Current main image">
                            <p>Gambar saat ini</p>
                        </div>
                        <div class="form-group">
                            <label>Ganti Foto Utama (Opsional)</label>
                            <input type="file" name="main_image" accept="image/*">
                            <small>Format: JPG, PNG. Maksimal 2MB</small>
                        </div>
                    </section>

                    <!-- Basic Information -->
                    <section class="form-section">
                        <h2>Informasi Dasar</h2>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Nama Bangunan<span class="required">*</span></label>
                                <input type="text" 
                                       name="building_name" 
                                       required 
                                       value="<?php echo htmlspecialchars($building['building_name']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Kategori<span class="required">*</span></label>
                                <select name="category" required>
                                    <option value="candi" <?php echo $building['category'] === 'candi' ? 'selected' : ''; ?>>Candi</option>
                                    <option value="istana" <?php echo $building['category'] === 'istana' ? 'selected' : ''; ?>>Istana</option>
                                    <option value="benteng" <?php echo $building['category'] === 'benteng' ? 'selected' : ''; ?>>Benteng</option>
                                    <option value="monumen" <?php echo $building['category'] === 'monumen' ? 'selected' : ''; ?>>Monumen</option>
                                    <option value="masjid" <?php echo $building['category'] === 'masjid' ? 'selected' : ''; ?>>Masjid</option>
                                    <option value="museum" <?php echo $building['category'] === 'museum' ? 'selected' : ''; ?>>Museum</option>
                                    <option value="makam" <?php echo $building['category'] === 'makam' ? 'selected' : ''; ?>>Makam</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tahun Pembangunan</label>
                                <input type="text" 
                                       name="construction_year" 
                                       value="<?php echo htmlspecialchars($building['construction_year']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="aktif" <?php echo isset($building['status']) && $building['status'] === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="tidak_aktif" <?php echo isset($building['status']) && $building['status'] === 'tidak_aktif' ? 'selected' : ''; ?>>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- Historical Information -->
                    <section class="form-section">
                        <h2>Informasi Sejarah</h2>
                        <div class="form-group">
                            <label>Sejarah Lengkap<span class="required">*</span></label>
                            <textarea name="complete_history" rows="6" required><?php echo htmlspecialchars($building['complete_history']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Tokoh Penting</label>
                            <textarea name="important_figures" rows="4"><?php echo htmlspecialchars($building['important_figures']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Nilai Budaya</label>
                            <textarea name="cultural_value" rows="4"><?php echo htmlspecialchars($building['cultural_value']); ?></textarea>
                        </div>
                    </section>

                    <!-- Practical Information -->
                    <section class="form-section">
                        <h2>Informasi Praktis</h2>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label>Alamat<span class="required">*</span></label>
                                <textarea name="address" rows="3" required><?php echo htmlspecialchars($building['address']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Jam Operasional</label>
                                <textarea name="open_hours" rows="3"><?php echo htmlspecialchars($building['open_hours']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Harga Tiket</label>
                                <input type="text" 
                                       name="ticket_price" 
                                       value="<?php echo htmlspecialchars($building['ticket_price']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Kontak</label>
                                <input type="text" 
                                       name="contact" 
                                       value="<?php echo htmlspecialchars($building['contact']); ?>">
                            </div>
                        </div>
                    </section>

                    <!-- Location Information -->
                    <section class="form-section">
                        <h2>Informasi Lokasi</h2>
                        <div class="map-editor">
                            <div id="map" class="map-container"></div>
                            <div class="location-inputs">
                                <div class="form-group">
                                    <label>Nama Lokasi</label>
                                    <input type="text" 
                                        name="location_name" 
                                        id="location_name"
                                        value="<?php echo isset($location['location_name']) ? htmlspecialchars($location['location_name']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Latitude</label>
                                    <input type="number" 
                                        step="any" 
                                        name="latitude" 
                                        id="latitude"
                                        value="<?php echo isset($location['latitude']) ? htmlspecialchars($location['latitude']) : ''; ?>"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label>Longitude</label>
                                    <input type="number" 
                                        step="any" 
                                        name="longitude" 
                                        id="longitude"
                                        value="<?php echo isset($location['longitude']) ? htmlspecialchars($location['longitude']) : ''; ?>"
                                        readonly>
                                </div>
                                <div class="form-group full-width">
                                    <label>Deskripsi Lokasi</label>
                                    <textarea name="description" rows="3"><?php echo isset($location['description']) ? htmlspecialchars($location['description']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Submit Button -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
let map;
let marker;

function initMap() {
    // Default ke Jogja atau lokasi yang sudah ada
    const defaultLat = <?php echo isset($location['latitude']) ? $location['latitude'] : -7.797068 ?>;
    const defaultLng = <?php echo isset($location['longitude']) ? $location['longitude'] : 110.370529 ?>;

    // Inisialisasi peta
    map = L.map('map').setView([defaultLat, defaultLng], 13);

    // Tambahkan layer OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Tambahkan marker
    marker = L.marker([defaultLat, defaultLng], {
        draggable: true
    }).addTo(map);

    // Update koordinat saat marker dipindah
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        document.getElementById('latitude').value = position.lat;
        document.getElementById('longitude').value = position.lng;
    });

    // Klik pada map untuk memindahkan marker
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
    });
}

// Initialize map when page loads
window.onload = initMap;
</script>