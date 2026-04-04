<?php
/**
 * Service Model - Quản lý dịch vụ y tế
 */
require_once __DIR__ . '/../config/database.php';

class Service {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM services ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT * FROM services WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO services (service_name, price, description) VALUES (:service_name, :price, :description)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':service_name', $data['service_name']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE services SET service_name = :service_name, price = :price, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':service_name', $data['service_name']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM services WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function count() {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM services");
        return $stmt->fetch()['total'];
    }
}
