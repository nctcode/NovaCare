<?php
/**
 * Patient Model - Quản lý bảng patients + users
 * 
 * Đã tích hợp:
 * - Soft Delete (không xóa cứng, chỉ đánh dấu deleted_at)
 * - Audit Log
 * - WHERE deleted_at IS NULL trên mọi query
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class Patient {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả bệnh nhân (chỉ lấy chưa bị xóa mềm)
    public function getAll() {
        $sql = "SELECT p.*, u.name, u.email, u.phone 
                FROM patients p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.deleted_at IS NULL AND u.deleted_at IS NULL
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
                WHERE p.id = :id AND p.deleted_at IS NULL LIMIT 1";
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
                WHERE p.user_id = :user_id AND p.deleted_at IS NULL LIMIT 1";
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
        $hashedPassword = Security::hashPassword($password);
        $stmt->bindParam(':password', $hashedPassword);
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
        $patientId = $this->conn->lastInsertId();

        AuditLog::logCreate('patients', $patientId, ['name' => $data['name'], 'email' => $data['email']]);
        return $patientId;
    }

    // Cập nhật bệnh nhân
    public function update($id, $data) {
        // Lấy user_id
        $patient = $this->findById($id);
        if (!$patient) return false;

        // Cập nhật users
        $sql = "UPDATE users SET name = :name, email = :email, phone = :phone 
                WHERE id = :user_id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':user_id', $patient['user_id']);
        $stmt->execute();

        // Cập nhật patients
        $sql = "UPDATE patients SET date_of_birth = :dob, gender = :gender, 
                address = :address, blood_type = :blood_type, medical_history = :medical_history 
                WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dob', $data['date_of_birth']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':blood_type', $data['blood_type']);
        $stmt->bindParam(':medical_history', $data['medical_history']);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        AuditLog::logUpdate('patients', $id,
            ['name' => $patient['name'], 'email' => $patient['email']],
            ['name' => $data['name'], 'email' => $data['email']]
        );
        return $result;
    }

    // Xóa mềm bệnh nhân (soft delete cả user + patient)
    public function delete($id) {
        $patient = $this->findById($id);
        if (!$patient) return false;

        // Soft delete patient
        $sql = "UPDATE patients SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Soft delete user liên quan
        $sql = "UPDATE users SET deleted_at = NOW() WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $patient['user_id']);
        $stmt->execute();

        AuditLog::logDelete('patients', $id, ['name' => $patient['name'], 'email' => $patient['email']]);
        return true;
    }

    // Lấy bệnh nhân mới nhất
    public function getRecentPatients($limit = 5) {
        $sql = "SELECT u.name, u.created_at 
                FROM patients p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.deleted_at IS NULL
                ORDER BY p.id DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy bệnh nhân liên kết động với một bác sĩ cụ thể
    public function getByDoctorId($doctorId) {
        $sql = "SELECT p.*, u.name, u.email, u.phone 
                FROM patients p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.deleted_at IS NULL AND u.deleted_at IS NULL
                AND p.id IN (
                    SELECT patient_id FROM appointments WHERE doctor_id = :doctor_id1 AND deleted_at IS NULL
                    UNION
                    SELECT patient_id FROM medical_records WHERE doctor_id = :doctor_id2 AND deleted_at IS NULL
                    UNION
                    SELECT patient_id FROM admissions WHERE doctor_id = :doctor_id3 AND deleted_at IS NULL
                )
                ORDER BY u.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id1', $doctorId, PDO::PARAM_INT);
        $stmt->bindParam(':doctor_id2', $doctorId, PDO::PARAM_INT);
        $stmt->bindParam(':doctor_id3', $doctorId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng bệnh nhân (chỉ đếm chưa bị xóa mềm)
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM patients WHERE deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
}
