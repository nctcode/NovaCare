<?php
/**
 * Patient Model - Quản lý bảng patients + users
 */
require_once __DIR__ . '/../config/database.php';

class Patient {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả bệnh nhân (JOIN users)
    public function getAll() {
        $sql = "SELECT p.*, u.name, u.email, u.phone 
                FROM patients p 
                JOIN users u ON p.user_id = u.id 
                ORDER BY u.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm bệnh nhân theo ID
    public function findById($id) {
        $sql = "SELECT p.*, u.name, u.email, u.phone 
                FROM patients p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Tìm bệnh nhân theo user_id
    public function findByUserId($userId) {
        $sql = "SELECT p.*, u.name, u.email, u.phone 
                FROM patients p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Tạo bệnh nhân mới (tạo user + patient)
    public function create($data) {
        // Bước 1: Tạo user
        $sql = "INSERT INTO users (name, email, password, phone, role) 
                VALUES (:name, :email, :password, :phone, 'patient')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $password = $data['password'] ?? '123456';
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->execute();
        $userId = $this->conn->lastInsertId();

        // Bước 2: Tạo patient
        $sql = "INSERT INTO patients (user_id, date_of_birth, gender, address, blood_type, medical_history) 
                VALUES (:user_id, :dob, :gender, :address, :blood_type, :medical_history)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':dob', $data['date_of_birth']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':blood_type', $data['blood_type']);
        $stmt->bindParam(':medical_history', $data['medical_history']);
        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    // Cập nhật bệnh nhân
    public function update($id, $data) {
        // Lấy user_id
        $patient = $this->findById($id);
        if (!$patient) return false;

        // Cập nhật users
        $sql = "UPDATE users SET name = :name, email = :email, phone = :phone 
                WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':user_id', $patient['user_id']);
        $stmt->execute();

        // Cập nhật patients
        $sql = "UPDATE patients SET date_of_birth = :dob, gender = :gender, 
                address = :address, blood_type = :blood_type, medical_history = :medical_history 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dob', $data['date_of_birth']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':blood_type', $data['blood_type']);
        $stmt->bindParam(':medical_history', $data['medical_history']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Xóa bệnh nhân
    public function delete($id) {
        $patient = $this->findById($id);
        if (!$patient) return false;

        // Xóa patient trước
        $sql = "DELETE FROM patients WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Xóa user liên quan
        $sql = "DELETE FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $patient['user_id']);
        return $stmt->execute();
    }

    // Lấy bệnh nhân mới nhất
    public function getRecentPatients($limit = 5) {
        $sql = "SELECT u.name, u.created_at 
                FROM patients p 
                JOIN users u ON p.user_id = u.id 
                ORDER BY p.id DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng bệnh nhân
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM patients";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
}
