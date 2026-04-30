<?php
/**
 * Shift Model - Quản lý ca trực
 * 
 * Quy tắc:
 * - Mỗi người phải đăng ký ít nhất 2 ca night / tuần
 * - Mỗi ca night tối đa 20 bác sĩ
 */
require_once __DIR__ . '/../config/database.php';

class Shift {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả ca trực (dùng LEFT JOIN thay correlated subquery cho performance)
    public function getAll() {
        $sql = "SELECT s.*, COALESCE(ds_count.registered_count, 0) as registered_count
                FROM shifts s
                LEFT JOIN (
                    SELECT shift_id, COUNT(*) as registered_count 
                    FROM doctor_shifts 
                    GROUP BY shift_id
                ) ds_count ON ds_count.shift_id = s.id
                ORDER BY s.shift_date ASC, s.shift_type ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm ca trực theo ID
    public function findById($id) {
        $sql = "SELECT * FROM shifts WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Tạo ca trực mới
    public function create($data) {
        $sql = "INSERT INTO shifts (shift_date, shift_type) VALUES (:shift_date, :shift_type)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':shift_date', $data['shift_date']);
        $stmt->bindParam(':shift_type', $data['shift_type']);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Đăng ký ca trực cho bác sĩ/y tá
    public function registerShift($doctorId, $shiftId) {
        // Kiểm tra đã đăng ký chưa
        $sql = "SELECT COUNT(*) as cnt FROM doctor_shifts WHERE doctor_id = :doctor_id AND shift_id = :shift_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->bindParam(':shift_id', $shiftId);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row['cnt'] > 0) return 'already_registered';

        // Kiểm tra ca night tối đa 20 người
        $shift = $this->findById($shiftId);
        if ($shift['shift_type'] === 'night') {
            $sql = "SELECT COUNT(*) as cnt FROM doctor_shifts WHERE shift_id = :shift_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':shift_id', $shiftId);
            $stmt->execute();
            $row = $stmt->fetch();
            if ($row['cnt'] >= 20) return 'night_shift_full';
        }

        // Đăng ký
        $sql = "INSERT INTO doctor_shifts (doctor_id, shift_id) VALUES (:doctor_id, :shift_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->bindParam(':shift_id', $shiftId);
        $stmt->execute();
        return 'success';
    }

    // Hủy đăng ký ca trực
    public function unregisterShift($doctorId, $shiftId) {
        $sql = "DELETE FROM doctor_shifts WHERE doctor_id = :doctor_id AND shift_id = :shift_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->bindParam(':shift_id', $shiftId);
        return $stmt->execute();
    }

    // Lấy ca trực của 1 bác sĩ
    public function getShiftsByDoctorId($doctorId) {
        $sql = "SELECT s.*, ds.id as registration_id
                FROM doctor_shifts ds 
                JOIN shifts s ON ds.shift_id = s.id 
                WHERE ds.doctor_id = :doctor_id 
                ORDER BY s.shift_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm ca night trong tuần của 1 bác sĩ
    public function countNightShiftsInWeek($doctorId, $weekStart) {
        $weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));
        $sql = "SELECT COUNT(*) as cnt FROM doctor_shifts ds 
                JOIN shifts s ON ds.shift_id = s.id 
                WHERE ds.doctor_id = :doctor_id 
                AND s.shift_type = 'night' 
                AND s.shift_date BETWEEN :week_start AND :week_end";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->bindParam(':week_start', $weekStart);
        $stmt->bindParam(':week_end', $weekEnd);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['cnt'];
    }

    // Lấy danh sách bác sĩ đăng ký ca trực
    public function getRegisteredDoctors($shiftId) {
        $sql = "SELECT u.name, u.phone, d.specialty
                FROM doctor_shifts ds
                JOIN doctors d ON ds.doctor_id = d.id
                JOIN users u ON d.user_id = u.id
                WHERE ds.shift_id = :shift_id
                ORDER BY u.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':shift_id', $shiftId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
