<?php
class Database {
    private $host = "localhost";
    private $username = "root";  // Sesuaikan dengan username MySQL Anda
    private $password = "";      // Sesuaikan dengan password MySQL Anda
    private $database = "historical_jogja"; // Sesuaikan dengan nama database Anda
    private $conn;

    public function connect() {
        try {
            $this->conn = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database
            );

            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            return $this->conn;
        } catch (Exception $e) {
            die("Connection error: " . $e->getMessage());
        }
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
</antArtifact