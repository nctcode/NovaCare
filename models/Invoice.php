<?php
/**
 * Invoice Model - Quản lý hóa đơn thanh toán
 */
require_once __DIR__ . '/../config/database.php';

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
                WHERE i.patient_id = :patient_id
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
                WHERE i.patient_id = :patient_id AND i.status = 'pending'
                ORDER BY i.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm hóa đơn theo ID (kèm thông tin bệnh nhân)
    public function findById($id) {
        $sql = "SELECT i.*, 
                    u.name as patient_name, u.phone as patient_phone, u.email as patient_email,
                    p.address as patient_address, p.date_of_birth,
                    cu.name as created_by_name
                FROM invoices i
                JOIN patients p ON i.patient_id = p.id
                JOIN users u ON p.user_id = u.id
                LEFT JOIN users cu ON i.created_by = cu.id
                WHERE i.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy các items của hóa đơn
    public function getItems($invoiceId) {
        $sql = "SELECT * FROM invoice_items WHERE invoice_id = :invoice_id ORDER BY id ASC";
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
        return $this->conn->lastInsertId();
    }

    // Thêm item vào hóa đơn
    public function addItem($invoiceId, $item) {
        $sql = "INSERT INTO invoice_items (invoice_id, item_type, item_id, description, quantity, unit_price, amount) 
                VALUES (:invoice_id, :item_type, :item_id, :description, :quantity, :unit_price, :amount)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':invoice_id', $invoiceId);
        $stmt->bindParam(':item_type', $item['item_type']);
        $stmt->bindParam(':item_id', $item['item_id']);
        $stmt->bindParam(':description', $item['description']);
        $stmt->bindParam(':quantity', $item['quantity']);
        $stmt->bindParam(':unit_price', $item['unit_price']);
        $amount = $item['quantity'] * $item['unit_price'];
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Cập nhật tổng tiền
    public function updateTotals($invoiceId, $discount = 0) {
        // Tính tổng từ items
        $sql = "SELECT SUM(amount) as total FROM invoice_items WHERE invoice_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $invoiceId);
        $stmt->execute();
        $row = $stmt->fetch();
        $total = $row['total'] ?? 0;
        $final = $total - $discount;

        $sql2 = "UPDATE invoices SET total_amount = :total, discount = :discount, final_amount = :final WHERE id = :id";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bindParam(':total', $total);
        $stmt2->bindParam(':discount', $discount);
        $stmt2->bindParam(':final', $final);
        $stmt2->bindParam(':id', $invoiceId);
        $stmt2->execute();
    }

    // Đánh dấu đã thanh toán
    public function markPaid($id, $method) {
        $sql = "UPDATE invoices SET status = 'paid', payment_method = :method WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':method', $method);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Hủy hóa đơn
    public function cancel($id) {
        $sql = "UPDATE invoices SET status = 'cancelled' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Đếm hóa đơn
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM invoices";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM invoices WHERE status = :status";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    // Tổng doanh thu (đã thanh toán)
    public function getTotalRevenue() {
        $sql = "SELECT COALESCE(SUM(final_amount), 0) as revenue FROM invoices WHERE status = 'paid'";
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

    // Lấy danh sách bệnh nhân
    public function getPatients() {
        $sql = "SELECT p.id, u.name, u.phone FROM patients p JOIN users u ON p.user_id = u.id ORDER BY u.name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
