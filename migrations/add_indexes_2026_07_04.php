<?php
/**
 * Migration: Bổ sung chỉ mục (Index) hiệu năng cho các truy vấn của REST API Phase 2A
 */
require_once __DIR__ . '/../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $indexes = [
        [
            'table' => 'appointments',
            'name' => 'idx_apt_doc_date_status',
            'columns' => 'doctor_id, appointment_date, status',
            'sql' => 'ALTER TABLE appointments ADD INDEX idx_apt_doc_date_status (doctor_id, appointment_date, status)'
        ],
        [
            'table' => 'appointments',
            'name' => 'idx_apt_pat_date_status',
            'columns' => 'patient_id, appointment_date, status',
            'sql' => 'ALTER TABLE appointments ADD INDEX idx_apt_pat_date_status (patient_id, appointment_date, status)'
        ],
        [
            'table' => 'queue_tickets',
            'name' => 'idx_qt_pat_date',
            'columns' => 'patient_id, queue_date',
            'sql' => 'ALTER TABLE queue_tickets ADD INDEX idx_qt_pat_date (patient_id, queue_date)'
        ],
        [
            'table' => 'medical_records',
            'name' => 'idx_mr_pat',
            'columns' => 'patient_id',
            'sql' => 'ALTER TABLE medical_records ADD INDEX idx_mr_pat (patient_id)'
        ],
        [
            'table' => 'prescriptions',
            'name' => 'idx_pr_mr',
            'columns' => 'medical_record_id',
            'sql' => 'ALTER TABLE prescriptions ADD INDEX idx_pr_mr (medical_record_id)'
        ],
        [
            'table' => 'invoices',
            'name' => 'idx_inv_pat',
            'columns' => 'patient_id',
            'sql' => 'ALTER TABLE invoices ADD INDEX idx_inv_pat (patient_id)'
        ]
    ];

    echo "=== KHỞI CHẠY MIGRATION THÊM INDEX HIỆU NĂNG ===\n";

    foreach ($indexes as $idx) {
        $table = $idx['table'];
        $name = $idx['name'];
        $sql = $idx['sql'];

        // Kiểm tra xem index đã tồn tại hay chưa
        $stmt = $conn->prepare("SHOW INDEX FROM `{$table}` WHERE Key_name = :key_name");
        $stmt->execute([':key_name' => $name]);
        $exists = $stmt->fetch();

        if ($exists) {
            echo "Index '{$name}' đã tồn tại trên bảng '{$table}'. Bỏ qua.\n";
        } else {
            echo "Đang tạo index '{$name}' trên bảng '{$table}' ({$idx['columns']})...\n";
            $conn->exec($sql);
            echo "=> Tạo index '{$name}' thành công!\n";
        }
    }

    echo "=== MIGRATION INDEX HOÀN TẤT THÀNH CÔNG ===\n";

} catch (Exception $e) {
    echo "LỖI MIGRATION: " . $e->getMessage() . "\n";
    exit(1);
}
