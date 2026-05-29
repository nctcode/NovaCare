<?php
/**
 * Invoice Model - Quản lý hóa đơn thanh toán
 * 
 * Đã tích hợp:
 * - Soft Delete (WHERE deleted_at IS NULL)
 * - Tính tổng tiền hóa đơn bằng PHP (thay vì Trigger DB)
 * - Cấu trúc invoice_items mới (service_id, medicine_id, room_id thay cho polymorphic)
 * - Audit Log + created_by / updated_by
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class Invoice {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả hóa đơn
    public function getAll() {
        $sql = "SELECT i.*, 
                    u.name as patient_name, u.phone as patient_phone,
                    cu.name as created_by_name
                FROM invoices i
                JOIN patients p ON i.patient_id = p.id
                JOIN users u ON p.user_id = u.id
                LEFT JOIN users cu ON i.created_by = cu.id
                WHERE i.deleted_at IS NULL
                ORDER BY i.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy hóa đơn theo bệnh nhân
    public function getByPatientId($patientId) {
        $sql = "SELECT i.*, cu.name as created_by_name 
                FROM invoices i
                LEFT JOIN users cu ON i.created_by = cu.id
                WHERE i.patient_id = :patient_id AND i.deleted_at IS NULL
                ORDER BY i.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPendingByPatientId($patientId) {
        $sql = "SELECT i.*, cu.name as created_by_name 
                FROM invoices i
                LEFT JOIN users cu ON i.created_by = cu.id
                WHERE i.patient_id = :patient_id AND i.status = 'pending' AND i.deleted_at IS NULL
                ORDER BY i.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm hóa đơn theo ID
    public function findById($id) {
        $sql = "SELECT i.*, 
                    u.name as patient_name, u.phone as patient_phone, u.email as patient_email,
                    p.address as patient_address, p.date_of_birth,
                    cu.name as created_by_name
                FROM invoices i
                JOIN patients p ON i.patient_id = p.id
                JOIN users u ON p.user_id = u.id
                LEFT JOIN users cu ON i.created_by = cu.id
                WHERE i.id = :id AND i.deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy các items của hóa đơn
    public function getItems($invoiceId) {
        $sql = "SELECT ii.*, 
                    s.service_name, 
                    med.name as medicine_name,
                    r.room_number
                FROM invoice_items ii
                LEFT JOIN services s ON ii.service_id = s.id
                LEFT JOIN medicines med ON ii.medicine_id = med.id
                LEFT JOIN rooms r ON ii.room_id = r.id
                WHERE ii.invoice_id = :invoice_id ORDER BY ii.id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':invoice_id', $invoiceId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tạo hóa đơn
    public function create($data) {
        $sql = "INSERT INTO invoices (patient_id, appointment_id, admission_id, prescription_id, total_amount, discount, final_amount, insurance_number, insurance_rate, insurance_coverage, patient_payment, payment_method, status, notes, created_by) 
                VALUES (:patient_id, :appointment_id, :admission_id, :prescription_id, :total_amount, :discount, :final_amount, :insurance_number, :insurance_rate, :insurance_coverage, :patient_payment, :payment_method, 'pending', :notes, :created_by)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindValue(':appointment_id', $data['appointment_id'] ?: null, $data['appointment_id'] ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':admission_id', $data['admission_id'] ?: null, $data['admission_id'] ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':prescription_id', $data['prescription_id'] ?: null, $data['prescription_id'] ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindParam(':total_amount', $data['total_amount']);
        $stmt->bindParam(':discount', $data['discount']);
        $stmt->bindParam(':final_amount', $data['final_amount']);
        $stmt->bindValue(':insurance_number', $data['insurance_number'] ?: null, $data['insurance_number'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':insurance_rate', $data['insurance_rate']);
        $stmt->bindParam(':insurance_coverage', $data['insurance_coverage']);
        $stmt->bindParam(':patient_payment', $data['patient_payment']);
        $stmt->bindParam(':payment_method', $data['payment_method']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':created_by', $data['created_by']);
        $stmt->execute();
        $newId = $this->conn->lastInsertId();

        AuditLog::logCreate('invoices', $newId, ['patient_id' => $data['patient_id'], 'total' => $data['total_amount']]);
        return $newId;
    }

    // Thêm item vào hóa đơn (cấu trúc mới: service_id, medicine_id, room_id)
    public function addItem($invoiceId, $item) {
        $sql = "INSERT INTO invoice_items (invoice_id, service_id, medicine_id, room_id, description, quantity, unit_price, amount) 
                VALUES (:invoice_id, :service_id, :medicine_id, :room_id, :description, :quantity, :unit_price, :amount)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':invoice_id', $invoiceId);

        $serviceId = $item['service_id'] ?? null;
        $medicineId = $item['medicine_id'] ?? null;
        $roomId = $item['room_id'] ?? null;
        $stmt->bindValue(':service_id', $serviceId, $serviceId ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':medicine_id', $medicineId, $medicineId ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':room_id', $roomId, $roomId ? PDO::PARAM_INT : PDO::PARAM_NULL);

        $stmt->bindParam(':description', $item['description']);
        $stmt->bindParam(':quantity', $item['quantity']);
        $stmt->bindParam(':unit_price', $item['unit_price']);
        $amount = $item['quantity'] * $item['unit_price'];
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();

        // Tự động cập nhật tổng tiền hóa đơn bằng PHP (thay cho Trigger đã xóa)
        $this->recalculateTotals($invoiceId);

        return $this->conn->lastInsertId();
    }

    // Tính lại tổng tiền hóa đơn từ tất cả items (PHP xử lý, không cần Trigger)
    public function recalculateTotals($invoiceId) {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM invoice_items WHERE invoice_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $invoiceId);
        $stmt->execute();
        $total = $stmt->fetch()['total'];

        // Lấy discount và tỷ lệ BHYT hiện tại
        $sql2 = "SELECT discount, insurance_rate FROM invoices WHERE id = :id";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bindParam(':id', $invoiceId);
        $stmt2->execute();
        $invoiceData = $stmt2->fetch();
        $discount = $invoiceData['discount'] ?? 0;
        $insuranceRate = $invoiceData['insurance_rate'] ?? 0;

        $final = $total - $discount;
        $insuranceCoverage = $final * ($insuranceRate / 100);
        $patientPayment = $final - $insuranceCoverage;

        $sql3 = "UPDATE invoices 
                 SET total_amount = :total, 
                     final_amount = :final, 
                     insurance_coverage = :insurance_coverage, 
                     patient_payment = :patient_payment, 
                     updated_by = :updated_by 
                 WHERE id = :id";
        $stmt3 = $this->conn->prepare($sql3);
        $stmt3->bindParam(':total', $total);
        $stmt3->bindParam(':final', $final);
        $stmt3->bindParam(':insurance_coverage', $insuranceCoverage);
        $stmt3->bindParam(':patient_payment', $patientPayment);
        $userId = $_SESSION['user']['id'] ?? null;
        $stmt3->bindParam(':updated_by', $userId);
        $stmt3->bindParam(':id', $invoiceId);
        $stmt3->execute();
    }

    // Cập nhật tổng tiền (giữ lại cho backward compatibility)
    public function updateTotals($invoiceId, $discount = 0) {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM invoice_items WHERE invoice_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $invoiceId);
        $stmt->execute();
        $total = $stmt->fetch()['total'];

        $sql2 = "SELECT insurance_rate FROM invoices WHERE id = :id";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bindParam(':id', $invoiceId);
        $stmt2->execute();
        $insuranceRate = $stmt2->fetch()['insurance_rate'] ?? 0;

        $final = $total - $discount;
        $insuranceCoverage = $final * ($insuranceRate / 100);
        $patientPayment = $final - $insuranceCoverage;

        $userId = $_SESSION['user']['id'] ?? null;
        $sql3 = "UPDATE invoices 
                 SET total_amount = :total, 
                     discount = :discount, 
                     final_amount = :final, 
                     insurance_coverage = :insurance_coverage, 
                     patient_payment = :patient_payment, 
                     updated_by = :updated_by 
                 WHERE id = :id";
        $stmt3 = $this->conn->prepare($sql3);
        $stmt3->bindParam(':total', $total);
        $stmt3->bindParam(':discount', $discount);
        $stmt3->bindParam(':final', $final);
        $stmt3->bindParam(':insurance_coverage', $insuranceCoverage);
        $stmt3->bindParam(':patient_payment', $patientPayment);
        $stmt3->bindParam(':updated_by', $userId);
        $stmt3->bindParam(':id', $invoiceId);
        $stmt3->execute();
    }

    // Đánh dấu đã thanh toán
    public function markPaid($id, $method) {
        $userId = $_SESSION['user']['id'] ?? null;
        $sql = "UPDATE invoices SET status = 'paid', payment_method = :method, updated_by = :updated_by WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':method', $method);
        $stmt->bindParam(':updated_by', $userId);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        // Tự động cập nhật trạng thái đơn thuốc liên kết thành 'paid'
        $invoice = $this->findById($id);
        if ($invoice && !empty($invoice['prescription_id'])) {
            require_once __DIR__ . '/Prescription.php';
            $prescriptionModel = new Prescription();
            $prescriptionModel->updateStatus($invoice['prescription_id'], 'paid');
        }

        AuditLog::logUpdate('invoices', $id, ['status' => 'pending'], ['status' => 'paid', 'method' => $method]);
        return $result;
    }

    // Hủy hóa đơn (soft: đổi status, không xóa)
    public function cancel($id) {
        $userId = $_SESSION['user']['id'] ?? null;
        $sql = "UPDATE invoices SET status = 'cancelled', updated_by = :updated_by WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':updated_by', $userId);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        // Tự động chuyển đơn thuốc liên kết về lại 'draft'
        $invoice = $this->findById($id);
        if ($invoice && !empty($invoice['prescription_id'])) {
            require_once __DIR__ . '/Prescription.php';
            $prescriptionModel = new Prescription();
            $prescriptionModel->updateStatus($invoice['prescription_id'], 'draft');
        }

        AuditLog::logUpdate('invoices', $id, null, ['status' => 'cancelled']);
        return $result;
    }

    // Xóa mềm hóa đơn
    public function delete($id) {
        $old = $this->findById($id);
        $userId = $_SESSION['user']['id'] ?? null;

        $sql = "UPDATE invoices SET deleted_at = NOW(), updated_by = :updated_by WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':updated_by', $userId);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        AuditLog::logDelete('invoices', $id, $old ? ['total' => $old['total_amount']] : null);
        return $result;
    }

    // Đếm hóa đơn
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM invoices WHERE deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM invoices WHERE status = :status AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    // Tổng doanh thu (đã thanh toán)
    public function getTotalRevenue() {
        $sql = "SELECT COALESCE(SUM(final_amount), 0) as revenue FROM invoices WHERE status = 'paid' AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['revenue'];
    }

    // Hóa đơn gần đây
    public function getRecentInvoices($limit = 5) {
        $sql = "SELECT i.*, u.name as patient_name
                FROM invoices i
                JOIN patients p ON i.patient_id = p.id
                JOIN users u ON p.user_id = u.id
                WHERE i.deleted_at IS NULL
                ORDER BY i.created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách dịch vụ 
    public function getServices() {
        $sql = "SELECT * FROM services ORDER BY service_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy danh sách thuốc
    public function getMedicines() {
        $sql = "SELECT * FROM medicines ORDER BY name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy danh sách bệnh nhân (chỉ lấy chưa bị xóa mềm)
    public function getPatients() {
        $sql = "SELECT p.id, u.name, u.phone, p.insurance_number FROM patients p JOIN users u ON p.user_id = u.id WHERE p.deleted_at IS NULL ORDER BY u.name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
