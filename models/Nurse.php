<?php
/**
 * Nurse Model - Quản lý y tá
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Security.php';

class Nurse {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll() {
        $sql = "SELECT n.*, u.name, u.email, u.phone, dep.name as department_name
                FROM nurses n
                JOIN users u ON n.user_id = u.id
                LEFT JOIN departments dep ON n.department_id = dep.id
                ORDER BY u.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT n.*, u.name, u.email, u.phone, dep.name as department_name
                FROM nurses n
                JOIN users u ON n.user_id = u.id
                LEFT JOIN departments dep ON n.department_id = dep.id
                WHERE n.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findByUserId($userId) {
        $sql = "SELECT n.*, u.name, u.email, u.phone, dep.name as department_name
                FROM nurses n
                JOIN users u ON n.user_id = u.id
                LEFT JOIN departments dep ON n.department_id = dep.id
                WHERE n.user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $this->conn->beginTransaction();
        try {
            // Tạo user với role nurse (password mặc định được hash)
            $password = $data['password'] ?? '123456';
            $hashedPassword = Security::hashPassword($password);
            $sql = "INSERT INTO users (name, email, password, phone, role) VALUES (:name, :email, :password, :phone, 'nurse')";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->execute();
            $userId = $this->conn->lastInsertId();

            // Tạo nurse
            $sql = "INSERT INTO nurses (user_id, department_id) VALUES (:user_id, :department_id)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':department_id', $data['department_id']);
            $stmt->execute();

            $this->conn->commit();
            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function update($id, $data) {
        $nurse = $this->findById($id);
        if (!$nurse) return false;

        // Cập nhật users
        $sql = "UPDATE users SET name = :name, email = :email, phone = :phone WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':user_id', $nurse['user_id']);
        $stmt->execute();

        // Cập nhật nurses
        $sql = "UPDATE nurses SET department_id = :department_id WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':department_id', $data['department_id']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $nurse = $this->findById($id);
        if (!$nurse) return false;
        
        $stmt = $this->conn->prepare("DELETE FROM nurses WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :uid");
        $stmt->bindParam(':uid', $nurse['user_id']);
        return $stmt->execute();
    }

    public function count() {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM nurses");
        return $stmt->fetch()['total'];
    }
}
