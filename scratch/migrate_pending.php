<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Alter admissions status ENUM
    $sql = "ALTER TABLE `admissions` MODIFY COLUMN `status` ENUM('pending', 'active', 'discharged', 'transferred') DEFAULT 'pending'";
    $conn->exec($sql);
    
    echo "Migration successful: admissions.status enum updated to support 'pending'.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
