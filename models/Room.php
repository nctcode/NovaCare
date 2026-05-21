<?php
/**
 * Room Model - Quản lý phòng và giường bệnh
 */
require_once __DIR__ . '/../config/database.php';

class Room {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả phòng kèm thông tin khoa & số giường
    public function getAll() {
        $sql = "SELECT r.*, 
                    d.name as department_name,
                    (SELECT COUNT(*) FROM beds WHERE room_id = r.id) as total_beds,
                    (SELECT COUNT(*) FROM beds WHERE room_id = r.id AND status = 'available') as available_beds
                FROM rooms r
                LEFT JOIN departments d ON r.department_id = d.id
                ORDER BY r.room_number";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT r.*, d.name as department_name
                FROM rooms r
                LEFT JOIN departments d ON r.department_id = d.id
                WHERE r.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy giường của phòng
    public function getBeds($roomId) {
        $sql = "SELECT b.*, 
                    CASE WHEN b.status = 'occupied' 
                        THEN (SELECT u.name FROM admissions a JOIN patients p ON a.patient_id = p.id JOIN users u ON p.user_id = u.id WHERE a.bed_id = b.id AND a.status = 'active' LIMIT 1) 
                        ELSE NULL END as patient_name
                FROM beds b 
                WHERE b.room_id = :room_id 
                ORDER BY b.bed_number";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':room_id', $roomId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy tất cả giường trống
    public function getAvailableBeds() {
        $sql = "SELECT b.id, b.bed_number, r.room_number, r.room_type, r.price_per_day, d.name as department_name
                FROM beds b
                JOIN rooms r ON b.room_id = r.id
                LEFT JOIN departments d ON r.department_id = d.id
                WHERE b.status = 'available'
                ORDER BY r.room_number, b.bed_number";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm giường trống
    public function countAvailableBeds() {
        $sql = "SELECT COUNT(*) as total FROM beds WHERE status = 'available'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    // Cập nhật trạng thái giường
    public function updateBedStatus($bedId, $status) {
        $sql = "UPDATE beds SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $bedId);
        return $stmt->execute();
    }

    // Cập nhật trạng thái phòng dựa trên giường
    public function refreshRoomStatus($roomId) {
        $sql = "SELECT COUNT(*) as total, SUM(CASE WHEN status='available' THEN 1 ELSE 0 END) as avail FROM beds WHERE room_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $roomId);
        $stmt->execute();
        $row = $stmt->fetch();

        $status = 'available';
        if ($row['avail'] == 0) $status = 'full';

        $sql2 = "UPDATE rooms SET status = :status WHERE id = :id";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bindParam(':status', $status);
        $stmt2->bindParam(':id', $roomId);
        $stmt2->execute();
    }

    // Đếm phòng
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM rooms";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    /**
     * Lấy room_id từ bed_id
     * @param int $bedId
     * @return int|null
     */
    public function getRoomIdByBedId($bedId) {
        $sql = "SELECT room_id FROM beds WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $bedId);
        $stmt->execute();
        $bed = $stmt->fetch();
        return $bed ? $bed['room_id'] : null;
    }
}
