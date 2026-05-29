<?php
require_once __DIR__ . '/../config/database.php';
$db = new Database();
$conn = $db->getConnection();

echo "=== USER TO DOCTOR MAPPING ===\n";
$stmt = $conn->query("SELECT u.id as user_id, u.name as user_name, u.email, d.id as doctor_id 
                      FROM users u 
                      LEFT JOIN doctors d ON d.user_id = u.id 
                      WHERE u.role = 'doctor'");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "User ID: {$row['user_id']} | Email: {$row['email']} | Doctor ID: " . ($row['doctor_id'] ?? 'NULL') . "\n";
}