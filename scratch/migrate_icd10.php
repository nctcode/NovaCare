<?php
/**
 * Database Migration Script - ICD-10
 */

try {
    echo "Starting ICD-10 database migration (connecting to port 3307 on localhost)...\n";
    $conn = new PDO("mysql:host=127.0.0.1;port=3307;dbname=hospital_management;charset=utf8mb4", "root", "root");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Drop table if exists to avoid collation conflict on code column
    $conn->exec("DROP TABLE IF EXISTS icd10_codes;");
    echo "- Dropped existing 'icd10_codes' table.\n";

    // 1. Create icd10_codes table with correct collation
    $conn->exec("CREATE TABLE icd10_codes (
        code VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        name_en VARCHAR(255) NULL,
        category VARCHAR(100) NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;");
    echo "- Created 'icd10_codes' table with utf8mb4_0900_ai_ci collation.\n";

    // 2. Add foreign key constraint if it doesn't exist
    // Check if fk already exists
    $stmt = $conn->query("SELECT CONSTRAINT_NAME 
                          FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                          WHERE TABLE_NAME = 'medical_records' 
                            AND CONSTRAINT_NAME = 'fk_medical_records_icd10'");
    if ($stmt->rowCount() === 0) {
        $conn->exec("ALTER TABLE medical_records ADD CONSTRAINT fk_medical_records_icd10 FOREIGN KEY (icd10_code) REFERENCES icd10_codes(code) ON DELETE SET NULL ON UPDATE CASCADE;");
        echo "- Added foreign key constraint.\n";
    } else {
        echo "- Foreign key constraint already exists.\n";
    }

    // 3. Populate sample common ICD-10 codes (using IGNORE to avoid duplicate key errors)
    $sampleCodes = [
        ['A09', 'Tiêu chảy và viêm dạ dày ruột do nhiễm trùng', 'Diarrhoea and gastroenteritis of infectious origin', 'Bệnh đường ruột'],
        ['A37', 'Ho gà', 'Whooping cough', 'Nhiễm trùng hô hấp'],
        ['B01', 'Thủy đậu', 'Varicella (chickenpox)', 'Nhiễm virus'],
        ['B18', 'Viêm gan virus mạn tính', 'Chronic viral hepatitis', 'Bệnh gan'],
        ['E11', 'Đái tháo đường không phụ thuộc insulin (Type 2)', 'Type 2 diabetes mellitus', 'Nội tiết'],
        ['E03', 'Suy giáp khác', 'Other hypothyroidism', 'Nội tiết'],
        ['E66', 'Béo phì', 'Obesity', 'Nội tiết'],
        ['F32', 'Giai đoạn trầm cảm', 'Depressive episode', 'Tâm thần'],
        ['G43', 'Đau nửa đầu (Migraine)', 'Migraine', 'Thần kinh'],
        ['G47', 'Rối loạn giấc ngủ (Mất ngủ)', 'Sleep disorders', 'Thần kinh'],
        ['H10', 'Viêm kết mạc (Đau mắt đỏ)', 'Conjunctivitis', 'Mắt'],
        ['I10', 'Tăng huyết áp vô căn (nguyên phát)', 'Essential (primary) hypertension', 'Tim mạch'],
        ['I20', 'Cơn đau thắt ngực', 'Angina pectoris', 'Tim mạch'],
        ['I25', 'Bệnh tim thiếu máu cục bộ mạn tính', 'Chronic ischemic heart disease', 'Tim mạch'],
        ['I63', 'Nhồi máu não', 'Cerebral infarction', 'Tim mạch'],
        ['J00', 'Viêm mũi họng cấp (Cảm thường)', 'Acute nasopharyngitis (common cold)', 'Hô hấp'],
        ['J02', 'Viêm họng cấp', 'Acute pharyngitis', 'Hô hấp'],
        ['J03', 'Viêm amidan cấp', 'Acute tonsillitis', 'Hô hấp'],
        ['J06', 'Nhiễm khuẩn hô hấp trên cấp tính nhiều nơi/không xác định', 'Acute upper respiratory infections of multiple and unspecified sites', 'Hô hấp'],
        ['J20', 'Viêm phế quản cấp', 'Acute bronchitis', 'Hô hấp'],
        ['J30', 'Viêm mũi dị ứng và viêm mũi vận mạch', 'Vasomotor and allergic rhinitis', 'Hô hấp'],
        ['J45', 'Hen phế quản', 'Asthma', 'Hô hấp'],
        ['K21', 'Bệnh trào ngược dạ dày - thực quản (GERD)', 'Gastro-esophageal reflux disease', 'Tiêu hóa'],
        ['K29', 'Viêm dạ dày và tá tràng', 'Gastritis and duodenitis', 'Tiêu hóa'],
        ['K58', 'Hội chứng ruột kích thích (IBS)', 'Irritable bowel syndrome', 'Tiêu hóa'],
        ['L20', 'Viêm da cơ địa', 'Atopic dermatitis', 'Da liễu'],
        ['M45', 'Viêm cột sống dính khớp', 'Ankylosing spondylitis', 'Cơ xương khớp'],
        ['M54', 'Đau lưng', 'Dorsalgia', 'Cơ xương khớp'],
        ['M81', 'Loãng xương không có gãy xương bệnh lý', 'Osteoporosis without pathological fracture', 'Cơ xương khớp'],
        ['N30', 'Viêm bàng quang', 'Cystitis', 'Thận tiết niệu'],
        ['N39', 'Các rối loạn khác của hệ tiết niệu (Nhiễm trùng tiểu)', 'Other disorders of urinary system', 'Thận tiết niệu'],
        ['R50', 'Sốt không rõ nguyên nhân', 'Fever of other and unknown origin', 'Triệu chứng chung'],
        ['R51', 'Đau đầu', 'Headache', 'Triệu chứng chung'],
        ['R52', 'Đau, chưa phân loại nơi khác', 'Pain, not elsewhere classified', 'Triệu chứng chung'],
        ['Z00', 'Khám sức khỏe tổng quát', 'General examination and investigation of persons without complaint', 'Khám kiểm tra']
    ];

    $stmt = $conn->prepare("INSERT IGNORE INTO icd10_codes (code, name, name_en, category) VALUES (?, ?, ?, ?)");
    foreach ($sampleCodes as $c) {
        $stmt->execute($c);
    }
    echo "- Populated " . count($sampleCodes) . " sample ICD-10 codes into 'icd10_codes'.\n";

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
