<?php
/**
 * Migration: Tạo bảng invoices + invoice_items + rooms + beds + admissions
 * Chạy 1 lần: http://localhost/CNM1/add_billing_tables.php
 */
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

$sqls = [
    // ========== BILLING ==========
    "CREATE TABLE IF NOT EXISTS invoices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT,
        appointment_id INT NULL,
        admission_id INT NULL,
        total_amount DECIMAL(12,2) DEFAULT 0,
        discount DECIMAL(12,2) DEFAULT 0,
        final_amount DECIMAL(12,2) DEFAULT 0,
        payment_method ENUM('cash','card','momo','vnpay','transfer') DEFAULT 'cash',
        status ENUM('pending','paid','cancelled') DEFAULT 'pending',
        notes TEXT NULL,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        KEY patient_id (patient_id),
        KEY appointment_id (appointment_id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",

    "CREATE TABLE IF NOT EXISTS invoice_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        invoice_id INT,
        item_type ENUM('service','medicine','room','other') DEFAULT 'service',
        item_id INT NULL,
        description VARCHAR(255),
        quantity INT DEFAULT 1,
        unit_price DECIMAL(12,2),
        amount DECIMAL(12,2),
        KEY invoice_id (invoice_id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",

    // ========== INPATIENT ==========
    "CREATE TABLE IF NOT EXISTS rooms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room_number VARCHAR(20) NOT NULL,
        department_id INT,
        room_type ENUM('standard','vip','icu') DEFAULT 'standard',
        price_per_day DECIMAL(12,2) DEFAULT 0,
        status ENUM('available','full','maintenance') DEFAULT 'available',
        KEY department_id (department_id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",

    "CREATE TABLE IF NOT EXISTS beds (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room_id INT,
        bed_number VARCHAR(20),
        status ENUM('available','occupied','maintenance') DEFAULT 'available',
        KEY room_id (room_id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",

    "CREATE TABLE IF NOT EXISTS admissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT,
        doctor_id INT,
        bed_id INT,
        admission_date DATETIME,
        discharge_date DATETIME NULL,
        diagnosis TEXT,
        status ENUM('active','discharged','transferred') DEFAULT 'active',
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        KEY patient_id (patient_id),
        KEY doctor_id (doctor_id),
        KEY bed_id (bed_id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",
];

// Sample data
$sampleData = [
    // Rooms
    "INSERT IGNORE INTO rooms (id, room_number, department_id, room_type, price_per_day, status) VALUES
        (1, 'P101', 1, 'standard', 500000, 'available'),
        (2, 'P102', 1, 'vip', 1500000, 'available'),
        (3, 'P201', 2, 'standard', 500000, 'available'),
        (4, 'P202', 4, 'icu', 3000000, 'available'),
        (5, 'P301', 3, 'standard', 500000, 'available')",

    // Beds
    "INSERT IGNORE INTO beds (id, room_id, bed_number, status) VALUES
        (1, 1, 'G1', 'available'),
        (2, 1, 'G2', 'available'),
        (3, 2, 'G1', 'available'),
        (4, 2, 'G2', 'available'),
        (5, 3, 'G1', 'available'),
        (6, 3, 'G2', 'available'),
        (7, 4, 'G1', 'available'),
        (8, 4, 'G2', 'available'),
        (9, 5, 'G1', 'available'),
        (10, 5, 'G2', 'available'),
        (11, 5, 'G3', 'available')",

    // Sample invoice
    "INSERT IGNORE INTO invoices (id, patient_id, appointment_id, total_amount, discount, final_amount, payment_method, status, created_by) VALUES
        (1, 1, 1, 705000, 0, 705000, 'cash', 'paid', 1),
        (2, 2, 2, 500000, 50000, 450000, 'momo', 'pending', 1)",

    // Sample invoice items
    "INSERT IGNORE INTO invoice_items (id, invoice_id, item_type, item_id, description, quantity, unit_price, amount) VALUES
        (1, 1, 'service', 1, 'Xét nghiệm máu', 1, 200000, 200000),
        (2, 1, 'medicine', 1, 'Paracetamol 500mg', 2, 2500, 5000),
        (3, 1, 'service', 2, 'Chụp X-quang', 1, 500000, 500000),
        (4, 2, 'service', 2, 'Chụp X-quang', 1, 500000, 500000)",

    // Sample admission
    "INSERT IGNORE INTO admissions (id, patient_id, doctor_id, bed_id, admission_date, diagnosis, status, notes) VALUES
        (1, 1, 1, 1, '2026-03-06 08:00:00', 'Rối loạn nhịp tim cần theo dõi', 'active', 'Theo dõi nhịp tim 24h')",

    // Update bed 1 to occupied
    "UPDATE beds SET status = 'occupied' WHERE id = 1",
];

echo "<h2>🏥 NovaCare — Database Migration</h2>";
echo "<style>body{font-family:Arial;padding:20px} .ok{color:green} .err{color:red}</style>";

foreach ($sqls as $sql) {
    try {
        $conn->exec($sql);
        preg_match('/CREATE TABLE IF NOT EXISTS (\w+)/', $sql, $m);
        echo "<p class='ok'>✅ Bảng <b>{$m[1]}</b> — OK</p>";
    } catch (Exception $e) {
        echo "<p class='err'>❌ Lỗi: {$e->getMessage()}</p>";
    }
}

echo "<hr><h3>📦 Thêm dữ liệu mẫu...</h3>";
foreach ($sampleData as $sql) {
    try {
        $conn->exec($sql);
        echo "<p class='ok'>✅ Dữ liệu mẫu — OK</p>";
    } catch (Exception $e) {
        echo "<p class='err'>❌ Lỗi: {$e->getMessage()}</p>";
    }
}

echo "<hr><p>✅ <b>Migration hoàn tất!</b> <a href='index.php?page=dashboard'>← Quay về Dashboard</a></p>";
