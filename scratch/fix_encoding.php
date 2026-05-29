<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $sql = "UPDATE technicians SET specialty = :specialty WHERE id = 1";
    $stmt = $conn->prepare($sql);
    $specialty = 'Xét nghiệm & Chẩn đoán hình ảnh';
    $stmt->bindParam(':specialty', $specialty);
    $stmt->execute();
    
    echo "Successfully updated technician specialty with correct UTF-8 encoding!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
