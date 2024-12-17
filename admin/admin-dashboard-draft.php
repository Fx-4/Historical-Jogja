<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draft Bangunan - Historical Jogja Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin-dashboard-draft.css">
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
            <!-- Bangunan Bersejarah Management -->
            <a href="admin-dashboard.php" class="nav-link">Input Bangunan</a>
            <a href="list-building.php" class="nav-link">List Bangunan</a>
            <a href="gallery.php" class="nav-link">Galeri</a>
            <a href="tourist-map.php" class="nav-link">Peta Wisata</a>
            <a href="messages.php" class="nav-link">Pesan</a>
            <a href="adminlogin.php" class="nav-link logout">Logout</a>
        </div>
    </div>
</nav>

    <!-- Sidebar -->
    <aside class="sidebar">
        <button id="sidebarToggle" class="sidebar-toggle">Menu Navigasi ▼</button>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="#semua-draft" class="active">Semua Draft</a></li>
                <li><a href="#menunggu-review">Menunggu Review</a></li>
                <li><a href="#perlu-revisi">Perlu Revisi</a></li>
                <li><a href="#siap-publish">Siap Publish</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-wrapper">
            <!-- Header with Search and Filter -->
            <div class="content-header">
                <h1 class="page-title">Draft Bangunan Bersejarah</h1>
                <div class="header-actions">
                    <select class="filter-select">
                        <option value="">Semua Kategori</option>
                        <option value="candi">Candi</option>
                        <option value="istana">Istana</option>
                        <option value="benteng">Benteng</option>
                        <option value="monumen">Monumen</option>
                        <option value="masjid">Masjid</option>
                        <option value="museum">Museum</option>
                        <option value="makam">Makam</option>
                    </select>
                    <input type="text" placeholder="Cari draft..." class="search-input">
                </div>
            </div>

            <!-- Table Content -->
            <div class="content-section">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nama Bangunan</th>
                                <th>Kategori</th>
                                <th>Tanggal Dibuat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="building-image">
                                        <img src="placeholder.jpg" alt="Candi Prambanan">
                                    </div>
                                </td>
                                <td>Candi Prambanan</td>
                                <td>Candi</td>
                                <td>2024-03-20</td>
                                <td><span class="status-badge draft">Draft</span></td>
                                <td class="action-buttons">
                                    <button class="btn-icon edit" title="Edit">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </button>
                                    <button class="btn-icon preview" title="Preview">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </button>
                                    <button class="btn-icon publish" title="Publish">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 2L11 13"></path>
                                            <path d="M22 2L15 22l-4-9-9-4 22-7z"></path>
                                        </svg>
                                    </button>
                                    <button class="btn-icon delete" title="Hapus">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <!-- Additional rows similar to above -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <button class="btn-page prev">Previous</button>
                    <div class="page-numbers">
                        <button class="btn-page active">1</button>
                        <button class="btn-page">2</button>
                        <button class="btn-page">3</button>
                    </div>
                    <button class="btn-page next">Next</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Overlay untuk mobile -->
    <div class="overlay" id="overlay"></div>

    <script>
        // Toggle Sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('sidebar-active');
            document.getElementById('overlay').classList.toggle('active');
        });

        // Close sidebar when clicking overlay
        document.getElementById('overlay').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.remove('active');
            document.querySelector('.main-content').classList.remove('sidebar-active');
            this.classList.remove('active');
        });
    </script>
</body>
</html>