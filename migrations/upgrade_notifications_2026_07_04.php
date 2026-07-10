<?php
/**
 * Migration: Bổ sung các cột type và read_at vào bảng notifications phục vụ API Mobile (tương thích ngược)
 */
require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    echo "=== KHỞI CHẠY MIGRATION UPGRADE NOTIFICATIONS ===\n";

    // 1. Kiểm tra cột type
    $stmtType = $conn->query("SHOW COLUMNS FROM `notifications` LIKE 'type'");
    if (!$stmtType->fetch()) {
        echo "Thêm cột 'type' vào bảng notifications...\n";
        $conn->exec("ALTER TABLE `notifications` ADD COLUMN `type` VARCHAR(50) DEFAULT 'general';");
        echo "=> Thêm cột 'type' thành công.\n";
    } else {
        echo "=> Cột 'type' đã tồn tại. Bỏ qua.\n";
    }

    // 2. Kiểm tra cột read_at
    $stmtReadAt = $conn->query("SHOW COLUMNS FROM `notifications` LIKE 'read_at'");
    if (!$stmtReadAt->fetch()) {
        echo "Thêm cột 'read_at' vào bảng notifications...\n";
        $conn->exec("ALTER TABLE `notifications` ADD COLUMN `read_at` DATETIME DEFAULT NULL;");
        echo "=> Thêm cột 'read_at' thành công.\n";
    } else {
        echo "=> Cột 'read_at' đã tồn tại. Bỏ qua.\n";
    }

    // 3. Tạo index cho type
    $stmtIndex = $conn->prepare("SHOW INDEX FROM `notifications` WHERE Key_name = :key_name");
    $stmtIndex->execute([':key_name' => 'idx_noti_type']);
    if (!$stmtIndex->fetch()) {
        $conn->exec("ALTER TABLE `notifications` ADD INDEX idx_noti_type (type)");
        echo "=> Tạo chỉ mục 'idx_noti_type' thành công.\n";
    } else {
        echo "=> Chỉ mục 'idx_noti_type' đã tồn tại. Bỏ qua.\n";
    }

    echo "=== MIGRATION UPGRADE NOTIFICATIONS HOÀN TẤT ===\n";

} catch (Exception $e) {
    echo "LỖI MIGRATION: " . $e->getMessage() . "\n";
    exit(1);
}
