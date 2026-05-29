<?php
/**
 * ExaminationRoom Model - Quản lý Phòng khám ngoại trú
 * 
 * Khác với Room (phòng nội trú), ExaminationRoom dùng cho khám ngoại trú.
 * Mỗi phòng khám thuộc 1 khoa, có thể gắn BS cố định.
 */
require_once __DIR__ . '/../config/database.php';

class ExaminationRoom {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả phòng khám (join khoa + BS)
    public function getAll() {
        $sql = "SELECT er.*, 
                    dep.name as department_name,
                    du.name as doctor_name
                FROM examination_rooms er
                LEFT JOIN departments dep ON er.department_id = dep.id
                LEFT JOIN doctors d ON er.doctor_id = d.id
                LEFT JOIN users du ON d.user_id = du.id
                ORDER BY er.room_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy phòng khám theo khoa
    public function getByDepartment($departmentId) {
        $sql = "SELECT er.*, 
                    dep.name as department_name,
                    du.name as doctor_name
                FROM examination_rooms er
                LEFT JOIN departments dep ON er.department_id = dep.id
                LEFT JOIN doctors d ON er.doctor_id = d.id
                LEFT JOIN users du ON d.user_id = du.id
                WHERE er.department_id = :dept_id AND er.status = 'active'
                ORDER BY er.room_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dept_id', $departmentId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm theo ID
    public function findById($id) {
        $sql = "SELECT er.*, 
                    dep.name as department_name,
                    du.name as doctor_name
                FROM examination_rooms er
                LEFT JOIN departments dep ON er.department_id = dep.id
                LEFT JOIN doctors d ON er.doctor_id = d.id
                LEFT JOIN users du ON d.user_id = du.id
                WHERE er.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy phòng khám đang hoạt động
    public function getActive() {
        $sql = "SELECT er.*, 
                    dep.name as department_name,
                    du.name as doctor_name
                FROM examination_rooms er
                LEFT JOIN departments dep ON er.department_id = dep.id
                LEFT JOIN doctors d ON er.doctor_id = d.id
                LEFT JOIN users du ON d.user_id = du.id
                WHERE er.status = 'active'
                ORDER BY er.room_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm phòng khám
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM examination_rooms WHERE status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    // Tự động phân phòng khám có ít người chờ nhất
    public function autoAssignRoom($departmentId) {
        if (!$departmentId) {
            $sql = "SELECT id FROM examination_rooms WHERE status = 'active' LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $room = $stmt->fetch();
            return $room ? $room['id'] : null;
        }

        $sql = "SELECT er.id, COUNT(qt.id) as active_tickets
                FROM examination_rooms er
                LEFT JOIN queue_tickets qt ON er.id = qt.examination_room_id 
                    AND qt.queue_date = CURDATE() 
                    AND qt.status IN ('waiting', 'called', 'in_progress')
                WHERE er.department_id = :dept_id AND er.status = 'active'
                GROUP BY er.id
                ORDER BY active_tickets ASC, er.room_name ASC
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dept_id', $departmentId);
        $stmt->execute();
        $room = $stmt->fetch();
        
        if ($room) {
            return $room['id'];
        }
        
        $sql = "SELECT id FROM examination_rooms WHERE status = 'active' LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $room = $stmt->fetch();
        return $room ? $room['id'] : null;
    }
}
