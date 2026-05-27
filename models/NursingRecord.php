<?php
/**
 * NursingRecord Model - Hồ sơ chăm sóc điều dưỡng
 * Ghi nhận dấu hiệu sinh tồn và chăm sóc bệnh nhân nội trú
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class NursingRecord {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy hồ sơ chăm sóc theo admission
    public function getByAdmissionId($admissionId) {
        $sql = "SELECT nr.*, u.name as nurse_name
                FROM nursing_records nr
                JOIN nurses n ON nr.nurse_id = n.id
                JOIN users u ON n.user_id = u.id
                WHERE nr.admission_id = :admission_id
                ORDER BY nr.record_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':admission_id', $admissionId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy hồ sơ mới nhất
    public function getLatest($admissionId) {
        $sql = "SELECT nr.*, u.name as nurse_name
                FROM nursing_records nr
                JOIN nurses n ON nr.nurse_id = n.id
                JOIN users u ON n.user_id = u.id
                WHERE nr.admission_id = :admission_id
                ORDER BY nr.record_time DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':admission_id', $admissionId);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Tìm theo ID
    public function findById($id) {
        $sql = "SELECT nr.*, u.name as nurse_name
                FROM nursing_records nr
                JOIN nurses n ON nr.nurse_id = n.id
                JOIN users u ON n.user_id = u.id
                WHERE nr.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Tạo hồ sơ chăm sóc mới
    public function create($data) {
        $sql = "INSERT INTO nursing_records 
                (admission_id, nurse_id, temperature, blood_pressure_sys, blood_pressure_dia, 
                 heart_rate, respiratory_rate, spo2, care_notes, medication_given, diet_notes)
                VALUES 
                (:admission_id, :nurse_id, :temperature, :bp_sys, :bp_dia,
                 :heart_rate, :respiratory_rate, :spo2, :care_notes, :medication_given, :diet_notes)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':admission_id', $data['admission_id']);
        $stmt->bindParam(':nurse_id', $data['nurse_id']);
        $stmt->bindValue(':temperature', $data['temperature'] ?: null);
        $stmt->bindValue(':bp_sys', $data['blood_pressure_sys'] ?: null);
        $stmt->bindValue(':bp_dia', $data['blood_pressure_dia'] ?: null);
        $stmt->bindValue(':heart_rate', $data['heart_rate'] ?: null);
        $stmt->bindValue(':respiratory_rate', $data['respiratory_rate'] ?: null);
        $stmt->bindValue(':spo2', $data['spo2'] ?: null);
        $stmt->bindParam(':care_notes', $data['care_notes']);
        $stmt->bindParam(':medication_given', $data['medication_given']);
        $stmt->bindParam(':diet_notes', $data['diet_notes']);
        $stmt->execute();
        $id = $this->conn->lastInsertId();
        AuditLog::logCreate('nursing_records', $id, [
            'admission_id' => $data['admission_id'],
            'nurse_id' => $data['nurse_id']
        ]);
        return $id;
    }

    // Đếm số hồ sơ theo nurse_id (hôm nay)
    public function countTodayByNurseId($nurseId) {
        $sql = "SELECT COUNT(*) as total FROM nursing_records 
                WHERE nurse_id = :nurse_id AND DATE(record_time) = CURDATE()";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nurse_id', $nurseId);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    // Lấy danh sách sinh hiệu cho biểu đồ (7 records gần nhất)
    public function getVitalChartData($admissionId, $limit = 7) {
        $sql = "SELECT record_time, temperature, blood_pressure_sys, blood_pressure_dia, 
                       heart_rate, respiratory_rate, spo2
                FROM nursing_records
                WHERE admission_id = :admission_id
                ORDER BY record_time ASC
                LIMIT :lim";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':admission_id', $admissionId);
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
