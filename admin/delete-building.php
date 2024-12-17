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

try {
    // Start transaction
    $conn->begin_transaction();

    // Get building details first (for file deletion)
    $sql = "SELECT main_image FROM historical_buildings WHERE building_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $building_id);
    $stmt->execute();
    $building = $stmt->get_result()->fetch_assoc();

    // Get gallery images
    $sql_gallery = "SELECT image_path FROM gallery WHERE building_id = ?";
    $stmt_gallery = $conn->prepare($sql_gallery);
    $stmt_gallery->bind_param("i", $building_id);
    $stmt_gallery->execute();
    $gallery_result = $stmt_gallery->get_result();

    // Delete main image file
    if ($building && $building['main_image']) {
        $main_image_path = "../uploads/main/" . $building['main_image'];
        if (file_exists($main_image_path)) {
            unlink($main_image_path);
        }
    }

    // Delete gallery image files
    while ($image = $gallery_result->fetch_assoc()) {
        $gallery_image_path = "../uploads/gallery/" . $image['image_path'];
        if (file_exists($gallery_image_path)) {
            unlink($gallery_image_path);
        }
    }

    // Delete gallery records
    $sql_delete_gallery = "DELETE FROM gallery WHERE building_id = ?";
    $stmt_delete_gallery = $conn->prepare($sql_delete_gallery);
    $stmt_delete_gallery->bind_param("i", $building_id);
    $stmt_delete_gallery->execute();

    // Delete location record
    $sql_delete_location = "DELETE FROM tourist_map WHERE building_id = ?";
    $stmt_delete_location = $conn->prepare($sql_delete_location);
    $stmt_delete_location->bind_param("i", $building_id);
    $stmt_delete_location->execute();

    // Finally, delete the building record
    $sql_delete_building = "DELETE FROM historical_buildings WHERE building_id = ?";
    $stmt_delete_building = $conn->prepare($sql_delete_building);
    $stmt_delete_building->bind_param("i", $building_id);
    $stmt_delete_building->execute();

    // Commit transaction
    $conn->commit();

    $_SESSION['success_message'] = "Bangunan bersejarah berhasil dihapus!";
    header("Location: list-building.php");
    exit;

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    $_SESSION['error_message'] = "Gagal menghapus bangunan: " . $e->getMessage();
    header("Location: list-building.php");
    exit;
}
?>
