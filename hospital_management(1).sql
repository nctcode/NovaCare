-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 02, 2026 at 04:21 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS=0;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hospital_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admissions`
--

DROP TABLE IF EXISTS `admissions`;
CREATE TABLE IF NOT EXISTS `admissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `bed_id` int DEFAULT NULL,
  `admission_date` datetime DEFAULT NULL,
  `discharge_date` datetime DEFAULT NULL,
  `diagnosis` text,
  `status` enum('active','discharged','transferred') DEFAULT 'active',
  `notes` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `bed_id` (`bed_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admissions`
--

INSERT INTO `admissions` (`id`, `patient_id`, `doctor_id`, `bed_id`, `admission_date`, `discharge_date`, `diagnosis`, `status`, `notes`, `created_at`) VALUES
(1, 4, 1, 1, '2026-03-13 17:00:00', '2026-03-18 10:00:00', 'Suy tim độ II (NYHA) - Cần theo dõi ECG liên tục', 'discharged', 'Theo dõi nhịp tim 24h. Hạn chế muối và nước. Báo BS khi SpO2 < 92%.', '2026-03-13 17:00:00'),
(2, 6, 1, 4, '2026-05-06 10:00:00', NULL, 'Cơn nhịp nhanh trên thất', 'active', 'Theo dõi đáp ứng thuốc chống loạn nhịp', '2026-05-06 10:00:00'),
(3, 12, 5, 8, '2026-05-08 14:00:00', NULL, 'Tăng huyết áp kháng trị, chuẩn bị mổ', 'active', 'Kiểm soát HA bằng đường tĩnh mạch trước mổ', '2026-05-08 14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `appointment_date` datetime DEFAULT NULL,
  `reason` text,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `appointment_date` (`appointment_date`),
  KEY `status` (`status`),
  KEY `doctor_status` (`doctor_id`, `status`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `reason`, `status`, `created_at`) VALUES
(1, 1, 1, '2026-03-10 09:00:00', 'Đau ngực trái khi gắng sức', 'confirmed', '2026-03-05 08:36:41'),
(2, 2, 2, '2026-03-11 14:00:00', 'Đau đầu kéo dài kèm chóng mặt', 'pending', '2026-03-05 08:36:41'),
(3, 3, 3, '2026-03-12 10:00:00', 'Khám sức khỏe tổng quát định kỳ', 'cancelled', '2026-03-05 08:36:41'),
(4, 4, 1, '2026-03-13 15:00:00', 'Khó thở khi nằm, phù chân', 'completed', '2026-03-05 08:36:41'),
(5, 1, 3, '2026-04-15 09:30:00', 'Tái khám theo hẹn, kiểm tra huyết áp', 'completed', '2026-04-10 07:00:00'),
(6, 2, 2, '2026-05-05 10:00:00', 'Đau đầu tái phát, cần chụp CT', 'pending', '2026-04-30 18:22:49'),
(7, 6, 1, '2026-05-06 08:30:00', 'Tức ngực, nhịp tim nhanh', 'confirmed', '2026-05-01 09:00:00'),
(8, 7, 3, '2026-05-06 13:00:00', 'Đau dạ dày sau khi ăn', 'confirmed', '2026-05-02 10:15:00'),
(9, 8, 4, '2026-05-07 09:00:00', 'Bé bị ho khan, sốt nhẹ', 'pending', '2026-05-02 14:20:00'),
(10, 9, 2, '2026-05-07 14:30:00', 'Chóng mặt khi thay đổi tư thế', 'confirmed', '2026-05-02 16:00:00'),
(11, 10, 3, '2026-05-08 10:00:00', 'Sưng đau khớp ngón chân', 'confirmed', '2026-05-02 17:30:00'),
(12, 11, 4, '2026-05-08 15:00:00', 'Nổi mẩn đỏ ngứa', 'pending', '2026-05-02 18:00:00'),
(13, 12, 5, '2026-05-09 09:30:00', 'Hẹn tư vấn mổ hở van tim', 'confirmed', '2026-05-02 19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `beds`
--

DROP TABLE IF EXISTS `beds`;
CREATE TABLE IF NOT EXISTS `beds` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_id` int DEFAULT NULL,
  `bed_number` varchar(20) DEFAULT NULL,
  `status` enum('available','occupied','maintenance') DEFAULT 'available',
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `beds`
--

INSERT INTO `beds` (`id`, `room_id`, `bed_number`, `status`) VALUES
(1, 1, 'G1', 'occupied'),
(2, 1, 'G2', 'available'),
(3, 2, 'G1', 'available'),
(4, 2, 'G2', 'available'),
(5, 3, 'G1', 'available'),
(6, 3, 'G2', 'available'),
(7, 4, 'G1', 'available'),
(8, 4, 'G2', 'available'),
(9, 5, 'G1', 'available'),
(10, 5, 'G2', 'available'),
(11, 5, 'G3', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`) VALUES
(1, 'Tim mạch', 'Chẩn đoán và điều trị bệnh tim'),
(2, 'Thần kinh', 'Điều trị bệnh liên quan đến não và hệ thần kinh'),
(3, 'Nhi khoa', 'Khám và điều trị cho trẻ em'),
(4, 'Nội tổng quát', 'Khám và điều trị bệnh tổng quát');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
CREATE TABLE IF NOT EXISTS `doctors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `specialty` varchar(100) DEFAULT NULL,
  `experience_years` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `department_id`, `specialty`, `experience_years`) VALUES
(1, 2, 1, 'Chuyên khoa tim mạch', 10),
(2, 3, 2, 'Chuyên khoa thần kinh', 8),
(3, 4, 4, 'Bác sĩ đa khoa', 5),
(4, 14, 3, 'Chuyên khoa nhi', 7),
(5, 15, 1, 'Phẫu thuật tim mạch', 12);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_shifts`
--

DROP TABLE IF EXISTS `doctor_shifts`;
CREATE TABLE IF NOT EXISTS `doctor_shifts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `doctor_id` int DEFAULT NULL,
  `shift_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `shift_id` (`shift_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_shifts`
--

INSERT INTO `doctor_shifts` (`id`, `doctor_id`, `shift_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 3),
(4, 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

DROP TABLE IF EXISTS `equipment`;
CREATE TABLE IF NOT EXISTS `equipment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipment_name` varchar(150) NOT NULL,
  `quantity` int DEFAULT '0',
  `status` enum('available','maintenance') DEFAULT 'available',
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `equipment_name`, `quantity`, `status`, `description`) VALUES
(1, 'Máy theo dõi bệnh nhân', 10, 'available', 'Theo dõi nhịp tim, huyết áp, SpO2'),
(2, 'Máy thở', 5, 'maintenance', 'Hỗ trợ hô hấp cấp cứu'),
(3, 'Xe đẩy cấp cứu', 8, 'available', 'Trang bị đầy đủ thuốc và dụng cụ cấp cứu'),
(4, 'Bơm tiêm điện', 15, 'available', 'Truyền thuốc tự động theo liều lượng');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int DEFAULT NULL,
  `appointment_id` int DEFAULT NULL,
  `admission_id` int DEFAULT NULL,
  `total_amount` decimal(12,2) DEFAULT '0.00',
  `discount` decimal(12,2) DEFAULT '0.00',
  `final_amount` decimal(12,2) DEFAULT '0.00',
  `payment_method` enum('cash','card','momo','vnpay','transfer') DEFAULT 'cash',
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `notes` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `appointment_id` (`appointment_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `patient_id`, `appointment_id`, `admission_id`, `total_amount`, `discount`, `final_amount`, `payment_method`, `status`, `notes`, `created_by`, `created_at`) VALUES
(1, 1, 1, NULL, 705000.00, 0.00, 705000.00, 'cash', 'paid', 'Khám tim mạch + xét nghiệm', 12, '2026-03-10 11:00:00'),
(2, 2, 2, NULL, 500000.00, 50000.00, 450000.00, 'momo', 'paid', 'Khám thần kinh + chụp X-quang', 12, '2026-03-11 16:00:00'),
(3, 6, 7, NULL, 450000.00, 0.00, 450000.00, 'card', 'paid', 'Đo điện tim + Khám tổng quát', 12, '2026-05-06 09:30:00'),
(4, 7, 8, NULL, 300000.00, 0.00, 300000.00, 'transfer', 'pending', 'Khám tổng quát', 12, '2026-05-06 14:00:00'),
(5, 4, 4, 1, 3500000.00, 500000.00, 3000000.00, 'cash', 'paid', 'Phí tạm ứng nhập viện và lưu viện ban đầu', 12, '2026-03-13 17:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int DEFAULT NULL,
  `service_id` int DEFAULT NULL,
  `medicine_id` int DEFAULT NULL,
  `room_id` int DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `quantity` int DEFAULT '1',
  `unit_price` decimal(12,2) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `service_id` (`service_id`),
  KEY `medicine_id` (`medicine_id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `service_id`, `medicine_id`, `room_id`, `description`, `quantity`, `unit_price`, `amount`) VALUES
(1, 1, 1, NULL, NULL, 'Xét nghiệm máu tổng quát', 1, 200000.00, 200000.00),
(2, 1, NULL, 1, NULL, 'Paracetamol 500mg', 2, 2500.00, 5000.00),
(3, 1, 2, NULL, NULL, 'Chụp X-quang ngực', 1, 500000.00, 500000.00),
(4, 2, 2, NULL, NULL, 'Chụp X-quang sọ não', 1, 500000.00, 500000.00),
(5, 3, 5, NULL, NULL, 'Đo điện tim (ECG)', 1, 150000.00, 150000.00),
(6, 3, 6, NULL, NULL, 'Khám tổng quát', 1, 300000.00, 300000.00),
(7, 4, 6, NULL, NULL, 'Khám tổng quát', 1, 300000.00, 300000.00),
(8, 5, NULL, NULL, 1, 'Tiền phòng tiêu chuẩn (5 ngày)', 5, 500000.00, 2500000.00),
(9, 5, 1, NULL, NULL, 'Xét nghiệm máu tổng quát', 1, 200000.00, 200000.00),
(10, 5, 5, NULL, NULL, 'Đo điện tim (ECG)', 2, 150000.00, 300000.00),
(11, 5, NULL, 5, NULL, 'Furosemide 40mg', 10, 3000.00, 30000.00),
(12, 5, NULL, 6, NULL, 'Enalapril 5mg', 10, 4500.00, 45000.00),
(13, 5, NULL, NULL, NULL, 'Phí sinh hoạt khác', 1, 425000.00, 425000.00);

-- --------------------------------------------------------

--
-- Table structure for table `medical_devices`
--

DROP TABLE IF EXISTS `medical_devices`;
CREATE TABLE IF NOT EXISTS `medical_devices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `device_code` varchar(100) DEFAULT NULL,
  `status` enum('available','in_use','maintenance') DEFAULT 'available',
  `department_id` int DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medical_devices`
--

INSERT INTO `medical_devices` (`id`, `name`, `device_code`, `status`, `department_id`, `purchase_date`) VALUES
(1, 'Máy đo điện tim (ECG)', 'DEV-TM-001', 'in_use', 1, '2023-05-01'),
(2, 'Máy chụp MRI 1.5T', 'DEV-TK-001', 'available', 2, '2022-08-15'),
(3, 'Máy chụp X-quang kỹ thuật số', 'DEV-NK-001', 'available', 4, '2021-10-10'),
(4, 'Máy siêu âm 4D', 'DEV-NK-002', 'available', 3, '2024-01-20'),
(5, 'Máy xét nghiệm huyết học tự động', 'DEV-XN-001', 'available', 4, '2023-11-05');

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

DROP TABLE IF EXISTS `medical_records`;
CREATE TABLE IF NOT EXISTS `medical_records` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `appointment_id` int DEFAULT NULL,
  `diagnosis` text,
  `treatment` text,
  `notes` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `appointment_id` (`appointment_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`id`, `patient_id`, `doctor_id`, `appointment_id`, `diagnosis`, `treatment`, `notes`, `created_at`) VALUES
(1, 1, 1, 1, 'Rối loạn nhịp tim nhẹ (Loạn nhịp xoang)', 'Theo dõi Holter 24h, hạn chế caffeine và rượu bia', 'Bệnh nhân có tiền sử dị ứng Penicillin. Hẹn tái khám sau 2 tuần.', '2026-03-10 10:00:00'),
(2, 3, 3, 3, 'Sức khỏe tổng quát bình thường', 'Không cần điều trị. Bổ sung Vitamin tổng hợp.', 'Các chỉ số xét nghiệm trong giới hạn bình thường. Hẹn khám lại sau 6 tháng.', '2026-03-12 11:00:00'),
(3, 4, 1, 4, 'Suy tim độ II (NYHA)', 'Furosemide 40mg/ngày, Enalapril 5mg x 2 lần/ngày, hạn chế muối', 'Bệnh nhân phù 2 chi dưới, khó thở khi nằm. Cần nhập viện theo dõi.', '2026-03-13 16:30:00'),
(4, 2, 2, 2, 'Đau nửa đầu Migraine', 'Paracetamol 1g khi đau, Topiramate 25mg/ngày phòng ngừa', 'Tiền sử gia đình có người bị Migraine. Tránh stress và thiếu ngủ.', '2026-03-11 15:30:00'),
(5, 6, 1, 7, 'Cơn nhịp nhanh trên thất', 'Theo dõi ngoại trú, kê đơn chống loạn nhịp', 'Khuyên bệnh nhân ngừng hút thuốc', '2026-05-06 09:00:00'),
(6, 7, 3, 8, 'Viêm loét dạ dày - tá tràng', 'Omeprazole 20mg x 2 lần/ngày, thay đổi thói quen ăn uống', 'Tránh đồ ăn cay nóng, chua, thức khuya', '2026-05-06 13:45:00'),
(7, 9, 2, 10, 'Thiểu năng tuần hoàn não', 'Cải thiện tuần hoàn não, nghỉ ngơi', 'Nghi ngờ liên quan đến loãng xương cổ', '2026-05-07 15:00:00'),
(8, 10, 3, 11, 'Cơn Gout cấp', 'Colchicine 1mg, tăng liều Allopurinol', 'Chườm lạnh, kiêng đạm động vật', '2026-05-08 10:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

DROP TABLE IF EXISTS `medicines`;
CREATE TABLE IF NOT EXISTS `medicines` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text,
  `quantity` int DEFAULT '0',
  `expiry_date` date DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `description`, `quantity`, `expiry_date`, `price`) VALUES
(1, 'Paracetamol 500mg', 'Thuốc giảm đau hạ sốt, dùng phổ biến', 500, '2027-05-01', 2500.00),
(2, 'Amoxicillin 500mg', 'Kháng sinh nhóm Beta-lactam, điều trị nhiễm khuẩn', 300, '2026-12-01', 5000.00),
(3, 'Aspirin 81mg', 'Thuốc chống kết tập tiểu cầu, hỗ trợ tim mạch', 200, '2027-03-15', 3500.00),
(4, 'Vitamin C 1000mg', 'Tăng cường sức đề kháng, chống oxy hóa', 600, '2027-08-01', 2000.00),
(5, 'Furosemide 40mg', 'Thuốc lợi tiểu, điều trị suy tim và phù', 150, '2027-06-01', 3000.00),
(6, 'Enalapril 5mg', 'Thuốc ức chế ACE, điều trị tăng huyết áp và suy tim', 200, '2027-04-15', 4500.00),
(7, 'Topiramate 25mg', 'Thuốc chống động kinh, phòng ngừa Migraine', 8, '2027-01-20', 8000.00),
(8, 'Omeprazole 20mg', 'Thuốc ức chế bơm proton, điều trị viêm loét dạ dày', 350, '2027-09-01', 3500.00);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `message` text,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `status`, `created_at`) VALUES
(1, 7, 'Nhắc lịch khám', 'Bạn có lịch khám vào ngày 10/03/2026 lúc 09:00 với BS. Nguyễn Văn An (Tim mạch).', 'unread', '2026-03-09 08:00:00'),
(2, 8, 'Xác nhận lịch khám', 'Lịch khám của bạn ngày 11/03/2026 với BS. Trần Thị Bình (Thần kinh) đã được đặt thành công.', 'unread', '2026-03-05 08:36:41'),
(3, 9, 'Kết quả xét nghiệm', 'Kết quả xét nghiệm máu ngày 12/03/2026 của bạn đã có. Vui lòng liên hệ lễ tân để nhận kết quả.', 'read', '2026-03-13 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `nurses`
--

DROP TABLE IF EXISTS `nurses`;
CREATE TABLE IF NOT EXISTS `nurses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `nurses`
--

INSERT INTO `nurses` (`id`, `user_id`, `department_id`) VALUES
(1, 5, 1),
(2, 6, 3),
(3, 16, 2);

-- --------------------------------------------------------

--
-- Table structure for table `online_meetings`
--

DROP TABLE IF EXISTS `online_meetings`;
CREATE TABLE IF NOT EXISTS `online_meetings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `appointment_id` int DEFAULT NULL,
  `meeting_id` varchar(100) DEFAULT NULL,
  `meeting_link` varchar(255) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  PRIMARY KEY (`id`),
  KEY `appointment_id` (`appointment_id`),
  KEY `meeting_id` (`meeting_id`),
  KEY `start_time` (`start_time`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `online_meetings`
--

INSERT INTO `online_meetings` (`id`, `appointment_id`, `meeting_id`, `meeting_link`, `start_time`, `status`) VALUES
(1, 1, 'NC-A1B2C3D4', 'https://meet.novacare.com/NC-A1B2C3D4', '2026-03-10 09:00:00', 'completed'),
(2, 2, 'NC-E5F6G7H8', 'https://meet.novacare.com/NC-E5F6G7H8', '2026-03-11 14:00:00', 'scheduled'),
(3, 5, 'NC-J9K0L1M2', 'https://meet.novacare.com/NC-J9K0L1M2', '2026-04-15 09:30:00', 'completed'),
(4, 6, 'NC-N3P4Q5R6', 'https://meet.novacare.com/NC-N3P4Q5R6', '2026-05-05 10:00:00', 'scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
CREATE TABLE IF NOT EXISTS `patients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text,
  `blood_type` varchar(5) DEFAULT NULL,
  `medical_history` text,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `date_of_birth` (`date_of_birth`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `user_id`, `date_of_birth`, `gender`, `address`, `blood_type`, `medical_history`) VALUES
(1, 7, '1998-05-20', 'male', '123 Nguyễn Trãi, P. Bến Thành, Q.1, TP.HCM', 'O+', 'Dị ứng Penicillin. Tiền sử tăng huyết áp nhẹ.'),
(2, 8, '2000-11-10', 'female', '45 Đại lộ Bình Dương, P. Hiệp Thành, TX. Thủ Dầu Một, Bình Dương', 'A+', 'Tiền sử gia đình: Migraine. Không dị ứng thuốc.'),
(3, 9, '1995-02-14', 'male', '78 Trần Phú, P. Bửu Long, TP. Biên Hòa, Đồng Nai', 'B+', 'Không có bệnh lý nền. Sức khỏe tốt.'),
(4, 10, '2001-07-08', 'female', '256 Võ Văn Kiệt, P. Cầu Kho, Q.1, TP.HCM', 'AB+', 'Tiền sử bệnh tim bẩm sinh (nhẹ). Đang theo dõi.'),
(5, 11, '2014-02-20', 'female', '32/5 Nguyễn Văn Ngọi, P.7, Q. Gò Vấp, TP.HCM', 'B-', 'Rối loạn phổ tự kỉ (ASD). Đang điều trị tại khoa Nhi.'),
(6, 17, '1985-09-12', 'male', '110 Nam Kỳ Khởi Nghĩa, Q.3, TP.HCM', 'O+', 'Đái tháo đường type 2 (phát hiện 2 năm).'),
(7, 18, '1990-03-25', 'male', '55 Lê Lợi, TP. Vũng Tàu', 'A-', 'Trào ngược dạ dày thực quản (GERD).'),
(8, 19, '1993-11-05', 'female', '12 Nguyễn Huệ, TP. Huế', 'B+', 'Viêm xoang mạn tính.'),
(9, 20, '1978-01-30', 'female', '88 Phan Đăng Lưu, Phú Nhuận, TP.HCM', 'O-', 'Rối loạn tiền đình, loãng xương mức độ nhẹ.'),
(10, 21, '1982-08-14', 'male', '44 Trần Hưng Đạo, Q.5, TP.HCM', 'AB-', 'Gout. Đang dùng Allopurinol.'),
(11, 22, '2005-06-18', 'female', '101 Cách Mạng Tháng Tám, Tân Bình, TP.HCM', 'O+', 'Dị ứng hải sản.'),
(12, 23, '1965-12-02', 'male', '23 Nguyễn Đình Chiểu, Q.3, TP.HCM', 'A+', 'Tăng huyết áp vô căn, suy thận độ 2.'),
(13, 24, '1970-04-19', 'female', '9 Lê Duẩn, Q.1, TP.HCM', 'B+', 'Thoái hóa khớp gối 2 bên.');

-- --------------------------------------------------------

--
-- Table structure for table `patient_services`
--

DROP TABLE IF EXISTS `patient_services`;
CREATE TABLE IF NOT EXISTS `patient_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int DEFAULT NULL,
  `service_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `result` text,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `service_id` (`service_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `service_date` (`service_date`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `patient_services`
--

INSERT INTO `patient_services` (`id`, `patient_id`, `service_id`, `doctor_id`, `service_date`, `result`) VALUES
(1, 1, 1, 1, '2026-03-10', 'Chỉ số máu bình thường'),
(2, 2, 2, 2, '2026-03-11', 'Không phát hiện bất thường'),
(3, 3, 1, 3, '2026-03-12', 'Kết quả bình thường');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

DROP TABLE IF EXISTS `prescriptions`;
CREATE TABLE IF NOT EXISTS `prescriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `medical_record_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `medical_record_id` (`medical_record_id`),
  KEY `doctor_id` (`doctor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `medical_record_id`, `doctor_id`, `created_at`) VALUES
(1, 1, 1, '2026-03-10 10:30:00'),
(2, 3, 1, '2026-03-13 17:00:00'),
(3, 4, 2, '2026-03-11 16:00:00'),
(4, 6, 3, '2026-05-06 14:00:00'),
(5, 8, 3, '2026-05-08 10:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `prescription_items`
--

DROP TABLE IF EXISTS `prescription_items`;
CREATE TABLE IF NOT EXISTS `prescription_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `prescription_id` int DEFAULT NULL,
  `medicine_id` int DEFAULT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `instructions` text,
  PRIMARY KEY (`id`),
  KEY `prescription_id` (`prescription_id`),
  KEY `medicine_id` (`medicine_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `prescription_items`
--

INSERT INTO `prescription_items` (`id`, `prescription_id`, `medicine_id`, `dosage`, `duration`, `instructions`) VALUES
(1, 1, 1, '500mg x 3 lần/ngày', '5 ngày', 'Uống sau khi ăn no, cách nhau 6 tiếng'),
(2, 1, 3, '81mg x 1 lần/ngày', '30 ngày', 'Uống vào buổi sáng sau ăn'),
(3, 2, 5, '40mg x 1 lần/ngày', '14 ngày', 'Uống buổi sáng, theo dõi lượng nước tiểu'),
(4, 2, 6, '5mg x 2 lần/ngày', '30 ngày', 'Uống sáng-tối, theo dõi huyết áp'),
(5, 3, 1, '1g khi đau', 'Khi cần', 'Không quá 4g/ngày. Uống khi cơn đau xuất hiện.'),
(6, 3, 7, '25mg x 1 lần/ngày', '60 ngày', 'Uống tối trước khi ngủ, phòng ngừa Migraine'),
(7, 4, 8, '20mg x 2 lần/ngày', '14 ngày', 'Uống trước ăn sáng và tối 30 phút'),
(8, 5, 1, '500mg x 2 lần/ngày', '3 ngày', 'Giảm đau chống viêm'),
(9, 5, 4, '1000mg x 1 lần/ngày', '10 ngày', 'Uống buổi sáng');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_number` varchar(20) NOT NULL,
  `department_id` int DEFAULT NULL,
  `room_type` enum('standard','vip','icu') DEFAULT 'standard',
  `price_per_day` decimal(12,2) DEFAULT '0.00',
  `status` enum('available','full','maintenance') DEFAULT 'available',
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_number`, `department_id`, `room_type`, `price_per_day`, `status`) VALUES
(1, 'P101', 1, 'standard', 500000.00, 'available'),
(2, 'P102', 1, 'vip', 1500000.00, 'available'),
(3, 'P201', 2, 'standard', 500000.00, 'available'),
(4, 'P202', 4, 'icu', 3000000.00, 'available'),
(5, 'P301', 3, 'standard', 500000.00, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_name` varchar(150) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `price`, `description`) VALUES
(1, 'Xét nghiệm máu tổng quát', 200000.00, 'Kiểm tra công thức máu, đường huyết, mỡ máu, chức năng gan-thận'),
(2, 'Chụp X-quang', 500000.00, 'Chụp X-quang kỹ thuật số các vùng: ngực, bụng, xương'),
(3, 'Chụp MRI', 2000000.00, 'Chụp cộng hưởng từ não, cột sống, khớp'),
(4, 'Siêu âm tổng quát', 350000.00, 'Siêu âm bụng, tuyến giáp, tim'),
(5, 'Đo điện tim (ECG)', 150000.00, 'Đo điện tâm đồ 12 chuyển đạo'),
(6, 'Khám tổng quát', 300000.00, 'Khám sức khỏe định kỳ, bao gồm xét nghiệm cơ bản');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
CREATE TABLE IF NOT EXISTS `shifts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shift_date` date DEFAULT NULL,
  `shift_type` enum('day','night') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shift_date` (`shift_date`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `shift_date`, `shift_type`) VALUES
(1, '2026-05-05', 'day'),
(2, '2026-05-05', 'night'),
(3, '2026-05-06', 'day'),
(4, '2026-05-06', 'night'),
(5, '2026-05-07', 'day'),
(6, '2026-05-07', 'night');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','doctor','nurse','patient','receptionist','pharmacist') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `phone` (`phone`),
  KEY `role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(1, 'Quản trị hệ thống', 'admin@benhvien.com', '$2y$12$0BAlF6htZbXMngo40xVSKu6xHa60HgruKrU0lQNHU1l8RLxGiqhfa', '0900000001', 'admin', '2026-03-05 08:36:40'),
(2, 'Bác sĩ Nguyễn Văn An', 'doctor1@benhvien.com', '$2y$12$fOpZQ/XX.HBAi3OcnHv/TO6Oe3kIarLODFa5rvR6YGME0yJFi7Uqe', '0900000002', 'doctor', '2026-03-05 08:36:40'),
(3, 'Bác sĩ Trần Thị Bình', 'doctor2@benhvien.com', '$2y$12$g2jdv.DUHV7Bm40CQEz6D.sPfIRkLgCToDv9aSvQ98RhoYp78btUW', '0900000003', 'doctor', '2026-03-05 08:36:40'),
(4, 'Bác sĩ Phạm Văn Hùng', 'doctor3@benhvien.com', '$2y$12$PymVnnD6XegzwYk3YuEH7uST7z8B8haYY7YCNoDnm9FfgnMPmU0bW', '0900000004', 'doctor', '2026-03-05 08:36:40'),
(5, 'Y tá Lê Thị Chi', 'nurse1@benhvien.com', '$2y$12$mF6LGgZpVHvh57e2bDZdg./2Ywo9rGQU/mYkil2hPbdopEu.24MYO', '0900000005', 'nurse', '2026-03-05 08:36:40'),
(6, 'Y tá Nguyễn Thị Lan', 'nurse2@benhvien.com', '$2y$12$Wuf926HXq0dT3cm9J3MFqe.kb6O4KPWL1Y.F69Y9PgDZ.WJ3uKG3a', '0900000006', 'nurse', '2026-03-05 08:36:40'),
(7, 'Phạm Minh Đức', 'benhnhan1@gmail.com', '$2y$12$NfpYby/t3KqDHYWQqoLBeOw8TQciPfUjlWg2aAfyqUFd9ic7Kf9dK', '0900000007', 'patient', '2026-03-05 08:36:40'),
(8, 'Nguyễn Thị Hoa', 'benhnhan2@gmail.com', '$2y$12$dkM4HksQPbsPs1URTYUe/ObeyfCXwr0JanDg3WTcIrGK2Jejhxr9O', '0900000008', 'patient', '2026-03-05 08:36:40'),
(9, 'Trần Văn Nam', 'benhnhan3@gmail.com', '$2y$12$ADxPG4Vp4sxBsgHMtg/la.k4LQzR3AJIeFq.OLskyLlQL6.2A/.RS', '0900000009', 'patient', '2026-03-05 08:36:40'),
(10, 'Lê Thị Mai', 'benhnhan4@gmail.com', '$2y$12$a/N3nwrF.YDeR0t3VrHxt.2HEvYfsTTeYJM5xpbf/0JrKceAGhLr.', '0900000010', 'patient', '2026-03-05 08:36:40'),
(11, 'Trịnh Văn Vũ', 'vu@gmail.com', '$2y$12$AQKbxupa.fzqXf/j8aY0OukcNV8N0VdEmeLV/n58uhuk8EY4WQm8G', '0356478461', 'patient', '2026-03-06 10:43:26'),
(12, 'Lễ tân Nguyễn Thị Hương', 'letan@benhvien.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000012', 'receptionist', '2026-05-02 16:17:50'),
(13, 'Dược sĩ Trần Văn Minh', 'duocsi@benhvien.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000013', 'pharmacist', '2026-05-02 16:17:50'),
(14, 'Bác sĩ Lê Quang Bảo', 'doctor4@benhvien.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000014', 'doctor', '2026-05-02 16:18:00'),
(15, 'Bác sĩ Đinh Thu Trà', 'doctor5@benhvien.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000015', 'doctor', '2026-05-02 16:18:00'),
(16, 'Y tá Trần Quang Đại', 'nurse3@benhvien.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000016', 'nurse', '2026-05-02 16:18:00'),
(17, 'Lâm Gia Khang', 'benhnhan5@gmail.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000017', 'patient', '2026-05-02 16:18:00'),
(18, 'Nguyễn Minh Tuấn', 'benhnhan6@gmail.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000018', 'patient', '2026-05-02 16:18:00'),
(19, 'Vũ Thanh Thảo', 'benhnhan7@gmail.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000019', 'patient', '2026-05-02 16:18:00'),
(20, 'Đặng Hoàng Yến', 'benhnhan8@gmail.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000020', 'patient', '2026-05-02 16:18:00'),
(21, 'Hoàng Trọng Nghĩa', 'benhnhan9@gmail.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000021', 'patient', '2026-05-02 16:18:00'),
(22, 'Phan Bích Ngọc', 'benhnhan10@gmail.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000022', 'patient', '2026-05-02 16:18:00'),
(23, 'Đoàn Hữu Phước', 'benhnhan11@gmail.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000023', 'patient', '2026-05-02 16:18:00'),
(24, 'Trương Thị Hà', 'benhnhan12@gmail.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000024', 'patient', '2026-05-02 16:18:00');

-- =========================================================
-- BẢNG AUDIT LOGS
-- =========================================================

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int NOT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `table_name` (`table_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =========================================================
-- FOREIGN KEY CONSTRAINTS
-- =========================================================

ALTER TABLE `doctors`
  ADD CONSTRAINT `fk_doctors_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doctors_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `nurses`
  ADD CONSTRAINT `fk_nurses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nurses_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patients_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_rooms_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `beds`
  ADD CONSTRAINT `fk_beds_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appt_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appt_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appt_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appt_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD UNIQUE KEY `uk_doc_appt` (`doctor_id`, `appointment_date`);

ALTER TABLE `admissions`
  ADD CONSTRAINT `fk_adm_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adm_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adm_bed` FOREIGN KEY (`bed_id`) REFERENCES `beds` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adm_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adm_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `medical_records`
  ADD CONSTRAINT `fk_mr_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mr_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mr_appt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mr_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mr_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `prescriptions`
  ADD CONSTRAINT `fk_pres_record` FOREIGN KEY (`medical_record_id`) REFERENCES `medical_records` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pres_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `prescription_items`
  ADD CONSTRAINT `fk_pi_pres` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pi_med` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_inv_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inv_appt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inv_admission` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inv_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inv_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `invoice_items`
  ADD CONSTRAINT `fk_ii_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ii_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ii_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ii_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `doctor_shifts`
  ADD CONSTRAINT `fk_ds_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ds_shift` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `online_meetings`
  ADD CONSTRAINT `fk_om_appt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `medical_devices`
  ADD CONSTRAINT `fk_mdev_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `patient_services`
  ADD CONSTRAINT `fk_ps_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ps_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ps_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- =========================================================
-- TRIGGERS - Đồng bộ dữ liệu tự động & Audit
-- =========================================================

-- Trigger 0: Chống xếp trùng giường (1 giường không thể có 2 bệnh nhân)
DELIMITER //
CREATE TRIGGER `trg_admission_before_insert` BEFORE INSERT ON `admissions`
FOR EACH ROW
BEGIN
    IF NEW.bed_id IS NOT NULL AND NEW.status = 'active' THEN
        IF (SELECT `status` FROM `beds` WHERE `id` = NEW.bed_id) = 'occupied' THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Giường này đã có bệnh nhân.';
        END IF;
    END IF;
END//

-- Trigger 1: Khi nhập viện → đánh dấu giường occupied
CREATE TRIGGER `trg_admission_after_insert` AFTER INSERT ON `admissions`
FOR EACH ROW
BEGIN
    IF NEW.bed_id IS NOT NULL THEN
        UPDATE `beds` SET `status` = 'occupied' WHERE `id` = NEW.bed_id;
    END IF;
END//

-- Trigger 2: Khi xuất viện → giải phóng giường
CREATE TRIGGER `trg_admission_after_update` AFTER UPDATE ON `admissions`
FOR EACH ROW
BEGIN
    IF NEW.status = 'discharged' AND OLD.status = 'active' AND NEW.bed_id IS NOT NULL THEN
        UPDATE `beds` SET `status` = 'available' WHERE `id` = NEW.bed_id;
    END IF;
END//

-- Trigger 3: Khi giường thay đổi → cập nhật trạng thái phòng
CREATE TRIGGER `trg_bed_after_update` AFTER UPDATE ON `beds`
FOR EACH ROW
BEGIN
    DECLARE total_beds INT;
    DECLARE occupied_beds INT;

    SELECT COUNT(*), SUM(IF(`status`='occupied',1,0))
    INTO total_beds, occupied_beds
    FROM `beds` WHERE `room_id` = NEW.room_id;

    IF occupied_beds >= total_beds THEN
        UPDATE `rooms` SET `status` = 'full' WHERE `id` = NEW.room_id;
    ELSE
        UPDATE `rooms` SET `status` = 'available' WHERE `id` = NEW.room_id;
    END IF;
END//

-- Trigger 4: Ghi Audit Log tự động khi sửa Bệnh án
CREATE TRIGGER `trg_medical_records_after_update` AFTER UPDATE ON `medical_records`
FOR EACH ROW
BEGIN
    INSERT INTO `audit_logs` (`user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`)
    VALUES (
        NEW.updated_by, 
        'UPDATE', 
        'medical_records', 
        NEW.id, 
        JSON_OBJECT('diagnosis', OLD.diagnosis, 'treatment', OLD.treatment), 
        JSON_OBJECT('diagnosis', NEW.diagnosis, 'treatment', NEW.treatment)
    );
END//

DELIMITER ;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;