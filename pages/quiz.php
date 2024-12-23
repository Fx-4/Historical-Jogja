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
    <title>Kuis Interaktif Sejarah</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/kuisinteraktif.css" />
    <link rel="stylesheet" href="../assets/css/navbar/primary-nav.css">
    <link rel="stylesheet" href="../assets/css/navbar/mobile-nav.css">
    <link rel="stylesheet" href="<?php echo __DIR__ . '/../assets/css/footer-waves.css'; ?>">
  </head>
  <body>
  <?php include_once '../components/navbar/PrimaryNav.php'; ?>
  <?php include_once '../components/navbar/MobileNav.php'; ?>

    <div class="quiz-container">
      <div class="quiz-header">
        <h1>Kuis Sejarah Interaktif</h1>
        <p>Uji pengetahuan sejarah Anda!</p>
        <div id="timer">Waktu: <span id="time">10</span></div>
      </div>

      <div id="question-container" class="question-container">
        <!-- Pertanyaan akan dimuat di sini -->
      </div>
      <div class="controls">
        <button id="prev-btn" disabled>Sebelumnya</button>
        <button id="next-btn">Selanjutnya</button>
      </div>

      <!-- KUIS SELESAI -->
      <div id="result-container" class="result-container" style="display: none">
        <h2>Kuis Selesai!</h2>
        <div class="score">Skor Anda: <span id="score">0</span></div>
        <p>Terima kasih telah berpartisipasi!</p>
        <p>Ingin Mencoba lagi?</p>
        <label class="switch">
          <input type="checkbox" class="chk" id="restart-btn" />
          <span class="slider"></span>
        </label>
      </div>

      <!-- Tambahkan setelah div result-container -->
      <div id="leaderboard-container" class="leaderboard-container">
        <h2>Papan Peringkat</h2>
        <div id="leaderboard-list"></div>
      </div>
    </div>

    <script>
      const allQuestions = [
        {
          question: "Kapan Keraton Yogyakarta didirikan?",
          options: ["1755", "1756", "1757", "1758"],
          correct: 0,
        },
        {
          question: "Siapa pendiri Keraton Yogyakarta?",
          options: [
            "Sultan Hamengku Buwono I",
            "Sultan Hamengku Buwono II",
            "Pangeran Mangkubumi",
            "Pakubuwono II",
          ],
          correct: 0,
        },
        {
          question:
            "Apa nama perjanjian yang membagi wilayah Mataram menjadi Surakarta dan Yogyakarta?",
          options: [
            "Perjanjian Giyanti",
            "Perjanjian Jatisari",
            "Perjanjian Salatiga",
            "Perjanjian Mataram",
          ],
          correct: 0,
        },
        {
          question: "Tahun berapa Proklamasi Kemerdekaan Indonesia?",
          options: ["1945", "1944", "1946", "1947"],
          correct: 0,
        },
        {
          question: "Siapa yang menulis teks Proklamasi?",
          options: [
            "Soekarno-Hatta",
            "Soekarno",
            "Mohammad Hatta",
            "Ahmad Soebardjo",
          ],
          correct: 0,
        },
        {
          question: "Di mana lokasi Tugu Yogyakarta?",
          options: [
            "Jalan Malioboro",
            "Alun-alun Utara",
            "Alun-alun Kidul",
            "Kotagede",
          ],
          correct: 0,
        },
        {
          question: "Kapan Perang Diponegoro terjadi?",
          options: ["1825-1830", "1820-1825", "1830-1835", "1835-1840"],
          correct: 0,
        },
        {
          question: "Siapa nama asli Pangeran Diponegoro?",
          options: [
            "Raden Mas Ontowiryo",
            "Raden Mas Sujono",
            "Raden Mas Sahid",
            "Raden Mas Suryo",
          ],
          correct: 0,
        },
        {
          question: "Apa nama benteng Belanda di Yogyakarta?",
          options: [
            "Benteng Vredeburg",
            "Benteng Marlborough",
            "Benteng Rotterdam",
            "Benteng Victoria",
          ],
          correct: 0,
        },
        {
          question: "Tahun berapa UGM didirikan?",
          options: ["1949", "1950", "1951", "1952"],
          correct: 0,
        },
        // ... tambahkan 40 soal lainnya dengan format yang sama
      ];

      // Fungsi untuk mengacak array
      function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1));
          [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
      }

      // Fungsi untuk mengambil 10 soal acak
      function getRandomQuestions() {
        return shuffleArray([...allQuestions]).slice(0, 10);
      }

      let questions = getRandomQuestions();

      let currentQuestion = 0;
      let score = 0;
      let answers = new Array(questions.length).fill(null);
      let timer;
      let timeLeft;

      const questionContainer = document.getElementById("question-container");
      const prevButton = document.getElementById("prev-btn");
      const nextButton = document.getElementById("next-btn");
      const resultContainer = document.getElementById("result-container");
      const restartButton = document.getElementById("restart-btn");

      function startTimer() {
        clearInterval(timer);
        timeLeft = 10;
        document.getElementById("time").textContent = timeLeft;

        timer = setInterval(() => {
          timeLeft--;
          document.getElementById("time").textContent = timeLeft;

          if (timeLeft <= 0) {
            clearInterval(timer);
            // Otomatis pindah ke pertanyaan berikutnya
            if (currentQuestion < questions.length - 1) {
              currentQuestion++;
              showQuestion(currentQuestion);
            } else {
              showResult();
            }
          }
        }, 1000);
      }

      function showQuestion(index) {
        // Sembunyikan leaderboard saat kuis dimulai
        document.getElementById("leaderboard-container").style.display = "none";

        const question = questions[index];
        questionContainer.innerHTML = `
                <div class="question">${index + 1}. ${question.question}</div>
                <div class="options">
                    ${question.options
                      .map(
                        (option, i) => `
                        <div class="option ${
                          answers[index] === i ? "selected" : ""
                        }" 
                             onclick="selectOption(${i})">
                            ${option}
                        </div>
                    `
                      )
                      .join("")}
                </div>
            `;

        prevButton.disabled = index === 0;
        nextButton.textContent =
          index === questions.length - 1 ? "Selesai" : "Selanjutnya";

        // Mulai timer baru
        startTimer();
      }

      function selectOption(optionIndex) {
        answers[currentQuestion] = optionIndex;
        // Hanya perbarui tampilan opsi tanpa me-reset timer
        const options = document.querySelectorAll(".option");
        options.forEach((option, index) => {
          if (index === optionIndex) {
            option.classList.add("selected");
          } else {
            option.classList.remove("selected");
          }
        });
      }

      function calculateScore() {
        score = 0;
        answers.forEach((answer, index) => {
          if (answer === questions[index].correct) {
            score++;
          }
        });
        return score;
      }

      function showResult() {
        clearInterval(timer);
        questionContainer.style.display = "none";
        document.querySelector(".controls").style.display = "none";
        document.querySelector(".quiz-header").style.display = "none";
        resultContainer.style.display = "block";

        const finalScore = calculateScore();

        resultContainer.innerHTML = `
            <h2>Kuis Selesai!</h2>
            <div class="score">Skor Anda: <span id="score">${finalScore}</span></div>
            <div class="name-input">
                <input type="text" id="player-name" placeholder="Masukkan nama Anda">
                <button id="save-score">Simpan Skor</button>
            </div>
            <div id="post-save" style="display: none">
                <p>Terima kasih telah berpartisipasi!</p>
                <p>Ingin Mencoba lagi?</p>
            </div>
            <label class="switch">
                <input type="checkbox" class="chk" id="restart-btn">
                <span class="slider"></span>
            </label>
        `;

        // Event listener untuk menyimpan skor
        document.getElementById("save-score").addEventListener("click", () => {
          const playerName = document.getElementById("player-name").value;
          if (playerName.trim() !== "") {
            saveScore(playerName, finalScore);
            showLeaderboard();

            // Hilangkan form input dan tampilkan pesan
            document.querySelector(".name-input").style.display = "none";
            document.getElementById("post-save").style.display = "block";
          } else {
            alert("Mohon masukkan nama Anda!");
          }
        });

        // Event listener untuk restart dengan delay
        document
          .getElementById("restart-btn")
          .addEventListener("change", function () {
            // Tambahkan delay 1 detik
            setTimeout(() => {
              questions = getRandomQuestions();
              currentQuestion = 0;
              answers = new Array(questions.length).fill(null);
              score = 0;
              questionContainer.style.display = "block";
              document.querySelector(".controls").style.display = "flex";
              resultContainer.style.display = "none";
              document.getElementById("leaderboard-container").style.display =
                "none";
              document.querySelector(".quiz-header").style.display = "block";
              showQuestion(currentQuestion);
            }, 1000); // Delay 1000ms (1 detik)
          });
      }

      // Fungsi saveScore yang disederhanakan
      function saveScore(name, score) {
        let scores = JSON.parse(localStorage.getItem("quizScores")) || [];
        scores.push({
          name,
          score,
          date: new Date().toISOString(),
        });
        scores.sort((a, b) => b.score - a.score);
        localStorage.setItem("quizScores", JSON.stringify(scores));
      }

      // Tambahkan fungsi ini setelah showLeaderboard()
      function createConfetti() {
        const container = document.querySelector(
          ".leaderboard-item:nth-child(1)"
        );
        for (let i = 0; i < 50; i++) {
          const confetti = document.createElement("div");
          confetti.className = "confetti";
          confetti.style.left = Math.random() * 100 + "%";
          confetti.style.animationDelay = Math.random() * 3 + "s";
          container.appendChild(confetti);
        }
      }

      // Modifikasi fungsi showLeaderboard() untuk menambahkan confetti
      function showLeaderboard() {
        const leaderboardContainer = document.getElementById(
          "leaderboard-container"
        );
        const leaderboardList = document.getElementById("leaderboard-list");
        const scores = JSON.parse(localStorage.getItem("quizScores")) || [];

        // Urutkan scores berdasarkan nilai tertinggi
        scores.sort((a, b) => b.score - a.score);

        // Tampilkan 3 peringkat teratas dengan urutan yang diinginkan
        const topThree = [];
        if (scores.length > 0) {
          if (scores.length > 1) topThree.push(scores[1]); // Juara 2 (kiri)
          topThree.push(scores[0]); // Juara 1 (tengah)
          if (scores.length > 2) topThree.push(scores[2]); // Juara 3 (kanan)
        }

        // Generate HTML untuk top 3
        let leaderboardHTML = topThree
          .map(
            (score) => `
                <div class="leaderboard-item">
                    <div class="score">${score.score}</div>
                    <div class="name">${score.name}</div>
                </div>
            `
          )
          .join("");

        // Tambahkan peringkat 4 dan seterusnya
        if (scores.length > 3) {
          const otherScores = scores.slice(3);
          leaderboardHTML += otherScores
            .map(
              (score, index) => `
                    <div class="leaderboard-item">
                        <span>${index + 4}. ${score.name}</span>
                        <span>${score.score}</span>
                    </div>
                `
            )
            .join("");
        }

        leaderboardList.innerHTML = leaderboardHTML;
        leaderboardContainer.style.display = "block";

        if (scores.length > 0) {
          createConfetti();
        }
      }

      prevButton.addEventListener("click", () => {
        clearInterval(timer); // Hentikan timer saat ini
        if (currentQuestion > 0) {
          currentQuestion--;
          showQuestion(currentQuestion);
        }
      });

      nextButton.addEventListener("click", () => {
        clearInterval(timer); // Hentikan timer saat ini
        if (currentQuestion < questions.length - 1) {
          currentQuestion++;
          showQuestion(currentQuestion);
        } else {
          showResult();
        }
      });

      // Mulai kuis
      showQuestion(currentQuestion);
    </script>

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

    <!-- Navigation Scripts -->
    <script src="../assets/js/navbar/primary-nav.js" defer></script>
    <script src="../assets/js/navbar/mobile-nav.js" defer></script>

  </body>
</html>
