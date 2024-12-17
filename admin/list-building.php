<?php
session_start();
require_once '../config/db-connect.php';

$current_page = basename($_SERVER['PHP_SELF']);
echo "Current page: " . $current_page;

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Database connection
$db = new Database();
$conn = $db->connect();

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search and filter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

// Build query with filters
$where_clauses = [];
$params = [];
$types = '';

if ($search) {
    $where_clauses[] = "building_name LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

if ($category_filter) {
    $where_clauses[] = "category = ?";
    $params[] = $category_filter;
    $types .= 's';
}

if ($status_filter) {
    $where_clauses[] = "status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Get total records for pagination
$count_sql = "SELECT COUNT(*) as total FROM historical_buildings $where_sql";
$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_records = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get buildings data
$sql = "SELECT building_id, building_name, category, status, main_image, created_at 
        FROM historical_buildings $where_sql 
        ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types . "ii", ...array_merge($params, [$records_per_page, $offset]));
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Bangunan Bersejarah - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/list-building-styles.css">
    <link rel="stylesheet" href="../assets/css/shared-navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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
    <!-- Main Content -->
    <main class="main-content">
        <div class="content-wrapper">
            <h1 class="page-title">Daftar Bangunan Bersejarah</h1>

            <!-- Success/Error Messages -->
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

            <div class="building-list">
                <!-- Filters -->
                <div class="filters">
                    <div class="search-box">
                        <input type="text" 
                               placeholder="Cari bangunan..." 
                               class="form-control"
                               value="<?php echo htmlspecialchars($search); ?>"
                               onchange="this.form.submit()">
                    </div>
                    <div class="filter-box">
                        <select class="form-control" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            <option value="candi" <?php echo $category_filter === 'candi' ? 'selected' : ''; ?>>Candi</option>
                            <option value="istana" <?php echo $category_filter === 'istana' ? 'selected' : ''; ?>>Istana</option>
                            <option value="benteng" <?php echo $category_filter === 'benteng' ? 'selected' : ''; ?>>Benteng</option>
                            <option value="monumen" <?php echo $category_filter === 'monumen' ? 'selected' : ''; ?>>Monumen</option>
                            <option value="masjid" <?php echo $category_filter === 'masjid' ? 'selected' : ''; ?>>masjid</option>
                            <option value="museum" <?php echo $category_filter === 'museum' ? 'selected' : ''; ?>>museum</option>
                            <option value="makam" <?php echo $category_filter === 'makam' ? 'selected' : ''; ?>>makam</option>
                        </select>
                    </div>
                    <div class="filter-box">
                        <select class="form-control" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="aktif" <?php echo $status_filter === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                            <option value="tidak_aktif" <?php echo $status_filter === 'tidak_aktif' ? 'selected' : ''; ?>>Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nama Bangunan</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Tanggal Input</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <img src="../uploads/main/<?php echo htmlspecialchars($row['main_image']); ?>" 
                                         class="thumbnail"
                                         onclick="showImage(this.src)"
                                         alt="<?php echo htmlspecialchars($row['building_name']); ?>">
                                </td>
                                <td><?php echo htmlspecialchars($row['building_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $row['status'] === 'aktif' ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                    <button class="btn-action btn-view" onclick="viewBuilding(<?php echo $row['building_id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action btn-edit" onclick="editBuilding(<?php echo $row['building_id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action btn-delete" onclick="deleteBuilding(<?php echo $row['building_id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                           class="page-link <?php echo $page == $i ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <img id="modalImage" src="" alt="Building Image">
        </div>
    </div>

    <script>
        // Show image in modal
        function showImage(src) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = 'flex';
            modalImg.src = src;
        }

        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });

        // Building actions
        function viewBuilding(id) {
            window.location.href = `view-building.php?id=${id}`;
        }

        function editBuilding(id) {
            window.location.href = `edit-building.php?id=${id}`;
        }

        function deleteBuilding(id) {
            if (confirm('Apakah Anda yakin ingin menghapus bangunan ini?')) {
                window.location.href = `delete-building.php?id=${id}`;
            }
        }

        // Form submission on filter change
        document.querySelectorAll('.filters select, .filters input').forEach(element => {
            element.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>
