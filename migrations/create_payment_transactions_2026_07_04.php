<?php
/**
 * Migration: Tạo bảng payment_transactions lưu trữ lịch sử nỗ lực thanh toán từ mobile
 */
require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    echo "=== KHỞI CHẠY MIGRATION CREATE PAYMENT TRANSACTIONS ===\n";

    // 1. Tạo bảng payment_transactions nếu chưa tồn tại
    $sql = "CREATE TABLE IF NOT EXISTS `payment_transactions` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `invoice_id` INT NOT NULL,
        `patient_id` INT NOT NULL,
        `provider` VARCHAR(50) NOT NULL,
        `transaction_ref` VARCHAR(100) NOT NULL,
        `amount` DECIMAL(12,2) NOT NULL,
        `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
        `request_payload` TEXT NULL,
        `response_payload` TEXT NULL,
        `paid_at` DATETIME NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT `fk_pay_txn_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `fk_pay_txn_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";

    $conn->exec($sql);
    echo "Bảng 'payment_transactions' đã được tạo/xác minh thành công.\n";

    // 2. Thêm chỉ mục
    $indexes = [
        'idx_pay_invoice' => 'ALTER TABLE payment_transactions ADD INDEX idx_pay_invoice (invoice_id)',
        'idx_pay_patient' => 'ALTER TABLE payment_transactions ADD INDEX idx_pay_patient (patient_id)',
        'idx_pay_status'  => 'ALTER TABLE payment_transactions ADD INDEX idx_pay_status (status)',
        'idx_pay_created' => 'ALTER TABLE payment_transactions ADD INDEX idx_pay_created (created_at)',
        'uk_pay_provider_ref' => 'ALTER TABLE payment_transactions ADD UNIQUE KEY uk_pay_provider_ref (provider, transaction_ref)'
    ];

    foreach ($indexes as $name => $indexSql) {
        $stmt = $conn->prepare("SHOW INDEX FROM `payment_transactions` WHERE Key_name = :key_name");
        $stmt->execute([':key_name' => $name]);
        if (!$stmt->fetch()) {
            $conn->exec($indexSql);
            echo "=> Tạo chỉ mục '{$name}' thành công.\n";
        } else {
            echo "=> Chỉ mục '{$name}' đã tồn tại. Bỏ qua.\n";
        }
    }

    echo "=== MIGRATION PAYMENT TRANSACTIONS HOÀN TẤT ===\n";

} catch (Exception $e) {
    echo "LỖI MIGRATION: " . $e->getMessage() . "\n";
    exit(1);
}
