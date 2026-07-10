<?php
/**
 * MedicalRecord Model - Quản lý hồ sơ bệnh án
 * 
 * Đã tích hợp:
 * - Soft Delete (WHERE deleted_at IS NULL)
 * - Audit Log (ghi nhật ký mỗi khi sửa bệnh án)
 * - Ghi created_by / updated_by
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class MedicalRecord {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll() {
        $sql = "SELECT m.*, p.user_id as patient_user_id, d.user_id as doctor_user_id, 
                       up.name as patient_name, ud.name as doctor_name
                FROM medical_records m
                JOIN patients p ON m.patient_id = p.id
                JOIN users up ON p.user_id = up.id
                JOIN doctors d ON m.doctor_id = d.id
                JOIN users ud ON d.user_id = ud.id
                WHERE m.deleted_at IS NULL
                ORDER BY m.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT m.*, p.user_id as patient_user_id, d.user_id as doctor_user_id, 
                       up.name as patient_name, ud.name as doctor_name,
                       a.appointment_date, a.reason
                FROM medical_records m
                JOIN patients p ON m.patient_id = p.id
                JOIN users up ON p.user_id = up.id
                JOIN doctors d ON m.doctor_id = d.id
                JOIN users ud ON d.user_id = ud.id
                LEFT JOIN appointments a ON m.appointment_id = a.id
                WHERE m.id = :id AND m.deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getByPatientId($patientId) {
        $sql = "SELECT m.*, d.user_id as doctor_user_id, ud.name as doctor_name
                FROM medical_records m
                JOIN doctors d ON m.doctor_id = d.id
                JOIN users ud ON d.user_id = ud.id
                WHERE m.patient_id = :patient_id AND m.deleted_at IS NULL
                ORDER BY m.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByDoctorId($doctorId) {
        $sql = "SELECT m.*, p.user_id as patient_user_id, up.name as patient_name
                FROM medical_records m
                JOIN patients p ON m.patient_id = p.id
                JOIN users up ON p.user_id = up.id
                WHERE m.doctor_id = :doctor_id AND m.deleted_at IS NULL
                ORDER BY m.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($data) {
        $userId = $_SESSION['user']['id'] ?? null;
        $sql = "INSERT INTO medical_records (patient_id, doctor_id, appointment_id, icd10_code, diagnosis, treatment, notes, created_by) 
                VALUES (:patient_id, :doctor_id, :appointment_id, :icd10_code, :diagnosis, :treatment, :notes, :created_by)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':doctor_id', $data['doctor_id']);
        
        $aptId = !empty($data['appointment_id']) ? $data['appointment_id'] : null;
        $stmt->bindValue(':appointment_id', $aptId, PDO::PARAM_INT);

        $icdCode = !empty($data['icd10_code']) ? $data['icd10_code'] : null;
        $stmt->bindValue(':icd10_code', $icdCode, PDO::PARAM_STR);
        
        $stmt->bindParam(':diagnosis', $data['diagnosis']);
        $stmt->bindParam(':treatment', $data['treatment']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':created_by', $userId);
        
        $stmt->execute();
        $newId = $this->conn->lastInsertId();

        AuditLog::logCreate('medical_records', $newId, [
            'diagnosis' => $data['diagnosis'],
            'treatment' => $data['treatment'],
            'icd10_code' => $icdCode
        ]);
        return $newId;
    }

    // Cập nhật bệnh án (ghi audit log: trước/sau khi sửa)
    public function update($id, $data) {
        $old = $this->findById($id);
        if (!$old) return false;

        $userId = $_SESSION['user']['id'] ?? null;
        $sql = "UPDATE medical_records SET 
                    diagnosis = :diagnosis, treatment = :treatment, notes = :notes, updated_by = :updated_by
                WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':diagnosis', $data['diagnosis']);
        $stmt->bindParam(':treatment', $data['treatment']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':updated_by', $userId);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        AuditLog::logUpdate('medical_records', $id, 
            ['diagnosis' => $old['diagnosis'], 'treatment' => $old['treatment']],
            ['diagnosis' => $data['diagnosis'], 'treatment' => $data['treatment']]
        );
        return $result;
    }

    // Xóa mềm bệnh án
    public function delete($id) {
        $old = $this->findById($id);
        $userId = $_SESSION['user']['id'] ?? null;

        $sql = "UPDATE medical_records SET deleted_at = NOW(), updated_by = :updated_by WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':updated_by', $userId);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        AuditLog::logDelete('medical_records', $id, $old ? ['diagnosis' => $old['diagnosis']] : null);
        return $result;
    }

    // Lấy hồ sơ bệnh án phân trang phục vụ API
    public function getPatientRecordsForApi($patientId, $filters = []) {
        $page = isset($filters['page']) ? (int)$filters['page'] : 1;
        $limit = isset($filters['limit']) ? (int)$filters['limit'] : 10;
        if ($limit > 50) $limit = 50;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT m.id, m.created_at as visit_date, 
                       ud.name as doctor_name, 
                       dep.name as department_name, 
                       m.diagnosis, m.icd10_code
                FROM medical_records m
                JOIN doctors d ON m.doctor_id = d.id
                JOIN users ud ON d.user_id = ud.id
                LEFT JOIN departments dep ON d.department_id = dep.id
                WHERE m.patient_id = :patient_id AND m.deleted_at IS NULL
                ORDER BY m.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map status hoàn thành ('completed')
        foreach ($records as &$rec) {
            $rec['status'] = 'completed';
        }

        // Đếm tổng
        $countSql = "SELECT COUNT(*) as total FROM medical_records m WHERE m.patient_id = :patient_id AND m.deleted_at IS NULL";
        $countStmt = $this->conn->prepare($countSql);
        $countStmt->execute([':patient_id' => $patientId]);
        $total = (int)$countStmt->fetch()['total'];

        return [
            'data' => $records,
            'meta' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ];
    }

    public function getRecentByPatientId($patientId, $limit = 3) {
        $sql = "SELECT m.*, d.user_id as doctor_user_id, ud.name as doctor_name
                FROM medical_records m
                JOIN doctors d ON m.doctor_id = d.id
                JOIN users ud ON d.user_id = ud.id
                WHERE m.patient_id = :patient_id AND m.deleted_at IS NULL
                ORDER BY m.created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
