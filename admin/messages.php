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

// Handle message actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $contactId = isset($_POST['contact_id']) ? intval($_POST['contact_id']) : 0;
        
        switch ($_POST['action']) {
            case 'mark_read':
                $sql = "UPDATE contact_messages SET status = 1 WHERE contact_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $contactId);
                $stmt->execute();
                break;
                
            case 'mark_unread':
                $sql = "UPDATE contact_messages SET status = 0 WHERE contact_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $contactId);
                $stmt->execute();
                break;
                
            case 'delete':
                $sql = "DELETE FROM contact_messages WHERE contact_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $contactId);
                $stmt->execute();
                break;
        }
        
        header("Location: messages.php");
        exit;
    }
}

// Get messages with pagination and filters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$where_clauses = [];
$params = [];
$types = '';

if ($status_filter !== '') {
    $where_clauses[] = "status = ?";
    $params[] = $status_filter;
    $types .= 'i';
}

if ($search) {
    $where_clauses[] = "(nama LIKE ? OR email LIKE ? OR pesan LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
    $types .= 'sss';
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Get total records
$count_sql = "SELECT COUNT(*) as total FROM contact_messages $where_sql";
$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_records = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Get messages
$sql = "SELECT * FROM contact_messages $where_sql ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types . "ii", ...array_merge($params, [$limit, $offset]));
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Historical Buildings Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/messages-styles.css">
    <link rel="stylesheet" href="../assets/css/shared-navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
        <!-- Di messages.php -->
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-left">
                    <a href="#" class="nav-brand">Historical Jogja Admin</a>
                </div>
                <div class="nav-links">
                    <a href="admin-dashboard.php" class="nav-link">Input Bangunan</a>
                    <a href="list-building.php" class="nav-link">List Bangunan</a>
                    <a href="gallery.php" class="nav-link">Galeri</a>
                    <a href="tourist-map.php" class="nav-link">Peta Wisata</a>
                    <a href="messages.php" class="nav-link active">Pesan</a>
                    <a href="logout.php" class="nav-link logout">Logout</a>
                </div>
            </div>
        </nav>

    <main class="messages-container">
        <div class="messages-header">
            <h1 class="messages-title">Pesan Kontak</h1>
            <div class="messages-filters">
                <input type="text" 
                       placeholder="Cari pesan..." 
                       class="filter-select"
                       value="<?php echo htmlspecialchars($search); ?>"
                       onchange="this.form.submit()">
                <select class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="0" <?php echo $status_filter === '0' ? 'selected' : ''; ?>>Belum Dibaca</option>
                    <option value="1" <?php echo $status_filter === '1' ? 'selected' : ''; ?>>Sudah Dibaca</option>
                </select>
            </div>
        </div>

        <div class="messages-list">
            <!-- Messages -->
            <?php while ($message = $messages->fetch_assoc()): ?>
                <div class="message-item <?php echo $message['status'] == 0 ? 'unread' : ''; ?>">
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-sender"><?php echo htmlspecialchars($message['nama']); ?></span>
                            <span class="message-date">
                                <?php echo date('d M Y H:i', strtotime($message['created_at'])); ?>
                            </span>
                        </div>
                        <div class="message-preview"><?php echo htmlspecialchars($message['pesan']); ?></div>
                    </div>
                    <div class="message-actions">
                        <button class="message-button" 
                                onclick="viewMessage(<?php echo $message['contact_id']; ?>)"
                                title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <?php if ($message['status'] == 0): ?>
                            <button class="message-button" 
                                    onclick="markAsRead(<?php echo $message['contact_id']; ?>)"
                                    title="Mark as read">
                                <i class="fas fa-envelope-open"></i>
                            </button>
                        <?php else: ?>
                            <button class="message-button" 
                                    onclick="markAsUnread(<?php echo $message['contact_id']; ?>)"
                                    title="Mark as unread">
                                <i class="fas fa-envelope"></i>
                            </button>
                        <?php endif; ?>
                        <button class="message-button" 
                                onclick="deleteMessage(<?php echo $message['contact_id']; ?>)"
                                title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search); ?>" 
                   class="page-link <?php echo $page == $i ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>

    <!-- Message View Modal -->
    <div id="messageModal" class="message-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Detail Pesan</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div id="messageDetails" class="message-details">
                <!-- Message details will be loaded here -->
            </div>
        </div>
    </div>

    <script>
    function viewMessage(contactId) {
        fetch(`get-message.php?id=${contactId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('messageDetails').innerHTML = `
                    <div class="message-field">
                        <div class="field-label">Dari:</div>
                        <div class="field-value">${data.nama} (${data.email})</div>
                    </div>
                    <div class="message-field">
                        <div class="field-label">Pesan:</div>
                        <div class="field-value">${data.pesan}</div>
                    </div>
                    <div class="message-field">
                        <div class="field-label">Tanggal:</div>
                        <div class="field-value">${new Date(data.created_at).toLocaleString('id-ID')}</div>
                    </div>
                `;
                document.getElementById('messageModal').style.display = 'block';
            });
    }

    function closeModal() {
        document.getElementById('messageModal').style.display = 'none';
    }

    function markAsRead(contactId) {
        const formData = new FormData();
        formData.append('action', 'mark_read');
        formData.append('contact_id', contactId);

        fetch('messages.php', {
            method: 'POST',
            body: formData
        }).then(() => location.reload());
    }

    function markAsUnread(contactId) {
        const formData = new FormData();
        formData.append('action', 'mark_unread');
        formData.append('contact_id', contactId);

        fetch('messages.php', {
            method: 'POST',
            body: formData
        }).then(() => location.reload());
    }

    function deleteMessage(contactId) {
        if (confirm('Apakah Anda yakin ingin menghapus pesan ini?')) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('contact_id', contactId);

            fetch('messages.php', {
                method: 'POST',
                body: formData
            }).then(() => location.reload());
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('messageModal');
        if (event.target === modal) {
            closeModal();
        }
    }
    </script>

</body>
</html>