<?php
/**
 * Database Migration Script - VNPay Columns for Invoices
 */

try {
    echo "Starting VNPay columns database migration...\n";
    $conn = new PDO("mysql:host=127.0.0.1;port=3307;dbname=hospital_management;charset=utf8mb4", "root", "root");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add vnpay_txn_ref, vnpay_transaction_no, vnpay_response_code if they don't exist
    $cols = [
        'vnpay_txn_ref' => "VARCHAR(100) NULL AFTER payment_method",
        'vnpay_transaction_no' => "VARCHAR(50) NULL AFTER vnpay_txn_ref",
        'vnpay_response_code' => "VARCHAR(10) NULL AFTER vnpay_transaction_no"
    ];

    foreach ($cols as $col => $definition) {
        $stmt = $conn->query("SHOW COLUMNS FROM invoices LIKE '$col'");
        if ($stmt->rowCount() === 0) {
            $conn->exec("ALTER TABLE invoices ADD COLUMN $col $definition;");
            echo "- Added column '$col' to 'invoices' table.\n";
        } else {
            echo "- Column '$col' already exists.\n";
        }
    }

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
