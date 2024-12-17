<?php
session_start();
require_once '../config/db-connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

// Handle POST request for updating caption
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gallery_id'])) {
    try {
        $gallery_id = intval($_POST['gallery_id']);
        $caption = trim($_POST['caption']);
        
        $sql = "UPDATE gallery SET caption = ? WHERE gallery_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $caption, $gallery_id);
        $stmt->execute();
        
        $_SESSION['success_message'] = "Caption updated successfully!";
        header("Location: gallery.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error updating caption: " . $e->getMessage();
        header("Location: gallery.php");
        exit;
    }
}

// Handle GET request for edit form
if (!isset($_GET['id'])) {
    header("Location: gallery.php");
    exit;
}

$gallery_id = intval($_GET['id']);

// Get image details
$sql = "SELECT g.*, hb.building_name 
        FROM gallery g
        JOIN historical_buildings hb ON g.building_id = hb.building_id
        WHERE g.gallery_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $gallery_id);
$stmt->execute();
$result = $stmt->get_result();
$image = $result->fetch_assoc();

if (!$image) {
    $_SESSION['error_message'] = "Image not found";
    header("Location: gallery.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Image Caption</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/gallery-styles.css">
    <style>
        /* Override modal styles for edit page */
        .modal {
            position: relative;
            display: block;
            padding: 2rem;
            background: var(--background-color);
            min-height: 100vh;
        }

        .modal-content {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .edit-image {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin: 1rem 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input[disabled] {
            background-color: #f3f4f6;
        }

        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            resize: vertical;
            min-height: 100px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
        }

        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--secondary-color);
            transition: color 0.2s;
        }

        .close-modal:hover {
            color: var(--danger-color);
        }

        @media (max-width: 768px) {
            .modal {
                padding: 1rem;
            }

            .modal-content {
                padding: 1.5rem;
            }
        }
    </style>
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

    <div class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Image Caption</h2>
                <a href="gallery.php" class="close-modal">&times;</a>
            </div>

            <form action="gallery-edit.php" method="POST">
                <input type="hidden" name="gallery_id" value="<?php echo $gallery_id; ?>">
                
                <div class="form-group">
                    <label>Building</label>
                    <input type="text" 
                           value="<?php echo htmlspecialchars($image['building_name']); ?>" 
                           disabled 
                           class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Current Image</label>
                    <img src="../uploads/gallery/<?php echo htmlspecialchars($image['image_path']); ?>" 
                         alt="Gallery image" 
                         class="edit-image">
                </div>
                
                <div class="form-group">
                    <label>Caption</label>
                    <textarea name="caption" 
                              class="form-control" 
                              rows="3"><?php echo htmlspecialchars($image['caption']); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="gallery.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
