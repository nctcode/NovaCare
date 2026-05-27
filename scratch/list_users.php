<?php
require_once __DIR__ . '/../config/database.php';
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->query("SELECT email, role FROM users GROUP BY role, email");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Role: " . $row['role'] . " | Email: " . $row['email'] . "\n";
}
