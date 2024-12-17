<?php
require_once '../config/db-connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historical Jogja Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="../assets/css/shared-navbar.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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
            <a href="admin-dashboard.php" class="nav-link <?php echo ($current_page == 'admin-dashboard.php') ? 'active' : ''; ?>">Input Bangunan</a>
            <a href="list-building.php" class="nav-link <?php echo ($current_page == 'list-building.php') ? 'active' : ''; ?>">List Bangunan</a>
            <a href="gallery.php" class="nav-link <?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>">Galeri</a>
            <a href="tourist-map.php" class="nav-link <?php echo ($current_page == 'tourist-map.php') ? 'active' : ''; ?>">Peta Wisata</a>
            <a href="messages.php" class="nav-link <?php echo ($current_page == 'messages.php') ? 'active' : ''; ?>">Pesan</a>
            <a href="logout.php" class="nav-link logout">Logout</a>
        </div>
    </div>
</nav>

<!-- Add this after navbar -->
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php 
        echo $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?php 
        echo $_SESSION['success_message'];
        unset($_SESSION['success_message']);
        ?>
    </div>
<?php endif; ?>

    <!-- Sidebar -->
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <ul>
                <li><a href="#foto-utama" class="active">Foto Utama</a></li>
                <li><a href="#informasi-dasar">Informasi Dasar</a></li>
                <li><a href="#informasi-sejarah">Informasi Sejarah</a></li>
                <li><a href="#informasi-praktis">Informasi Praktis</a></li>
                <li><a href="#galeri">Galeri Foto</a></li>
                <li><a href="#peta">Peta Lokasi</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-wrapper">
            <h1 class="page-title">Input Bangunan Bersejarah</h1>
            
            <form action="save-building.php" method="POST" enctype="multipart/form-data">
                <!-- Foto Utama Section -->
                <section id="foto-utama" class="content-section">
                    <h2>Foto Utama</h2>
                    <div class="form-group">
                        <div class="upload-area">
                            <input type="file" id="mainImage" name="main_image" accept="image/*" required>
                            <label for="mainImage">
                                <div class="upload-placeholder">
                                    <span class="upload-icon">📸</span>
                                    <span class="upload-text">Klik untuk upload foto utama</span>
                                    <span class="upload-info">Format: JPG, PNG. Maksimal 2MB</span>
                                </div>
                            </label>
                            <div id="imagePreview" class="image-preview"></div>
                        </div>
                    </div>
                </section>

                <!-- Previous sections remain unchanged until peta section -->
                
                <!-- Informasi Dasar Section -->
                <section id="informasi-dasar" class="content-section">
                    <h2>Informasi Dasar</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="buildingName">Nama Bangunan<span class="required">*</span></label>
                            <input type="text" id="buildingName" name="building_name" required>
                        </div>
                        <div class="form-group">
                            <label for="category">Kategori<span class="required">*</span></label>
                            <select id="category" name="category" required>
                                <option value="">Pilih Kategori</option>
                                <option value="candi">Candi</option>
                                <option value="istana">Istana</option>
                                <option value="benteng">Benteng</option>
                                <option value="monumen">Monumen</option>
                                <option value="masjid">Masjid</option>
                                <option value="museum">Museum</option>
                                <option value="makam">Makam</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="constructionYear">Tahun Pembangunan</label>
                            <input type="text" id="constructionYear" name="construction_year">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </section>

                    <!-- Informasi Sejarah Section -->
    <section id="informasi-sejarah" class="content-section">
        <h2>Informasi Sejarah</h2>
        <div class="form-group">
            <label for="history">Sejarah Lengkap<span class="required">*</span></label>
            <textarea id="history" name="complete_history" rows="6" required placeholder="Masukkan sejarah lengkap bangunan"></textarea>
        </div>
        <div class="form-group">
            <label for="figures">Tokoh Penting</label>
            <textarea id="figures" name="important_figures" rows="4" 
                placeholder="Sebutkan tokoh-tokoh penting yang berkaitan dengan bangunan ini"></textarea>
        </div>
        <div class="form-group">
            <label for="cultural">Nilai Budaya</label>
            <textarea id="cultural" name="cultural_value" rows="4" 
                placeholder="Jelaskan nilai-nilai budaya yang terkandung dalam bangunan ini"></textarea>
        </div>
    </section>

    <!-- Informasi Praktis Section -->
    <section id="informasi-praktis" class="content-section">
        <h2>Informasi Praktis</h2>
        <div class="form-grid">
            <div class="form-group">
                <label for="address">Alamat Lengkap<span class="required">*</span></label>
                <textarea id="address" name="address" rows="3" required 
                    placeholder="Masukkan alamat lengkap bangunan"></textarea>
            </div>
            <div class="form-group">
                <label for="openHours">Jam Operasional</label>
                <textarea id="openHours" name="open_hours" rows="3" 
                    placeholder="Contoh: Senin-Jumat: 08.00-16.00 WIB"></textarea>
            </div>
            <div class="form-group">
                <label for="ticketPrice">Harga Tiket</label>
                <input type="text" id="ticketPrice" name="ticket_price" 
                    placeholder="Contoh: Dewasa Rp 15.000, Anak-anak Rp 8.000">
            </div>
            <div class="form-group">
                <label for="contact">Kontak</label>
                <input type="text" id="contact" name="contact" 
                    placeholder="Nomor telepon atau email">
            </div>
        </div>
    </section>

                <!-- Galeri Section with Caption -->
                <section id="galeri" class="content-section">
                <h2>Galeri Foto</h2>
                <div class="gallery-container">
                    <div class="form-group">
                        <div class="gallery-upload">
                            <input type="file" 
                                id="galleryImages" 
                                name="gallery_images[]" 
                                multiple 
                                accept="image/jpeg,image/png">
                                
                            <label for="galleryImages">
                                <div class="upload-placeholder">
                                    <span class="upload-icon">🖼️</span>
                                    <span class="upload-text">Upload foto galeri (maksimal 10 foto)</span>
                                    <span class="upload-info">Format: JPG, PNG. Maksimal 2MB per foto</span>
                                </div>
                            </label>
                            <div id="galleryPreview" class="gallery-preview"></div>
                        </div>
                    </div>
                </div>
            </section>

                <!-- Updated Peta Section with OpenStreetMap -->
                <section id="peta" class="content-section">
                    <h2>Peta Lokasi</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="location_name">Nama Lokasi<span class="required">*</span></label>
                            <input type="text" id="location_name" name="location_name" required>
                        </div>
                        <div class="form-group">
                            <label for="latitude">Latitude<span class="required">*</span></label>
                            <input type="number" step="any" id="latitude" name="latitude" required>
                        </div>
                        <div class="form-group">
                            <label for="longitude">Longitude<span class="required">*</span></label>
                            <input type="number" step="any" id="longitude" name="longitude" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi Lokasi</label>
                            <textarea id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <!-- Map Preview -->
                    <div id="map" style="height: 400px; margin-top: 20px;"></div>
                    <div class="map-help">
                        <small>Klik pada peta untuk menentukan lokasi. Koordinat akan terisi otomatis.</small>
                    </div>
                </section>

                <form action="save-building.php" method="POST" enctype="multipart/form-data" id="buildingForm">
    <!-- ... form content ... -->
    
    <!-- Form Actions -->
    <div class="form-actions">
        <button type="submit" name="submit" class="btn btn-primary">
            Publikasikan
        </button>
    </div>
</form>

    <!-- Overlay untuk mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize map
        const map = L.map('map').setView([-7.7956, 110.3695], 13); // Koordinat Jogja
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker;

        // Handle map click
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
            
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
        });

        // Gallery preview with captions
        document.getElementById('galleryImages').addEventListener('change', function(e) {
            const container = document.getElementById('gallery-previews');
            container.innerHTML = ''; // Clear previous previews
            
            const files = Array.from(this.files);
            
            if (files.length > 10) {
                alert('Maksimal 10 foto yang dapat diunggah');
                this.value = '';
                return;
            }
            
            files.forEach((file, index) => {
                if (file.size > 2 * 1024 * 1024) {
                    alert(`File ${file.name} terlalu besar! Maksimal 2MB`);
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'gallery-preview-item';
                    previewDiv.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <input type="text" name="captions[]" placeholder="Tambahkan caption" class="gallery-caption">
                        <button type="button" class="remove-image">×</button>
                    `;
                    container.appendChild(previewDiv);
                    
                    // Add remove functionality
                    previewDiv.querySelector('.remove-image').addEventListener('click', function() {
                        previewDiv.remove();
                    });
                }
                reader.readAsDataURL(file);
            });
        });

        function previewGallery(input) {
    const preview = document.getElementById('galleryPreview');
    console.log('Preview function called'); // Debug line
    console.log('Files:', input.files); // Debug line
    
    preview.innerHTML = '';

    if (input.files.length > 10) {
        alert('Maksimal 10 foto yang dapat diupload');
        input.value = '';
        return;
    }

    let totalSize = 0;
    Array.from(input.files).forEach(file => {
        totalSize += file.size;

        // Check file size
        if (file.size > 2 * 1024 * 1024) {
            alert(`File ${file.name} terlalu besar! Maksimal 2MB per file`);
            input.value = '';
            preview.innerHTML = '';
            return;
        }

        const reader = new FileReader();
        
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'preview-item';
            
            div.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <textarea 
                    name="gallery_captions[]" 
                    placeholder="Tambahkan caption..."
                    class="caption-input"
                    rows="2"
                ></textarea>
                <button type="button" 
                        class="remove-preview" 
                        onclick="removePreviewItem(this)"
                        title="Hapus foto">&times;</button>
            `;
            preview.appendChild(div);
        };

        reader.readAsDataURL(file);
    });

    // Check total size (max 20MB)
    if (totalSize > 20 * 1024 * 1024) {
        alert('Total ukuran file tidak boleh lebih dari 20MB');
        input.value = '';
        preview.innerHTML = '';
    }
}

function removePreviewItem(button) {
    const item = button.closest('.preview-item');
    item.style.opacity = '0';
    setTimeout(() => {
        item.remove();
    }, 200);
}

document.addEventListener('DOMContentLoaded', function() {
    const galleryInput = document.getElementById('galleryImages');
    if (galleryInput) {
        galleryInput.addEventListener('change', function() {
            previewGallery(this);
        });
    }
});


    </script>
</body>

</div>
</html>