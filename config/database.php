<?php
/**
 * Database Singleton - Đảm bảo chỉ tạo 1 kết nối duy nhất mỗi request
 */
class Database {
    private $host = 'localhost';
    private $db_name = 'hospital_management';
    private $username = 'root';
    private $password = '';

    private static $instance = null;
    private static $conn = null;

    // Private constructor — không cho phép new Database() từ bên ngoài
    public function __construct() {
        // Giữ public để tương thích code cũ, nhưng dùng Singleton bên trong
    }

    /**
     * Lấy instance duy nhất
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Lấy kết nối PDO (Singleton - chỉ tạo 1 lần)
     */
    public function getConnection() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                    $this->username,
                    $this->password
                );
                // Bật chế độ báo lỗi
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // Trả về kết quả dạng associative array
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                // Dùng prepared statements thật (không emulate)
                self::$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $e) {
                error_log("Database connection error: " . $e->getMessage());
                die("Lỗi kết nối cơ sở dữ liệu. Vui lòng liên hệ quản trị viên.");
            }
        }
        return self::$conn;
    }
}
