<?php
/**
 * Technician Model - Quản lý kỹ thuật viên
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Security.php';

class Technician {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll() {
        $sql = "SELECT t.*, u.name, u.email, u.phone, dep.name as department_name
                FROM technicians t
                JOIN users u ON t.user_id = u.id
                LEFT JOIN departments dep ON t.department_id = dep.id
                ORDER BY u.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT t.*, u.name, u.email, u.phone, dep.name as department_name
                FROM technicians t
                JOIN users u ON t.user_id = u.id
                LEFT JOIN departments dep ON t.department_id = dep.id
                WHERE t.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findByUserId($userId) {
        $sql = "SELECT t.*, u.name, u.email, u.phone, dep.name as department_name
                FROM technicians t
                JOIN users u ON t.user_id = u.id
                LEFT JOIN departments dep ON t.department_id = dep.id
                WHERE t.user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $this->conn->beginTransaction();
        try {
            $password = $data['password'] ?? '123456';
            $hashedPassword = Security::hashPassword($password);
            $sql = "INSERT INTO users (name, email, password, phone, role) VALUES (:name, :email, :password, :phone, 'technician')";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->execute();
            $userId = $this->conn->lastInsertId();

            $sql = "INSERT INTO technicians (user_id, department_id, specialty) VALUES (:user_id, :department_id, :specialty)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':department_id', $data['department_id']);
            $stmt->bindParam(':specialty', $data['specialty']);
            $stmt->execute();

            $this->conn->commit();
            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function update($id, $data) {
        $tech = $this->findById($id);
        if (!$tech) return false;

        $sql = "UPDATE users SET name = :name, email = :email, phone = :phone WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':user_id', $tech['user_id']);
        $stmt->execute();

        $sql = "UPDATE technicians SET department_id = :department_id, specialty = :specialty WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':department_id', $data['department_id']);
        $stmt->bindParam(':specialty', $data['specialty']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $tech = $this->findById($id);
        if (!$tech) return false;
        
        $stmt = $this->conn->prepare("DELETE FROM technicians WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :uid");
        $stmt->bindParam(':uid', $tech['user_id']);
        return $stmt->execute();
    }

    public function count() {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM technicians");
        return $stmt->fetch()['total'];
    }
}
