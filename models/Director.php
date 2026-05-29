<?php
/**
 * Director Model - Quản lý ban giám đốc
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Security.php';

class Director {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll() {
        $sql = "SELECT id, name, email, phone, status, created_at FROM users WHERE role = 'director' AND deleted_at IS NULL ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT id, name, email, phone, status, created_at FROM users WHERE id = :id AND role = 'director' AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $password = $data['password'] ?? '123456';
        $hashedPassword = Security::hashPassword($password);
        $sql = "INSERT INTO users (name, email, password, phone, role) VALUES (:name, :email, :password, :phone, 'director')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':phone', $data['phone']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $sql = "UPDATE users SET name = :name, email = :email, phone = :phone WHERE id = :id AND role = 'director'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "UPDATE users SET deleted_at = NOW() WHERE id = :id AND role = 'director'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function count() {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'director' AND deleted_at IS NULL");
        return $stmt->fetch()['total'];
    }
}
