<?php
session_start();
require_once '../config/db-connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Define the validation function first, before it's used
function validateImage($file) {
    // Check file size (max 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new Exception("Image file is too large (max 2MB)");
    }

    // Check file type
    $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($file['type'], $allowed)) {
        throw new Exception("Invalid file type. Only JPG and PNG are allowed");
    }

    // Optional: Check image dimensions
    $image_info = getimagesize($file['tmp_name']);
    if ($image_info === false) {
        throw new Exception("Invalid image file");
    }

    // Optional: Set minimum dimensions
    if ($image_info[0] < 200 || $image_info[1] < 200) {
        throw new Exception("Image dimensions too small (min 200x200)");
    }

    return true;
}

// Helper function to reorganize files array
function reArrayFiles($files) {
    $fileArray = [];
    $fileCount = count($files['name']);
    $fileKeys = array_keys($files);
    
    for ($i = 0; $i < $fileCount; $i++) {
        foreach ($fileKeys as $key) {
            $fileArray[$i][$key] = $files[$key][$i];
        }
    }
    
    return $fileArray;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->connect();
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Get the category for folder structure
        $category = $_POST['category'];
        
        // Create category directory if it doesn't exist
        $category_dir = "../uploads/main/" . $category;
        if (!file_exists($category_dir)) {
            mkdir($category_dir, 0777, true);
        }

        // 1. Upload and save main image
        $mainImagePath = '';
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === 0) {
            $mainImage = $_FILES['main_image'];
            // Validate main image
            validateImage($mainImage);
            
            // Generate unique filename
            $filename = uniqid() . '_' . basename($mainImage['name']);
            $relative_path = $category . "/" . $filename;
            $upload_path = "../uploads/main/" . $relative_path;
            
            // Move uploaded file
            if (!move_uploaded_file($mainImage['tmp_name'], $upload_path)) {
                throw new Exception("Gagal mengupload file");
            }
            
            $mainImagePath = $relative_path;
        }
        
        // 2. Save building information
        $sql = "INSERT INTO historical_buildings (
            building_name, category, construction_year, status,
            complete_history, main_image, important_figures,
            cultural_value, address, open_hours, ticket_price,
            contact, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssss",
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
            $_POST['contact']
        );
        $stmt->execute();
        $buildingId = $conn->insert_id;
        
        // Create building directory for gallery
        $gallery_building_dir = "../uploads/gallery/" . $category . "/" . $buildingId;
        if (!file_exists($gallery_building_dir)) {
            mkdir($gallery_building_dir, 0777, true);
        }
        
        // 3. Save location/map information if provided
        if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
            $sqlLocation = "INSERT INTO tourist_map (
                building_id, location_name, latitude, longitude, description
            ) VALUES (?, ?, ?, ?, ?)";
            
            $stmtLocation = $conn->prepare($sqlLocation);
            $stmtLocation->bind_param("isdds",
                $buildingId,
                $_POST['location_name'],
                $_POST['latitude'],
                $_POST['longitude'],
                $_POST['description']
            );
            $stmtLocation->execute();
        }
        
        // 4. Handle gallery images
        if (isset($_FILES['gallery_images'])) {
            // Create directory structure for gallery
            $building_category = $_POST['category'];
            $gallery_base_path = "../uploads/gallery/{$building_category}/{$buildingId}";
            
            // Create directories if they don't exist
            if (!file_exists($gallery_base_path)) {
                mkdir($gallery_base_path, 0777, true);
            }

            $galleryImages = reArrayFiles($_FILES['gallery_images']);
            
            foreach ($galleryImages as $index => $image) {
                if ($image['error'] === 0) {
                    // Validate gallery image
                    validateImage($image);
                    
                    // Generate unique filename
                    $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '_' . date('Ymd') . '.' . $ext;
                    
                    // Set relative path for database and full path for upload
                    $relative_path = "{$building_category}/{$buildingId}/{$filename}";
                    $upload_path = "../uploads/gallery/{$relative_path}";

                    // Move uploaded file
                    if (move_uploaded_file($image['tmp_name'], $upload_path)) {
                        // Insert into gallery table
                        $sqlGallery = "INSERT INTO gallery (
                            building_id, 
                            image_path,
                            caption,
                            created_at
                        ) VALUES (?, ?, ?, NOW())";
                        
                        $caption = isset($_POST['gallery_captions'][$index]) ? $_POST['gallery_captions'][$index] : '';
                        
                        $stmtGallery = $conn->prepare($sqlGallery);
                        $stmtGallery->bind_param("iss",
                            $buildingId,
                            $relative_path,
                            $caption
                        );
                        $stmtGallery->execute();
                    } else {
                        throw new Exception("Failed to upload gallery image");
                    }
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        // Redirect with success message
        $_SESSION['success_message'] = "Bangunan bersejarah berhasil ditambahkan!";
        header("Location: list-building.php");
        exit;
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $_SESSION['error_message'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: admin-dashboard.php");
        exit;
    }
}
?>