<?php
require_once __DIR__ . '/../config/database.php';
$db = new Database();
$conn = $db->getConnection();

echo "=== DANH SÁCH BÁC SĨ ===\n";
$stmt = $conn->query("SELECT d.id, u.name, u.email FROM doctors d JOIN users u ON d.user_id = u.id");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Doctor ID: {$row['id']} | Name: {$row['name']} | Email: {$row['email']}\n";
    
    // Đếm liên kết
    $appCount = $conn->query("SELECT COUNT(*) FROM appointments WHERE doctor_id = " . $row['id'])->fetchColumn();
    $recCount = $conn->query("SELECT COUNT(*) FROM medical_records WHERE doctor_id = " . $row['id'])->fetchColumn();
    $admCount = $conn->query("SELECT COUNT(*) FROM admissions WHERE doctor_id = " . $row['id'])->fetchColumn();
    $queCount = $conn->query("SELECT COUNT(*) FROM queue_tickets WHERE doctor_id = " . $row['id'])->fetchColumn();
    
    echo "  - Hẹn khám: $appCount | Bệnh án: $recCount | Nhập viện: $admCount | Hàng chờ: $queCount\n";
}