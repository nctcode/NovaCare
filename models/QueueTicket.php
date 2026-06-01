<?php
/**
 * QueueTicket Model - Quản lý Số thứ tự / Hàng chờ khám
 * 
 * Workflow: waiting → called → in_progress → completed
 * Lễ tân cấp số, BS gọi khám, hoàn thành tự động cập nhật.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class QueueTicket {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy danh sách số thứ tự hôm nay (hoặc theo ngày + filter)
     */
    public function getToday($filters = []) {
        $date = $filters['date'] ?? date('Y-m-d');

        $sql = "SELECT qt.*, 
                    pu.name as patient_name, pu.phone as patient_phone,
                    p.insurance_number,
                    dep.name as department_name,
                    du.name as doctor_name,
                    er.room_name
                FROM queue_tickets qt
                JOIN patients p ON qt.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                LEFT JOIN departments dep ON qt.department_id = dep.id
                LEFT JOIN doctors d ON qt.doctor_id = d.id
                LEFT JOIN users du ON d.user_id = du.id
                LEFT JOIN examination_rooms er ON qt.examination_room_id = er.id
                WHERE qt.queue_date = :queue_date";

        $params = [':queue_date' => $date];

        if (!empty($filters['status'])) {
            $sql .= " AND qt.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['department_id'])) {
            $sql .= " AND qt.department_id = :dept_id";
            $params[':dept_id'] = $filters['department_id'];
        }
        if (!empty($filters['doctor_dept_ids']) && !empty($filters['doctor_id'])) {
            $sql .= " AND (qt.doctor_id = :doctor_id OR (qt.doctor_id IS NULL AND qt.department_id IN (" . implode(',', array_map('intval', $filters['doctor_dept_ids'])) . ")))";
            $params[':doctor_id'] = $filters['doctor_id'];
        } elseif (!empty($filters['doctor_id'])) {
            $sql .= " AND qt.doctor_id = :doctor_id";
            $params[':doctor_id'] = $filters['doctor_id'];
        }

        // Sắp xếp: emergency trước, rồi priority, rồi normal. Trong cùng priority thì FIFO
        $sql .= " ORDER BY FIELD(qt.priority, 'emergency', 'priority', 'normal'), qt.ticket_number ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy số thứ tự tiếp theo cho ngày cụ thể
     */
    public function getNextNumber($date = null) {
        $date = $date ?? date('Y-m-d');
        $sql = "SELECT COALESCE(MAX(ticket_number), 0) + 1 as next_number 
                FROM queue_tickets WHERE queue_date = :queue_date";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':queue_date', $date);
        $stmt->execute();
        return $stmt->fetch()['next_number'];
    }

    /**
     * Cấp số thứ tự mới
     */
    public function create($data) {
        $userId = $_SESSION['user']['id'] ?? null;
        $date = $data['queue_date'] ?? date('Y-m-d');
        $ticketNumber = $this->getNextNumber($date);

        $sql = "INSERT INTO queue_tickets 
                (patient_id, ticket_number, department_id, doctor_id, examination_room_id, 
                 appointment_id, priority, queue_date, insurance_verified, notes, created_by)
                VALUES (:patient_id, :ticket_number, :department_id, :doctor_id, :examination_room_id,
                        :appointment_id, :priority, :queue_date, :insurance_verified, :notes, :created_by)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':ticket_number', $ticketNumber);
        $stmt->bindValue(':department_id', $data['department_id'] ?: null, $data['department_id'] ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':doctor_id', $data['doctor_id'] ?: null, $data['doctor_id'] ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':examination_room_id', $data['examination_room_id'] ?: null, $data['examination_room_id'] ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':appointment_id', $data['appointment_id'] ?: null, $data['appointment_id'] ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindParam(':priority', $data['priority']);
        $stmt->bindParam(':queue_date', $date);
        $stmt->bindValue(':insurance_verified', $data['insurance_verified'] ?? 0, PDO::PARAM_INT);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':created_by', $userId);
        $stmt->execute();

        $newId = $this->conn->lastInsertId();
        AuditLog::logCreate('queue_tickets', $newId, [
            'patient_id' => $data['patient_id'],
            'ticket_number' => $ticketNumber,
            'department_id' => $data['department_id']
        ]);
        return ['id' => $newId, 'ticket_number' => $ticketNumber];
    }

    /**
     * Tìm theo ID
     */
    public function findById($id) {
        $sql = "SELECT qt.*, 
                    pu.name as patient_name, pu.phone as patient_phone,
                    p.insurance_number, p.date_of_birth, p.gender,
                    dep.name as department_name,
                    du.name as doctor_name,
                    er.room_name
                FROM queue_tickets qt
                JOIN patients p ON qt.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                LEFT JOIN departments dep ON qt.department_id = dep.id
                LEFT JOIN doctors d ON qt.doctor_id = d.id
                LEFT JOIN users du ON d.user_id = du.id
                LEFT JOIN examination_rooms er ON qt.examination_room_id = er.id
                WHERE qt.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id, $status) {
        $timeField = '';
        switch ($status) {
            case 'called':
                $timeField = ', called_at = NOW()';
                break;
            case 'in_progress':
                $timeField = ', started_at = NOW()';
                break;
            case 'completed':
                $timeField = ', completed_at = NOW()';
                break;
        }

        $sql = "UPDATE queue_tickets SET status = :status{$timeField} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();

        AuditLog::logUpdate('queue_tickets', $id, null, ['status' => $status]);
        return $result;
    }

    /**
     * Gọi bệnh nhân tiếp theo trong khoa (FIFO + priority)
     */
    public function callNext($departmentId = null, $doctorId = null) {
        $sql = "SELECT qt.id FROM queue_tickets qt
                WHERE qt.queue_date = CURDATE() AND qt.status = 'waiting'";
        $params = [];

        // Chuẩn hóa departmentId sang mảng để dễ lọc
        $deptIds = [];
        if ($departmentId) {
            if (is_array($departmentId)) {
                $deptIds = $departmentId;
            } else {
                $deptIds = [$departmentId];
            }
        }

        if ($doctorId && !empty($deptIds)) {
            // Bác sĩ gọi: Tìm vé của riêng bác sĩ OR vé chưa gán bác sĩ (doctor_id IS NULL) thuộc khoa của bác sĩ
            $deptPlaceholders = implode(',', array_map('intval', $deptIds));
            $sql .= " AND (qt.doctor_id = :doctor_id OR (qt.doctor_id IS NULL AND qt.department_id IN ($deptPlaceholders)))";
            $params[':doctor_id'] = $doctorId;
        } elseif ($doctorId) {
            $sql .= " AND qt.doctor_id = :doctor_id";
            $params[':doctor_id'] = $doctorId;
        } elseif (!empty($deptIds)) {
            $deptPlaceholders = implode(',', array_map('intval', $deptIds));
            $sql .= " AND qt.department_id IN ($deptPlaceholders)";
        }

        $sql .= " ORDER BY FIELD(qt.priority, 'emergency', 'priority', 'normal'), qt.ticket_number ASC LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        if ($row) {
            // Nếu vé chưa có bác sĩ và được gọi bởi bác sĩ thì tự động gán cho bác sĩ đó
            if ($doctorId) {
                $assignSql = "UPDATE queue_tickets SET doctor_id = :doctor_id WHERE id = :id";
                $assignStmt = $this->conn->prepare($assignSql);
                $assignStmt->execute([':doctor_id' => $doctorId, ':id' => $row['id']]);
            }
            $this->updateStatus($row['id'], 'called');
            return $this->findById($row['id']);
        }
        return null;
    }

    /**
     * Đếm theo trạng thái hôm nay
     */
    public function countByStatusToday($status) {
        $sql = "SELECT COUNT(*) as total FROM queue_tickets 
                WHERE queue_date = CURDATE() AND status = :status";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    /**
     * Thống kê hôm nay
     */
    public function getStatsToday() {
        return [
            'waiting'     => $this->countByStatusToday('waiting'),
            'called'      => $this->countByStatusToday('called'),
            'in_progress' => $this->countByStatusToday('in_progress'),
            'completed'   => $this->countByStatusToday('completed'),
            'cancelled'   => $this->countByStatusToday('cancelled'),
            'total'       => $this->countTotalToday(),
        ];
    }

    /**
     * Đếm tổng hôm nay
     */
    public function countTotalToday() {
        $sql = "SELECT COUNT(*) as total FROM queue_tickets WHERE queue_date = CURDATE()";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    /**
     * Lấy số đang được gọi (cho màn hình public)
     */
    public function getCurrentCalled() {
        $sql = "SELECT qt.*, 
                    pu.name as patient_name,
                    dep.name as department_name,
                    er.room_name
                FROM queue_tickets qt
                JOIN patients p ON qt.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                LEFT JOIN departments dep ON qt.department_id = dep.id
                LEFT JOIN examination_rooms er ON qt.examination_room_id = er.id
                WHERE qt.queue_date = CURDATE() AND qt.status IN ('called','in_progress')
                ORDER BY qt.called_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách đang chờ tiếp theo (cho màn hình public)
     */
    public function getNextWaiting($limit = 10) {
        $sql = "SELECT qt.ticket_number, qt.priority,
                    pu.name as patient_name,
                    dep.name as department_name,
                    er.room_name
                FROM queue_tickets qt
                JOIN patients p ON qt.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                LEFT JOIN departments dep ON qt.department_id = dep.id
                LEFT JOIN examination_rooms er ON qt.examination_room_id = er.id
                WHERE qt.queue_date = CURDATE() AND qt.status = 'waiting'
                ORDER BY FIELD(qt.priority, 'emergency', 'priority', 'normal'), qt.ticket_number ASC
                LIMIT :lim";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách bệnh nhân (cho dropdown check-in)
     */
    public function getPatients() {
        $sql = "SELECT p.id, u.name, u.phone, p.insurance_number 
                FROM patients p JOIN users u ON p.user_id = u.id 
                WHERE p.deleted_at IS NULL ORDER BY u.name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy số thứ tự hôm nay của một bệnh nhân cụ thể
     */
    public function getByPatientId($patientId) {
        $sql = "SELECT qt.*,
                    dep.name as department_name,
                    du.name as doctor_name,
                    er.room_name
                FROM queue_tickets qt
                LEFT JOIN departments dep ON qt.department_id = dep.id
                LEFT JOIN doctors d ON qt.doctor_id = d.id
                LEFT JOIN users du ON d.user_id = du.id
                LEFT JOIN examination_rooms er ON qt.examination_room_id = er.id
                WHERE qt.patient_id = :patient_id
                  AND qt.queue_date = CURDATE()
                  AND qt.status NOT IN ('cancelled','completed')
                ORDER BY qt.ticket_number ASC
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Đếm số người đứng trước bệnh nhân trong hàng chờ (cùng khoa, status = waiting)
     */
    public function countAhead($ticketNumber, $departmentId = null) {
        $sql = "SELECT COUNT(*) as cnt FROM queue_tickets
                WHERE queue_date = CURDATE()
                  AND status = 'waiting'
                  AND ticket_number < :ticket_number";
        $params = [':ticket_number' => $ticketNumber];
        if ($departmentId) {
            $sql .= " AND department_id = :dept_id";
            $params[':dept_id'] = $departmentId;
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetch()['cnt'];
    }

    /**
     * Lấy danh sách khoa (cho dropdown)
     */
    public function getDepartments() {
        $sql = "SELECT * FROM departments ORDER BY name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
