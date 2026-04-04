<?php
/**
 * Equipment Model - Quản lý trang thiết bị
 */
require_once __DIR__ . '/../config/database.php';

class Equipment {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM equipment ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT * FROM equipment WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO equipment (equipment_name, quantity, status, description) VALUES (:equipment_name, :quantity, :status, :description)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':equipment_name', $data['equipment_name']);
        $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE equipment SET equipment_name = :equipment_name, quantity = :quantity, status = :status, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':equipment_name', $data['equipment_name']);
        $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM equipment WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function count() {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM equipment");
        return $stmt->fetch()['total'];
    }
}
