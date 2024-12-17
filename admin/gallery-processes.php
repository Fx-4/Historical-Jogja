<?php
session_start();
require_once '../config/db-connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

// Check if it's a POST request for upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleUpload($conn);
}
// Check if it's a GET request for delete
else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    handleDelete($conn);
}
else {
    header("Location: gallery.php");
    exit;
}

// Function to handle file upload
function handleUpload($conn) {
    try {
        // Start transaction
        $conn->begin_transaction();

        // Check if files were uploaded
        if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
            throw new Exception("No images were uploaded");
        }

        // Check building_id
        if (!isset($_POST['building_id']) || empty($_POST['building_id'])) {
            throw new Exception("No building selected");
        }

        $building_id = intval($_POST['building_id']);

        // Get building category for folder structure
        $sql_building = "SELECT category FROM historical_buildings WHERE building_id = ?";
        $stmt_building = $conn->prepare($sql_building);
        $stmt_building->bind_param("i", $building_id);
        $stmt_building->execute();
        $building_result = $stmt_building->get_result();
        $building_data = $building_result->fetch_assoc();
        
        if (!$building_data) {
            throw new Exception("Building not found");
        }

        // Create category directory if it doesn't exist
        $category_dir = "../uploads/gallery/" . $building_data['category'];
        if (!file_exists($category_dir)) {
            mkdir($category_dir, 0777, true);
        }

        // Create building directory
        $building_dir = $category_dir . "/" . $building_id;
        if (!file_exists($building_dir)) {
            mkdir($building_dir, 0777, true);
        }

        $uploaded_files = reArrayFiles($_FILES['images']);

        // Process each uploaded file
        foreach ($uploaded_files as $file) {
            if ($file['error'] !== 0) {
                throw new Exception("Error uploading file: " . $file['name']);
            }

            if ($file['size'] > 2 * 1024 * 1024) {
                throw new Exception("File too large: " . $file['name']);
            }

            $allowed_types = ['image/jpeg', 'image/png'];
            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception("Invalid file type: " . $file['name']);
            }

            $filename = uniqid() . '_' . basename($file['name']);
            $relative_path = $building_data['category'] . "/" . $building_id . "/" . $filename;
            $upload_path = $building_dir . "/" . $filename;

            if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                throw new Exception("Failed to move uploaded file: " . $file['name']);
            }

            $sql = "INSERT INTO gallery (building_id, image_path, caption, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $caption = isset($_POST['caption']) ? $_POST['caption'] : '';
            $stmt->bind_param("iss", $building_id, $relative_path, $caption);
            $stmt->execute();
        }

        $conn->commit();
        $_SESSION['success_message'] = "Images uploaded successfully!";

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }

    header("Location: gallery.php");
    exit;
}

// Function to handle delete
function handleDelete($conn) {
    try {
        if (!isset($_GET['id'])) {
            throw new Exception("No image ID provided");
        }

        $gallery_id = intval($_GET['id']);
        
        // Start transaction
        $conn->begin_transaction();

        // Get image details first
        $sql = "SELECT image_path FROM gallery WHERE gallery_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $gallery_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $image = $result->fetch_assoc();

        if (!$image) {
            throw new Exception("Image not found");
        }

        // Delete physical file
        $file_path = "../uploads/gallery/" . $image['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
            
            // Try to remove directory if empty
            $dir_path = dirname($file_path);
            if (is_dir($dir_path) && count(glob("$dir_path/*")) === 0) {
                rmdir($dir_path);
            }
        }

        // Delete database record
        $sql_delete = "DELETE FROM gallery WHERE gallery_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $gallery_id);
        $stmt_delete->execute();

        $conn->commit();
        $_SESSION['success_message'] = "Image deleted successfully!";

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = "Error deleting image: " . $e->getMessage();
    }

    header("Location: gallery.php");
    exit;
}

// Helper function to reorganize files array
function reArrayFiles($files) {
    $file_array = [];
    $file_count = count($files['name']);
    $file_keys = array_keys($files);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_array[$i][$key] = $files[$key][$i];
        }
    }

    return $file_array;
}
?>