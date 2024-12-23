<?php
session_start();
require_once '../config/db-connect.php';

if (!isset($_GET['id'])) {
    header("Location: gallery.php");
    exit;
}

$building_id = intval($_GET['id']);

$db = new Database();
$conn = $db->connect();

// Get building info
$sql = "SELECT b.building_name, b.category 
        FROM historical_buildings b 
        WHERE b.building_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $building_id);
$stmt->execute();
$building = $stmt->get_result()->fetch_assoc();

if (!$building) {
    header("Location: gallery.php");
    exit;
}

// Get gallery images
$sql = "SELECT image_path, caption 
        FROM gallery 
        WHERE building_id = ? 
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $building_id);
$stmt->execute();
$images = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri - <?php echo htmlspecialchars($building['building_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/gallery.css">
    
    <style>
        .gallery-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.95);
            z-index: 1000;
            overflow-y: auto;
        }

        .gallery-content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
            background: white;
            border-radius: 0.5rem;
        }

        .gallery-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 1rem;
        }

        .gallery-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
        }

        .close-button {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: #f3f4f6;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 1.5rem;
            transition: all 0.2s;
        }

        .close-button:hover {
            background: #ef4444;
            color: white;
            transform: rotate(90deg);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            padding: 1rem;
        }

        .gallery-item {
            position: relative;
            aspect-ratio: 1;
            overflow: hidden;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            cursor: pointer;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .image-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 0.75rem;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            font-size: 0.875rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .gallery-item:hover .image-caption {
            transform: translateY(0);
        }

        .empty-gallery {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .empty-gallery i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #9ca3af;
        }

        @media (max-width: 640px) {
            .gallery-content {
                margin: 1rem;
            }

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 0.5rem;
                padding: 0.5rem;
            }
        }
    </style>

<style>
    /* Styles yang sudah ada tetap... */
    
    /* Image Preview Modal */
    .image-preview-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.95);
        z-index: 2000;
        padding: 2rem;
        align-items: center;
        justify-content: center;
    }

    .image-preview-modal.active {
        display: flex;
    }

    .preview-content {
        position: relative;
        max-width: 90vw;
        max-height: 90vh;
    }

    .preview-content img {
        max-width: 100%;
        max-height: 85vh;
        object-fit: contain;
    }

    .preview-close {
        position: absolute;
        top: -2rem;
        right: -2rem;
        width: 2.5rem;
        height: 2.5rem;
        background: rgba(255,255,255,0.1);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .preview-close:hover {
        background: #ef4444;
        transform: rotate(90deg);
    }

    .preview-caption {
        position: absolute;
        bottom: -2.5rem;
        left: 0;
        right: 0;
        text-align: center;
        color: white;
        padding: 0.5rem;
        font-size: 0.875rem;
    }
</style>
</head>
<body>
    <div class="gallery-modal">
        <div class="gallery-content">
            <div class="gallery-header">
                <h2><?php echo htmlspecialchars($building['building_name']); ?></h2>
                <a href="javascript:history.back()" class="close-button" title="Kembali">
                    <i class="fas fa-times"></i>
                </a>
            </div>

                        <!-- Tambahkan ini setelah div gallery-modal -->
            <div id="imagePreviewModal" class="image-preview-modal">
                <div class="preview-content">
                    <span class="preview-close" onclick="closeImagePreview()">&times;</span>
                    <img id="previewImage" src="" alt="">
                    <div id="previewCaption" class="preview-caption"></div>
                </div>
            </div>
            
            <div class="gallery-grid">
                <?php if ($images->num_rows > 0): ?>
                    <?php while($image = $images->fetch_assoc()): ?>
                        <div class="gallery-item" onclick="viewFullImage('../uploads/gallery/<?php echo htmlspecialchars($image['image_path']); ?>', '<?php echo htmlspecialchars($image['caption'] ?? $building['building_name']); ?>')">
                        <img src="../uploads/gallery/<?php echo htmlspecialchars($image['image_path']); ?>" 
                            alt="<?php echo htmlspecialchars($image['caption'] ?? $building['building_name']); ?>"
                            loading="lazy">
                        <?php if($image['caption']): ?>
                            <div class="image-caption">
                                <?php echo htmlspecialchars($image['caption']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-gallery">
                        <i class="fas fa-images"></i>
                        <p>Tidak ada foto dalam galeri ini</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    function viewFullImage(imageSrc, caption) {
        const modal = document.getElementById('imagePreviewModal');
        const previewImage = document.getElementById('previewImage');
        const previewCaption = document.getElementById('previewCaption');
        
        previewImage.src = imageSrc;
        previewCaption.textContent = caption || '';
        modal.classList.add('active');
    }

    function closeImagePreview() {
        const modal = document.getElementById('imagePreviewModal');
        modal.classList.remove('active');
    }

    // Close with escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImagePreview();
        }
    });

    // Close when clicking outside image
    document.getElementById('imagePreviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImagePreview();
        }
    });
</script>
</body>
</html>