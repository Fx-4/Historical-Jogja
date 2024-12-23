
<?php
require_once '../config/db-connect.php';

// Database connection
$db = new Database();
$conn = $db->connect();

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

?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Timeline Sejarah</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/timeline.css" />
    <link rel="stylesheet" href="../assets/css/navbar/primary-nav.css">
    <link rel="stylesheet" href="../assets/css/navbar/mobile-nav.css">
    <link rel="stylesheet" href="<?php echo __DIR__ . '/../assets/css/footer-waves.css'; ?>">
  </head>
  <body>
    <!-- Layar Intro -->
    <div class="intro-screen">
      <h1 class="intro-title">Selamat Datang di Timeline Sejarah</h1>
      <button class="begin-btn">Mulai Perjalanan</button>
    </div>

    <!-- Layar Timeline -->
    <div class="timeline-screen">
    <?php include_once '../components/navbar/PrimaryNav.php'; ?>
  <?php include_once '../components/navbar/MobileNav.php'; ?>


      <!-- Navigasi Tahun -->
      <div class="year-nav">
        <div class="btn-conteiner">
          <a href="#" class="btn-content" id="prevYear">
            <span class="icon-arrow">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 66 43"
                width="30px"
                height="30px"
                style="transform: rotate(180deg)"
              >
                <g
                  fill="none"
                  stroke="none"
                  stroke-width="1"
                  fill-rule="evenodd"
                >
                  <path
                    id="arrow-icon-one"
                    fill="#9ee5fa"
                    d="M40.1543933,3.89485454 L43.9763149,0.139296592 C44.1708311,-0.0518420739 44.4826329,-0.0518571125 44.6771675,0.139262789 L65.6916134,20.7848311 C66.0855801,21.1718824 66.0911863,21.8050225 65.704135,22.1989893 C65.7000188,22.2031791 65.6958657,22.2073326 65.6916762,22.2114492 L44.677098,42.8607841 C44.4825957,43.0519059 44.1708242,43.0519358 43.9762853,42.8608513 L40.1545186,39.1069479 C39.9575152,38.9134427 39.9546793,38.5968729 40.1481845,38.3998695 C40.1502893,38.3977268 40.1524132,38.395603 40.1545562,38.3934985 L56.9937789,21.8567812 C57.1908028,21.6632968 57.193672,21.3467273 57.0001876,21.1497035 C56.9980647,21.1475418 56.9959223,21.1453995 56.9937605,21.1432767 L40.1545208,4.60825197 C39.9574869,4.41477773 39.9546013,4.09820839 40.1480756,3.90117456 C40.1501626,3.89904911 40.1522686,3.89694235 40.1543933,3.89485454 Z"
                  ></path>
                  <path
                    id="arrow-icon-two"
                    fill="#9ee5fa"
                    d="M20.1543933,3.89485454 L23.9763149,0.139296592 C24.1708311,-0.0518420739 24.4826329,-0.0518571125 24.6771675,0.139262789 L45.6916134,20.7848311 C46.0855801,21.1718824 46.0911863,21.8050225 45.704135,22.1989893 C45.7000188,22.2031791 45.6958657,22.2073326 45.6916762,22.2114492 L24.677098,42.8607841 C24.4825957,43.0519059 24.1708242,43.0519358 23.9762853,42.8608513 L20.1545186,39.1069479 C19.9575152,38.9134427 19.9546793,38.5968729 20.1481845,38.3998695 C20.1502893,38.3977268 20.1524132,38.395603 20.1545562,38.3934985 L36.9937789,21.8567812 C37.1908028,21.6632968 37.193672,21.3467273 37.0001876,21.1497035 C36.9980647,21.1475418 36.9959223,21.1453995 36.9937605,21.1432767 L20.1545208,4.60825197 C19.9574869,4.41477773 19.9546013,4.09820839 20.1480756,3.90117456 C20.1501626,3.89904911 20.1522686,3.89694235 20.1543933,3.89485454 Z"
                  ></path>
                  <path
                    id="arrow-icon-three"
                    fill="#9ee5fa"
                    d="M0.154393339,3.89485454 L3.97631488,0.139296592 C4.17083111,-0.0518420739 4.48263286,-0.0518571125 4.67716753,0.139262789 L25.6916134,20.7848311 C26.0855801,21.1718824 26.0911863,21.8050225 25.704135,22.1989893 C25.7000188,22.2031791 25.6958657,22.2073326 25.6916762,22.2114492 L4.67709797,42.8607841 C4.48259567,43.0519059 4.17082418,43.0519358 3.97628526,42.8608513 L0.154518591,39.1069479 C-0.0424848215,38.9134427 -0.0453206733,38.5968729 0.148184538,38.3998695 C0.150289256,38.3977268 0.152413239,38.395603 0.154556228,38.3934985 L16.9937789,21.8567812 C17.1908028,21.6632968 17.193672,21.3467273 17.0001876,21.1497035 C16.9980647,21.1475418 16.9959223,21.1453995 16.9937605,21.1432767 L0.15452076,4.60825197 C-0.0425130651,4.41477773 -0.0453986756,4.09820839 0.148075568,3.90117456 C0.150162624,3.89904911 0.152268631,3.89694235 0.154393339,3.89485454 Z"
                  ></path>
                </g>
              </svg>
            </span>
          </a>
        </div>
        <span class="year">1945</span>
        <div class="btn-conteiner">
          <a href="#" class="btn-content" id="nextYear">
            <span class="icon-arrow">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 66 43"
                width="30px"
                height="30px"
              >
                <g
                  fill="none"
                  stroke="none"
                  stroke-width="1"
                  fill-rule="evenodd"
                >
                  <path
                    id="arrow-icon-one"
                    fill="#9ee5fa"
                    d="M40.1543933,3.89485454 L43.9763149,0.139296592 C44.1708311,-0.0518420739 44.4826329,-0.0518571125 44.6771675,0.139262789 L65.6916134,20.7848311 C66.0855801,21.1718824 66.0911863,21.8050225 65.704135,22.1989893 C65.7000188,22.2031791 65.6958657,22.2073326 65.6916762,22.2114492 L44.677098,42.8607841 C44.4825957,43.0519059 44.1708242,43.0519358 43.9762853,42.8608513 L40.1545186,39.1069479 C39.9575152,38.9134427 39.9546793,38.5968729 40.1481845,38.3998695 C40.1502893,38.3977268 40.1524132,38.395603 40.1545562,38.3934985 L56.9937789,21.8567812 C57.1908028,21.6632968 57.193672,21.3467273 57.0001876,21.1497035 C56.9980647,21.1475418 56.9959223,21.1453995 56.9937605,21.1432767 L40.1545208,4.60825197 C39.9574869,4.41477773 39.9546013,4.09820839 40.1480756,3.90117456 C40.1501626,3.89904911 40.1522686,3.89694235 40.1543933,3.89485454 Z"
                  ></path>
                  <path
                    id="arrow-icon-two"
                    fill="#9ee5fa"
                    d="M20.1543933,3.89485454 L23.9763149,0.139296592 C24.1708311,-0.0518420739 24.4826329,-0.0518571125 24.6771675,0.139262789 L45.6916134,20.7848311 C46.0855801,21.1718824 46.0911863,21.8050225 45.704135,22.1989893 C45.7000188,22.2031791 45.6958657,22.2073326 45.6916762,22.2114492 L24.677098,42.8607841 C24.4825957,43.0519059 24.1708242,43.0519358 23.9762853,42.8608513 L20.1545186,39.1069479 C19.9575152,38.9134427 19.9546793,38.5968729 20.1481845,38.3998695 C20.1502893,38.3977268 20.1524132,38.395603 20.1545562,38.3934985 L36.9937789,21.8567812 C37.1908028,21.6632968 37.193672,21.3467273 37.0001876,21.1497035 C36.9980647,21.1475418 36.9959223,21.1453995 36.9937605,21.1432767 L20.1545208,4.60825197 C19.9574869,4.41477773 19.9546013,4.09820839 20.1480756,3.90117456 C20.1501626,3.89904911 20.1522686,3.89694235 20.1543933,3.89485454 Z"
                  ></path>
                  <path
                    id="arrow-icon-three"
                    fill="#9ee5fa"
                    d="M0.154393339,3.89485454 L3.97631488,0.139296592 C4.17083111,-0.0518420739 4.48263286,-0.0518571125 4.67716753,0.139262789 L25.6916134,20.7848311 C26.0855801,21.1718824 26.0911863,21.8050225 25.704135,22.1989893 C25.7000188,22.2031791 25.6958657,22.2073326 25.6916762,22.2114492 L4.67709797,42.8607841 C4.48259567,43.0519059 4.17082418,43.0519358 3.97628526,42.8608513 L0.154518591,39.1069479 C-0.0424848215,38.9134427 -0.0453206733,38.5968729 0.148184538,38.3998695 C0.150289256,38.3977268 0.152413239,38.395603 0.154556228,38.3934985 L16.9937789,21.8567812 C17.1908028,21.6632968 17.193672,21.3467273 17.0001876,21.1497035 C16.9980647,21.1475418 16.9959223,21.1453995 16.9937605,21.1432767 L0.15452076,4.60825197 C-0.0425130651,4.41477773 -0.0453986756,4.09820839 0.148075568,3.90117456 C0.150162624,3.89904911 0.152268631,3.89694235 0.154393339,3.89485454 Z"
                  ></path>
                </g>
              </svg>
            </span>
          </a>
        </div>
      </div>

      <!-- Konten Utama -->
      <main>
        <div class="timeline-content"></div>
      </main>

        <!-- Footer -->
        <footer class="site-footer">
        <!-- Wave Animation -->
        <div class="footer-waves-wrapper">
            <?php 
            $svgPath = __DIR__ . '/../components/footer/footer-waves-svg.php';
            if (file_exists($svgPath)) {
                include $svgPath;
            }
            ?>
        </div>

    <!-- Footer Content -->
    <div class="footer-content">
        <div class="footer-grid">
            <!-- Company Info Section -->
            <div class="footer-section">
                <h3 class="footer-title">Historical Jogja</h3>
                <p class="footer-desc">Melestarikan Warisan Sejarah Yogyakarta melalui teknologi digital yang inovatif.</p>
                <div class="footer-social">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <!-- Quick Links Section -->
            <div class="footer-section">
                <h3 class="footer-title">Tautan Cepat</h3>
                <ul class="footer-links">
                <li><a href="beranda.php">Beranda</a></li>
                    <li><a href="bbsejarah.php">Bangunan Bersejarah</a></li>
                    <li><a href="gallery.php">Galeri</a></li>
                    <li><a href="timeline.php">Timeline</a></li>
                    <li><a href="kuis.php">Kuis</a></li>
                    <li><a href="petawisata.php">Peta</a></li>
                    <li><a href="kontak.php">Kontak</a></li>
                </ul>
            </div>

            <!-- Contact Section -->
            <div class="footer-section">
                <h3 class="footer-title">Kontak</h3>
                <ul class="footer-contact">
                    <li><i class="fas fa-map-marker-alt"></i> Jl. Malioboro No. 123, Yogyakarta</li>
                    <li><i class="fas fa-phone"></i> +62 274 123456</li>
                    <li><i class="fas fa-envelope"></i> info@historicaljogja.com</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Historical Jogja. All rights reserved.</p>
    </div>
</footer>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const introScreen = document.querySelector(".intro-screen");
        const timelineScreen = document.querySelector(".timeline-screen");
        const beginBtn = document.querySelector(".begin-btn");
        const yearNav = document.querySelector(".year-nav");
        const yearDisplay = document.querySelector(".year");
        const prevYearBtn = document.getElementById("prevYear");
        const nextYearBtn = document.getElementById("nextYear");
        const timelineContent = document.querySelector(".timeline-content");

        let currentYear = 1945;

        // Data template untuk setiap tahun
        const timelineData = {
          1755: {
            judul: "Pendirian Kasultanan Ngayogyakarta Hadiningrat",
            gambar: "Pendirian Kasultanan Ngayogyakarta Hadiningrat.jpeg",
            deskripsi:
              "Pada tanggal 13 Februari 1755, melalui Perjanjian Giyanti, Kerajaan Mataram Islam resmi terbagi menjadi dua: Kasunanan Surakarta dan Kasultanan Yogyakarta. Pangeran Mangkubumi yang kemudian bergelar Sultan Hamengku Buwono I memimpin pembangunan Keraton Yogyakarta sebagai pusat pemerintahan baru. Pemilihan lokasi keraton dilakukan dengan pertimbangan spiritual dan strategis, yaitu berada di antara dua sungai besar: Code dan Winongo.",
            infoTambahan:
              "Pembangunan Keraton Yogyakarta dimulai dengan ritual penanaman pohon beringin kembar di alun-alun utara yang masih berdiri hingga saat ini. Kompleks keraton dibangun dengan konsep kosmologi Jawa yang rumit, mencakup tata letak yang menggambarkan perjalanan hidup manusia dari lahir hingga mencapai kesempurnaan. Struktur keraton terdiri dari tujuh kompleks utama yang melambangkan tujuh tingkatan pencapaian spiritual dalam falsafah Jawa. Sultan Hamengku Buwono I juga membangun sistem pertahanan dengan mendirikan benteng Baluwerti yang mengelilingi keraton beserta parit-paritnya.",
            faktaMenarik:
              "1. Keraton Yogyakarta dibangun mengikuti sumbu imajiner yang menghubungkan Pantai Parangtritis, Keraton, Tugu Yogyakarta, dan Gunung Merapi. Garis ini diyakini memiliki makna spiritual mendalam.\n2. Pembangunan keraton hanya memakan waktu satu tahun, sebuah pencapaian luar biasa mengingat kompleksitas dan ukurannya.\n3. Setiap bangunan dalam kompleks keraton memiliki makna filosofis, seperti Bangsal Kencana yang melambangkan singgasana Tuhan.\n4. Sultan HB I menerapkan sistem pembagian wilayah yang disebut Negaragung, yang membagi wilayah ke dalam lingkaran-lingkaran konsentris dengan keraton sebagai pusatnya.",
          },
          1813: {
            judul: "Pembentukan Kadipaten Pakualaman",
            gambar: "Pembentukan Kadipaten Pakualaman.jpeg",
            deskripsi:
              "Kadipaten Pakualaman didirikan pada tahun 1813 di bawah pemerintahan kolonial Inggris yang saat itu dipimpin oleh Thomas Stamford Raffles. Pendirian ini merupakan hasil dari dinamika politik kompleks antara Kasultanan Yogyakarta, pemerintah kolonial, dan pangeran Notokusumo (putra Sultan Hamengku Buwono I) yang kemudian menjadi Adipati Paku Alam I. Kadipaten ini memiliki wilayah tersendiri dan sistem pemerintahan yang semi-otonom.",
            infoTambahan:
              "Puro Pakualaman dibangun dengan arsitektur yang memadukan gaya Jawa tradisional dan pengaruh Eropa, mencerminkan era transisi budaya saat itu. Kompleks Puro Pakualaman dilengkapi dengan perpustakaan yang menyimpan naskah-naskah kuno dan benda-benda bersejarah. Kadipaten ini memiliki struktur pemerintahan yang terorganisir, termasuk sistem administrasi dan peradilan sendiri. Para Adipati Pakualaman dikenal sebagai pemimpin yang memiliki visi progresif dalam pendidikan dan kebudayaan.",
            faktaMenarik:
              "1. Paku Alam I adalah seorang sastrawan yang menulis berbagai karya sastra penting, termasuk Serat Jatipusaka.\n2. Perpustakaan Puro Pakualaman menyimpan koleksi naskah kuno yang sangat berharga, termasuk manuskrip tentang sejarah, sastra, dan falsafah Jawa.\n3. Kadipaten Pakualaman memiliki pasukan militer sendiri yang disebut Legion Pakualaman.\n4. Hubungan diplomatik Pakualaman dengan pemerintah kolonial membantu melindungi kepentingan budaya dan pendidikan di Yogyakarta.",
          },
          1945: {
            judul: "Amanat Sri Sultan HB IX dan Dukungan terhadap RI",
            gambar: "amanat-sultan.jpg",
            deskripsi:
              "Pada tanggal 5 September 1945, Sultan Hamengku Buwono IX dan Paku Alam VIII mengeluarkan maklumat bersejarah yang menyatakan bahwa Kasultanan Yogyakarta dan Kadipaten Pakualaman menjadi bagian integral dari Republik Indonesia. Keputusan ini merupakan langkah berani dan visioner yang sangat mempengaruhi perjalanan sejarah Indonesia. Yogyakarta kemudian mendapat status sebagai Daerah Istimewa, sebuah pengakuan atas kontribusi dan keunikan sistem pemerintahannya.",
            infoTambahan:
              "Sultan HB IX melakukan berbagai perubahan progresif dalam sistem pemerintahan Yogyakarta, termasuk reformasi tanah dengan membagikan tanah lungguh kepada rakyat. Keraton dibuka untuk kepentingan pendidikan dan kebudayaan, mengubah paradigma keraton dari pusat kekuasaan menjadi pusat kebudayaan. Sultan juga mendukung penuh perjuangan kemerdekaan dengan menyediakan dana dan fasilitas bagi pejuang. Paku Alam VIII bekerja sama erat dengan Sultan dalam menjalankan pemerintahan dan mendukung perjuangan kemerdekaan.",
            faktaMenarik:
              "1. Sultan HB IX adalah satu-satunya raja di Indonesia yang memiliki gelar akademis dari Belanda saat dinobatkan.\n2. Keputusan bergabung dengan RI diambil hanya 18 hari setelah Proklamasi Kemerdekaan.\n3. Sultan HB IX menolak tawaran Belanda untuk menjadi Raja seluruh Jawa.\n4. Yogyakarta adalah satu-satunya wilayah yang mendapat status 'istimewa' dalam konteks modern Indonesia.\n5. Sultan HB IX secara aktif terlibat dalam diplomasi internasional untuk mendukung kemerdekaan Indonesia.",
          },
          1946: {
            judul: "Yogyakarta sebagai Ibukota RI",
            gambar: "ibukota-yogya.jpg",
            deskripsi:
              "Ketika Jakarta berada dalam ancaman pendudukan Belanda, Yogyakarta menjadi ibukota Republik Indonesia dari Januari 1946 hingga Desember 1949. Periode ini menjadi salah satu masa paling kritis dalam sejarah perjuangan kemerdekaan Indonesia. Presiden Soekarno, Wakil Presiden Mohammad Hatta, dan sejumlah pejabat tinggi negara menjalankan pemerintahan dari Yogyakarta. Kota ini menjadi pusat diplomasi dan perjuangan mempertahankan kemerdekaan.",
            infoTambahan:
              "Gedung-gedung bersejarah di Yogyakarta dialihfungsikan menjadi kantor pemerintahan. Gedung Agung (dahulu Istana Presiden) menjadi tempat tinggal dan kantor Presiden Soekarno. Kepatihan menjadi kantor berbagai kementerian. Sekolah Tinggi Teknik (sekarang UGM) menjadi tempat sidang KNIP. Keraton Yogyakarta menyediakan berbagai fasilitas untuk mendukung pemerintahan RI, termasuk tempat perlindungan bagi para pejuang. Masyarakat Yogyakarta secara aktif mendukung perjuangan dengan menyediakan logistik dan tempat persembunyian bagi para pejuang.",
            faktaMenarik:
              "1. Banyak pertemuan diplomatik penting terjadi di Yogyakarta, termasuk perundingan dengan pihak internasional.\n2. Uang Republik Indonesia pertama kali dicetak di Yogyakarta.\n3. Radio Republik Indonesia (RRI) Yogyakarta menjadi corong perjuangan yang vital.\n4. Surat kabar 'Kedaulatan Rakjat' menjadi media perjuangan yang penting.\n5. Sistem 'pos gerilya' dikembangkan di Yogyakarta untuk menjaga komunikasi perjuangan.",
          },
          1949: {
            judul: "Serangan Umum 1 Maret",
            gambar: "serangan-umum.jpg",
            deskripsi:
              "Serangan Umum 1 Maret 1949 adalah operasi militer bersejarah yang dipimpin oleh Letkol Soeharto. Selama 6 jam, pasukan TNI berhasil menduduki kota Yogyakarta yang saat itu dikuasai Belanda. Serangan ini membuktikan kepada dunia internasional bahwa TNI dan Republik Indonesia masih memiliki kekuatan untuk melawan, membantah klaim Belanda bahwa RI telah hancur. Peristiwa ini menjadi salah satu faktor penting yang memperkuat posisi Indonesia dalam perundingan internasional.",
            infoTambahan:
              "Persiapan serangan dilakukan dengan sangat cermat, melibatkan koordinasi antara pasukan TNI, Sultan HB IX, dan warga sipil. Strategi serangan disusun di Desa Wetah, Bantul. Pasukan dibagi menjadi beberapa kelompok yang menyerang dari berbagai arah. Serangan dimulai tepat pukul 06.00 WIB dan berhasil menduduki kota selama 6 jam. Warga sipil berperan penting dalam memberikan informasi intelijen dan dukungan logistik. Setelah serangan, pasukan TNI melakukan taktik gerilya untuk menghindari serangan balik Belanda.",
            faktaMenarik:
              "1. Sultan HB IX memainkan peran krusial dalam menyediakan informasi tentang posisi Belanda.\n2. Bendera Merah Putih dikibarkan di berbagai tempat strategis selama pendudukan 6 jam.\n3. Serangan ini dikoordinasikan dengan pemberitaan radio untuk efek propaganda internasional.\n4. Dokumentasi foto serangan ini menjadi bukti penting di forum internasional.\n5. Strategi serangan ini dipelajari di berbagai akademi militer sebagai contoh taktik gerilya yang sukses.",
          },
          1989: {
            judul: "Yogyakarta sebagai Kota Pendidikan",
            gambar: "kota-pendidikan.jpg",
            deskripsi:
              "Tahun 1989 menandai pengukuhan resmi Yogyakarta sebagai kota pendidikan terkemuka di Indonesia. Hal ini merupakan kulminasi dari sejarah panjang perkembangan pendidikan di kota ini, dimulai dari berdirinya UGM pada 1949. Yogyakarta menjadi magnet bagi pelajar dan mahasiswa dari seluruh Indonesia, menjadikannya salah satu pusat pengembangan ilmu pengetahuan dan kebudayaan terpenting di negeri ini.",
            infoTambahan:
              "Universitas Gadjah Mada, sebagai universitas pertama yang didirikan di Yogyakarta, menjadi model bagi pengembangan institusi pendidikan tinggi lainnya. Berbagai perguruan tinggi negeri dan swasta kemudian didirikan, masing-masing dengan kekhasan dan keunggulannya. Institut Seni Indonesia Yogyakarta menjadi pusat pendidikan seni yang terkemuka. UIN Sunan Kalijaga menjadi pusat studi Islam yang berpengaruh. Pengembangan infrastruktur pendidikan termasuk perpustakaan, laboratorium, dan pusat penelitian terus dilakukan. Interaksi antara dunia akademik dengan keraton dan budaya lokal menciptakan lingkungan belajar yang unik.",
            faktaMenarik:
              "1. Yogyakarta memiliki rasio mahasiswa terhadap penduduk tertinggi di Indonesia.\n2. Kota ini memiliki lebih dari 100 perguruan tinggi.\n3. Banyak tokoh nasional dan internasional yang pernah menempuh pendidikan di Yogyakarta.\n4. Perpustakaan dan museum di Yogyakarta menyimpan koleksi naskah dan artefak yang sangat berharga.\n5. Yogyakarta menjadi tempat lahirnya berbagai gerakan mahasiswa yang berpengaruh dalam sejarah Indonesia.",
          },
          2012: {
            judul: "Penetapan UU Keistimewaan Yogyakarta",
            gambar: "uu-keistimewaan.jpg",
            deskripsi:
              "UU No. 13 Tahun 2012 tentang Keistimewaan Daerah Istimewa Yogyakarta menjadi tonggak penting dalam sejarah modern Yogyakarta. Undang-undang ini mengatur secara khusus tata cara pengisian jabatan, kedudukan, tugas, dan wewenang Gubernur dan Wakil Gubernur DIY. Penetapan ini menegaskan status istimewa Yogyakarta dalam kerangka NKRI modern, sekaligus mengakui peran historis Kasultanan dan Pakualaman dalam pembentukan Indonesia.",
            infoTambahan:
              "UU Keistimewaan mengatur lima urusan keistimewaan: tata cara pengisian jabatan Gubernur dan Wakil Gubernur, kelembagaan Pemerintah DIY, kebudayaan, pertanahan, dan tata ruang. Dana Keistimewaan (Danais) dialokasikan khusus untuk menjalankan urusan keistimewaan tersebut. Undang-undang ini juga mengatur peran Keraton dan Pakualaman dalam pelestarian budaya dan nilai-nilai tradisional. Sistem ini menciptakan model unik dalam tata kelola pemerintahan yang menggabungkan unsur tradisional dan modern.",
            faktaMenarik:
              "1. Proses pembahasan UU ini memakan waktu lebih dari 10 tahun.\n2. UU ini mengakui tanah Keraton (Sultan Ground) dan tanah Pakualaman (Pakualaman Ground).\n3. Yogyakarta adalah satu-satunya daerah di Indonesia dengan sistem penetapan (bukan pemilihan) gubernur.\n4. Dana Keistimewaan memberi kesempatan untuk pengembangan budaya dan pelestarian warisan sejarah.\n5. Model pemerintahan Yogyakarta menjadi contoh unik perpaduan sistem monarki dan demokrasi modern dalam NKRI.",
          },
          2015: {
            judul: "Sabdatama dan Perubahan Tata Nilai Kasultanan",
            gambar: "sabdatama-2015.jpg",
            deskripsi:
              "Tahun 2015 menandai perubahan fundamental dalam sejarah Kasultanan Yogyakarta ketika Sultan Hamengku Buwono X mengeluarkan Sabdatama yang mengubah tata nilai dan aturan suksesi Kasultanan. Perubahan ini membuka kemungkinan bagi perempuan untuk memimpin Kasultanan, sebuah pergeseran dramatis dari tradisi sebelumnya yang hanya memperbolehkan laki-laki menjadi Sultan. Peristiwa ini menandai adaptasi institusi kerajaan terhadap nilai-nilai modern sambil tetap mempertahankan esensi budaya Jawa.",
            infoTambahan:
              "Sabdatama ini mengubah gelar putri tertua Sultan menjadi GKR Mangkubumi dengan gelar lengkap Gusti Kanjeng Ratu Mangkubumi Hamemayu Hayuning Bawana Langgeng ing Mataram. Perubahan ini menimbulkan berbagai tanggapan dari keluarga Keraton dan masyarakat luas. Sultan juga melakukan perubahan administratif dalam struktur internal Keraton untuk mengakomodasi sistem baru ini. Modernisasi dalam Keraton terus berlanjut dengan digitalisasi arsip dan dokumentasi Keraton, serta pengembangan sistem manajemen modern untuk pengelolaan aset budaya.",
            faktaMenarik:
              "1. Ini adalah perubahan terbesar dalam sistem suksesi Kasultanan sejak berdirinya pada 1755.\n2. Perubahan ini mencerminkan adaptasi nilai-nilai kesetaraan gender dalam institusi tradisional.\n3. Digitalisasi arsip Keraton melibatkan lebih dari 1 juta dokumen sejarah.\n4. Keraton mengembangkan program pendidikan khusus untuk mempersiapkan generasi penerus.\n5. Sistem administrasi baru Keraton menggabungkan praktik tradisional dengan manajemen modern.",
          },
          2020: {
            judul: "Yogyakarta di Era Digital dan Pandemi",
            gambar: "yogya-digital-era.jpg",
            deskripsi:
              "Tahun 2020 menjadi tahun yang mengubah wajah Yogyakarta secara signifikan dengan datangnya pandemi COVID-19 dan akselerasi transformasi digital. Kota budaya dan pendidikan ini menunjukkan ketangguhannya dalam beradaptasi dengan situasi baru, mengembangkan berbagai inovasi dalam pendidikan, pariwisata, dan pelestarian budaya melalui platform digital. Keraton dan institusi budaya lainnya mengadopsi teknologi untuk tetap menjalankan fungsinya sebagai pusat pelestarian budaya.",
            infoTambahan:
              "Yogyakarta mengembangkan sistem pembelajaran daring yang inovatif untuk mempertahankan posisinya sebagai kota pendidikan. Museum dan situs budaya mengadopsi teknologi virtual reality untuk tetap bisa dikunjungi secara virtual. Sektor UMKM mendapat dukungan untuk bertransformasi ke platform digital. Program 'Jogja Smart City' dipercepat implementasinya untuk mendukung pelayanan publik di masa pandemi. Keraton mengembangkan platform digital untuk mempertahankan hubungan dengan masyarakat dan melanjutkan peran kulturalnya.",
            faktaMenarik:
              "1. Lebih dari 100 festival budaya dialihkan ke format digital selama pandemi.\n2. Yogyakarta menjadi pioneer dalam pengembangan museum virtual di Indonesia.\n3. Sistem pembelajaran daring Yogyakarta menjadi model bagi kota-kota lain.\n4. Tingkat adopsi teknologi digital UMKM Yogyakarta mencapai 80% selama pandemi.\n5. Platform 'Virtual Keraton Tour' dikembangkan untuk memperkenalkan budaya Keraton secara digital.",
          },
          2023: {
            judul: "Revitalisasi Malioboro dan Pembangunan Berkelanjutan",
            gambar: "malioboro-2023.jpg",
            deskripsi:
              "Tahun 2023 ditandai dengan selesainya proyek revitalisasi Malioboro yang mengubah ikon wisata ini menjadi area pedestrian modern dengan tetap mempertahankan nilai historisnya. Proyek ini merupakan bagian dari visi pembangunan berkelanjutan Yogyakarta yang mengedepankan keseimbangan antara modernisasi, pelestarian budaya, dan kelestarian lingkungan. Revitalisasi ini menjadi model bagaimana kota bersejarah dapat berkembang tanpa kehilangan karakternya.",
            infoTambahan:
              "Revitalisasi meliputi penataan PKL, sistem transportasi terintegrasi, dan infrastruktur ramah lingkungan. Program Smart Heritage City dikembangkan untuk mengintegrasikan teknologi dalam pelestarian warisan budaya. Pembangunan jalur sepeda dan area hijau memperkuat komitmen terhadap transportasi berkelanjutan. Sistem manajemen sampah modern diimplementasikan untuk mendukung program zero waste. Pengembangan ekonomi kreatif diprioritaskan untuk mendukung industri budaya dan pariwisata.",
            faktaMenarik:
              "1. Malioboro menjadi area pedestrian terpanjang di Indonesia.\n2. Sistem transportasi terintegrasi menghubungkan 12 destinasi wisata utama.\n3. 70% PKL Malioboro telah ditata dalam sentra kuliner modern.\n4. Area hijau di Yogyakarta meningkat 30% dalam 5 tahun terakhir.\n5. Program Smart Heritage City Yogyakarta menjadi percontohan UNESCO.",
          },
          2024: {
            judul: "Yogyakarta Menuju Kota Budaya Global",
            gambar: "yogya-global-2024.jpg",
            deskripsi:
              "Di tahun 2024, Yogyakarta semakin mengukuhkan posisinya sebagai kota budaya berkelas dunia dengan berbagai pencapaian di bidang seni, pendidikan, dan pelestarian warisan budaya. Kota ini berhasil memadukan nilai-nilai tradisional dengan tuntutan modernitas, menciptakan model pembangunan yang unik dan berkelanjutan. Pengakuan internasional terhadap upaya pelestarian budaya dan inovasi dalam pendidikan membawa Yogyakarta ke level baru dalam perkembangan kota budaya global.",
            infoTambahan:
              "Program residensi seniman internasional memperkuat posisi Yogyakarta sebagai pusat seni kontemporer. Kerjasama dengan berbagai universitas dunia meningkatkan kualitas pendidikan tinggi. Pengembangan industri kreatif berbasis budaya menciptakan lapangan kerja baru. Festival-festival internasional digelar secara reguler, menggabungkan unsur tradisional dan modern. Keraton terus berperan sebagai pusat diplomasi budaya melalui berbagai program pertukaran budaya internasional.",
            faktaMenarik:
              "1. Yogyakarta terpilih sebagai World Creative City oleh UNESCO.\n2. 30% mahasiswa asing di Indonesia memilih belajar di Yogyakarta.\n3. Industri kreatif menyumbang 40% PDRB Yogyakarta.\n4. Program pertukaran budaya melibatkan lebih dari 50 negara.\n5. Model pelestarian budaya Yogyakarta diadopsi oleh berbagai kota bersejarah di Asia.",
          },
          // ... data tahun lainnya ...
        };

        function updateTimelineContent(year, direction) {
          const data = timelineData[year];
          const content = document.createElement("div");
          content.className = `timeline-item slide-${direction}`;

          if (!data) {
            content.innerHTML = `
              <div class="timeline-item">
                  <h2>Data Belum Tersedia</h2>
                  <div class="description">
                      <p>Mohon maaf, data untuk tahun ${year} belum tersedia.</p>
                  </div>
              </div>
            `;
          } else {
            content.innerHTML = `
              <div class="timeline-item">
                  ${data.judul ? `<h2>${data.judul}</h2>` : ""}
                  ${
                    data.gambar
                      ? `<img src="../assets/images/${data.gambar}" alt="${
                          data.judul || ""
                        }" class="timeline-image">`
                      : ""
                  }
                  ${
                    data.deskripsi
                      ? `<div class="description"><p>${data.deskripsi}</p></div>`
                      : ""
                  }
                  ${
                    data.infoTambahan
                      ? `<div class="additional-info"><p>${data.infoTambahan}</p></div>`
                      : ""
                  }
                  ${
                    data.faktaMenarik
                      ? `
                      <div class="tooltip-container">
                          <span class="tooltip">Fakta Menarik!</span>
                          <span class="text">Tahukah Kamu?</span>
                          <span>${data.faktaMenarik}</span>
                      </div>
                  `
                      : ""
                  }
                  ${
                    data.infoTambahan
                      ? `<div class="additional-info"><p>${data.infoTambahan}</p></div>`
                      : ""
                  }
              </div>
            `;
          }

          return content;
        }

        function changeYear(direction) {
          // Dapatkan array dari tahun-tahun yang tersedia
          const availableYears = Object.keys(timelineData)
            .map(Number)
            .sort((a, b) => a - b);
          const currentIndex = availableYears.indexOf(currentYear);
          const minYear = Math.min(...availableYears); // 1755

          // Cek apakah perpindahan tahun valid
          if (
            direction === "right" &&
            currentIndex === availableYears.length - 1
          )
            return;
          if (direction === "left" && currentYear <= minYear) return; // Mencegah navigasi ke tahun sebelum 1755

          // Tambahkan kelas animasi untuk konten yang akan hilang
          const currentContent = timelineContent.firstChild;
          yearDisplay.classList.add(`slide-${direction}`);
          if (currentContent) {
            currentContent.classList.add(`slide-${direction}`);
          }

          setTimeout(() => {
            // Update tahun berdasarkan indeks array
            if (direction === "right") {
              currentYear = availableYears[currentIndex + 1];
            } else {
              currentYear = availableYears[currentIndex - 1];
            }

            yearDisplay.textContent = currentYear;

            // Perbarui konten dengan animasi yang sama
            const newContent = updateTimelineContent(currentYear, direction);
            timelineContent.innerHTML = "";
            timelineContent.appendChild(newContent);

            // Periksa status tombol prev dan next
            prevYearBtn.disabled = currentYear <= minYear; // Nonaktifkan tombol prev jika di tahun minimum
            prevYearBtn.style.opacity = currentYear <= minYear ? "0.5" : "1";
            prevYearBtn.style.cursor =
              currentYear <= minYear ? "not-allowed" : "pointer";

            nextYearBtn.disabled = currentIndex >= availableYears.length - 1;
            nextYearBtn.style.opacity =
              currentIndex >= availableYears.length - 1 ? "0.5" : "1";
            nextYearBtn.style.cursor =
              currentIndex >= availableYears.length - 1
                ? "not-allowed"
                : "pointer";

            // Reset animasi classes
            yearDisplay.classList.remove(`slide-${direction}`);
            if (newContent) {
              newContent.classList.remove(`slide-${direction}`);
            }
          }, 300);
        }

        // Event listeners
        beginBtn.addEventListener("click", function () {
          introScreen.classList.add("shrink");
          timelineScreen.classList.add("active");
        });

        prevYearBtn.addEventListener("click", function () {
          changeYear("left");
        });

        nextYearBtn.addEventListener("click", function () {
          changeYear("right");
        });

        // Dapatkan array dari tahun-tahun yang tersedia
        const availableYears = Object.keys(timelineData)
          .map(Number)
          .sort((a, b) => a - b);
        currentYear = availableYears[0]; // Mulai dari tahun pertama yang tersedia (1755)
        yearDisplay.textContent = currentYear; // Update tampilan tahun awal

        // Inisialisasi konten awal
        timelineContent.appendChild(
          updateTimelineContent(currentYear, "right")
        );

        // Inisialisasi status tombol
        prevYearBtn.disabled = true;
        prevYearBtn.style.opacity = "0.5";
        prevYearBtn.style.cursor = "not-allowed";

        nextYearBtn.disabled = availableYears.length <= 1;
        nextYearBtn.style.opacity = availableYears.length <= 1 ? "0.5" : "1";
        nextYearBtn.style.cursor =
          availableYears.length <= 1 ? "not-allowed" : "pointer";
      });
    </script>

        <!-- Navigation Scripts -->
        <script src="../assets/js/navbar/primary-nav.js" defer></script>
    <script src="../assets/js/navbar/mobile-nav.js" defer></script>
  </body>
</html>
