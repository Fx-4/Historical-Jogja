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

// Pagination settings
$records_per_page = 12;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total records for pagination
$sql_count = "SELECT COUNT(*) as total FROM gallery";
$total_records = $conn->query($sql_count)->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get gallery images with building information
$sql = "SELECT g.*, hb.building_name 
        FROM gallery g
        LEFT JOIN historical_buildings hb ON g.building_id = hb.building_id
        ORDER BY g.created_at DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $records_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Get all buildings for the upload form
$sql_buildings = "SELECT building_id, building_name FROM historical_buildings ORDER BY building_name";
$buildings = $conn->query($sql_buildings);

// Get list of categories with images
$sql_categories = "SELECT DISTINCT hb.category 
                  FROM gallery g
                  JOIN historical_buildings hb ON g.building_id = hb.building_id
                  ORDER BY hb.category";
$categories = $conn->query($sql_categories);

// Get images grouped by category
$sql_images = "SELECT g.*, hb.building_name, hb.category
               FROM gallery g
               JOIN historical_buildings hb ON g.building_id = hb.building_id
               ORDER BY hb.category, g.created_at DESC";
$result = $conn->query($sql_images);

// Group images by category
$images_by_category = [];
while ($row = $result->fetch_assoc()) {
    $images_by_category[$row['category']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management - Historical Buildings</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/gallery-styles.css">
    <link rel="stylesheet" href="../assets/css/shared-navbar.css">
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
                <a href="gallery.php" class="nav-link active">Galeri</a>
                <a href="tourist-map.php" class="nav-link">Peta Wisata</a>
                <a href="messages.php" class="nav-link">Pesan</a>
                <a href="logout.php" class="nav-link logout">Logout</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
    <div class="content-wrapper">
        <div class="page-header">
            <h1>Gallery Management</h1>
            <button class="btn btn-primary" onclick="openUploadModal()">
                <i class="fas fa-upload"></i> Upload Images
            </button>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Category Tabs -->
        <div class="category-tabs">
            <?php foreach ($images_by_category as $category => $images): ?>
                <button class="category-tab" onclick="showCategory('<?php echo $category; ?>')">
                    <?php echo ucfirst($category); ?>
                </button>
            <?php endforeach; ?>
        </div>

<!-- Gallery Sections -->
<?php foreach ($images_by_category as $category => $images): ?>
    <div class="category-section" id="category-<?php echo $category; ?>">
        <h2><?php echo ucfirst($category); ?></h2>
        <div class="gallery-grid">
            <?php foreach ($images as $image): ?>
                <div class="gallery-item" data-id="<?php echo $image['gallery_id']; ?>">
                    <div class="image-container">
                        <img src="../uploads/gallery/<?php echo htmlspecialchars($image['image_path']); ?>"
                             alt="<?php echo htmlspecialchars($image['caption']); ?>"
                             onclick="openImageModal(this.src, '<?php echo htmlspecialchars($image['caption']); ?>')">
                        <div class="image-overlay">
                        <div class="image-actions">
                                <button class="btn-icon" onclick="editImage(<?php echo $image['gallery_id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon" onclick="deleteImage(<?php echo $image['gallery_id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="image-info">
                        <p class="image-caption"><?php echo htmlspecialchars($image['caption']); ?></p>
                        <p class="building-name"><?php echo htmlspecialchars($image['building_name']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>


    <!-- Upload Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Upload Images</h2>
                <button class="close-modal" onclick="closeUploadModal()">&times;</button>
            </div>
                    <form action="gallery-upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Select Building</label>
                <select name="building_id" required>
                    <option value="">Choose a building...</option>
                    <?php while ($building = $buildings->fetch_assoc()): ?>
                        <option value="<?php echo $building['building_id']; ?>">
                            <?php echo htmlspecialchars($building['building_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Upload Images (Max 10)</label>
                <input type="file" name="images[]" multiple accept="image/*" required>
                <small>Format: JPG, PNG. Maximum size: 2MB per image</small>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeUploadModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
        </div>
    </div>

    <!-- Image View Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeImageModal()">&times;</button>
            <img id="modalImage" src="" alt="">
            <p id="modalCaption" class="modal-caption"></p>
        </div>
    </div>

    <script>
        // Modal functions
        function openUploadModal() {
            document.getElementById('uploadModal').style.display = 'flex';
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').style.display = 'none';
        }

        function openImageModal(src, caption) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const modalCaption = document.getElementById('modalCaption');
            
            modal.style.display = 'flex';
            modalImg.src = src;
            modalCaption.textContent = caption;
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        // Image management functions
        function editImage(id) {
        window.location.href = `gallery-edit.php?id=${id}`;
        }

        // Ganti fungsi deleteImage di gallery.php
        function deleteImage(id) {
            if (confirm('Are you sure you want to delete this image?')) {
                window.location.href = `gallery-processes.php?action=delete&id=${id}`;
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

                // Show default category on load
        document.addEventListener('DOMContentLoaded', function() {
            const firstCategory = document.querySelector('.category-section');
            if (firstCategory) {
                firstCategory.classList.add('active');
                const categoryId = firstCategory.id.split('-')[1];
                document.querySelector(`.category-tab[onclick*="${categoryId}"]`).classList.add('active');
            }
        });

        function showCategory(category) {
            // Hide all sections
            document.querySelectorAll('.category-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById('category-' + category).classList.add('active');
            
            // Update tab styling
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
