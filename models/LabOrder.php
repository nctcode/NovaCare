<?php
/**
 * LabOrder Model - Chỉ định Cận lâm sàng (Xét nghiệm / Chẩn đoán hình ảnh)
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class LabOrder {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả chỉ định CLS
    public function getAll($filters = []) {
        $sql = "SELECT lo.*, 
                    du.name as doctor_name,
                    pu.name as patient_name,
                    pu.phone as patient_phone
                FROM lab_orders lo
                JOIN doctors d ON lo.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN patients p ON lo.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id";
        
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "lo.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['order_type'])) {
            $where[] = "lo.order_type = :order_type";
            $params[':order_type'] = $filters['order_type'];
        }
        if (!empty($filters['doctor_id'])) {
            $where[] = "lo.doctor_id = :doctor_id";
            $params[':doctor_id'] = $filters['doctor_id'];
        }
        if (!empty($filters['patient_id'])) {
            $where[] = "lo.patient_id = :patient_id";
            $params[':patient_id'] = $filters['patient_id'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " ORDER BY lo.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Lấy chỉ định theo bác sĩ
    public function getByDoctorId($doctorId) {
        return $this->getAll(['doctor_id' => $doctorId]);
    }

    // Lấy chỉ định chờ xử lý (cho KTV)
    public function getPending() {
        return $this->getAll(['status' => 'pending']);
    }

    // Lấy chỉ định đang xử lý (cho KTV)
    public function getInProgress() {
        return $this->getAll(['status' => 'in_progress']);
    }

    // Tìm theo ID
    public function findById($id) {
        $sql = "SELECT lo.*, 
                    du.name as doctor_name,
                    pu.name as patient_name, pu.phone as patient_phone,
                    p.date_of_birth, p.gender
                FROM lab_orders lo
                JOIN doctors d ON lo.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN patients p ON lo.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                WHERE lo.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Tạo chỉ định CLS
    public function create($data) {
        $sql = "INSERT INTO lab_orders (patient_id, doctor_id, appointment_id, order_type, test_name, priority, notes, created_by)
                VALUES (:patient_id, :doctor_id, :appointment_id, :order_type, :test_name, :priority, :notes, :created_by)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':doctor_id', $data['doctor_id']);
        $stmt->bindValue(':appointment_id', $data['appointment_id'] ?: null, $data['appointment_id'] ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindParam(':order_type', $data['order_type']);
        $stmt->bindParam(':test_name', $data['test_name']);
        $stmt->bindParam(':priority', $data['priority']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':created_by', $data['created_by']);
        $stmt->execute();
        $id = $this->conn->lastInsertId();
        AuditLog::logCreate('lab_orders', $id, ['test_name' => $data['test_name'], 'patient_id' => $data['patient_id']]);
        return $id;
    }

    // Cập nhật trạng thái
    public function updateStatus($id, $status) {
        $sql = "UPDATE lab_orders SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();
        AuditLog::logUpdate('lab_orders', $id, null, ['status' => $status]);
        return $result;
    }

    // Lấy kết quả của chỉ định
    public function getResult($labOrderId) {
        $sql = "SELECT lr.*, u.name as technician_name
                FROM lab_results lr
                JOIN users u ON lr.technician_id = u.id
                WHERE lr.lab_order_id = :lab_order_id
                ORDER BY lr.created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':lab_order_id', $labOrderId);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lưu kết quả CLS (KTV nhập)
    public function saveResult($data) {
        $sql = "INSERT INTO lab_results (lab_order_id, technician_id, result_text, result_value, normal_range, unit, conclusion, completed_at, image_path)
                VALUES (:lab_order_id, :technician_id, :result_text, :result_value, :normal_range, :unit, :conclusion, NOW(), :image_path)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':lab_order_id', $data['lab_order_id']);
        $stmt->bindParam(':technician_id', $data['technician_id']);
        $stmt->bindParam(':result_text', $data['result_text']);
        $stmt->bindParam(':result_value', $data['result_value']);
        $stmt->bindParam(':normal_range', $data['normal_range']);
        $stmt->bindParam(':unit', $data['unit']);
        $stmt->bindParam(':conclusion', $data['conclusion']);
        $stmt->bindParam(':image_path', $data['image_path']);
        $stmt->execute();
        $id = $this->conn->lastInsertId();

        // Cập nhật trạng thái chỉ định thành completed
        $this->updateStatus($data['lab_order_id'], 'completed');

        AuditLog::logCreate('lab_results', $id, ['lab_order_id' => $data['lab_order_id']]);
        return $id;
    }

    // Lấy kết quả CLS theo bệnh nhân (cho BS xem)
    public function getResultsByPatientId($patientId) {
        $sql = "SELECT lo.*, lr.result_text, lr.result_value, lr.normal_range, lr.unit, lr.conclusion, lr.image_path,
                    lr.completed_at as result_date, u.name as technician_name,
                    du.name as doctor_name
                FROM lab_orders lo
                LEFT JOIN lab_results lr ON lr.lab_order_id = lo.id
                JOIN doctors d ON lo.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                LEFT JOIN users u ON lr.technician_id = u.id
                WHERE lo.patient_id = :patient_id
                ORDER BY lo.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm theo trạng thái
    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM lab_orders WHERE status = :status";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    // Đếm tổng
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM lab_orders";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    // Lấy danh sách bệnh nhân (cho dropdown)
    public function getPatients() {
        $sql = "SELECT p.id, u.name, u.phone FROM patients p JOIN users u ON p.user_id = u.id WHERE p.deleted_at IS NULL ORDER BY u.name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
