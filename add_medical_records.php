<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

$sql = "
CREATE TABLE IF NOT EXISTS `medical_records` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `appointment_id` int DEFAULT NULL,
  `diagnosis` text NOT NULL,
  `treatment` text,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `medical_records` (`patient_id`, `doctor_id`, `appointment_id`, `diagnosis`, `treatment`, `notes`) 
VALUES 
(1, 1, 1, 'Viêm họng cấp tính', 'Kê đơn thuốc kháng sinh và giảm ho. Uống nhiều nước ấm.', 'Bệnh nhân có tiền sử dị ứng Penicillin. Khám lại sau 5 ngày.'),
(2, 2, NULL, 'Rối loạn tiêu hóa', 'Men tiêu hóa, bù nước', 'Hạn chế đồ ăn cay nóng');
";

try {
    $conn->exec($sql);
    echo "Table 'medical_records' created successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
