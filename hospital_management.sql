-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 30, 2026 at 05:20 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `bed_id` (`bed_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admissions`
--

INSERT INTO `admissions` (`id`, `patient_id`, `doctor_id`, `bed_id`, `admission_date`, `discharge_date`, `diagnosis`, `status`, `notes`, `created_at`) VALUES
(1, 1, 1, 1, '2026-03-06 08:00:00', NULL, 'Rối loạn nhịp tim cần theo dõi', 'active', 'Theo dõi nhịp tim 24h', '2026-03-06 11:20:27');

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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `reason`, `status`, `created_at`) VALUES
(1, 1, 1, '2026-03-10 09:00:00', 'Đau ngực', 'confirmed', '2026-03-05 08:36:41'),
(2, 2, 2, '2026-03-11 14:00:00', 'Đau đầu kéo dài', 'pending', '2026-03-05 08:36:41'),
(3, 3, 3, '2026-03-12 10:00:00', 'Khám sức khỏe tổng quát', 'cancelled', '2026-03-05 08:36:41'),
(4, 4, 1, '2026-03-13 15:00:00', 'Khó thở', 'completed', '2026-03-05 08:36:41'),
(5, 1, 3, '2026-03-13 18:35:00', 'vb', 'pending', '2026-03-06 11:35:47');

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
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `department_id`, `specialty`, `experience_years`) VALUES
(1, 2, 1, 'Chuyên khoa tim mạch', 10),
(2, 3, 2, 'Chuyên khoa thần kinh', 8),
(3, 4, 4, 'Bác sĩ đa khoa', 5);

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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_shifts`
--

INSERT INTO `doctor_shifts` (`id`, `doctor_id`, `shift_id`) VALUES
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `equipment_name`, `quantity`, `status`, `description`) VALUES
(1, 'Máy theo dõi bệnh nhân', 10, 'available', 'Theo dõi nhịp tim, huyết áp'),
(2, 'Máy thở', 5, 'maintenance', 'Hỗ trợ hô hấp cấp cứu'),
(3, 'Máy theo dõi bệnh nhân', 10, 'available', 'Theo dõi nhịp tim, huyết áp'),
(4, 'Máy thở', 5, 'maintenance', 'Hỗ trợ hô hấp cấp cứu');

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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `appointment_id` (`appointment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `patient_id`, `appointment_id`, `admission_id`, `total_amount`, `discount`, `final_amount`, `payment_method`, `status`, `notes`, `created_by`, `created_at`) VALUES
(1, 1, 1, NULL, 705000.00, 0.00, 705000.00, 'cash', 'paid', NULL, 1, '2026-03-06 11:20:27'),
(2, 2, 2, NULL, 500000.00, 50000.00, 450000.00, 'momo', 'paid', NULL, 1, '2026-03-06 11:20:27');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int DEFAULT NULL,
  `item_type` enum('service','medicine','room','other') DEFAULT 'service',
  `item_id` int DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `quantity` int DEFAULT '1',
  `unit_price` decimal(12,2) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_type`, `item_id`, `description`, `quantity`, `unit_price`, `amount`) VALUES
(1, 1, 'service', 1, 'Xét nghiệm máu', 1, 200000.00, 200000.00),
(2, 1, 'medicine', 1, 'Paracetamol 500mg', 2, 2500.00, 5000.00),
(3, 1, 'service', 2, 'Chụp X-quang', 1, 500000.00, 500000.00),
(4, 2, 'service', 2, 'Chụp X-quang', 1, 500000.00, 500000.00);

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medical_devices`
--

INSERT INTO `medical_devices` (`id`, `name`, `device_code`, `status`, `department_id`, `purchase_date`) VALUES
(1, 'Máy đo điện tim', 'DEV001', 'available', 1, '2023-05-01'),
(2, 'Máy chụp MRI', 'DEV002', 'available', 2, '2022-08-15'),
(3, 'Máy chụp X-quang', 'DEV003', 'available', 4, '2021-10-10');

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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `appointment_id` (`appointment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`id`, `patient_id`, `doctor_id`, `appointment_id`, `diagnosis`, `treatment`, `notes`, `created_at`) VALUES
(1, 1, 1, 1, 'Rối loạn nhịp tim nhẹ', NULL, 'Khuyên bệnh nhân hạn chế caffeine', '2026-03-05 08:36:41'),
(2, 3, 3, 3, 'Sức khỏe bình thường', NULL, 'Tiếp tục duy trì chế độ ăn uống hợp lý', '2026-03-05 08:36:41'),
(3, 1, 1, 1, 'Viêm họng cấp tính', 'Kê đơn thuốc kháng sinh và giảm ho. Uống nhiều nước ấm.', 'Bệnh nhân có tiền sử dị ứng Penicillin. Khám lại sau 5 ngày.', '2026-04-30 14:57:03'),
(4, 2, 2, NULL, 'Rối loạn tiêu hóa', 'Men tiêu hóa, bù nước', 'Hạn chế đồ ăn cay nóng', '2026-04-30 14:57:03');

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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `description`, `quantity`, `expiry_date`, `price`) VALUES
(1, 'Paracetamol', 'Thuốc giảm đau hạ sốt', 500, '2027-05-01', 2500.00),
(2, 'Amoxicillin', 'Kháng sinh điều trị nhiễm khuẩn', 300, '2026-12-01', 5000.00),
(3, 'Aspirin', 'Thuốc hỗ trợ làm loãng máu', 200, '2027-03-15', 3500.00),
(4, 'Vitamin C', 'Tăng cường sức đề kháng', 600, '2027-08-01', 2000.00);

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
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `status`, `created_at`) VALUES
(1, 7, 'Nhắc lịch khám', 'Bạn có lịch khám vào ngày mai lúc 9 giờ', 'unread', '2026-03-05 08:36:41'),
(2, 8, 'Xác nhận lịch khám', 'Lịch khám của bạn đã được đặt thành công', 'unread', '2026-03-05 08:36:41'),
(3, 9, 'Kết quả xét nghiệm', 'Kết quả xét nghiệm máu của bạn đã có', 'unread', '2026-03-05 08:36:41');

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `nurses`
--

INSERT INTO `nurses` (`id`, `user_id`, `department_id`) VALUES
(1, 5, 1),
(2, 6, 3);

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
  KEY `appointment_id` (`appointment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `online_meetings`
--

INSERT INTO `online_meetings` (`id`, `appointment_id`, `meeting_id`, `meeting_link`, `start_time`, `status`) VALUES
(1, 2, 'MEET001', 'https://meet.benhvien.vn/MEET001', '2026-03-11 14:00:00', 'scheduled'),
(2, 1, 'MEET4032', 'https://meet.benhvien.vn/MEET4032', '2026-03-10 09:00:00', 'scheduled'),
(3, 5, 'NC-2B9DEAD1', 'https://meet.novacare.com/NC-2B9DEAD1', '2026-03-13 18:35:00', 'scheduled');

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
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `user_id`, `date_of_birth`, `gender`, `address`, `blood_type`, `medical_history`) VALUES
(1, 7, '1998-05-20', 'male', 'TP Hồ Chí Minh', 'O+', NULL),
(2, 8, '2000-11-10', 'female', 'Bình Dương', 'A+', NULL),
(3, 9, '1995-02-14', 'male', 'Đồng Nai', 'B+', NULL),
(4, 10, '2001-07-08', 'female', 'TP Hồ Chí Minh', 'AB+', NULL),
(5, 11, '2014-02-20', 'female', 'Gò Vấp', 'B-', 'Tự kỉ');

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
  KEY `doctor_id` (`doctor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `medical_record_id`, `doctor_id`, `created_at`) VALUES
(1, 1, 1, '2026-03-05 08:36:41'),
(2, 2, 3, '2026-03-05 08:36:41');

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `prescription_items`
--

INSERT INTO `prescription_items` (`id`, `prescription_id`, `medicine_id`, `dosage`, `duration`, `instructions`) VALUES
(1, 1, 1, '500mg', '5 ngày', 'Uống sau khi ăn'),
(2, 1, 3, '100mg', '7 ngày', 'Uống mỗi ngày một lần'),
(3, 2, 4, '1 viên', '10 ngày', 'Uống sau bữa sáng');

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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `price`, `description`) VALUES
(1, 'Xét nghiệm máu', 200000.00, 'Kiểm tra các chỉ số máu'),
(2, 'Chụp X-quang', 500000.00, 'Chụp X-quang vùng ngực'),
(3, 'Chụp MRI', 2000000.00, 'Chụp cộng hưởng từ');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
CREATE TABLE IF NOT EXISTS `shifts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shift_date` date DEFAULT NULL,
  `shift_type` enum('day','night') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `shift_date`, `shift_type`) VALUES
(1, '2026-03-10', 'day'),
(2, '2026-03-10', 'night'),
(3, '2026-03-11', 'night'),
(4, '2026-03-12', 'day');

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
  `role` enum('admin','doctor','nurse','patient') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(11, 'Trịnh Văn Vũ', 'vu@gmail.com', '$2y$12$AQKbxupa.fzqXf/j8aY0OukcNV8N0VdEmeLV/n58uhuk8EY4WQm8G', '0356478461', 'patient', '2026-03-06 10:43:26');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
