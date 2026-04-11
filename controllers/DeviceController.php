<?php
/**
 * DeviceController - Quản lý thiết bị y tế (Admin)
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Doctor.php';

class DeviceController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Danh sách thiết bị
    public function index() {
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

        $pageTitle = 'Quản lý Thiết bị Y tế';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/devices/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form thêm thiết bị
    public function create() {
        $sql = "SELECT * FROM departments ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $departments = $stmt->fetchAll();

        $pageTitle = 'Thêm Thiết bị Y tế';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/devices/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu thiết bị mới
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                $_SESSION['success'] = 'Thêm thiết bị thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        header('Location: index.php?page=devices');
        exit;
    }

    // Cập nhật trạng thái
    public function updateStatus() {
        $id = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? '';

        $validStatuses = ['available', 'in_use', 'maintenance'];
        if (in_array($status, $validStatuses)) {
            $sql = "UPDATE medical_devices SET status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);

            try {
                $stmt->execute();
                $_SESSION['success'] = 'Cập nhật trạng thái thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        header('Location: index.php?page=devices');
        exit;
    }

    // Xóa thiết bị
    public function delete() {
        $id = $_GET['id'] ?? 0;
        $sql = "DELETE FROM medical_devices WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();
            $_SESSION['success'] = 'Xóa thiết bị thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=devices');
        exit;
    }
}
