-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 05, 2026 at 08:39 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `reason`, `status`, `created_at`) VALUES
(1, 1, 1, '2026-03-10 09:00:00', 'Đau ngực', 'confirmed', '2026-03-05 08:36:41'),
(2, 2, 2, '2026-03-11 14:00:00', 'Đau đầu kéo dài', 'pending', '2026-03-05 08:36:41'),
(3, 3, 3, '2026-03-12 10:00:00', 'Khám sức khỏe tổng quát', 'confirmed', '2026-03-05 08:36:41'),
(4, 4, 1, '2026-03-13 15:00:00', 'Khó thở', 'pending', '2026-03-05 08:36:41');

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medical_devices`
--

INSERT INTO `medical_devices` (`id`, `name`, `device_code`, `status`, `department_id`, `purchase_date`) VALUES
(1, 'Máy đo điện tim', 'DEV001', 'available', 1, '2023-05-01'),
(2, 'Máy chụp MRI', 'DEV002', 'maintenance', 2, '2022-08-15'),
(3, 'Máy chụp X-quang', 'DEV003', 'in_use', 4, '2021-10-10');

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
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `appointment_id` (`appointment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`id`, `patient_id`, `doctor_id`, `appointment_id`, `diagnosis`, `notes`, `created_at`) VALUES
(1, 1, 1, 1, 'Rối loạn nhịp tim nhẹ', 'Khuyên bệnh nhân hạn chế caffeine', '2026-03-05 08:36:41'),
(2, 3, 3, 3, 'Sức khỏe bình thường', 'Tiếp tục duy trì chế độ ăn uống hợp lý', '2026-03-05 08:36:41');

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `online_meetings`
--

INSERT INTO `online_meetings` (`id`, `appointment_id`, `meeting_id`, `meeting_link`, `start_time`, `status`) VALUES
(1, 2, 'MEET001', 'https://meet.benhvien.vn/MEET001', '2026-03-11 14:00:00', 'scheduled');

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `user_id`, `date_of_birth`, `gender`, `address`, `blood_type`, `medical_history`) VALUES
(1, 7, '1998-05-20', 'male', 'TP Hồ Chí Minh', 'O+', NULL),
(2, 8, '2000-11-10', 'female', 'Bình Dương', 'A+', NULL),
(3, 9, '1995-02-14', 'male', 'Đồng Nai', 'B+', NULL),
(4, 10, '2001-07-08', 'female', 'TP Hồ Chí Minh', 'AB+', NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(1, 'Quản trị hệ thống', 'admin@benhvien.com', '123456', '0900000001', 'admin', '2026-03-05 08:36:40'),
(2, 'Bác sĩ Nguyễn Văn An', 'doctor1@benhvien.com', '123456', '0900000002', 'doctor', '2026-03-05 08:36:40'),
(3, 'Bác sĩ Trần Thị Bình', 'doctor2@benhvien.com', '123456', '0900000003', 'doctor', '2026-03-05 08:36:40'),
(4, 'Bác sĩ Phạm Văn Hùng', 'doctor3@benhvien.com', '123456', '0900000004', 'doctor', '2026-03-05 08:36:40'),
(5, 'Y tá Lê Thị Chi', 'nurse1@benhvien.com', '123456', '0900000005', 'nurse', '2026-03-05 08:36:40'),
(6, 'Y tá Nguyễn Thị Lan', 'nurse2@benhvien.com', '123456', '0900000006', 'nurse', '2026-03-05 08:36:40'),
(7, 'Phạm Minh Đức', 'benhnhan1@gmail.com', '123456', '0900000007', 'patient', '2026-03-05 08:36:40'),
(8, 'Nguyễn Thị Hoa', 'benhnhan2@gmail.com', '123456', '0900000008', 'patient', '2026-03-05 08:36:40'),
(9, 'Trần Văn Nam', 'benhnhan3@gmail.com', '123456', '0900000009', 'patient', '2026-03-05 08:36:40'),
(10, 'Lê Thị Mai', 'benhnhan4@gmail.com', '123456', '0900000010', 'patient', '2026-03-05 08:36:40');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
