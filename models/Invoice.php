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
        $sql = "INSERT INTO invoices (patient_id, appointment_id, admission_id, total_amount, discount, final_amount, payment_method, status, notes, created_by) 
                VALUES (:patient_id, :appointment_id, :admission_id, :total_amount, :discount, :final_amount, :payment_method, 'pending', :notes, :created_by)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':appointment_id', $data['appointment_id']);
        $stmt->bindParam(':admission_id', $data['admission_id']);
        $stmt->bindParam(':total_amount', $data['total_amount']);
        $stmt->bindParam(':discount', $data['discount']);
        $stmt->bindParam(':final_amount', $data['final_amount']);
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

        // Lấy discount hiện tại
        $sql2 = "SELECT discount FROM invoices WHERE id = :id";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bindParam(':id', $invoiceId);
        $stmt2->execute();
        $discount = $stmt2->fetch()['discount'] ?? 0;

        $final = $total - $discount;

        $sql3 = "UPDATE invoices SET total_amount = :total, final_amount = :final, updated_by = :updated_by WHERE id = :id";
        $stmt3 = $this->conn->prepare($sql3);
        $stmt3->bindParam(':total', $total);
        $stmt3->bindParam(':final', $final);
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
        $final = $total - $discount;

        $userId = $_SESSION['user']['id'] ?? null;
        $sql2 = "UPDATE invoices SET total_amount = :total, discount = :discount, final_amount = :final, updated_by = :updated_by WHERE id = :id";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bindParam(':total', $total);
        $stmt2->bindParam(':discount', $discount);
        $stmt2->bindParam(':final', $final);
        $stmt2->bindParam(':updated_by', $userId);
        $stmt2->bindParam(':id', $invoiceId);
        $stmt2->execute();
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
        $sql = "SELECT p.id, u.name, u.phone FROM patients p JOIN users u ON p.user_id = u.id WHERE p.deleted_at IS NULL ORDER BY u.name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
