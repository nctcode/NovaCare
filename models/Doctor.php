<?php
/**
 * Doctor Model - Quản lý bảng doctors + users + departments
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Security.php';

class Doctor {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả bác sĩ (JOIN users + departments)
    public function getAll() {
        $sql = "SELECT d.*, u.name, u.email, u.phone, dep.name as department_name 
                FROM doctors d 
                JOIN users u ON d.user_id = u.id 
                LEFT JOIN departments dep ON d.department_id = dep.id 
                WHERE d.deleted_at IS NULL AND u.deleted_at IS NULL
                ORDER BY u.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm bác sĩ theo ID
    public function findById($id) {
        $sql = "SELECT d.*, u.name, u.email, u.phone, dep.name as department_name 
                FROM doctors d 
                JOIN users u ON d.user_id = u.id 
                LEFT JOIN departments dep ON d.department_id = dep.id 
                WHERE d.id = :id AND d.deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Tìm bác sĩ theo user_id
    public function findByUserId($userId) {
        $sql = "SELECT d.*, u.name, u.email, u.phone, dep.name as department_name 
                FROM doctors d 
                JOIN users u ON d.user_id = u.id 
                LEFT JOIN departments dep ON d.department_id = dep.id 
                WHERE d.user_id = :user_id AND d.deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Tạo bác sĩ mới
    public function create($data) {
        // Tạo user
        $sql = "INSERT INTO users (name, email, password, phone, role) 
                VALUES (:name, :email, :password, :phone, 'doctor')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $password = $data['password'] ?? '123456';
        $hashedPassword = Security::hashPassword($password);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->execute();
        $userId = $this->conn->lastInsertId();

        // Tạo doctor
        $sql = "INSERT INTO doctors (user_id, department_id, specialty, experience_years) 
                VALUES (:user_id, :dept_id, :specialty, :exp_years)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':dept_id', $data['department_id']);
        $stmt->bindParam(':specialty', $data['specialty']);
        $stmt->bindParam(':exp_years', $data['experience_years']);
        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    // Cập nhật bác sĩ
    public function update($id, $data) {
        $doctor = $this->findById($id);
        if (!$doctor) return false;

        // Cập nhật users
        $sql = "UPDATE users SET name = :name, email = :email, phone = :phone 
                WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':user_id', $doctor['user_id']);
        $stmt->execute();

        // Cập nhật doctors
        $sql = "UPDATE doctors SET department_id = :dept_id, specialty = :specialty, 
                experience_years = :exp_years WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dept_id', $data['department_id']);
        $stmt->bindParam(':specialty', $data['specialty']);
        $stmt->bindParam(':exp_years', $data['experience_years']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Xóa bác sĩ
    public function delete($id) {
        $doctor = $this->findById($id);
        if (!$doctor) return false;

        $sql = "UPDATE doctors SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $sql = "UPDATE users SET deleted_at = NOW() WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $doctor['user_id']);
        return $stmt->execute();
    }

    // Lấy danh sách departments
    public function getDepartments() {
        $sql = "SELECT * FROM departments ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng bác sĩ
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM doctors WHERE deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
}
