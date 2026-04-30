<?php
/**
 * Migration: Nâng cấp Database NovaCare
 * 
 * 1. Chuyển tất cả bảng từ MyISAM → InnoDB
 * 2. Thêm cột treatment vào medical_records (nếu chưa có)
 * 3. Thêm Foreign Key constraints
 * 4. Thêm indexes cho các cột filter thường xuyên
 * 5. Tạo bảng activity_logs (Audit Log)
 * 
 * Chạy 1 lần: php migrate_to_innodb.php (CLI) 
 * hoặc http://localhost/CNM1/migrate_to_innodb.php (Web - chỉ trong development)
 */
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

$isCli = php_sapi_name() === 'cli';

function output($msg, $type = 'ok', $isCli = false) {
    if ($isCli) {
        $prefix = ['ok' => '✅', 'skip' => '⏭', 'err' => '❌', 'info' => 'ℹ️'];
        echo ($prefix[$type] ?? '') . " $msg\n";
    } else {
        $colors = ['ok' => 'green', 'skip' => 'gray', 'err' => 'red', 'info' => '#2196F3'];
        echo "<p style='color:{$colors[$type]};margin:4px 0'>$msg</p>";
    }
}

if (!$isCli) {
    echo "<h2>🏥 NovaCare — Database Migration (InnoDB + FK + Indexes + Audit)</h2>";
    echo "<style>body{font-family:'Segoe UI',Arial,sans-serif;padding:20px;max-width:900px;margin:0 auto;background:#f5f5f5} h2{color:#1a237e} h3{color:#37474f;border-bottom:1px solid #ddd;padding-bottom:5px}</style>";
}

$errors = 0;

// ===================================================
// BƯỚC 1: Chuyển MyISAM → InnoDB
// ===================================================
if (!$isCli) echo "<h3>1️⃣ Chuyển Engine → InnoDB</h3>";
else echo "\n=== BƯỚC 1: Chuyển Engine → InnoDB ===\n";

$tables = $conn->query("SHOW TABLE STATUS")->fetchAll();
foreach ($tables as $table) {
    $name = $table['Name'];
    $engine = $table['Engine'];
    if (strtolower($engine) === 'myisam') {
        try {
            $conn->exec("ALTER TABLE `$name` ENGINE=InnoDB");
            output("Đã chuyển <b>$name</b> từ MyISAM → InnoDB", 'ok', $isCli);
        } catch (Exception $e) {
            output("Lỗi chuyển $name: " . $e->getMessage(), 'err', $isCli);
            $errors++;
        }
    } else {
        output("$name đã là $engine, bỏ qua", 'skip', $isCli);
    }
}

// ===================================================
// BƯỚC 2: Thêm cột treatment vào medical_records
// ===================================================
if (!$isCli) echo "<h3>2️⃣ Thêm cột treatment</h3>";
else echo "\n=== BƯỚC 2: Thêm cột treatment ===\n";

$stmt = $conn->query("SHOW COLUMNS FROM medical_records LIKE 'treatment'");
if ($stmt->rowCount() > 0) {
    output("Cột <b>treatment</b> đã tồn tại, bỏ qua", 'skip', $isCli);
} else {
    try {
        $conn->exec("ALTER TABLE medical_records ADD COLUMN treatment TEXT AFTER diagnosis");
        output("Đã thêm cột <b>treatment</b> vào <b>medical_records</b>", 'ok', $isCli);
    } catch (Exception $e) {
        output("Lỗi thêm cột treatment: " . $e->getMessage(), 'err', $isCli);
        $errors++;
    }
}

// ===================================================
// BƯỚC 3: Thêm Foreign Key Constraints
// ===================================================
if (!$isCli) echo "<h3>3️⃣ Thêm Foreign Key Constraints</h3>";
else echo "\n=== BƯỚC 3: Thêm Foreign Key Constraints ===\n";

// Danh sách FK cần thêm: [tên_FK, bảng, cột, bảng_tham_chiếu, cột_tham_chiếu, on_delete]
$foreignKeys = [
    ['fk_doctors_user',        'doctors',            'user_id',           'users',           'id', 'CASCADE'],
    ['fk_doctors_dept',        'doctors',            'department_id',     'departments',     'id', 'SET NULL'],
    ['fk_patients_user',       'patients',           'user_id',           'users',           'id', 'CASCADE'],
    ['fk_nurses_user',         'nurses',             'user_id',           'users',           'id', 'CASCADE'],
    ['fk_nurses_dept',         'nurses',             'department_id',     'departments',     'id', 'SET NULL'],
    ['fk_appointments_patient','appointments',       'patient_id',        'patients',        'id', 'CASCADE'],
    ['fk_appointments_doctor', 'appointments',       'doctor_id',         'doctors',         'id', 'CASCADE'],
    ['fk_records_patient',     'medical_records',    'patient_id',        'patients',        'id', 'CASCADE'],
    ['fk_records_doctor',      'medical_records',    'doctor_id',         'doctors',         'id', 'CASCADE'],
    ['fk_records_appointment', 'medical_records',    'appointment_id',    'appointments',    'id', 'SET NULL'],
    ['fk_prescriptions_record','prescriptions',      'medical_record_id', 'medical_records', 'id', 'CASCADE'],
    ['fk_prescriptions_doctor','prescriptions',      'doctor_id',         'doctors',         'id', 'CASCADE'],
    ['fk_presc_items_presc',   'prescription_items', 'prescription_id',   'prescriptions',   'id', 'CASCADE'],
    ['fk_presc_items_medicine','prescription_items',  'medicine_id',       'medicines',       'id', 'RESTRICT'],
    ['fk_dshifts_doctor',      'doctor_shifts',      'doctor_id',         'doctors',         'id', 'CASCADE'],
    ['fk_dshifts_shift',       'doctor_shifts',      'shift_id',          'shifts',          'id', 'CASCADE'],
    ['fk_notifications_user',  'notifications',      'user_id',           'users',           'id', 'CASCADE'],
    ['fk_devices_dept',        'medical_devices',    'department_id',     'departments',     'id', 'SET NULL'],
    ['fk_pservices_patient',   'patient_services',   'patient_id',        'patients',        'id', 'CASCADE'],
    ['fk_pservices_service',   'patient_services',   'service_id',        'services',        'id', 'CASCADE'],
    ['fk_pservices_doctor',    'patient_services',   'doctor_id',         'doctors',         'id', 'CASCADE'],
    ['fk_meetings_appointment','online_meetings',    'appointment_id',    'appointments',    'id', 'CASCADE'],
];

foreach ($foreignKeys as $fk) {
    [$fkName, $table, $column, $refTable, $refColumn, $onDelete] = $fk;
    
    // Kiểm tra FK đã tồn tại chưa
    $checkSql = "SELECT COUNT(*) as cnt FROM information_schema.TABLE_CONSTRAINTS 
                 WHERE CONSTRAINT_SCHEMA = DATABASE() 
                 AND TABLE_NAME = :table 
                 AND CONSTRAINT_NAME = :fk_name 
                 AND CONSTRAINT_TYPE = 'FOREIGN KEY'";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bindParam(':table', $table);
    $checkStmt->bindParam(':fk_name', $fkName);
    $checkStmt->execute();
    
    if ($checkStmt->fetch()['cnt'] > 0) {
        output("FK <b>$fkName</b> ($table.$column) đã tồn tại, bỏ qua", 'skip', $isCli);
        continue;
    }
    
    try {
        $sql = "ALTER TABLE `$table` ADD CONSTRAINT `$fkName` 
                FOREIGN KEY (`$column`) REFERENCES `$refTable`(`$refColumn`) 
                ON DELETE $onDelete ON UPDATE CASCADE";
        $conn->exec($sql);
        output("Đã thêm FK <b>$fkName</b>: $table.$column → $refTable.$refColumn (ON DELETE $onDelete)", 'ok', $isCli);
    } catch (Exception $e) {
        output("Lỗi FK $fkName ($table.$column): " . $e->getMessage(), 'err', $isCli);
        $errors++;
    }
}

// ===================================================
// BƯỚC 4: Thêm Indexes
// ===================================================
if (!$isCli) echo "<h3>4️⃣ Thêm Indexes</h3>";
else echo "\n=== BƯỚC 4: Thêm Indexes ===\n";

$indexes = [
    ['idx_appointments_date',   'appointments',  'appointment_date'],
    ['idx_appointments_status', 'appointments',  'status'],
    ['idx_medical_records_created', 'medical_records', 'created_at'],
];

foreach ($indexes as $idx) {
    [$idxName, $table, $column] = $idx;
    
    // Kiểm tra index đã tồn tại chưa
    $checkSql = "SELECT COUNT(*) as cnt FROM information_schema.STATISTICS 
                 WHERE TABLE_SCHEMA = DATABASE() 
                 AND TABLE_NAME = :table 
                 AND INDEX_NAME = :idx_name";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bindParam(':table', $table);
    $checkStmt->bindParam(':idx_name', $idxName);
    $checkStmt->execute();
    
    if ($checkStmt->fetch()['cnt'] > 0) {
        output("Index <b>$idxName</b> đã tồn tại, bỏ qua", 'skip', $isCli);
        continue;
    }
    
    try {
        $conn->exec("ALTER TABLE `$table` ADD INDEX `$idxName` (`$column`)");
        output("Đã thêm index <b>$idxName</b> trên $table.$column", 'ok', $isCli);
    } catch (Exception $e) {
        output("Lỗi index $idxName: " . $e->getMessage(), 'err', $isCli);
        $errors++;
    }
}

// ===================================================
// BƯỚC 5: Tạo bảng activity_logs (Audit Log)
// ===================================================
if (!$isCli) echo "<h3>5️⃣ Tạo bảng activity_logs (Audit Log)</h3>";
else echo "\n=== BƯỚC 5: Tạo bảng activity_logs ===\n";

$checkTable = $conn->query("SHOW TABLES LIKE 'activity_logs'")->rowCount();
if ($checkTable > 0) {
    output("Bảng <b>activity_logs</b> đã tồn tại, bỏ qua", 'skip', $isCli);
} else {
    try {
        $conn->exec("
            CREATE TABLE activity_logs (
                id INT NOT NULL AUTO_INCREMENT,
                user_id INT DEFAULT NULL,
                user_name VARCHAR(100) DEFAULT NULL,
                action VARCHAR(50) NOT NULL COMMENT 'create, update, delete, login, logout, etc.',
                table_name VARCHAR(100) DEFAULT NULL,
                record_id INT DEFAULT NULL,
                old_data JSON DEFAULT NULL,
                new_data JSON DEFAULT NULL,
                ip_address VARCHAR(45) DEFAULT NULL,
                user_agent VARCHAR(500) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_logs_user (user_id),
                KEY idx_logs_action (action),
                KEY idx_logs_table (table_name),
                KEY idx_logs_created (created_at),
                CONSTRAINT fk_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        output("Đã tạo bảng <b>activity_logs</b> thành công!", 'ok', $isCli);
    } catch (Exception $e) {
        output("Lỗi tạo bảng activity_logs: " . $e->getMessage(), 'err', $isCli);
        $errors++;
    }
}

// ===================================================
// KẾT QUẢ
// ===================================================
if (!$isCli) {
    echo "<hr>";
    if ($errors === 0) {
        echo "<p style='color:green;font-size:18px'>✅ <b>Migration hoàn tất thành công! Không có lỗi.</b></p>";
    } else {
        echo "<p style='color:red;font-size:18px'>⚠️ <b>Migration hoàn tất với $errors lỗi.</b> Kiểm tra lại ở trên.</p>";
    }
    echo "<p><a href='index.php?page=dashboard'>← Quay về Dashboard</a></p>";
} else {
    echo "\n" . str_repeat('=', 50) . "\n";
    if ($errors === 0) {
        echo "✅ Migration hoàn tất thành công! Không có lỗi.\n";
    } else {
        echo "⚠️ Migration hoàn tất với $errors lỗi.\n";
    }
}
