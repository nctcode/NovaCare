<?php
/**
 * ReportController - Báo cáo & Thống kê cho Ban giám đốc
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Security.php';

class ReportController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Trang báo cáo chính
    public function index() {
        Security::requireRole(['admin', 'director']);

        $stats = $this->getOverviewStats();
        $revenueByMonth = $this->getRevenueByMonth();
        $patientsByDept = $this->getPatientsByDepartment();
        $appointmentsByStatus = $this->getAppointmentsByStatus();
        $topDoctors = $this->getTopDoctors();
        $bedOccupancy = $this->getBedOccupancy();

        $pageTitle = 'Báo cáo & Thống kê';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/reports/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // API JSON cho Chart.js
    public function chartData() {
        Security::requireRole(['admin', 'director']);
        $type = $_GET['type'] ?? '';
        $data = [];

        switch ($type) {
            case 'revenue':
                $data = $this->getRevenueByMonth();
                break;
            case 'patients_dept':
                $data = $this->getPatientsByDepartment();
                break;
            case 'appointments':
                $data = $this->getAppointmentsByStatus();
                break;
        }

        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Thống kê tổng quan
    private function getOverviewStats() {
        $stats = [];

        // Tổng BN
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM patients WHERE deleted_at IS NULL");
        $stats['total_patients'] = $stmt->fetch()['total'];

        // Tổng BS
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM doctors");
        $stats['total_doctors'] = $stmt->fetch()['total'];

        // Tổng Y tá
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM nurses");
        $stats['total_nurses'] = $stmt->fetch()['total'];

        // Lịch hẹn hôm nay
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM appointments WHERE DATE(appointment_date) = CURDATE()");
        $stats['today_appointments'] = $stmt->fetch()['total'];

        // Doanh thu tháng này
        $stmt = $this->conn->query("SELECT COALESCE(SUM(final_amount), 0) as total FROM invoices WHERE status = 'paid' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND deleted_at IS NULL");
        $stats['month_revenue'] = $stmt->fetch()['total'];

        // Tổng doanh thu
        $stmt = $this->conn->query("SELECT COALESCE(SUM(final_amount), 0) as total FROM invoices WHERE status = 'paid' AND deleted_at IS NULL");
        $stats['total_revenue'] = $stmt->fetch()['total'];

        // BN nội trú hiện tại
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM admissions WHERE status = 'admitted'");
        $stats['current_inpatients'] = $stmt->fetch()['total'];

        // Thuốc tồn kho thấp
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM medicines WHERE quantity <= 10");
        $stats['low_stock_medicines'] = $stmt->fetch()['total'];

        // CLS chờ xử lý
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM lab_orders WHERE status IN ('pending','in_progress')");
            $stats['pending_lab_orders'] = $stmt->fetch()['total'];
        } catch (Exception $e) {
            $stats['pending_lab_orders'] = 0;
        }

        return $stats;
    }

    // Doanh thu theo tháng (12 tháng gần nhất)
    private function getRevenueByMonth() {
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                       COALESCE(SUM(final_amount), 0) as revenue,
                       COUNT(*) as invoice_count
                FROM invoices 
                WHERE status = 'paid' AND deleted_at IS NULL
                  AND created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Bệnh nhân theo khoa
    private function getPatientsByDepartment() {
        $sql = "SELECT dep.name as department_name, COUNT(DISTINCT mr.patient_id) as patient_count
                FROM medical_records mr
                JOIN doctors d ON mr.doctor_id = d.id
                JOIN departments dep ON d.department_id = dep.id
                WHERE mr.deleted_at IS NULL
                GROUP BY dep.id, dep.name
                ORDER BY patient_count DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lịch hẹn theo trạng thái
    private function getAppointmentsByStatus() {
        $sql = "SELECT status, COUNT(*) as total
                FROM appointments
                WHERE MONTH(appointment_date) = MONTH(CURDATE()) AND YEAR(appointment_date) = YEAR(CURDATE())
                GROUP BY status";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Top bác sĩ (số lượng khám)
    private function getTopDoctors() {
        $sql = "SELECT u.name, d.specialty, COUNT(mr.id) as record_count
                FROM medical_records mr
                JOIN doctors d ON mr.doctor_id = d.id
                JOIN users u ON d.user_id = u.id
                WHERE mr.deleted_at IS NULL AND mr.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY d.id, u.name, d.specialty
                ORDER BY record_count DESC LIMIT 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Công suất giường bệnh
    private function getBedOccupancy() {
        $result = [];
        
        // Tổng giường
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM beds");
        $result['total_beds'] = $stmt->fetch()['total'];

        // Giường đang sử dụng
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM beds WHERE status = 'occupied'");
        $result['occupied_beds'] = $stmt->fetch()['total'];

        $result['occupancy_rate'] = $result['total_beds'] > 0 
            ? round(($result['occupied_beds'] / $result['total_beds']) * 100, 1) 
            : 0;

        return $result;
    }
}
