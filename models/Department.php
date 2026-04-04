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

    public function getAll() {
        $sql = "SELECT d.*, 
                    (SELECT COUNT(*) FROM doctors doc WHERE doc.department_id = d.id) as doctor_count,
                    (SELECT COUNT(*) FROM nurses n WHERE n.department_id = d.id) as nurse_count
                FROM departments d ORDER BY d.name ASC";
        $stmt = $this->conn->prepare($sql);
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
        $sql = "SELECT d.*, u.name, u.email FROM doctors d JOIN users u ON d.user_id = u.id WHERE d.department_id = :dept_id";
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
}
