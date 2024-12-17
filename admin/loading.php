<?php
session_start();
// Pastikan user sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading - Historical Buildings</title>
    <style>
        <?php include '../assets/css/loading.css'; ?>
    </style>
</head>
<body>
    <input class="sr-only" type="checkbox" id="flip">
    <label for="flip"></label>
    <h1>tap.</h1>
    <div class="container">
        <div class="scene">
            <span class="loading-label">Loading...</span>
            <div class="bar" style="--columns: 20% 0 10% 0 10% 0 10% 0 20% 0 10% 0 20%; --total-length: 400;">
                <div style="--name: segment-1; --delay: 0; --length: 20; --depth: 0;" class="bar__segment bar__segment--aligned"></div>
                <div style="--name: segment-2; --delay: 20; --length: 30; --depth: 0;" class="bar__segment bar__segment--front"></div>
                <div style="--name: segment-3; --delay: 50; --length: 10; --depth: 30;" class="bar__segment bar__segment--aligned"></div>
                <div style="--name: segment-4; --delay: 60; --length: 50; --depth: 30;" class="bar__segment bar__segment--back"></div>
                <div style="--name: segment-5; --delay: 110; --length: 10; --depth: -20;" class="bar__segment bar__segment--aligned"></div>
                <div style="--name: segment-6; --delay: 120; --length: 60; --depth: -20;" class="bar__segment bar__segment--front"></div>
                <div style="--name: segment-7; --delay: 180; --length: 10; --depth: 40;" class="bar__segment bar__segment--aligned"></div>
                <div style="--name: segment-8; --delay: 190; --length: 70; --depth: 40;" class="bar__segment bar__segment--back"></div>
                <div style="--name: segment-9; --delay: 260; --length: 20; --depth: -30;" class="bar__segment bar__segment--aligned"></div>
                <div style="--name: segment-10; --delay: 280; --length: 50; --depth: -30;" class="bar__segment bar__segment--front"></div>
                <div style="--name: segment-11; --delay: 330; --length: 30; --depth: 20;" class="bar__segment bar__segment--aligned"></div>
                <div style="--name: segment-12; --delay: 360; --length: 20; --depth: 20;" class="bar__segment bar__segment--back"></div>
                <div style="--name: segment-13; --delay: 380; --length: 20; --depth: 0;" class="bar__segment bar__segment--aligned"></div>
            </div>
        </div>
    </div>

    <script>
        // Redirect ke dashboard setelah animasi selesai
        setTimeout(() => {
            window.location.href = 'admin-dashboard.php';
        }, 10000); // 10 detik, sesuai dengan --loading-speed di CSS
    </script>
</body>
</html>