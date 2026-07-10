<?php
/**
 * Migration: Tạo bảng chat_histories lưu lịch sử chat AI trợ lý y khoa từ mobile
 */
require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    echo "=== KHỞI CHẠY MIGRATION CREATE CHAT HISTORIES ===\n";

    // 1. Tạo bảng chat_histories nếu chưa tồn tại
    $sql = "CREATE TABLE IF NOT EXISTS `chat_histories` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `patient_id` INT NOT NULL,
        `sender` VARCHAR(20) NOT NULL,
        `message` TEXT NOT NULL,
        `provider` VARCHAR(50) NULL,
        `model` VARCHAR(50) NULL,
        `intent` VARCHAR(100) NULL,
        `urgency_level` VARCHAR(20) NULL,
        `department_suggestion` VARCHAR(100) NULL,
        `raw_response` TEXT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `deleted_at` TIMESTAMP NULL DEFAULT NULL,
        CONSTRAINT `fk_chat_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `fk_chat_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";

    $conn->exec($sql);
    echo "Bảng 'chat_histories' đã được tạo/xác minh thành công.\n";

    // 2. Thêm chỉ mục
    $indexes = [
        'idx_chat_user' => 'ALTER TABLE chat_histories ADD INDEX idx_chat_user (user_id)',
        'idx_chat_patient' => 'ALTER TABLE chat_histories ADD INDEX idx_chat_patient (patient_id)',
        'idx_chat_created' => 'ALTER TABLE chat_histories ADD INDEX idx_chat_created (created_at)',
        'idx_chat_deleted' => 'ALTER TABLE chat_histories ADD INDEX idx_chat_deleted (deleted_at)'
    ];

    foreach ($indexes as $name => $indexSql) {
        $stmt = $conn->prepare("SHOW INDEX FROM `chat_histories` WHERE Key_name = :key_name");
        $stmt->execute([':key_name' => $name]);
        if (!$stmt->fetch()) {
            $conn->exec($indexSql);
            echo "=> Tạo chỉ mục '{$name}' thành công.\n";
        } else {
            echo "=> Chỉ mục '{$name}' đã tồn tại. Bỏ qua.\n";
        }
    }

    echo "=== MIGRATION CHAT HISTORIES HOÀN TẤT ===\n";

} catch (Exception $e) {
    echo "LỖI MIGRATION: " . $e->getMessage() . "\n";
    exit(1);
}
