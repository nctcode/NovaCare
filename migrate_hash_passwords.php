<?php
/**
 * Migration: Hash tất cả password plaintext hiện tại thành bcrypt
 * Chạy 1 lần: http://localhost/CNM1/migrate_hash_passwords.php
 * 
 * SAU KHI CHẠY XONG, XÓA HOẶC ĐỔI TÊN FILE NÀY.
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/Security.php';

$db = new Database();
$conn = $db->getConnection();

echo "<h2>🔐 NovaCare — Hash Passwords Migration</h2>";
echo "<style>body{font-family:Arial;padding:20px} .ok{color:green} .skip{color:gray} .err{color:red}</style>";

$stmt = $conn->query("SELECT id, name, email, password FROM users ORDER BY id");
$users = $stmt->fetchAll();

$count = 0;
foreach ($users as $user) {
    // Kiểm tra xem password đã hash chưa (bcrypt bắt đầu bằng $2y$)
    if (strpos($user['password'], '$2y$') === 0) {
        echo "<p class='skip'>⏭ ID {$user['id']} ({$user['email']}) — Đã hash, bỏ qua.</p>";
        continue;
    }

    // Hash password plaintext
    $hashed = Security::hashPassword($user['password']);
    $updateStmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
    $updateStmt->bindParam(':password', $hashed);
    $updateStmt->bindParam(':id', $user['id']);
    $updateStmt->execute();

    echo "<p class='ok'>✅ ID {$user['id']} ({$user['email']}) — Đã hash thành công.</p>";
    $count++;
}

echo "<hr><p>✅ <b>Hoàn tất! Đã hash {$count} password.</b></p>";
echo "<p>⚠️ <b>Hãy xóa file này sau khi chạy xong!</b></p>";
echo "<p><a href='index.php?page=login'>← Quay về trang Login</a></p>";
