<?php
// Tambahkan ini di paling atas kontak.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kemudian cek session dan database connection
session_start();
require_once '../config/db-connect.php';

// Inisialisasi koneksi database
try {
    $db = new Database();
    $conn = $db->connect();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    die("Connection Error: " . $e->getMessage());
}

// Message variables initialization
$feedback = [
    'type' => '',
    'message' => '',
    'icon' => ''
];

// Cookie check
if(isset($_COOKIE['historical_jogja_user'])) {
    error_log("Cookie exists: " . $_COOKIE['historical_jogja_user']);
} else {
    error_log("Cookie not set");
}

// Handle form submission with enhanced validation and feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $nama = trim(filter_var($_POST['nama'], FILTER_SANITIZE_STRING));
    $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $pesan = trim(filter_var($_POST['pesan'], FILTER_SANITIZE_STRING));
    
    // Enhanced validation
    $errors = [];
    if (empty($nama)) {
        $errors[] = "Nama lengkap harus diisi";
    } elseif (strlen($nama) < 3) {
        $errors[] = "Nama terlalu pendek";
    }
    
    if (empty($email)) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    if (empty($pesan)) {
        $errors[] = "Pesan harus diisi";
    } elseif (strlen($pesan) < 10) {
        $errors[] = "Pesan terlalu pendek (minimal 10 karakter)";
    }
    // If no validation errors, proceed
   if (empty($errors)) {
    try {
        // Set cookie with enhanced security
        setcookie('historical_jogja_user', hash('sha256', $email), [
            'expires' => time() + (86400 * 30), // 30 days
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        // Prepare statement with timestamp
        $sql = "INSERT INTO contact_messages (nama, email, pesan, status, created_at) 
                VALUES (?, ?, ?, 0, CURRENT_TIMESTAMP)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nama, $email, $pesan);
        
        if ($stmt->execute()) {
            $feedback = [
                'type' => 'success',
                'message' => "Terima kasih! Pesan Anda telah terkirim dan akan segera kami tanggapi.",
                'icon' => 'check-circle'
            ];
            // Clear form data
            $nama = $email = $pesan = '';
        } else {
            throw new Exception("Database error");
        }
    } catch (Exception $e) {
        $feedback = [
            'type' => 'error',
            'message' => "Maaf, terjadi kesalahan teknis. Silakan coba beberapa saat lagi.",
            'icon' => 'exclamation-circle'
        ];
        error_log("Contact form error: " . $e->getMessage());
    }
} else {
    $feedback = [
        'type' => 'error',
        'message' => implode("<br>", $errors),
        'icon' => 'exclamation-triangle'
    ];
}
}

// Get user data from cookie if exists
$user_data = isset($_COOKIE['historical_jogja_user']) ? $_COOKIE['historical_jogja_user'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kontak Kami - Historical Jogja</title>

<!-- CSS and Font Loading -->
 
<link rel="preload" as="style" href="../assets/css/kontak.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Urutkan dari umum ke spesifik -->
<link rel="stylesheet" href="../assets/css/kontak.css">
<link rel="stylesheet" href="../assets/css/floating-labels.css">
<link rel="stylesheet" href="../assets/css/navbar/primary-nav.css">

<link rel="stylesheet" href="../assets/css/navbar/mobile-nav.css">

    <!-- JavaScript -->
<!-- JavaScript -->

<script src="../assets/js/navbar/animated-navbar.js" defer></script>  <!-- Tambahkan ini -->
<script src="../assets/js/navbar/primary-nav.js" defer></script>

<script src="../assets/js/navbar/mobile-nav.js" defer></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


</head>

<body>
<!-- Urutkan sesuai prioritas -->
<?php include_once '../components/navbar/PrimaryNav.php'; ?>    <!-- Desktop -->

<?php include_once '../components/navbar/MobileNav.php'; ?>     <!-- Mobile Only -->


   <main class="main-content">
       <!-- Hero Section -->
       <section class="hero-section">
           <div class="hero-content">
               <h1 class="hero-title">Hubungi Kami</h1>
               <p class="hero-subtitle">Sampaikan pertanyaan, saran, atau kolaborasi dengan tim kami</p>
           </div>
           <div class="hero-pattern"></div>
       </section>
       <!-- Contact Form Section -->
       <section class="contact-section">
           <?php if ($feedback['message']): ?>
               <div class="feedback-message feedback-<?php echo $feedback['type']; ?>">
                   <i class="fas fa-<?php echo $feedback['icon']; ?>"></i>
                   <p><?php echo $feedback['message']; ?></p>
               </div>
           <?php endif; ?>

           <div class="contact-container">
               <!-- Contact Info -->
               <div class="contact-info">
                   <h2>Informasi Kontak</h2>
                   <div class="info-item">
                       <i class="fas fa-map-marker-alt"></i>
                       <div>
                           <h3>Alamat</h3>
                           <p>Jl. Malioboro No. 123<br>Yogyakarta, Indonesia</p>
                       </div>
                   </div>
                   <div class="info-item">
                       <i class="fas fa-phone"></i>
                       <div>
                           <h3>Telepon</h3>
                           <p>+62 274 123456</p>
                       </div>
                   </div>
                   <div class="info-item">
                       <i class="fas fa-envelope"></i>
                       <div>
                           <h3>Email</h3>
                           <p>info@historicaljogja.com</p>
                       </div>
                   </div>
               </div>

               <!-- Contact Form -->
               <form id="contact-form" class="contact-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                   <div class="form-group">
                       <input type="text" 
                              class="form-control"
                              id="nama"
                              name="nama" 
                              placeholder=" "
                              required 
                              value="<?php echo isset($nama) ? htmlspecialchars($nama) : ''; ?>"
                              autocomplete="name">
                       <label for="nama" class="floating-label">Nama Lengkap</label>
                       <div class="input-icon">
                           <i class="fas fa-user"></i>
                       </div>
                   </div>

                   <div class="form-group">
                       <input type="email" 
                              class="form-control"
                              id="email"
                              name="email" 
                              placeholder=" "
                              required
                              value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                              autocomplete="email">
                       <label for="email" class="floating-label">Alamat Email</label>
                       <div class="input-icon">
                           <i class="fas fa-envelope"></i>
                       </div>
                   </div>

                   <div class="form-group">
                       <textarea name="pesan" 
                               id="pesan"
                               class="form-control"
                               rows="4"
                               placeholder=" "
                               required><?php echo isset($pesan) ? htmlspecialchars($pesan) : ''; ?></textarea>
                       <label for="pesan" class="floating-label">Pesan</label>
                       <div class="input-icon">
                           <i class="fas fa-comment"></i>
                       </div>
                   </div>

                   <button type="submit" class="submit-button">
                       <span class="button-text">Kirim Pesan</span>
                       <span class="button-icon">
                           <i class="fas fa-paper-plane"></i>
                       </span>
                       <div class="button-loader"></div>
                   </button>
               </form>
           </div>
       </section>
       <!-- Team Section -->
       <section class="team-section" data-scroll-reveal>
           <h2 class="section-title">Tim Pengembang</h2>
           <div class="team-grid">
               <!-- Developer 1 -->
               <div class="team-card" data-tilt>
                   <div class="card-inner">
                       <div class="card-front">
                           <div class="profile-image">
                               <img src="../assets/images/team/dev1.jpg" alt="Lead Developer" loading="lazy">
                           </div>
                           <h3>Haikal Hifzhi Helmy</h3>
                           <p class="role">Lead Developer</p>
                       </div>
                       <div class="card-back">
                           <h4>Keahlian:</h4>
                           <ul class="skills-list">
                               <li>Full Stack Development</li>
                               <li>UI/UX Design</li>
                               <li>Database Architecture</li>
                           </ul>
                           <div class="social-links">
                               <a href="#" class="social-link">
                                   <i class="fab fa-github"></i>
                               </a>
                               <a href="#" class="social-link">
                                   <i class="fab fa-linkedin"></i>
                               </a>
                               <a href="#" class="social-link">
                                   <i class="fab fa-twitter"></i>
                               </a>
                           </div>
                       </div>
                   </div>
               </div>

               <!-- Developer 2 -->
               <div class="team-card" data-tilt>
                   <div class="card-inner">
                       <div class="card-front">
                           <div class="profile-image">
                               <img src="../assets/images/team/dev2.jpg" alt="UI/UX Designer" loading="lazy">
                           </div>
                           <h3>Faisa Edenia Sekarlangit</h3>
                           <p class="role">UI/UX Designer</p>
                       </div>
                       <div class="card-back">
                           <h4>Keahlian:</h4>
                           <ul class="skills-list">
                               <li>User Interface Design</li>
                               <li>User Experience Research</li>
                               <li>Prototyping</li>
                           </ul>
                           <div class="social-links">
                               <a href="#" class="social-link">
                                   <i class="fab fa-dribbble"></i>
                               </a>
                               <a href="#" class="social-link">
                                   <i class="fab fa-behance"></i>
                               </a>
                               <a href="#" class="social-link">
                                   <i class="fab fa-instagram"></i>
                               </a>
                           </div>
                       </div>
                   </div>
               </div>
               <!-- Developer 3 -->
               <div class="team-card" data-tilt>
                   <div class="card-inner">
                       <div class="card-front">
                           <div class="profile-image">
                               <img src="../assets/images/team/dev3.jpg" alt="Content Specialist" loading="lazy">
                           </div>
                           <h3>Muhammad Dzaki Wirayuda</h3>
                           <p class="role">Content Specialist</p>
                       </div>
                       <div class="card-back">
                           <h4>Keahlian:</h4>
                           <ul class="skills-list">
                               <li>Historical Research</li>
                               <li>Content Writing</li>
                               <li>Digital Storytelling</li>
                           </ul>
                           <div class="social-links">
                               <a href="#" class="social-link">
                                   <i class="fab fa-medium"></i>
                               </a>
                               <a href="#" class="social-link">
                                   <i class="fab fa-linkedin"></i>
                               </a>
                               <a href="#" class="social-link">
                                   <i class="fab fa-instagram"></i>
                               </a>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </section>

       <!-- About Section -->
       <section class="about-section">
           <div class="about-content">
               <h2 class="section-title">Tentang Historical Jogja</h2>
               <div class="about-grid">
                   <div class="about-card mission-card">
                       <div class="card-icon">
                           <i class="fas fa-bullseye"></i>
                       </div>
                       <h3>Misi Kami</h3>
                       <p>Melestarikan dan memperkenalkan kekayaan sejarah Yogyakarta melalui platform digital yang informatif dan interaktif.</p>
                   </div>

                   <div class="about-card features-card">
                       <div class="card-icon">
                           <i class="fas fa-star"></i>
                       </div>
                       <h3>Fitur Utama</h3>
                       <ul class="features-list">
                           <li><i class="fas fa-check"></i> Informasi detail bangunan bersejarah</li>
                           <li><i class="fas fa-check"></i> Galeri foto berkualitas tinggi</li>
                           <li><i class="fas fa-check"></i> Timeline sejarah interaktif</li>
                           <li><i class="fas fa-check"></i> Kuis edukatif</li>
                           <li><i class="fas fa-check"></i> Peta wisata sejarah</li>
                       </ul>
                   </div>
                   <div class="about-card tech-card">
                       <div class="card-icon">
                           <i class="fas fa-code"></i>
                       </div>
                       <h3>Teknologi</h3>
                       <p>Dibangun menggunakan teknologi web modern untuk memberikan pengalaman pengguna yang optimal dan responsif.</p>
                   </div>
               </div>
           </div>
       </section>
   </main>

   <!-- Footer -->
   <footer class="site-footer">
       <div class="footer-waves">
           <svg class="waves" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
               <path fill="#914e18" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,165.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
           </svg>
       </div>
       
       <div class="footer-content">
           <div class="footer-grid">
               <div class="footer-section">
                   <h3 class="footer-title">Historical Jogja</h3>
                   <p class="footer-desc">Melestarikan Warisan Sejarah Yogyakarta melalui teknologi digital yang inovatif.</p>
                   <div class="footer-social">
                       <a href="#" class="social-link" title="Facebook">
                           <i class="fab fa-facebook-f"></i>
                       </a>
                       <a href="#" class="social-link" title="Instagram">
                           <i class="fab fa-instagram"></i>
                       </a>
                       <a href="#" class="social-link" title="Twitter">
                           <i class="fab fa-twitter"></i>
                       </a>
                       <a href="#" class="social-link" title="YouTube">
                           <i class="fab fa-youtube"></i>
                       </a>
                   </div>
               </div>
               <div class="footer-section">
                   <h3 class="footer-title">Tautan Cepat</h3>
                   <ul class="footer-links">
                       <li><a href="beranda.php">Beranda</a></li>
                       <li><a href="bbsejarah.php">Bangunan Bersejarah</a></li>
                       <li><a href="gallery.php">Galeri</a></li>
                       <li><a href="timeline.php">Timeline</a></li>
                       <li><a href="quiz.php">Kuis</a></li>
                   </ul>
               </div>

               <div class="footer-section">
                   <h3 class="footer-title">Kontak</h3>
                   <ul class="footer-contact">
                       <li>
                           <i class="fas fa-map-marker-alt"></i>
                           <span>Jl. Malioboro No. 123, Yogyakarta</span>
                       </li>
                       <li>
                           <i class="fas fa-phone"></i>
                           <span>+62 274 123456</span>
                       </li>
                       <li>
                           <i class="fas fa-envelope"></i>
                           <span>info@historicaljogja.com</span>
                       </li>
                   </ul>
               </div>

               <div class="footer-section">
                   <h3 class="footer-title">Newsletter</h3>
                   <form class="newsletter-form">
                       <div class="form-group">
                           <input type="email" placeholder="Masukkan email Anda" required>
                           <button type="submit">
                               <i class="fas fa-arrow-right"></i>
                           </button>
                       </div>
                   </form>
               </div>
           </div>
       </div>

       <div class="footer-bottom">
           <p>&copy; <?php echo date('Y'); ?> Historical Jogja. All rights reserved.</p>
       </div>
   </footer>

   <!-- Scripts -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.0/vanilla-tilt.min.js"></script>
   <script>
       // Initialize Tilt Effect for Team Cards
       VanillaTilt.init(document.querySelectorAll(".team-card"), {
           max: 15,
           speed: 400,
           glare: true,
           "max-glare": 0.2
       });

       // Form Validation dan Animation
       document.addEventListener('DOMContentLoaded', function() {
           const form = document.getElementById('contact-form');
           const inputs = form.querySelectorAll('.form-input');
           const submitButton = form.querySelector('.submit-button');
           const messageInput = document.getElementById('pesan');

           // Input animation and validation
           inputs.forEach(input => {
               input.addEventListener('focus', () => {
                   input.parentElement.classList.add('focused');
               });

               input.addEventListener('blur', () => {
                   if (!input.value) {
                       input.parentElement.classList.remove('focused');
                   }
                   validateInput(input);
               });

               input.addEventListener('input', () => validateInput(input));
           });

           // Handle submit form
           form.addEventListener('submit', async function(e) {
               e.preventDefault();
               
               if (!validateForm()) return;

               submitButton.classList.add('loading');
               
               try {
                   const formData = new FormData(form);
                   const response = await fetch(form.action, {
                       method: 'POST',
                       body: formData
                   });

                   if (response.ok) {
                       showFeedback('success', 'Pesan berhasil terkirim!');
                       form.reset();
                   } else {
                       throw new Error('Network response was not ok');
                   }
               } catch (error) {
                   showFeedback('error', 'Terjadi kesalahan. Silakan coba lagi.');
               } finally {
                   submitButton.classList.remove('loading');
               }
           });

           // Fungsi validasi input
           function validateInput(input) {
               const value = input.value.trim();
               let isValid = true;
               let errorMessage = '';

               switch(input.type) {
                   case 'email':
                       isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                       errorMessage = 'Email tidak valid';
                       break;
                   case 'text':
                       isValid = value.length >= 3;
                       errorMessage = 'Minimal 3 karakter';
                       break;
                   default:
                       isValid = value.length >= 10;
                       errorMessage = 'Minimal 10 karakter';
               }

               const indicator = input.parentElement.querySelector('.form-indicator');
               indicator.textContent = isValid ? '' : errorMessage;
               input.parentElement.classList.toggle('error', !isValid);
               input.parentElement.classList.toggle('valid', isValid);

               return isValid;
           }

           // Fungsi validasi seluruh form
           function validateForm() {
               let isValid = true;
               inputs.forEach(input => {
                   if (!validateInput(input)) isValid = false;
               });
               return isValid;
           }

           // Fungsi menampilkan pesan feedback
           function showFeedback(type, message) {
               const feedback = document.createElement('div');
               feedback.className = `feedback-message feedback-${type}`;
               feedback.innerHTML = `
                   <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                   <p>${message}</p>
               `;
               
               form.parentElement.insertBefore(feedback, form);
               
               setTimeout(() => {
                   feedback.remove();
               }, 5000);
           }
       });

       // Scroll Reveal Animation
       const scrollElements = document.querySelectorAll("[data-scroll-reveal]");

       const elementInView = (el, dividend = 1) => {
           const elementTop = el.getBoundingClientRect().top;
           return (
               elementTop <=
               (window.innerHeight || document.documentElement.clientHeight) / dividend
           );
       };

       const displayScrollElement = (element) => {
           element.classList.add("scrolled");
       };

       const hideScrollElement = (element) => {
           element.classList.remove("scrolled");
       };

       const handleScrollAnimation = () => {
           scrollElements.forEach((el) => {
               if (elementInView(el, 1.25)) {
                   displayScrollElement(el);
               } else {
                   hideScrollElement(el);
               }
           });
       }

       window.addEventListener("scroll", () => {
           handleScrollAnimation();
       });

       // Initialize scroll reveal on load
       handleScrollAnimation();

       // About Cards Animation
       document.addEventListener('DOMContentLoaded', function() {
           const aboutCards = document.querySelectorAll('.about-card');
           
           const aboutObserver = new IntersectionObserver((entries) => {
               entries.forEach(entry => {
                   if (entry.isIntersecting) {
                       entry.target.classList.add('visible');
                   }
               });
           }, {
               threshold: 0.2
           });

           aboutCards.forEach(card => {
               aboutObserver.observe(card);
           });
       });
   </script>
   <!-- Pindahkan ke akhir file, sebelum closing body -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</body>
</html>