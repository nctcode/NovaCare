<?php
/**
 * Prescription Model - Quản lý đơn thuốc
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class Prescription {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả đơn thuốc
    public function getAll() {
        $sql = "SELECT pr.*, 
                    du.name as doctor_name,
                    pu.name as patient_name,
                    mr.diagnosis
                FROM prescriptions pr
                JOIN doctors d ON pr.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN medical_records mr ON pr.medical_record_id = mr.id
                JOIN patients p ON mr.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                WHERE mr.deleted_at IS NULL
                ORDER BY pr.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy đơn thuốc theo bác sĩ
    public function getByDoctorId($doctorId) {
        $sql = "SELECT pr.*, 
                    du.name as doctor_name,
                    pu.name as patient_name,
                    mr.diagnosis
                FROM prescriptions pr
                JOIN doctors d ON pr.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN medical_records mr ON pr.medical_record_id = mr.id
                JOIN patients p ON mr.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                WHERE pr.doctor_id = :doctor_id AND mr.deleted_at IS NULL
                ORDER BY pr.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy đơn thuốc theo bệnh nhân
    public function getByPatientId($patientId) {
        $sql = "SELECT pr.*, 
                    du.name as doctor_name,
                    mr.diagnosis
                FROM prescriptions pr
                JOIN doctors d ON pr.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN medical_records mr ON pr.medical_record_id = mr.id
                WHERE mr.patient_id = :patient_id AND mr.deleted_at IS NULL
                ORDER BY pr.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm đơn thuốc theo ID
    public function findById($id) {
        $sql = "SELECT pr.*, 
                    du.name as doctor_name,
                    pu.name as patient_name,
                    mr.patient_id,
                    mr.diagnosis, mr.notes,
                    au.name as approved_by_name
                FROM prescriptions pr
                JOIN doctors d ON pr.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN medical_records mr ON pr.medical_record_id = mr.id
                JOIN patients p ON mr.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                LEFT JOIN users au ON pr.approved_by = au.id
                WHERE pr.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Dược sĩ duyệt đơn thuốc
    public function approve($id, $pharmacistId, $notes = '') {
        $prescription = $this->findById($id);
        if (!$prescription) return false;
        
        // Chỉ duyệt đơn thuốc đã được thanh toán (paid)
        if ($prescription['status'] !== 'paid') {
            return false;
        }

        $sql = "UPDATE prescriptions 
                SET status = 'approved', 
                    approved_by = :approved_by, 
                    approved_at = NOW(), 
                    pharmacist_notes = :pharmacist_notes 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':approved_by', $pharmacistId);
        $stmt->bindParam(':pharmacist_notes', $notes);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        if ($result) {
            AuditLog::logUpdate('prescriptions', $id, ['status' => 'paid'], ['status' => 'approved', 'notes' => $notes]);
        }
        return $result;
    }

    // Lấy items trong đơn thuốc
    public function getItems($prescriptionId) {
        $sql = "SELECT pi.*, m.name as medicine_name, m.price
                FROM prescription_items pi
                JOIN medicines m ON pi.medicine_id = m.id
                WHERE pi.prescription_id = :prescription_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':prescription_id', $prescriptionId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tạo đơn thuốc mới
    public function create($data) {
        $sql = "INSERT INTO prescriptions (medical_record_id, doctor_id) 
                VALUES (:medical_record_id, :doctor_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':medical_record_id', $data['medical_record_id']);
        $stmt->bindParam(':doctor_id', $data['doctor_id']);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Thêm thuốc vào đơn
    public function addItem($data) {
        $sql = "INSERT INTO prescription_items (prescription_id, medicine_id, quantity, dosage, duration, instructions) 
                VALUES (:prescription_id, :medicine_id, :quantity, :dosage, :duration, :instructions)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':prescription_id', $data['prescription_id']);
        $stmt->bindParam(':medicine_id', $data['medicine_id']);
        $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':dosage', $data['dosage']);
        $stmt->bindParam(':duration', $data['duration']);
        $stmt->bindParam(':instructions', $data['instructions']);
        return $stmt->execute();
    }

    // Lấy danh sách medical_records
    public function getMedicalRecords() {
        $sql = "SELECT mr.*, pu.name as patient_name, du.name as doctor_name
                FROM medical_records mr
                JOIN patients p ON mr.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                JOIN doctors d ON mr.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                WHERE mr.deleted_at IS NULL
                ORDER BY mr.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy medical_records theo doctor_id
    public function getMedicalRecordsByDoctorId($doctorId) {
        $sql = "SELECT mr.*, pu.name as patient_name
                FROM medical_records mr
                JOIN patients p ON mr.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                WHERE mr.doctor_id = :doctor_id 
                  AND mr.deleted_at IS NULL
                  AND mr.id NOT IN (
                      SELECT medical_record_id 
                      FROM prescriptions 
                      WHERE status != 'cancelled'
                  )
                ORDER BY mr.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy các đơn thuốc chưa thanh toán (status = 'draft') của bệnh nhân
    public function getUnpaidByPatientId($patientId) {
        $sql = "SELECT pr.*, 
                    du.name as doctor_name,
                    mr.diagnosis
                FROM prescriptions pr
                JOIN doctors d ON pr.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN medical_records mr ON pr.medical_record_id = mr.id
                WHERE mr.patient_id = :patient_id 
                  AND pr.status = 'draft' 
                  AND mr.deleted_at IS NULL
                ORDER BY pr.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Cập nhật trạng thái đơn thuốc và điều chỉnh kho tương ứng
    public function updateStatus($id, $status) {
        $prescription = $this->findById($id);
        if (!$prescription) return false;
        
        $oldStatus = $prescription['status'];
        if ($oldStatus === $status) return true;

        $sql = "UPDATE prescriptions SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        if ($result) {
            AuditLog::logUpdate('prescriptions', $id, ['status' => $oldStatus], ['status' => $status]);

            // Trừ kho thật hoặc giải phóng tùy vào trạng thái mới
            $items = $this->getItems($id);
            require_once __DIR__ . '/Medicine.php';
            $medicineModel = new Medicine();

            if ($status === 'dispensed') {
                // Giao thuốc: trừ tồn kho thật, giảm reserved
                foreach ($items as $item) {
                    $medicineModel->dispenseStock($item['medicine_id'], $item['quantity']);
                }
            } elseif ($status === 'cancelled') {
                // Hủy đơn thuốc: giải phóng lượng đặt trước
                if (in_array($oldStatus, ['draft', 'paid', 'approved'])) {
                    foreach ($items as $item) {
                        $medicineModel->releaseStock($item['medicine_id'], $item['quantity']);
                    }
                }
            } elseif ($status === 'draft' && $oldStatus === 'cancelled') {
                // Khôi phục: đặt trước lại
                foreach ($items as $item) {
                    $medicineModel->reserveStock($item['medicine_id'], $item['quantity']);
                }
            }
        }
        return $result;
    }

    // Lấy đơn thuốc phân trang phục vụ API
    public function getPatientPrescriptionsForApi($patientId, $filters = []) {
        $page = isset($filters['page']) ? (int)$filters['page'] : 1;
        $limit = isset($filters['limit']) ? (int)$filters['limit'] : 10;
        if ($limit > 50) $limit = 50;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT pr.id, pr.created_at, 
                       du.name as doctor_name, pr.status,
                       (SELECT COUNT(*) FROM prescription_items pi WHERE pi.prescription_id = pr.id) as total_items
                FROM prescriptions pr
                JOIN doctors d ON pr.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN medical_records mr ON pr.medical_record_id = mr.id
                WHERE mr.patient_id = :patient_id AND mr.deleted_at IS NULL";

        $params = [':patient_id' => $patientId];

        if (!empty($filters['status'])) {
            $sql .= " AND pr.status = :status";
            $params[':status'] = $filters['status'];
        }

        $sql .= " ORDER BY pr.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Đếm tổng số đơn thuốc cho meta
        $countSql = "SELECT COUNT(*) as total 
                     FROM prescriptions pr 
                     JOIN medical_records mr ON pr.medical_record_id = mr.id 
                     WHERE mr.patient_id = :patient_id AND mr.deleted_at IS NULL";
        if (!empty($filters['status'])) {
            $countSql .= " AND pr.status = :status";
        }
        $countStmt = $this->conn->prepare($countSql);
        foreach ($params as $key => $val) {
            $countStmt->bindValue($key, $val);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetch()['total'];

        return [
            'data' => $prescriptions,
            'meta' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ];
    }
}
