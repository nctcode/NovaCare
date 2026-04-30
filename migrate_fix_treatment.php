<?php
/**
 * Migration: Thêm cột treatment vào bảng medical_records
 * Chạy 1 lần: http://localhost/CNM1/migrate_fix_treatment.php
 */
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

echo "<h2>🏥 NovaCare — Fix Treatment Column</h2>";
echo "<style>body{font-family:Arial;padding:20px} .ok{color:green} .err{color:red}</style>";

try {
    // Kiểm tra cột đã tồn tại chưa
    $stmt = $conn->query("SHOW COLUMNS FROM medical_records LIKE 'treatment'");
    if ($stmt->rowCount() > 0) {
        echo "<p class='ok'>⏭ Cột <b>treatment</b> đã tồn tại, không cần thêm.</p>";
    } else {
        $conn->exec("ALTER TABLE medical_records ADD COLUMN treatment TEXT AFTER diagnosis");
        echo "<p class='ok'>✅ Đã thêm cột <b>treatment</b> vào bảng <b>medical_records</b>.</p>";
    }
} catch (Exception $e) {
    echo "<p class='err'>❌ Lỗi: {$e->getMessage()}</p>";
}

echo "<hr><p><a href='index.php?page=dashboard'>← Quay về Dashboard</a></p>";
