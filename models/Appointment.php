<?php
/**
 * Appointment Model - Quản lý lịch hẹn khám bệnh
 * 
 * Đã tích hợp:
 * - Soft Delete (WHERE deleted_at IS NULL)
 * - Audit Log (ghi nhật ký thay đổi)
 * - Kiểm tra trùng lịch bác sĩ (±30 phút)
 * - Ghi created_by / updated_by
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class Appointment {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả lịch hẹn (chỉ lấy chưa bị xóa mềm)
    public function getAll() {
        $sql = "SELECT a.*, 
                    pu.name as patient_name, 
                    du.name as doctor_name,
                    dep.name as department_name
                FROM appointments a 
                JOIN patients p ON a.patient_id = p.id 
                JOIN users pu ON p.user_id = pu.id 
                JOIN doctors d ON a.doctor_id = d.id 
                JOIN users du ON d.user_id = du.id 
                LEFT JOIN departments dep ON d.department_id = dep.id
                WHERE a.deleted_at IS NULL
                ORDER BY a.appointment_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy lịch hẹn theo bác sĩ (doctor_id từ bảng doctors)
    public function getByDoctorId($doctorId) {
        $sql = "SELECT a.*, 
                    pu.name as patient_name, pu.phone as patient_phone
                FROM appointments a 
                JOIN patients p ON a.patient_id = p.id 
                JOIN users pu ON p.user_id = pu.id 
                WHERE a.doctor_id = :doctor_id AND a.deleted_at IS NULL
                ORDER BY a.appointment_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy lịch hẹn theo bệnh nhân
    public function getByPatientId($patientId) {
        $sql = "SELECT a.*, 
                    du.name as doctor_name, d.specialty
                FROM appointments a 
                JOIN doctors d ON a.doctor_id = d.id 
                JOIN users du ON d.user_id = du.id 
                WHERE a.patient_id = :patient_id AND a.deleted_at IS NULL
                ORDER BY a.appointment_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm lịch hẹn theo ID
    public function findById($id) {
        $sql = "SELECT a.*, 
                    pu.name as patient_name, 
                    du.name as doctor_name
                FROM appointments a 
                JOIN patients p ON a.patient_id = p.id 
                JOIN users pu ON p.user_id = pu.id 
                JOIN doctors d ON a.doctor_id = d.id 
                JOIN users du ON d.user_id = du.id 
                WHERE a.id = :id AND a.deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Tạo lịch hẹn mới (ghi created_by + audit log)
    public function create($data) {
        $userId = $_SESSION['user']['id'] ?? null;
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, reason, status, created_by) 
                VALUES (:patient_id, :doctor_id, :appointment_date, :reason, 'pending', :created_by)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':doctor_id', $data['doctor_id']);
        $stmt->bindParam(':appointment_date', $data['appointment_date']);
        $stmt->bindParam(':reason', $data['reason']);
        $stmt->bindParam(':created_by', $userId);
        $stmt->execute();
        $newId = $this->conn->lastInsertId();

        AuditLog::logCreate('appointments', $newId, $data);
        return $newId;
    }

    // Cập nhật trạng thái lịch hẹn (ghi updated_by + audit log)
    public function updateStatus($id, $status) {
        $userId = $_SESSION['user']['id'] ?? null;
        $old = $this->findById($id);

        $sql = "UPDATE appointments SET status = :status, updated_by = :updated_by WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':updated_by', $userId);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        AuditLog::logUpdate('appointments', $id, 
            ['status' => $old['status'] ?? null], 
            ['status' => $status]
        );
        return $result;
    }

    // Kiểm tra trùng lịch hẹn (cùng bác sĩ, trong khung giờ ±30 phút)
    public function checkDuplicate($doctorId, $appointmentDate, $excludeId = null) {
        $sql = "SELECT COUNT(*) as cnt FROM appointments 
                WHERE doctor_id = :doctor_id 
                AND status NOT IN ('cancelled') 
                AND deleted_at IS NULL
                AND ABS(TIMESTAMPDIFF(MINUTE, appointment_date, :appointment_date)) < 30";
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->bindParam(':appointment_date', $appointmentDate);
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['cnt'] > 0;
    }

    // Xóa mềm lịch hẹn (soft delete)
    public function delete($id) {
        $old = $this->findById($id);
        $userId = $_SESSION['user']['id'] ?? null;

        $sql = "UPDATE appointments SET deleted_at = NOW(), updated_by = :updated_by WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':updated_by', $userId);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        AuditLog::logDelete('appointments', $id, $old ? ['reason' => $old['reason'], 'status' => $old['status']] : null);
        return $result;
    }

    // Lấy các lịch hẹn mới nhất
    public function getRecentAppointments($limit = 5) {
        $sql = "SELECT u.name as patient_name, a.appointment_date, a.status 
                FROM appointments a 
                JOIN patients p ON a.patient_id = p.id 
                JOIN users u ON p.user_id = u.id 
                WHERE a.deleted_at IS NULL
                ORDER BY a.id DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm lịch hẹn (chỉ đếm chưa bị xóa mềm)
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM appointments WHERE deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    // Đếm lịch hẹn pending
    public function countPending() {
        return $this->countByStatus('pending');
    }

    // Đếm lịch hẹn theo trạng thái
    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM appointments WHERE status = :status AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    // Lấy các lịch hẹn mới nhất của bệnh nhân
    public function getRecentByPatientId($patientId, $limit = 4) {
        $sql = "SELECT a.*, 
                    du.name as doctor_name, d.specialty
                FROM appointments a 
                JOIN doctors d ON a.doctor_id = d.id 
                JOIN users du ON d.user_id = du.id 
                WHERE a.patient_id = :patient_id AND a.deleted_at IS NULL
                ORDER BY a.appointment_date DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
