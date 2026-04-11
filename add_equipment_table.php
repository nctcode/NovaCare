<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

$sql = "
CREATE TABLE IF NOT EXISTS `equipment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipment_name` varchar(150) NOT NULL,
  `quantity` int DEFAULT '0',
  `status` enum('available','maintenance') DEFAULT 'available',
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `equipment` (`equipment_name`, `quantity`, `status`, `description`) 
VALUES 
('Máy theo dõi bệnh nhân', 10, 'available', 'Theo dõi nhịp tim, huyết áp'),
('Máy thở', 5, 'maintenance', 'Hỗ trợ hô hấp cấp cứu');
";

try {
    $conn->exec($sql);
    echo "Table 'equipment' created successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
