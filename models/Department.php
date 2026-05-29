<?php
/**
 * Department Model - Quản lý khoa
 */
require_once __DIR__ . '/../config/database.php';

class Department {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll($filters = []) {
        $sql = "SELECT d.*, 
                    (SELECT COUNT(*) FROM doctor_departments dd WHERE dd.department_id = d.id) as doctor_count,
                    (SELECT COUNT(*) FROM nurses n WHERE n.department_id = d.id) as nurse_count
                FROM departments d";
        
        $conditions = [];
        $params = [];

        if (!empty($filters['search'])) {
            $conditions[] = "(d.name LIKE :search_name OR d.description LIKE :search_desc)";
            $params[':search_name'] = '%' . $filters['search'] . '%';
            $params[':search_desc'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['has_doctors'])) {
            $conditions[] = "(SELECT COUNT(*) FROM doctor_departments dd WHERE dd.department_id = d.id) > 0";
        }
        
        if (!empty($filters['has_nurses'])) {
            $conditions[] = "(SELECT COUNT(*) FROM nurses n WHERE n.department_id = d.id) > 0";
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sortMap = [
            'name_asc' => 'd.name ASC',
            'name_desc' => 'd.name DESC',
            'doctors_desc' => 'doctor_count DESC',
            'nurses_desc' => 'nurse_count DESC'
        ];
        
        $orderBy = 'd.name ASC';
        if (!empty($filters['sort']) && isset($sortMap[$filters['sort']])) {
            $orderBy = $sortMap[$filters['sort']];
        }
        
        $sql .= " ORDER BY " . $orderBy;

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT * FROM departments WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO departments (name, description) VALUES (:name, :description)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE departments SET name = :name, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM departments WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function count() {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM departments");
        return $stmt->fetch()['total'];
    }

    // Lấy bác sĩ theo khoa
    public function getDoctorsByDept($deptId) {
        $sql = "SELECT d.*, u.name, u.email 
                FROM doctor_departments dd 
                JOIN doctors d ON dd.doctor_id = d.id 
                JOIN users u ON d.user_id = u.id 
                WHERE dd.department_id = :dept_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dept_id', $deptId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy y tá theo khoa
    public function getNursesByDept($deptId) {
        $sql = "SELECT n.*, u.name, u.email FROM nurses n JOIN users u ON n.user_id = u.id WHERE n.department_id = :dept_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dept_id', $deptId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Cập nhật danh sách bác sĩ cho khoa
    public function assignDoctors($deptId, $doctorIds) {
        // Lấy danh sách ID bác sĩ hiện tại thuộc khoa này trước khi xóa
        $sqlCurrent = "SELECT doctor_id FROM doctor_departments WHERE department_id = :dept_id";
        $stmtCurrent = $this->conn->prepare($sqlCurrent);
        $stmtCurrent->bindParam(':dept_id', $deptId);
        $stmtCurrent->execute();
        $currentDoctorIds = $stmtCurrent->fetchAll(PDO::FETCH_COLUMN);

        // Xóa tất cả bác sĩ hiện tại khỏi khoa này trong bảng liên kết doctor_departments
        $sqlDelete = "DELETE FROM doctor_departments WHERE department_id = :dept_id";
        $stmtDelete = $this->conn->prepare($sqlDelete);
        $stmtDelete->bindParam(':dept_id', $deptId);
        $stmtDelete->execute();

        // Thêm liên kết mới
        if (!empty($doctorIds)) {
            $sqlInsert = "INSERT INTO doctor_departments (doctor_id, department_id) VALUES (:doctor_id, :dept_id)";
            $stmtInsert = $this->conn->prepare($sqlInsert);
            foreach ($doctorIds as $doctorId) {
                $stmtInsert->bindValue(':doctor_id', $doctorId, PDO::PARAM_INT);
                $stmtInsert->bindValue(':dept_id', $deptId, PDO::PARAM_INT);
                $stmtInsert->execute();
            }
        }

        // Reset is_head = 0 cho các bác sĩ bị loại khỏi khoa này
        $removedDoctorIds = array_diff($currentDoctorIds, $doctorIds);
        if (!empty($removedDoctorIds)) {
            $inQuery = implode(',', array_fill(0, count($removedDoctorIds), '?'));
            $sqlResetHead = "UPDATE doctors SET is_head = 0 WHERE id IN ($inQuery)";
            $stmtResetHead = $this->conn->prepare($sqlResetHead);
            $stmtResetHead->execute(array_values($removedDoctorIds));
        }

        // Đồng bộ cột legacy department_id trong bảng doctors
        $sqlSync = "UPDATE doctors d 
                    SET d.department_id = (
                        SELECT dd.department_id 
                        FROM doctor_departments dd 
                        WHERE dd.doctor_id = d.id 
                        LIMIT 1
                    )";
        $this->conn->exec($sqlSync);
    }

    // Đặt trưởng khoa
    public function setHeadDoctor($deptId, $doctorId) {
        // Hủy head cũ của các bác sĩ đang trong khoa này
        $sql = "UPDATE doctors d 
                JOIN doctor_departments dd ON d.id = dd.doctor_id
                SET d.is_head = 0 
                WHERE dd.department_id = :dept_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dept_id', $deptId);
        $stmt->execute();

        if ($doctorId) {
            $sql = "UPDATE doctors SET is_head = 1 WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $doctorId);
            $stmt->execute();
        }
    }

    // Cập nhật danh sách y tá cho khoa
    public function assignNurses($deptId, $nurseIds) {
        $sql = "UPDATE nurses SET department_id = NULL, is_head = 0 WHERE department_id = :dept_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dept_id', $deptId);
        $stmt->execute();

        if (!empty($nurseIds)) {
            $inQuery = implode(',', array_fill(0, count($nurseIds), '?'));
            $sql = "UPDATE nurses SET department_id = ? WHERE id IN ($inQuery)";
            $stmt = $this->conn->prepare($sql);
            $params = array_merge([$deptId], $nurseIds);
            $stmt->execute($params);
        }
    }

    // Đặt điều dưỡng trưởng
    public function setHeadNurse($deptId, $nurseId) {
        // Hủy head cũ
        $sql = "UPDATE nurses SET is_head = 0 WHERE department_id = :dept_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dept_id', $deptId);
        $stmt->execute();

        if ($nurseId) {
            $sql = "UPDATE nurses SET is_head = 1 WHERE id = :id AND department_id = :dept_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $nurseId);
            $stmt->bindParam(':dept_id', $deptId);
            $stmt->execute();
        }
    }
}
