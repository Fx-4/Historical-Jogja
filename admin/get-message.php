<?php
session_start();
require_once '../config/db-connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}

$contactId = intval($_GET['id']);

// Database connection
$db = new Database();
$conn = $db->connect();

// Get message details
$sql = "SELECT * FROM contact_messages WHERE contact_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $contactId);
$stmt->execute();
$message = $stmt->get_result()->fetch_assoc();

if (!$message) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

// Update status to read if unread
if ($message['status'] == 0) {
    $sql = "UPDATE contact_messages SET status = 1 WHERE contact_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $contactId);
    $stmt->execute();
}

// Return message details as JSON
header('Content-Type: application/json');
echo json_encode([
    'contact_id' => $message['contact_id'],
    'nama' => htmlspecialchars($message['nama']),
    'email' => htmlspecialchars($message['email']),
    'pesan' => htmlspecialchars($message['pesan']),
    'status' => $message['status'],
    'created_at' => $message['created_at']
]);
