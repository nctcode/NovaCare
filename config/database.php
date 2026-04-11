<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'hospital_management';
    private $username = 'root';
    private $password = '';
    private $conn;

    // Kết nối database bằng PDO
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password
            );
            // Bật chế độ báo lỗi
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Trả về kết quả dạng associative array
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Lỗi kết nối: " . $e->getMessage();
            die();
        }
        return $this->conn;
    }
}
