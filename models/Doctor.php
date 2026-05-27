<?php
/**
 * Doctor Model - Quản lý bảng doctors + users + departments
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Security.php';

class Doctor {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
        
        try {
            // Auto-create doctor_departments table if it doesn't exist
            $sql = "CREATE TABLE IF NOT EXISTS `doctor_departments` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `doctor_id` INT NOT NULL,
                `department_id` INT NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_doc_dept` (`doctor_id`, `department_id`),
                CONSTRAINT `fk_dd_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_dd_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";
            $this->conn->exec($sql);
            
            // Migrate existing doctor -> department mappings if any doctor still has department_id
            // and doesn't have rows in doctor_departments yet
            $sqlMigrate = "INSERT INTO `doctor_departments` (`doctor_id`, `department_id`)
                           SELECT `id`, `department_id` FROM `doctors` 
                           WHERE `department_id` IS NOT NULL 
                           AND `id` NOT IN (SELECT DISTINCT `doctor_id` FROM `doctor_departments`)";
            $this->conn->exec($sqlMigrate);
        } catch (Exception $e) {
            error_log("Doctor Model Init migration error: " . $e->getMessage());
        }
    }

    // Lấy tất cả bác sĩ (JOIN users + doctor_departments)
    public function getAll() {
        $sql = "SELECT d.*, u.name, u.email, u.phone, 
                       (SELECT GROUP_CONCAT(dep.name SEPARATOR ', ') 
                        FROM doctor_departments dd 
                        JOIN departments dep ON dd.department_id = dep.id 
                        WHERE dd.doctor_id = d.id) as department_name 
                FROM doctors d 
                JOIN users u ON d.user_id = u.id 
                WHERE d.deleted_at IS NULL AND u.deleted_at IS NULL
                ORDER BY u.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm bác sĩ theo ID
    public function findById($id) {
        $sql = "SELECT d.*, u.name, u.email, u.phone, 
                       (SELECT GROUP_CONCAT(dep.name SEPARATOR ', ') 
                        FROM doctor_departments dd 
                        JOIN departments dep ON dd.department_id = dep.id 
                        WHERE dd.doctor_id = d.id) as department_name 
                FROM doctors d 
                JOIN users u ON d.user_id = u.id 
                WHERE d.id = :id AND d.deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $doctor = $stmt->fetch();
        if ($doctor) {
            // Get array of department IDs
            $sqlDepts = "SELECT department_id FROM doctor_departments WHERE doctor_id = :doctor_id";
            $stmtDepts = $this->conn->prepare($sqlDepts);
            $stmtDepts->bindParam(':doctor_id', $doctor['id']);
            $stmtDepts->execute();
            $doctor['department_ids'] = $stmtDepts->fetchAll(PDO::FETCH_COLUMN);
        }
        return $doctor;
    }

    // Tìm bác sĩ theo user_id
    public function findByUserId($userId) {
        $sql = "SELECT d.*, u.name, u.email, u.phone, 
                       (SELECT GROUP_CONCAT(dep.name SEPARATOR ', ') 
                        FROM doctor_departments dd 
                        JOIN departments dep ON dd.department_id = dep.id 
                        WHERE dd.doctor_id = d.id) as department_name 
                FROM doctors d 
                JOIN users u ON d.user_id = u.id 
                WHERE d.user_id = :user_id AND d.deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $doctor = $stmt->fetch();
        if ($doctor) {
            // Get array of department IDs
            $sqlDepts = "SELECT department_id FROM doctor_departments WHERE doctor_id = :doctor_id";
            $stmtDepts = $this->conn->prepare($sqlDepts);
            $stmtDepts->bindParam(':doctor_id', $doctor['id']);
            $stmtDepts->execute();
            $doctor['department_ids'] = $stmtDepts->fetchAll(PDO::FETCH_COLUMN);
        }
        return $doctor;
    }

    // Tạo bác sĩ mới
    public function create($data) {
        $this->conn->beginTransaction();
        try {
            // Tạo user
            $sql = "INSERT INTO users (name, email, password, phone, role) 
                    VALUES (:name, :email, :password, :phone, 'doctor')";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $password = $data['password'] ?? '123456';
            $hashedPassword = Security::hashPassword($password);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->execute();
            $userId = $this->conn->lastInsertId();

            // Tạo doctor
            $firstDeptId = !empty($data['department_ids']) ? $data['department_ids'][0] : null;
            $sql = "INSERT INTO doctors (user_id, department_id, specialty, experience_years) 
                    VALUES (:user_id, :dept_id, :specialty, :exp_years)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':dept_id', $firstDeptId);
            $stmt->bindParam(':specialty', $data['specialty']);
            $stmt->bindParam(':exp_years', $data['experience_years']);
            $stmt->execute();
            $doctorId = $this->conn->lastInsertId();

            // Thêm vào doctor_departments
            if (!empty($data['department_ids'])) {
                $sqlDD = "INSERT INTO doctor_departments (doctor_id, department_id) VALUES (:doctor_id, :dept_id)";
                $stmtDD = $this->conn->prepare($sqlDD);
                foreach ($data['department_ids'] as $deptId) {
                    $stmtDD->bindValue(':doctor_id', $doctorId, PDO::PARAM_INT);
                    $stmtDD->bindValue(':dept_id', $deptId, PDO::PARAM_INT);
                    $stmtDD->execute();
                }
            }

            $this->conn->commit();
            return $doctorId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // Cập nhật bác sĩ
    public function update($id, $data) {
        $doctor = $this->findById($id);
        if (!$doctor) return false;

        $this->conn->beginTransaction();
        try {
            // Cập nhật users
            $sql = "UPDATE users SET name = :name, email = :email, phone = :phone 
                    WHERE id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':user_id', $doctor['user_id']);
            $stmt->execute();

            // Cập nhật doctors
            $firstDeptId = !empty($data['department_ids']) ? $data['department_ids'][0] : null;
            $sql = "UPDATE doctors SET department_id = :dept_id, specialty = :specialty, 
                    experience_years = :exp_years WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':dept_id', $firstDeptId);
            $stmt->bindParam(':specialty', $data['specialty']);
            $stmt->bindParam(':exp_years', $data['experience_years']);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Cập nhật doctor_departments
            $sqlDelete = "DELETE FROM doctor_departments WHERE doctor_id = :doctor_id";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->bindValue(':doctor_id', $id, PDO::PARAM_INT);
            $stmtDelete->execute();

            if (!empty($data['department_ids'])) {
                $sqlDD = "INSERT INTO doctor_departments (doctor_id, department_id) VALUES (:doctor_id, :dept_id)";
                $stmtDD = $this->conn->prepare($sqlDD);
                foreach ($data['department_ids'] as $deptId) {
                    $stmtDD->bindValue(':doctor_id', $id, PDO::PARAM_INT);
                    $stmtDD->bindValue(':dept_id', $deptId, PDO::PARAM_INT);
                    $stmtDD->execute();
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // Xóa bác sĩ
    public function delete($id) {
        $doctor = $this->findById($id);
        if (!$doctor) return false;

        $sql = "UPDATE doctors SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $sql = "UPDATE users SET deleted_at = NOW() WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $doctor['user_id']);
        return $stmt->execute();
    }

    // Lấy danh sách departments
    public function getDepartments() {
        $sql = "SELECT * FROM departments ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng bác sĩ
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM doctors WHERE deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
}
