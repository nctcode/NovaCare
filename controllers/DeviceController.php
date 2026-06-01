<?php
/**
 * DeviceController - Quản lý thiết bị y tế (Admin only)
 * 
 * Đã hardened:
 * - Thêm Security::requireRole cho mọi action
 * - Chuyển updateStatus và delete sang POST
 * - Thêm CSRF protection
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class DeviceController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Danh sách thiết bị
    public function index() {
        Security::requireRole(['admin', 'director', 'technician']);
        $sql = "SELECT md.*, dep.name as department_name 
                FROM medical_devices md 
                LEFT JOIN departments dep ON md.department_id = dep.id 
                ORDER BY md.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $devices = $stmt->fetchAll();

        // Lấy departments cho form
        $sql = "SELECT * FROM departments ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $departments = $stmt->fetchAll();

        $pageTitle = 'Quản lý Máy móc y tế';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/devices/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form thêm thiết bị
    public function create() {
        Security::requireRole('admin');
        $sql = "SELECT * FROM departments ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $departments = $stmt->fetchAll();

        $pageTitle = 'Thêm Máy móc y tế';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/devices/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu thiết bị mới
    public function store() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=devices');
        Security::requireCsrf();

        $sql = "INSERT INTO medical_devices (name, device_code, status, department_id, purchase_date) 
                VALUES (:name, :device_code, :status, :department_id, :purchase_date)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $_POST['name']);
        $stmt->bindParam(':device_code', $_POST['device_code']);
        $stmt->bindParam(':status', $_POST['status']);
        $stmt->bindParam(':department_id', $_POST['department_id']);
        $stmt->bindParam(':purchase_date', $_POST['purchase_date']);

        try {
            $stmt->execute();
            $id = $this->conn->lastInsertId();
            AuditLog::logCreate('medical_devices', $id, ['name' => $_POST['name']]);
            $_SESSION['success'] = 'Thêm thiết bị thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=devices');
        exit;
    }

    // Cập nhật trạng thái (POST only)
    public function updateStatus() {
        Security::requireRole(['admin', 'technician']);
        Security::requirePost('index.php?page=devices');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';

        $validStatuses = ['available', 'in_use', 'maintenance'];
        if (in_array($status, $validStatuses)) {
            $sql = "UPDATE medical_devices SET status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);

            try {
                $stmt->execute();
                AuditLog::logUpdate('medical_devices', $id, null, ['status' => $status]);
                $_SESSION['success'] = 'Cập nhật trạng thái thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Trạng thái không hợp lệ.';
        }
        header('Location: index.php?page=devices');
        exit;
    }

    // Xóa thiết bị (POST only)
    public function delete() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=devices');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        $sql = "DELETE FROM medical_devices WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();
            AuditLog::logDelete('medical_devices', $id);
            $_SESSION['success'] = 'Xóa thiết bị thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=devices');
        exit;
    }
}
