<?php
/**
 * User Model - Quản lý bảng users
 */
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table = 'users';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Tìm user theo email (dùng cho login)
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Tìm user theo ID
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy tất cả users
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tạo user mới
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, email, password, phone, role) 
                VALUES (:name, :email, :password, :phone, :role)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Đếm số user theo role
    public function countByRole($role) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE role = :role";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
}
