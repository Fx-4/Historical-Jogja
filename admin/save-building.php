<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_log("Save building process started"); // Tambahkan logging

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

require_once '../config/db-connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $db = new Database();
        $conn = $db->connect();
        error_log("Database connection established");
        
        // Debug: Log semua data POST dan FILES
        error_log("POST Data: " . print_r($_POST, true));
        error_log("FILES Data: " . print_r($_FILES, true));
        
        // Validasi input yang wajib diisi
        $required_fields = [
            'building_name' => 'Nama bangunan',
            'category' => 'Kategori',
            'complete_history' => 'Sejarah lengkap',
            'address' => 'Alamat lengkap'
        ];

        foreach ($required_fields as $field => $label) {
            if (empty($_POST[$field])) {
                throw new Exception("$label harus diisi");
            }
        }
        
        // Proses upload main image
        $mainImagePath = null;
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            $fileName = time() . '_' . basename($_FILES['main_image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (!move_uploaded_file($_FILES['main_image']['tmp_name'], $targetPath)) {
                error_log("Failed to upload file: " . error_get_last()['message']);
                throw new Exception("Gagal mengupload file utama");
            }
            $mainImagePath = $fileName;
            error_log("Main image uploaded successfully: " . $mainImagePath);
        }
        
        // Query disesuaikan dengan struktur tabel
        $query = "INSERT INTO historical_buildings (
            building_name, 
            category, 
            construction_year, 
            STATUS, 
            complete_history, 
            main_image, 
            important_figures, 
            cultural_value, 
            address, 
            open_hours, 
            ticket_price, 
            contact,
            map_embed
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        error_log("Preparing query: " . $query);
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare statement failed: " . $conn->error);
            throw new Exception("Database error: " . $conn->error);
        }
        
        // Bind parameter disesuaikan dengan jumlah field
        $mapEmbed = ''; // Default empty string for map_embed
        $stmt->bind_param("sssssssssssss",
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
            $mapEmbed
        );
        
        error_log("Executing statement");
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            throw new Exception("Gagal menyimpan data: " . $stmt->error);
        }
        
        $buildingId = $conn->insert_id;
        error_log("Building inserted successfully with ID: " . $buildingId);
        
        // Proses gallery images
        if (isset($_FILES['gallery_images'])) {
            $uploadDir = '../uploads/gallery/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileName = time() . '_' . basename($_FILES['gallery_images']['name'][$key]);
                    $targetPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($tmp_name, $targetPath)) {
                        $caption = isset($_POST['gallery_captions'][$key]) ? $_POST['gallery_captions'][$key] : '';
                        $galleryStmt = $conn->prepare("INSERT INTO gallery (building_id, image_path, caption) VALUES (?, ?, ?)");
                        $galleryStmt->bind_param("iss", $buildingId, $fileName, $caption);
                        $galleryStmt->execute();
                    }
                }
            }
        }
        
        $_SESSION['success_message'] = "Data bangunan berhasil disimpan";
        error_log("Redirecting to list-building.php");
        header("Location: list-building.php");
        exit();
        
    } catch (Exception $e) {
        error_log("Error occurred: " . $e->getMessage());
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: admin-dashboard.php");
        exit();
    }
}
?>