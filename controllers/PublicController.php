<?php
/**
 * PublicController - Các trang public (landing page, about, services, etc.)
 */
require_once __DIR__ . '/../config/database.php';

class PublicController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Trang chủ (Home)
    public function home() {
        // Lấy dữ liệu thống kê
        $stats = $this->getStats();
        // Lấy dịch vụ
        $services = $this->getServices();
        // Lấy danh sách bác sĩ
        $doctors = $this->getDoctorsList();
        
        require_once __DIR__ . '/../views/public/home.php';
    }

    // Trang Giới thiệu
    public function about() {
        $stats = $this->getStats();
        require_once __DIR__ . '/../views/public/about.php';
    }

    // Trang Dịch vụ
    public function services() {
        $services = $this->getServices();
        require_once __DIR__ . '/../views/public/services.php';
    }

    // Trang Bác sĩ
    public function doctors() {
        $doctors = $this->getDoctorsList();
        require_once __DIR__ . '/../views/public/doctors.php';
    }

    // Trang Liên hệ
    public function contact() {
        require_once __DIR__ . '/../views/public/contact.php';
    }

    // Trang Đặt lịch khám (public)
    public function bookappointment() {
        $doctors = $this->getDoctorsList();
        require_once __DIR__ . '/../views/public/book_appointment.php';
    }

    // --- Helpers ---
    private function getStats() {
        $stats = [];
        
        $stmt = $this->conn->query("SELECT COUNT(*) as c FROM doctors");
        $stats['doctors'] = $stmt->fetch()['c'];
        
        $stmt = $this->conn->query("SELECT COUNT(*) as c FROM patients");
        $stats['patients'] = $stmt->fetch()['c'];
        
        $stmt = $this->conn->query("SELECT COUNT(*) as c FROM departments");
        $stats['departments'] = $stmt->fetch()['c'];
        
        $stmt = $this->conn->query("SELECT COUNT(*) as c FROM appointments");
        $stats['appointments'] = $stmt->fetch()['c'];
        
        return $stats;
    }

    private function getServices() {
        $stmt = $this->conn->query("SELECT * FROM services ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    private function getDoctorsList() {
        $sql = "SELECT d.*, u.name, u.email, u.phone, dep.name as department_name 
                FROM doctors d 
                JOIN users u ON d.user_id = u.id 
                LEFT JOIN departments dep ON d.department_id = dep.id 
                ORDER BY u.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
