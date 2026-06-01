<?php
/**
 * Admission Model - Quản lý nhập viện / nội trú
 * 
 * Đã tích hợp:
 * - Soft Delete (WHERE deleted_at IS NULL)
 * - Audit Log
 * - Ghi created_by / updated_by
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class Admission {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả ca nhập viện
    public function getAll($doctorId = null) {
        $sql = "SELECT a.*, 
                    u.name as patient_name, u.phone as patient_phone,
                    du.name as doctor_name,
                    b.bed_number, r.room_number, r.room_type, r.price_per_day,
                    dep.name as department_name
                FROM admissions a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON p.user_id = u.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                LEFT JOIN beds b ON a.bed_id = b.id
                LEFT JOIN rooms r ON b.room_id = r.id
                LEFT JOIN departments dep ON r.department_id = dep.id
                WHERE a.deleted_at IS NULL";
        if ($doctorId !== null) {
            $sql .= " AND a.doctor_id = :doctor_id";
        }
        $sql .= " ORDER BY a.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        if ($doctorId !== null) {
            $stmt->bindParam(':doctor_id', $doctorId);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy ca nhập viện đang active
    public function getActive($doctorId = null) {
        $sql = "SELECT a.*, 
                    u.name as patient_name, u.phone as patient_phone,
                    du.name as doctor_name,
                    b.bed_number, r.room_number, r.room_type, r.price_per_day,
                    dep.name as department_name
                FROM admissions a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON p.user_id = u.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                LEFT JOIN beds b ON a.bed_id = b.id
                LEFT JOIN rooms r ON b.room_id = r.id
                LEFT JOIN departments dep ON r.department_id = dep.id
                WHERE a.status = 'active' AND a.deleted_at IS NULL";
        if ($doctorId !== null) {
            $sql .= " AND a.doctor_id = :doctor_id";
        }
        $sql .= " ORDER BY a.admission_date DESC";
        $stmt = $this->conn->prepare($sql);
        if ($doctorId !== null) {
            $stmt->bindParam(':doctor_id', $doctorId);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm theo ID
    public function findById($id) {
        $sql = "SELECT a.*, 
                    u.name as patient_name, u.phone as patient_phone, u.email as patient_email,
                    p.address as patient_address, p.date_of_birth, p.blood_type, p.gender,
                    du.name as doctor_name, d.specialty,
                    b.bed_number, b.room_id, r.room_number, r.room_type, r.price_per_day,
                    dep.name as department_name
                FROM admissions a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON p.user_id = u.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                LEFT JOIN beds b ON a.bed_id = b.id
                LEFT JOIN rooms r ON b.room_id = r.id
                LEFT JOIN departments dep ON r.department_id = dep.id
                WHERE a.id = :id AND a.deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Nhập viện (ghi created_by + audit log)
    public function admit($data) {
        $userId = $_SESSION['user']['id'] ?? null;
        $status = $data['status'] ?? 'pending';
        $sql = "INSERT INTO admissions (patient_id, doctor_id, bed_id, admission_date, diagnosis, notes, status, created_by) 
                VALUES (:patient_id, :doctor_id, :bed_id, :admission_date, :diagnosis, :notes, :status, :created_by)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':doctor_id', $data['doctor_id']);
        $stmt->bindParam(':bed_id', $data['bed_id']);
        $stmt->bindParam(':admission_date', $data['admission_date']);
        $stmt->bindParam(':diagnosis', $data['diagnosis']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':created_by', $userId);
        $stmt->execute();
        $newId = $this->conn->lastInsertId();

        AuditLog::logCreate('admissions', $newId, ['patient_id' => $data['patient_id'], 'bed_id' => $data['bed_id']]);
        return $newId;
    }

    // Xuất viện (ghi updated_by + audit log)
    public function discharge($id) {
        $userId = $_SESSION['user']['id'] ?? null;
        $sql = "UPDATE admissions SET status = 'discharged', discharge_date = NOW(), discharge_ordered = 1, discharge_ordered_at = COALESCE(discharge_ordered_at, NOW()), updated_by = :updated_by 
                WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':updated_by', $userId);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        AuditLog::logUpdate('admissions', $id, ['status' => 'active'], ['status' => 'discharged']);
        return $result;
    }

    // Chỉ định xuất viện (lâm sàng)
    public function orderDischarge($id) {
        $userId = $_SESSION['user']['id'] ?? null;
        $sql = "UPDATE admissions SET discharge_ordered = 1, discharge_ordered_at = NOW(), updated_by = :updated_by 
                WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':updated_by', $userId);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        AuditLog::logUpdate('admissions', $id, ['discharge_ordered' => 0], ['discharge_ordered' => 1]);
        return $result;
    }

    // Lấy ca nhập viện đang hoạt động và có chỉ định xuất viện để lập hóa đơn
    public function getActiveWithDischargeOrder($patientId) {
        $sql = "SELECT a.*, 
                    b.bed_number, b.room_id, r.room_number, r.room_type, r.price_per_day
                FROM admissions a
                LEFT JOIN beds b ON a.bed_id = b.id
                LEFT JOIN rooms r ON b.room_id = r.id
                WHERE a.patient_id = :patient_id 
                  AND a.status = 'active' 
                  AND a.discharge_ordered = 1 
                  AND a.deleted_at IS NULL 
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy danh sách tất cả ca nội trú đang hoạt động và có chỉ định xuất viện lâm sàng chờ thanh toán
    public function getPendingDischargeAdmissions() {
        $sql = "SELECT a.*, 
                    u.name as patient_name, u.phone as patient_phone, p.date_of_birth,
                    b.bed_number, r.room_number, r.price_per_day,
                    doc_u.name as doctor_name
                FROM admissions a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON p.user_id = u.id
                LEFT JOIN beds b ON a.bed_id = b.id
                LEFT JOIN rooms r ON b.room_id = r.id
                LEFT JOIN doctors d ON a.doctor_id = d.id
                LEFT JOIN users doc_u ON d.user_id = doc_u.id
                WHERE a.status = 'active' 
                  AND a.discharge_ordered = 1 
                  AND a.deleted_at IS NULL
                ORDER BY a.discharge_ordered_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật ghi chú (dùng cho Y tá / Bác sĩ)
    public function updateNotes($id, $notes) {
        $userId = $_SESSION['user']['id'] ?? null;
        $sql = "UPDATE admissions SET notes = :notes, updated_by = :updated_by WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':updated_by', $userId);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();
        
        AuditLog::logUpdate('admissions', $id, null, ['notes' => 'Cập nhật ghi chú chăm sóc']);
        return $result;
    }

    // Xóa mềm ca nhập viện
    public function delete($id) {
        $old = $this->findById($id);
        $userId = $_SESSION['user']['id'] ?? null;

        $sql = "UPDATE admissions SET deleted_at = NOW(), updated_by = :updated_by WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':updated_by', $userId);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        AuditLog::logDelete('admissions', $id, $old ? ['diagnosis' => $old['diagnosis']] : null);
        return $result;
    }

    // Lấy theo bệnh nhân
    public function getByPatientId($patientId) {
        $sql = "SELECT a.*, du.name as doctor_name, b.bed_number, r.room_number
                FROM admissions a
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                LEFT JOIN beds b ON a.bed_id = b.id
                LEFT JOIN rooms r ON b.room_id = r.id
                WHERE a.patient_id = :patient_id AND a.deleted_at IS NULL
                ORDER BY a.admission_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy danh sách chờ xếp giường (pending)
    public function getPending($doctorId = null) {
        $sql = "SELECT a.*, 
                    u.name as patient_name, u.phone as patient_phone,
                    du.name as doctor_name,
                    dep.name as department_name
                FROM admissions a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON p.user_id = u.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                LEFT JOIN beds b ON a.bed_id = b.id
                LEFT JOIN rooms r ON b.room_id = r.id
                LEFT JOIN departments dep ON r.department_id = dep.id
                WHERE a.status = 'pending' AND a.deleted_at IS NULL";
        if ($doctorId !== null) {
            $sql .= " AND a.doctor_id = :doctor_id";
        }
        $sql .= " ORDER BY a.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        if ($doctorId !== null) {
            $stmt->bindParam(':doctor_id', $doctorId);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Thực hiện xếp giường/nhập viện cho hồ sơ chờ
    public function assignBed($id, $bedId, $admissionDate = null) {
        $userId = $_SESSION['user']['id'] ?? null;
        $admDateSql = $admissionDate ? ", admission_date = :admission_date" : "";
        $sql = "UPDATE admissions SET bed_id = :bed_id, status = 'active', updated_by = :updated_by $admDateSql 
                WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':bed_id', $bedId);
        $stmt->bindParam(':updated_by', $userId);
        $stmt->bindParam(':id', $id);
        if ($admissionDate) {
            $stmt->bindParam(':admission_date', $admissionDate);
        }
        $result = $stmt->execute();
        
        AuditLog::logUpdate('admissions', $id, ['status' => 'pending'], ['status' => 'active', 'bed_id' => $bedId]);
        return $result;
    }

    // Đếm (chỉ đếm chưa bị xóa mềm)
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM admissions WHERE deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    public function countActive() {
        $sql = "SELECT COUNT(*) as total FROM admissions WHERE status = 'active' AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }
}