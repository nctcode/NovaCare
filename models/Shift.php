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
        $this->checkAndMigrate();
    }

    private function checkAndMigrate() {
        // Check shifts table
        try {
            $stmt = $this->conn->prepare("SHOW COLUMNS FROM shifts LIKE 'department_id'");
            $stmt->execute();
            if (!$stmt->fetch()) {
                $this->conn->exec("ALTER TABLE shifts 
                    ADD COLUMN department_id int DEFAULT NULL,
                    ADD COLUMN name varchar(150) DEFAULT NULL,
                    ADD COLUMN start_time time DEFAULT NULL,
                    ADD COLUMN end_time time DEFAULT NULL,
                    ADD COLUMN required_doctors int DEFAULT 0,
                    ADD COLUMN required_nurses int DEFAULT 0,
                    ADD COLUMN notes text,
                    ADD INDEX (department_id),
                    ADD CONSTRAINT fk_shifts_dept FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE SET NULL ON UPDATE CASCADE
                ");
            }
        } catch (Exception $e) {
            error_log("Migration error for shifts: " . $e->getMessage());
        }

        // Check doctor_shifts table
        try {
            $stmt = $this->conn->prepare("SHOW COLUMNS FROM doctor_shifts LIKE 'status'");
            $stmt->execute();
            if (!$stmt->fetch()) {
                $this->conn->exec("ALTER TABLE doctor_shifts ADD COLUMN status enum('pending','approved','rejected') DEFAULT 'pending'");
            }
        } catch (Exception $e) {
            error_log("Migration error for doctor_shifts: " . $e->getMessage());
        }

        // Check nurse_shifts table
        try {
            $stmt = $this->conn->prepare("SHOW COLUMNS FROM nurse_shifts LIKE 'status'");
            $stmt->execute();
            if (!$stmt->fetch()) {
                $this->conn->exec("ALTER TABLE nurse_shifts ADD COLUMN status enum('pending','approved','rejected') DEFAULT 'pending'");
            }
        } catch (Exception $e) {
            error_log("Migration error for nurse_shifts: " . $e->getMessage());
        }
    }

    // Lấy tất cả ca trực (hỗ trợ filter khoa và đếm số lượng đã duyệt/chờ duyệt)
    public function getAll($departmentId = null) {
        $sql = "SELECT s.*, dep.name as department_name,
                       (SELECT COUNT(*) FROM doctor_shifts ds WHERE ds.shift_id = s.id AND ds.status = 'approved') as approved_doctors,
                       (SELECT COUNT(*) FROM nurse_shifts ns WHERE ns.shift_id = s.id AND ns.status = 'approved') as approved_nurses,
                       (SELECT COUNT(*) FROM doctor_shifts ds WHERE ds.shift_id = s.id) as total_registered_doctors,
                       (SELECT COUNT(*) FROM nurse_shifts ns WHERE ns.shift_id = s.id) as total_registered_nurses
                FROM shifts s
                LEFT JOIN departments dep ON s.department_id = dep.id";
        
        $params = [];
        if ($departmentId !== null) {
            $sql .= " WHERE s.department_id = :department_id";
            $params[':department_id'] = $departmentId;
        }
        
        $sql .= " ORDER BY s.shift_date ASC, s.start_time ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
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

    // Tạo ca trực mới với đầy đủ thông tin
    public function create($data) {
        $sql = "INSERT INTO shifts (department_id, name, shift_date, start_time, end_time, required_doctors, required_nurses, shift_type, notes) 
                VALUES (:department_id, :name, :shift_date, :start_time, :end_time, :required_doctors, :required_nurses, :shift_type, :notes)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':department_id', $data['department_id']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':shift_date', $data['shift_date']);
        $stmt->bindParam(':start_time', $data['start_time']);
        $stmt->bindParam(':end_time', $data['end_time']);
        $stmt->bindParam(':required_doctors', $data['required_doctors']);
        $stmt->bindParam(':required_nurses', $data['required_nurses']);
        $stmt->bindParam(':shift_type', $data['shift_type']);
        $stmt->bindParam(':notes', $data['notes']);
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
        $sql = "SELECT s.*, ds.id as registration_id, ds.status as registration_status, dep.name as department_name
                FROM doctor_shifts ds 
                JOIN shifts s ON ds.shift_id = s.id 
                LEFT JOIN departments dep ON s.department_id = dep.id
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

    // ==========================================
    //  NURSE SHIFTS (Bảng nurse_shifts)
    // ==========================================

    // Đăng ký ca trực cho y tá
    public function registerNurseShift($nurseId, $shiftId) {
        // Kiểm tra đã đăng ký chưa
        $sql = "SELECT COUNT(*) as cnt FROM nurse_shifts WHERE nurse_id = :nurse_id AND shift_id = :shift_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nurse_id', $nurseId);
        $stmt->bindParam(':shift_id', $shiftId);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row['cnt'] > 0) return 'already_registered';

        // Kiểm tra ca night tối đa 20 người (tổng cả doctor + nurse)
        $shift = $this->findById($shiftId);
        if ($shift['shift_type'] === 'night') {
            $sql = "SELECT 
                        (SELECT COUNT(*) FROM doctor_shifts WHERE shift_id = :s1) +
                        (SELECT COUNT(*) FROM nurse_shifts WHERE shift_id = :s2) as total";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':s1', $shiftId);
            $stmt->bindParam(':s2', $shiftId);
            $stmt->execute();
            $row = $stmt->fetch();
            if ($row['total'] >= 20) return 'night_shift_full';
        }

        $sql = "INSERT INTO nurse_shifts (nurse_id, shift_id) VALUES (:nurse_id, :shift_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nurse_id', $nurseId);
        $stmt->bindParam(':shift_id', $shiftId);
        $stmt->execute();
        return 'success';
    }

    // Hủy đăng ký ca trực y tá
    public function unregisterNurseShift($nurseId, $shiftId) {
        $sql = "DELETE FROM nurse_shifts WHERE nurse_id = :nurse_id AND shift_id = :shift_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nurse_id', $nurseId);
        $stmt->bindParam(':shift_id', $shiftId);
        return $stmt->execute();
    }

    // Lấy ca trực của 1 y tá
    public function getShiftsByNurseId($nurseId) {
        $sql = "SELECT s.*, ns.id as registration_id, ns.status as registration_status, dep.name as department_name
                FROM nurse_shifts ns 
                JOIN shifts s ON ns.shift_id = s.id 
                LEFT JOIN departments dep ON s.department_id = dep.id
                WHERE ns.nurse_id = :nurse_id 
                ORDER BY s.shift_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nurse_id', $nurseId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy danh sách đăng ký chờ duyệt cho một ca trực (Trưởng khoa dùng)
    public function getPendingRegistrations($shiftId) {
        // Lấy danh sách bác sĩ
        $sqlDoc = "SELECT ds.id as registration_id, 'doctor' as role, u.name, u.phone, d.specialty, ds.status
                   FROM doctor_shifts ds
                   JOIN doctors d ON ds.doctor_id = d.id
                   JOIN users u ON d.user_id = u.id
                   WHERE ds.shift_id = :shift_id";
        $stmtDoc = $this->conn->prepare($sqlDoc);
        $stmtDoc->execute([':shift_id' => $shiftId]);
        $docs = $stmtDoc->fetchAll();

        // Lấy danh sách y tá
        $sqlNurse = "SELECT ns.id as registration_id, 'nurse' as role, u.name, u.phone, 'Điều dưỡng' as specialty, ns.status
                     FROM nurse_shifts ns
                     JOIN nurses n ON ns.nurse_id = n.id
                     JOIN users u ON n.user_id = u.id
                     WHERE ns.shift_id = :shift_id";
        $stmtNurse = $this->conn->prepare($sqlNurse);
        $stmtNurse->execute([':shift_id' => $shiftId]);
        $nurses = $stmtNurse->fetchAll();

        // Gộp hai danh sách
        $all = array_merge($docs, $nurses);
        // Sắp xếp theo ngày tạo (đăng ký trước hiện trước) hoặc theo trạng thái
        usort($all, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        return $all;
    }

    // Duyệt đăng ký
    public function approveRegistration($role, $registrationId) {
        $table = ($role === 'doctor') ? 'doctor_shifts' : 'nurse_shifts';
        $sql = "UPDATE {$table} SET status = 'approved' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $registrationId]);
    }

    // Từ chối đăng ký
    public function rejectRegistration($role, $registrationId) {
        $table = ($role === 'doctor') ? 'doctor_shifts' : 'nurse_shifts';
        $sql = "UPDATE {$table} SET status = 'rejected' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $registrationId]);
    }

    // Đếm ca night trong tuần của 1 y tá
    public function countNurseNightShiftsInWeek($nurseId, $weekStart) {
        $weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));
        $sql = "SELECT COUNT(*) as cnt FROM nurse_shifts ns 
                JOIN shifts s ON ns.shift_id = s.id 
                WHERE ns.nurse_id = :nurse_id 
                AND s.shift_type = 'night' 
                AND s.shift_date BETWEEN :week_start AND :week_end";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nurse_id', $nurseId);
        $stmt->bindParam(':week_start', $weekStart);
        $stmt->bindParam(':week_end', $weekEnd);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['cnt'];
    }
}
