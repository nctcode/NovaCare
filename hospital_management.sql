-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Máy chủ: db:3306
-- Thời gian đã tạo: Th5 31, 2026 lúc 10:50 AM
-- Phiên bản máy phục vụ: 8.0.46
-- Phiên bản PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `hospital_management`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admissions`
--

CREATE TABLE `admissions` (
  `id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `bed_id` int DEFAULT NULL,
  `admission_date` datetime DEFAULT NULL,
  `discharge_date` datetime DEFAULT NULL,
  `diagnosis` text,
  `status` enum('pending','active','discharged','transferred') DEFAULT 'pending',
  `notes` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `admissions`
--

INSERT INTO `admissions` (`id`, `patient_id`, `doctor_id`, `bed_id`, `admission_date`, `discharge_date`, `diagnosis`, `status`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 1, 1, '2026-03-13 17:00:00', '2026-03-18 10:00:00', 'Suy tim độ II (NYHA) - Cần theo dõi ECG liên tục', 'discharged', 'Theo dõi nhịp tim 24h. Hạn chế muối và nước. Báo BS khi SpO2 < 92%.', NULL, NULL, '2026-03-13 17:00:00', '2026-05-02 17:12:27', NULL),
(2, 6, 1, 4, '2026-05-06 10:00:00', '2026-05-27 16:39:30', 'Cơn nhịp nhanh trên thất', 'discharged', 'Theo dõi đáp ứng thuốc chống loạn nhịp', NULL, 5, '2026-05-06 10:00:00', '2026-05-27 16:39:30', NULL),
(3, 12, 5, 8, '2026-05-08 14:00:00', NULL, 'Tăng huyết áp kháng trị, chuẩn bị mổ', 'active', 'Kiểm soát HA bằng đường tĩnh mạch trước mổ', NULL, NULL, '2026-05-08 14:00:00', '2026-05-02 17:12:27', NULL),
(5, 6, 1, 2, '2026-05-29 09:43:00', NULL, 'Suy nhược cơ thể nghiêm trọng cần truyền dịch', 'active', '', 2, 5, '2026-05-29 09:43:01', '2026-05-29 09:43:55', NULL),
(6, 6, 1, 3, '2026-05-31 03:40:00', '2026-05-31 03:42:56', 'nhức đầu', 'discharged', '', 2, 5, '2026-05-31 03:39:48', '2026-05-31 03:42:56', NULL);

--
-- Bẫy `admissions`
--
DELIMITER $$
CREATE TRIGGER `trg_admission_after_insert` AFTER INSERT ON `admissions` FOR EACH ROW BEGIN
    IF NEW.bed_id IS NOT NULL THEN
        UPDATE `beds` SET `status` = 'occupied' WHERE `id` = NEW.bed_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_admission_after_update` AFTER UPDATE ON `admissions` FOR EACH ROW BEGIN
    IF NEW.status = 'discharged' AND OLD.status = 'active' AND NEW.bed_id IS NOT NULL THEN
        UPDATE `beds` SET `status` = 'available' WHERE `id` = NEW.bed_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_admission_before_insert` BEFORE INSERT ON `admissions` FOR EACH ROW BEGIN
    IF NEW.bed_id IS NOT NULL AND NEW.status = 'active' THEN
        IF (SELECT `status` FROM `beds` WHERE `id` = NEW.bed_id) = 'occupied' THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Giường này đã có bệnh nhân.';
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `appointments`
--

CREATE TABLE `appointments` (
  `id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `appointment_date` datetime DEFAULT NULL,
  `reason` text,
  `status` enum('pending','confirmed','cancelled','completed','emergency') DEFAULT 'pending',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `reason`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2026-03-10 09:00:00', 'Đau ngực trái khi gắng sức', 'confirmed', NULL, NULL, '2026-03-05 08:36:41', '2026-05-02 17:12:27', NULL),
(2, 2, 2, '2026-03-11 14:00:00', 'Đau đầu kéo dài kèm chóng mặt', 'pending', NULL, NULL, '2026-03-05 08:36:41', '2026-05-02 17:12:27', NULL),
(3, 3, 3, '2026-03-12 10:00:00', 'Khám sức khỏe tổng quát định kỳ', 'cancelled', NULL, NULL, '2026-03-05 08:36:41', '2026-05-02 17:12:27', NULL),
(4, 4, 1, '2026-03-13 15:00:00', 'Khó thở khi nằm, phù chân', 'completed', NULL, NULL, '2026-03-05 08:36:41', '2026-05-02 17:12:27', NULL),
(5, 1, 3, '2026-04-15 09:30:00', 'Tái khám theo hẹn, kiểm tra huyết áp', 'completed', NULL, NULL, '2026-04-10 07:00:00', '2026-05-02 17:12:27', NULL),
(6, 2, 2, '2026-05-05 10:00:00', 'Đau đầu tái phát, cần chụp CT', 'pending', NULL, NULL, '2026-04-30 18:22:49', '2026-05-02 17:12:27', NULL),
(7, 6, 1, '2026-05-06 08:30:00', 'Tức ngực, nhịp tim nhanh', 'confirmed', NULL, NULL, '2026-05-01 09:00:00', '2026-05-02 17:12:27', NULL),
(8, 7, 3, '2026-05-06 13:00:00', 'Đau dạ dày sau khi ăn', 'confirmed', NULL, NULL, '2026-05-02 10:15:00', '2026-05-02 17:12:27', NULL),
(9, 8, 4, '2026-05-07 09:00:00', 'Bé bị ho khan, sốt nhẹ', 'pending', NULL, NULL, '2026-05-02 14:20:00', '2026-05-02 17:12:27', NULL),
(10, 9, 2, '2026-05-07 14:30:00', 'Chóng mặt khi thay đổi tư thế', 'confirmed', NULL, NULL, '2026-05-02 16:00:00', '2026-05-02 17:12:27', NULL),
(11, 10, 3, '2026-05-08 10:00:00', 'Sưng đau khớp ngón chân', 'confirmed', NULL, NULL, '2026-05-02 17:30:00', '2026-05-02 17:12:27', NULL),
(12, 11, 4, '2026-05-08 15:00:00', 'Nổi mẩn đỏ ngứa', 'pending', NULL, NULL, '2026-05-02 18:00:00', '2026-05-02 17:12:27', NULL),
(13, 12, 5, '2026-05-09 09:30:00', 'Hẹn tư vấn mổ hở van tim', 'confirmed', NULL, NULL, '2026-05-02 19:00:00', '2026-05-02 17:12:27', NULL),
(14, 14, 1, '2026-05-08 21:43:00', 'Kiểm tra trình trạng bênh', 'pending', 26, NULL, '2026-05-07 14:43:55', '2026-05-07 14:43:55', NULL),
(15, 14, 1, '2026-05-07 21:49:00', 'Bị đau bụng khó tiêu nhiều ngày liền', 'pending', 26, NULL, '2026-05-07 14:49:52', '2026-05-07 14:49:52', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `log_type` varchar(50) DEFAULT 'data_change',
  `table_name` varchar(100) NOT NULL,
  `record_id` int NOT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `log_type`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `created_at`) VALUES
(1, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-02 17:12:58'),
(2, 7, 'LOGIN', 'data_change', 'users', 7, NULL, '{\"email\": \"benhnhan1@gmail.com\"}', NULL, '2026-05-02 17:13:38'),
(3, 5, 'LOGIN', 'data_change', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', NULL, '2026-05-02 17:14:31'),
(4, 12, 'LOGIN', 'data_change', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', NULL, '2026-05-02 17:15:03'),
(5, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-02 17:54:52'),
(6, 2, 'LOGIN', 'data_change', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', NULL, '2026-05-02 17:56:32'),
(7, 1, 'DELETE', 'data_change', 'patients', 5, '{\"name\": \"Trịnh Văn Vũ\", \"email\": \"vu@gmail.com\"}', NULL, NULL, '2026-05-02 17:59:27'),
(8, 1, 'DELETE', 'data_change', 'patients', 5, '{\"name\": \"Trịnh Văn Vũ\"}', NULL, NULL, '2026-05-02 17:59:27'),
(9, 2, 'LOGIN', 'data_change', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', NULL, '2026-05-02 18:24:31'),
(10, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-02 18:25:26'),
(11, 2, 'LOGIN', 'data_change', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', NULL, '2026-05-02 18:25:46'),
(12, 12, 'LOGIN', 'data_change', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', NULL, '2026-05-02 18:26:19'),
(13, 16, 'LOGIN', 'data_change', 'users', 16, NULL, '{\"email\": \"nurse3@benhvien.com\"}', NULL, '2026-05-02 18:27:15'),
(14, 7, 'LOGIN', 'data_change', 'users', 7, NULL, '{\"email\": \"benhnhan1@gmail.com\"}', NULL, '2026-05-02 18:27:32'),
(15, 12, 'LOGIN', 'data_change', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', NULL, '2026-05-02 18:36:30'),
(16, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-02 18:36:44'),
(17, 12, 'LOGIN', 'data_change', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', NULL, '2026-05-02 18:46:37'),
(18, 2, 'LOGIN', 'data_change', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', NULL, '2026-05-07 14:28:26'),
(19, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-07 14:28:49'),
(20, 2, 'LOGIN', 'data_change', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', NULL, '2026-05-07 14:30:37'),
(21, 25, 'LOGIN', 'data_change', 'users', 25, NULL, '{\"email\": \"trinhvanvu2018v@gmail.com\"}', NULL, '2026-05-07 14:35:47'),
(22, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-07 14:37:00'),
(23, 25, 'LOGIN', 'data_change', 'users', 25, NULL, '{\"email\": \"trinhvanvu2018v@gmail.com\"}', NULL, '2026-05-07 14:37:45'),
(24, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-07 14:40:40'),
(25, 1, 'INSERT', 'data_change', 'patients', 14, NULL, '{\"name\": \"Nguyễn Văn Vũ\", \"email\": \"trinhvanvu2018v@gmail.com\"}', NULL, '2026-05-07 14:41:41'),
(26, 1, 'INSERT', 'data_change', 'patients', 14, NULL, '{\"name\": \"Nguyễn Văn Vũ\", \"email\": \"trinhvanvu2018v@gmail.com\"}', NULL, '2026-05-07 14:41:41'),
(27, 26, 'LOGIN', 'data_change', 'users', 26, NULL, '{\"email\": \"trinhvanvu2018v@gmail.com\"}', NULL, '2026-05-07 14:42:18'),
(28, 26, 'INSERT', 'data_change', 'appointments', 14, NULL, '{\"reason\": \"Kiểm tra trình trạng bênh\", \"doctor_id\": \"1\", \"patient_id\": \"14\", \"appointment_date\": \"2026-05-08T21:43\"}', NULL, '2026-05-07 14:43:55'),
(29, 26, 'INSERT', 'data_change', 'appointments', 14, NULL, '{\"date\": \"2026-05-08T21:43\", \"doctor_id\": \"1\"}', NULL, '2026-05-07 14:43:55'),
(30, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-07 14:44:06'),
(31, 26, 'LOGIN', 'data_change', 'users', 26, NULL, '{\"email\": \"trinhvanvu2018v@gmail.com\"}', NULL, '2026-05-07 14:47:41'),
(32, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-07 14:48:19'),
(33, 26, 'INSERT', 'data_change', 'appointments', 15, NULL, '{\"reason\": \"Bị đau bụng khó tiêu nhiều ngày liền\", \"doctor_id\": \"1\", \"patient_id\": \"14\", \"appointment_date\": \"2026-05-07T21:49\"}', NULL, '2026-05-07 14:49:52'),
(34, 26, 'INSERT', 'data_change', 'appointments', 15, NULL, '{\"date\": \"2026-05-07T21:49\", \"doctor_id\": \"1\"}', NULL, '2026-05-07 14:49:52'),
(35, 2, 'LOGIN', 'data_change', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', NULL, '2026-05-07 14:51:15'),
(36, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-07 14:55:33'),
(37, 12, 'LOGIN', 'data_change', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', NULL, '2026-05-07 14:55:48'),
(38, 2, 'LOGIN', 'data_change', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', NULL, '2026-05-07 14:57:44'),
(39, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-07 14:58:50'),
(40, 2, 'LOGIN', 'data_change', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', NULL, '2026-05-07 14:59:53'),
(41, 2, 'LOGIN', 'data_change', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', NULL, '2026-05-07 15:02:16'),
(42, 12, 'LOGIN', 'data_change', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', NULL, '2026-05-07 15:05:15'),
(43, 5, 'LOGIN', 'data_change', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', NULL, '2026-05-07 15:22:44'),
(44, 12, 'LOGIN', 'data_change', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', NULL, '2026-05-07 15:27:04'),
(45, 7, 'LOGIN', 'data_change', 'users', 7, NULL, '{\"email\": \"benhnhan1@gmail.com\"}', NULL, '2026-05-09 00:04:45'),
(46, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-09 00:19:01'),
(47, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-21 12:42:43'),
(48, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-21 12:42:43'),
(49, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-21 12:42:52'),
(50, 2, 'LOGIN', 'data_change', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', NULL, '2026-05-21 13:03:53'),
(51, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-21 13:04:23'),
(52, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-21 13:38:02'),
(53, 2, 'LOGIN', 'data_change', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', NULL, '2026-05-21 13:39:45'),
(54, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-24 15:20:57'),
(55, 1, 'LOGIN', 'data_change', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', NULL, '2026-05-24 15:29:33'),
(56, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-24 16:21:51'),
(57, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-24 16:52:42'),
(58, 14, 'LOGIN', 'auth', 'users', 14, NULL, '{\"email\": \"doctor4@benhvien.com\"}', '172.18.0.1', '2026-05-24 17:28:04'),
(59, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-24 17:28:24'),
(60, 7, 'LOGIN', 'auth', 'users', 7, NULL, '{\"email\": \"benhnhan1@gmail.com\"}', '172.18.0.1', '2026-05-24 17:49:36'),
(61, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-24 17:53:28'),
(62, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-25 08:40:37'),
(63, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-25 08:42:01'),
(64, 1, 'INSERT', 'data_change', 'invoices', 6, NULL, '{\"total\": \"37500\", \"patient_id\": \"1\"}', '127.0.0.1', '2026-05-25 09:27:17'),
(65, 1, 'UPDATE', 'data_change', 'prescriptions', 6, '{\"status\": \"draft\"}', '{\"status\": \"paid\"}', '127.0.0.1', '2026-05-25 09:27:17'),
(66, 1, 'UPDATE', 'data_change', 'invoices', 6, '{\"status\": \"pending\"}', '{\"method\": \"cash\", \"status\": \"paid\"}', '127.0.0.1', '2026-05-25 09:27:17'),
(67, 1, 'UPDATE', 'data_change', 'prescriptions', 6, '{\"status\": \"paid\"}', '{\"status\": \"dispensed\"}', '127.0.0.1', '2026-05-25 09:27:17'),
(68, 1, 'INSERT', 'data_change', 'invoices', 7, NULL, '{\"total\": \"37500\", \"patient_id\": \"1\"}', '127.0.0.1', '2026-05-25 09:27:39'),
(69, 1, 'UPDATE', 'data_change', 'prescriptions', 7, '{\"status\": \"draft\"}', '{\"status\": \"paid\"}', '127.0.0.1', '2026-05-25 09:27:39'),
(70, 1, 'UPDATE', 'data_change', 'invoices', 7, '{\"status\": \"pending\"}', '{\"method\": \"cash\", \"status\": \"paid\"}', '127.0.0.1', '2026-05-25 09:27:39'),
(71, 1, 'UPDATE', 'data_change', 'prescriptions', 7, '{\"status\": \"paid\"}', '{\"status\": \"dispensed\"}', '127.0.0.1', '2026-05-25 09:27:39'),
(72, 1, 'INSERT', 'data_change', 'invoices', 8, NULL, '{\"total\": \"37500\", \"patient_id\": \"1\"}', '127.0.0.1', '2026-05-25 09:27:52'),
(73, 1, 'UPDATE', 'data_change', 'prescriptions', 8, '{\"status\": \"draft\"}', '{\"status\": \"paid\"}', '127.0.0.1', '2026-05-25 09:27:53'),
(74, 1, 'UPDATE', 'data_change', 'invoices', 8, '{\"status\": \"pending\"}', '{\"method\": \"cash\", \"status\": \"paid\"}', '127.0.0.1', '2026-05-25 09:27:53'),
(75, 1, 'UPDATE', 'data_change', 'prescriptions', 8, '{\"status\": \"paid\"}', '{\"status\": \"dispensed\"}', '127.0.0.1', '2026-05-25 09:27:53'),
(76, 1, 'UPDATE', 'data_change', 'prescriptions', 9, '{\"status\": \"draft\"}', '{\"status\": \"cancelled\"}', '127.0.0.1', '2026-05-25 09:27:53'),
(77, 1, 'INSERT', 'data_change', 'invoices', 9, NULL, '{\"total\": \"37500\", \"patient_id\": \"1\"}', '127.0.0.1', '2026-05-25 09:29:12'),
(78, 1, 'UPDATE', 'data_change', 'prescriptions', 10, '{\"status\": \"draft\"}', '{\"status\": \"paid\"}', '127.0.0.1', '2026-05-25 09:29:12'),
(79, 1, 'UPDATE', 'data_change', 'invoices', 9, '{\"status\": \"pending\"}', '{\"method\": \"cash\", \"status\": \"paid\"}', '127.0.0.1', '2026-05-25 09:29:12'),
(80, 1, 'UPDATE', 'data_change', 'prescriptions', 10, '{\"status\": \"paid\"}', '{\"status\": \"dispensed\"}', '127.0.0.1', '2026-05-25 09:29:12'),
(81, 1, 'UPDATE', 'data_change', 'prescriptions', 11, '{\"status\": \"draft\"}', '{\"status\": \"cancelled\"}', '127.0.0.1', '2026-05-25 09:29:12'),
(82, 7, 'LOGIN', 'auth', 'users', 7, NULL, '{\"email\": \"benhnhan1@gmail.com\"}', '172.18.0.1', '2026-05-25 09:30:20'),
(83, 3, 'LOGIN', 'auth', 'users', 3, NULL, '{\"email\": \"doctor2@benhvien.com\"}', '172.18.0.1', '2026-05-25 09:30:55'),
(84, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-25 09:32:26'),
(85, 13, 'LOGIN', 'auth', 'users', 13, NULL, '{\"email\": \"duocsi@benhvien.com\"}', '172.18.0.1', '2026-05-25 10:00:26'),
(86, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-25 10:00:54'),
(87, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-25 10:01:34'),
(88, 13, 'LOGIN', 'auth', 'users', 13, NULL, '{\"email\": \"duocsi@benhvien.com\"}', '172.18.0.1', '2026-05-25 10:01:59'),
(89, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-25 10:02:32'),
(90, 12, 'INSERT', 'data_change', 'invoices', 10, NULL, '{\"total\": \"3500\", \"patient_id\": \"6\"}', '172.18.0.1', '2026-05-25 10:13:18'),
(91, 12, 'INSERT', 'data_change', 'invoices', 10, NULL, '{\"patient_id\": \"6\", \"final_amount\": 3500}', '172.18.0.1', '2026-05-25 10:13:18'),
(92, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-25 10:14:52'),
(93, 12, 'UPDATE', 'data_change', 'prescriptions', 12, '{\"status\": \"draft\"}', '{\"status\": \"paid\"}', '172.18.0.1', '2026-05-25 10:29:46'),
(94, 12, 'UPDATE', 'data_change', 'invoices', 10, '{\"status\": \"pending\"}', '{\"method\": \"cash\", \"status\": \"paid\"}', '172.18.0.1', '2026-05-25 10:29:46'),
(95, 12, 'UPDATE', 'data_change', 'invoices', 10, NULL, '{\"method\": \"cash\", \"status\": \"paid\"}', '172.18.0.1', '2026-05-25 10:29:46'),
(96, 13, 'LOGIN', 'auth', 'users', 13, NULL, '{\"email\": \"duocsi@benhvien.com\"}', '172.18.0.1', '2026-05-25 10:30:03'),
(97, 13, 'UPDATE', 'data_change', 'prescriptions', 12, '{\"status\": \"paid\"}', '{\"status\": \"dispensed\"}', '172.18.0.1', '2026-05-25 10:30:16'),
(98, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-25 10:30:54'),
(99, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-25 11:14:22'),
(100, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-25 11:18:48'),
(101, 13, 'LOGIN', 'auth', 'users', 13, NULL, '{\"email\": \"duocsi@benhvien.com\"}', '172.18.0.1', '2026-05-26 08:56:37'),
(102, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-26 08:57:40'),
(103, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-26 11:23:53'),
(104, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-27 14:32:03'),
(105, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-27 14:33:14'),
(106, 28, 'LOGIN', 'auth', 'users', 28, NULL, '{\"email\": \"giamdoc@benhvien.com\"}', '172.18.0.1', '2026-05-27 14:55:07'),
(107, 27, 'LOGIN', 'auth', 'users', 27, NULL, '{\"email\": \"ktv@benhvien.com\"}', '172.18.0.1', '2026-05-27 14:55:35'),
(108, 7, 'LOGIN', 'auth', 'users', 7, NULL, '{\"email\": \"benhnhan1@gmail.com\"}', '172.18.0.1', '2026-05-27 14:55:51'),
(109, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-27 14:56:24'),
(110, 13, 'LOGIN', 'auth', 'users', 13, NULL, '{\"email\": \"duocsi@benhvien.com\"}', '172.18.0.1', '2026-05-27 14:57:16'),
(111, 29, 'LOGIN', 'auth', 'users', 29, NULL, '{\"email\": \"thungan@benhvien.com\"}', '172.18.0.1', '2026-05-27 15:13:26'),
(112, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-27 15:23:40'),
(113, 1, 'UPDATE', 'data_change', 'doctors', 5, NULL, '{\"name\": \"Bác sĩ Đinh Thu Trà\"}', '172.18.0.1', '2026-05-27 15:29:29'),
(114, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-27 15:43:42'),
(115, 1, 'UPDATE', 'data_change', 'patients', 9, '{\"name\": \"Đặng Hoàng Yến\", \"email\": \"benhnhan8@gmail.com\"}', '{\"name\": \"Đặng Hoàng Yến\", \"email\": \"benhnhan8@gmail.com\"}', '172.18.0.1', '2026-05-27 15:58:28'),
(116, 1, 'UPDATE', 'data_change', 'patients', 9, NULL, '{\"name\": \"Đặng Hoàng Yến\", \"email\": \"benhnhan8@gmail.com\"}', '172.18.0.1', '2026-05-27 15:58:28'),
(117, 1, 'UPDATE', 'data_change', 'doctors', 5, NULL, '{\"name\": \"Bác sĩ Đinh Thu Trà\"}', '172.18.0.1', '2026-05-27 16:09:03'),
(118, 1, 'UPDATE', 'data_change', 'doctors', 5, NULL, '{\"name\": \"Bác sĩ Đinh Thu Trà\"}', '172.18.0.1', '2026-05-27 16:09:32'),
(119, 1, 'UPDATE', 'data_change', 'doctors', 5, NULL, '{\"name\": \"Bác sĩ Đinh Thu Trà\"}', '172.18.0.1', '2026-05-27 16:09:38'),
(120, 1, 'UPDATE', 'data_change', 'doctors', 5, NULL, '{\"name\": \"Bác sĩ Đinh Thu Trà\"}', '172.18.0.1', '2026-05-27 16:11:21'),
(121, 1, 'INSERT', 'data_change', 'doctors', 6, NULL, '{\"name\": \"Phan Tấn Trung\", \"email\": \"phantantrung@gmail.com\"}', '172.18.0.1', '2026-05-27 16:12:40'),
(122, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-27 16:21:26'),
(123, 27, 'LOGIN', 'auth', 'users', 27, NULL, '{\"email\": \"ktv@benhvien.com\"}', '172.18.0.1', '2026-05-27 16:24:54'),
(124, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-27 16:25:53'),
(125, 2, 'INSERT', 'data_change', 'lab_orders', 1, NULL, '{\"test_name\": \"Xét nghiệm công thức máu\", \"patient_id\": \"9\"}', '172.18.0.1', '2026-05-27 16:28:01'),
(126, 27, 'LOGIN', 'auth', 'users', 27, NULL, '{\"email\": \"ktv@benhvien.com\"}', '172.18.0.1', '2026-05-27 16:28:23'),
(127, 27, 'UPDATE', 'data_change', 'lab_orders', 1, NULL, '{\"status\": \"in_progress\"}', '172.18.0.1', '2026-05-27 16:28:28'),
(128, 27, 'UPDATE', 'data_change', 'lab_orders', 1, NULL, '{\"status\": \"completed\"}', '172.18.0.1', '2026-05-27 16:33:16'),
(129, 27, 'INSERT', 'data_change', 'lab_results', 1, NULL, '{\"lab_order_id\": \"1\"}', '172.18.0.1', '2026-05-27 16:33:16'),
(130, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-27 16:33:29'),
(131, 5, 'LOGIN', 'auth', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', '172.18.0.1', '2026-05-27 16:34:57'),
(132, 13, 'LOGIN', 'auth', 'users', 13, NULL, '{\"email\": \"duocsi@benhvien.com\"}', '172.18.0.1', '2026-05-27 16:36:48'),
(133, 5, 'LOGIN', 'auth', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', '172.18.0.1', '2026-05-27 16:37:16'),
(134, 5, 'UPDATE', 'data_change', 'admissions', 2, '{\"status\": \"active\"}', '{\"status\": \"discharged\"}', '172.18.0.1', '2026-05-27 16:39:30'),
(135, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-27 17:04:10'),
(136, 28, 'LOGIN', 'auth', 'users', 28, NULL, '{\"email\": \"giamdoc@benhvien.com\"}', '172.18.0.1', '2026-05-27 17:13:56'),
(137, 29, 'LOGIN', 'auth', 'users', 29, NULL, '{\"email\": \"thungan@benhvien.com\"}', '172.18.0.1', '2026-05-27 17:14:06'),
(138, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-27 17:14:17'),
(139, 1, 'UPDATE', 'data_change', 'medical_devices', 2, NULL, '{\"status\": \"in_use\"}', '172.18.0.1', '2026-05-27 17:19:25'),
(140, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-27 17:31:09'),
(141, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-27 17:36:59'),
(142, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-27 17:41:48'),
(143, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-27 17:42:07'),
(144, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-27 17:57:15'),
(145, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-28 03:34:03'),
(146, 29, 'LOGIN', 'auth', 'users', 29, NULL, '{\"email\": \"thungan@benhvien.com\"}', '172.18.0.1', '2026-05-28 03:35:50'),
(147, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-28 03:36:40'),
(148, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-28 03:38:19'),
(149, 28, 'LOGIN', 'auth', 'users', 28, NULL, '{\"email\": \"giamdoc@benhvien.com\"}', '172.18.0.1', '2026-05-28 03:39:58'),
(150, 29, 'LOGIN', 'auth', 'users', 29, NULL, '{\"email\": \"thungan@benhvien.com\"}', '172.18.0.1', '2026-05-28 03:43:57'),
(151, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-28 03:47:51'),
(152, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-28 03:47:51'),
(153, 29, 'LOGIN', 'auth', 'users', 29, NULL, '{\"email\": \"thungan@benhvien.com\"}', '172.18.0.1', '2026-05-28 03:51:27'),
(154, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-28 06:48:53'),
(155, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-28 06:54:13'),
(156, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-28 06:55:24'),
(157, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-28 06:55:24'),
(158, 5, 'LOGIN', 'auth', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', '172.18.0.1', '2026-05-28 07:04:12'),
(159, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-28 07:05:51'),
(160, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-28 07:15:27'),
(161, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-28 07:16:48'),
(162, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-28 08:04:09'),
(163, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-28 08:04:09'),
(164, 29, 'LOGIN', 'auth', 'users', 29, NULL, '{\"email\": \"thungan@benhvien.com\"}', '172.18.0.1', '2026-05-28 08:27:19'),
(165, 1, 'INSERT', 'data_change', 'invoices', 11, NULL, '{\"total\": \"500000\", \"patient_id\": \"1\"}', '127.0.0.1', '2026-05-28 08:27:51'),
(166, NULL, 'UPDATE', 'data_change', 'prescriptions', 14, '{\"status\": \"paid\"}', '{\"notes\": \"Take with warm water\", \"status\": \"approved\"}', '127.0.0.1', '2026-05-28 08:33:02'),
(167, NULL, 'UPDATE', 'data_change', 'prescriptions', 14, '{\"status\": \"approved\"}', '{\"status\": \"dispensed\"}', '127.0.0.1', '2026-05-28 08:33:02'),
(168, NULL, 'UPDATE', 'data_change', 'prescriptions', 15, '{\"status\": \"paid\"}', '{\"notes\": \"Cancel test\", \"status\": \"approved\"}', '127.0.0.1', '2026-05-28 08:33:02'),
(169, NULL, 'UPDATE', 'data_change', 'prescriptions', 15, '{\"status\": \"approved\"}', '{\"status\": \"cancelled\"}', '127.0.0.1', '2026-05-28 08:33:02'),
(170, 13, 'LOGIN', 'auth', 'users', 13, NULL, '{\"email\": \"duocsi@benhvien.com\"}', '172.18.0.1', '2026-05-28 08:34:38'),
(171, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-28 08:44:03'),
(172, 12, 'INSERT', 'data_change', 'queue_tickets', 1, NULL, '{\"patient_id\": \"9\", \"department_id\": \"6\", \"ticket_number\": \"1\"}', '172.18.0.1', '2026-05-28 08:45:12'),
(173, 12, 'INSERT', 'data_change', 'queue_tickets', 2, NULL, '{\"patient_id\": \"9\", \"department_id\": \"6\", \"ticket_number\": \"2\"}', '172.18.0.1', '2026-05-28 08:45:27'),
(174, 12, 'UPDATE', 'data_change', 'queue_tickets', 2, NULL, '{\"status\": \"cancelled\"}', '172.18.0.1', '2026-05-28 08:45:41'),
(175, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-28 08:46:05'),
(176, 2, 'UPDATE', 'data_change', 'queue_tickets', 1, NULL, '{\"status\": \"called\"}', '172.18.0.1', '2026-05-28 08:49:31'),
(177, 2, 'UPDATE', 'data_change', 'queue_tickets', 1, NULL, '{\"status\": \"in_progress\"}', '172.18.0.1', '2026-05-28 08:49:39'),
(178, 2, 'UPDATE', 'data_change', 'queue_tickets', 1, NULL, '{\"status\": \"completed\"}', '172.18.0.1', '2026-05-28 08:49:43'),
(179, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-28 09:00:33'),
(180, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-28 09:10:37'),
(181, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-28 09:11:10'),
(182, 12, 'INSERT', 'data_change', 'patients', 15, NULL, '{\"name\": \"Nguyễn Chí Thuận\", \"email\": \"thuanngu@gmail.com\"}', '172.18.0.1', '2026-05-28 09:13:24'),
(183, 12, 'INSERT', 'data_change', 'patients', 15, NULL, '{\"name\": \"Nguyễn Chí Thuận\", \"email\": \"thuanngu@gmail.com\"}', '172.18.0.1', '2026-05-28 09:13:24'),
(184, 12, 'INSERT', 'data_change', 'queue_tickets', 3, NULL, '{\"patient_id\": \"15\", \"department_id\": \"6\", \"ticket_number\": \"3\"}', '172.18.0.1', '2026-05-28 09:13:53'),
(185, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-28 09:14:26'),
(186, 12, 'UPDATE', 'data_change', 'queue_tickets', 3, NULL, '{\"status\": \"cancelled\"}', '172.18.0.1', '2026-05-28 09:15:03'),
(187, 12, 'INSERT', 'data_change', 'queue_tickets', 4, NULL, '{\"patient_id\": \"15\", \"department_id\": \"5\", \"ticket_number\": \"4\"}', '172.18.0.1', '2026-05-28 09:15:17'),
(188, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-28 09:18:38'),
(189, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-28 09:18:57'),
(190, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-28 09:19:55'),
(191, 12, 'UPDATE', 'data_change', 'queue_tickets', 4, NULL, '{\"status\": \"cancelled\"}', '172.18.0.1', '2026-05-28 09:20:08'),
(192, 12, 'INSERT', 'data_change', 'queue_tickets', 5, NULL, '{\"patient_id\": \"15\", \"department_id\": \"3\", \"ticket_number\": \"5\"}', '172.18.0.1', '2026-05-28 09:20:22'),
(193, 2, 'UPDATE', 'data_change', 'queue_tickets', 5, NULL, '{\"status\": \"called\"}', '172.18.0.1', '2026-05-28 09:20:29'),
(194, 2, 'UPDATE', 'data_change', 'queue_tickets', 5, NULL, '{\"status\": \"in_progress\"}', '172.18.0.1', '2026-05-28 09:20:32'),
(195, 2, 'UPDATE', 'data_change', 'queue_tickets', 5, NULL, '{\"status\": \"completed\"}', '172.18.0.1', '2026-05-28 09:20:35'),
(196, 12, 'INSERT', 'data_change', 'patients', 16, NULL, '{\"name\": \"Nguyễn Chí Thuận 1\", \"email\": \"thuangu1@gmail.com\"}', '172.18.0.1', '2026-05-28 09:24:24'),
(197, 12, 'INSERT', 'data_change', 'patients', 16, NULL, '{\"name\": \"Nguyễn Chí Thuận 1\", \"email\": \"thuangu1@gmail.com\"}', '172.18.0.1', '2026-05-28 09:24:24'),
(198, 12, 'INSERT', 'data_change', 'queue_tickets', 6, NULL, '{\"patient_id\": \"16\", \"department_id\": \"3\", \"ticket_number\": \"6\"}', '172.18.0.1', '2026-05-28 09:24:45'),
(199, 2, 'UPDATE', 'data_change', 'queue_tickets', 6, NULL, '{\"status\": \"called\"}', '172.18.0.1', '2026-05-28 09:25:19'),
(200, 2, 'UPDATE', 'data_change', 'queue_tickets', 6, NULL, '{\"status\": \"in_progress\"}', '172.18.0.1', '2026-05-28 09:25:21'),
(201, 2, 'UPDATE', 'data_change', 'queue_tickets', 6, NULL, '{\"status\": \"completed\"}', '172.18.0.1', '2026-05-28 09:25:22'),
(202, 5, 'LOGIN', 'auth', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', '172.18.0.1', '2026-05-28 09:27:18'),
(203, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-28 09:29:35'),
(204, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-29 04:19:03'),
(205, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 04:51:38'),
(206, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-29 05:08:19'),
(207, 7, 'LOGIN', 'auth', 'users', 7, NULL, '{\"email\": \"benhnhan1@gmail.com\"}', '172.18.0.1', '2026-05-29 05:09:27'),
(208, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 05:13:14'),
(209, 7, 'LOGIN', 'auth', 'users', 7, NULL, '{\"email\": \"benhnhan1@gmail.com\"}', '172.18.0.1', '2026-05-29 05:23:51'),
(210, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-29 05:35:14'),
(211, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 05:49:36'),
(212, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-29 06:06:25'),
(213, 27, 'LOGIN', 'auth', 'users', 27, NULL, '{\"email\": \"ktv@benhvien.com\"}', '172.18.0.1', '2026-05-29 06:14:21'),
(214, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 06:23:13'),
(215, 3, 'LOGIN', 'auth', 'users', 3, NULL, '{\"email\": \"doctor2@benhvien.com\"}', '172.18.0.1', '2026-05-29 06:34:25'),
(216, 3, 'LOGIN', 'auth', 'users', 3, NULL, '{\"email\": \"doctor2@benhvien.com\"}', '172.18.0.1', '2026-05-29 06:34:42'),
(217, 5, 'LOGIN', 'auth', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', '172.18.0.1', '2026-05-29 06:37:06'),
(218, 13, 'LOGIN', 'auth', 'users', 13, NULL, '{\"email\": \"duocsi@benhvien.com\"}', '172.18.0.1', '2026-05-29 06:38:34'),
(219, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-29 06:39:57'),
(220, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 06:41:09'),
(221, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 07:25:19'),
(222, 2, 'INSERT', 'data_change', 'medical_records', 10, NULL, '{\"diagnosis\": \"Tăng huyết áp vô căn giai đoạn 2. Triệu chứng: Đau đầu nhẹ, chóng mặt, nặng ngực khi gắng sức. Huyết áp phòng khám: 155/95 mmHg\", \"treatment\": \"Amlodipin 5mg: 01 viên/ngày (uống sáng).\\r\\n\\r\\nLosartan 50mg: 01 viên/ngày (uống sáng).\\r\\n\\r\\nChỉ định: Đo điện tâm đồ (ECG), siêu âm tim.\", \"icd10_code\": null}', '172.18.0.1', '2026-05-29 07:57:22'),
(223, 29, 'LOGIN', 'auth', 'users', 29, NULL, '{\"email\": \"thungan@benhvien.com\"}', '172.18.0.1', '2026-05-29 08:01:09'),
(224, 17, 'LOGIN', 'auth', 'users', 17, NULL, '{\"email\": \"benhnhan5@gmail.com\"}', '172.18.0.1', '2026-05-29 08:16:15'),
(225, 29, 'LOGIN', 'auth', 'users', 29, NULL, '{\"email\": \"thungan@benhvien.com\"}', '172.18.0.1', '2026-05-29 08:27:15'),
(226, 29, 'INSERT', 'data_change', 'invoices', 12, NULL, '{\"total\": \"5000\", \"patient_id\": \"6\"}', '172.18.0.1', '2026-05-29 08:31:21'),
(227, 29, 'INSERT', 'data_change', 'invoices', 12, NULL, '{\"patient_id\": \"6\", \"final_amount\": 5000}', '172.18.0.1', '2026-05-29 08:31:21'),
(228, 29, 'UPDATE', 'data_change', 'prescriptions', 16, '{\"status\": \"draft\"}', '{\"status\": \"paid\"}', '172.18.0.1', '2026-05-29 08:31:44'),
(229, 29, 'UPDATE', 'data_change', 'invoices', 12, '{\"status\": \"pending\"}', '{\"method\": \"vnpay\", \"status\": \"paid\"}', '172.18.0.1', '2026-05-29 08:31:45'),
(230, 29, 'UPDATE', 'data_change', 'invoices', 12, NULL, '{\"method\": \"vnpay\", \"status\": \"paid\"}', '172.18.0.1', '2026-05-29 08:31:45'),
(231, 13, 'LOGIN', 'auth', 'users', 13, NULL, '{\"email\": \"duocsi@benhvien.com\"}', '172.18.0.1', '2026-05-29 08:32:24'),
(232, 13, 'UPDATE', 'data_change', 'prescriptions', 16, '{\"status\": \"paid\"}', '{\"notes\": \"\", \"status\": \"approved\"}', '172.18.0.1', '2026-05-29 08:33:27'),
(233, 13, 'UPDATE', 'data_change', 'prescriptions', 16, '{\"status\": \"approved\"}', '{\"status\": \"dispensed\"}', '172.18.0.1', '2026-05-29 08:33:36'),
(234, 7, 'LOGIN', 'auth', 'users', 7, NULL, '{\"email\": \"benhnhan1@gmail.com\"}', '172.18.0.1', '2026-05-29 08:34:37'),
(235, 17, 'LOGIN', 'auth', 'users', 17, NULL, '{\"email\": \"benhnhan5@gmail.com\"}', '172.18.0.1', '2026-05-29 08:34:48'),
(236, 29, 'LOGIN', 'auth', 'users', 29, NULL, '{\"email\": \"thungan@benhvien.com\"}', '172.18.0.1', '2026-05-29 08:38:03'),
(237, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 08:40:28'),
(238, 2, 'INSERT', 'data_change', 'medical_records', 11, NULL, '{\"diagnosis\": \"Viêm phế quản cấp cơ địa dị ứng. Triệu chứng: Ho khạc đờm trắng đục, sốt nhẹ về chiều (38°C), rát họng, mệt mỏi. Phổi thô, có ít rale ẩm rải rác.\", \"treatment\": \"Glucophage 850mg (Metformin): 02 viên/ngày (chia 2 lần, uống ngay sau ăn sáng/tối).\\r\\nDiamicron MR 60mg: 01 viên/ngày (uống trước ăn sáng).\\r\\nChỉ định: Xét nghiệm bộ mỡ máu, HbA1c định kỳ.\", \"icd10_code\": null}', '172.18.0.1', '2026-05-29 08:41:46'),
(239, 29, 'LOGIN', 'auth', 'users', 29, NULL, '{\"email\": \"thungan@benhvien.com\"}', '172.18.0.1', '2026-05-29 08:42:59'),
(240, 29, 'INSERT', 'data_change', 'invoices', 13, NULL, '{\"total\": \"4500\", \"patient_id\": \"6\"}', '172.18.0.1', '2026-05-29 08:43:32'),
(241, 29, 'INSERT', 'data_change', 'invoices', 13, NULL, '{\"patient_id\": \"6\", \"final_amount\": 4500}', '172.18.0.1', '2026-05-29 08:43:32'),
(242, 29, 'UPDATE', 'data_change', 'prescriptions', 17, '{\"status\": \"draft\"}', '{\"status\": \"paid\"}', '172.18.0.1', '2026-05-29 08:44:05'),
(243, 29, 'UPDATE', 'data_change', 'invoices', 13, '{\"status\": \"pending\"}', '{\"method\": \"momo\", \"status\": \"paid\"}', '172.18.0.1', '2026-05-29 08:44:05'),
(244, 29, 'UPDATE', 'data_change', 'invoices', 13, NULL, '{\"method\": \"momo\", \"status\": \"paid\"}', '172.18.0.1', '2026-05-29 08:44:05'),
(245, 13, 'LOGIN', 'auth', 'users', 13, NULL, '{\"email\": \"duocsi@benhvien.com\"}', '172.18.0.1', '2026-05-29 08:45:04'),
(246, 13, 'UPDATE', 'data_change', 'prescriptions', 17, '{\"status\": \"paid\"}', '{\"notes\": \"\", \"status\": \"approved\"}', '172.18.0.1', '2026-05-29 08:46:59'),
(247, 13, 'UPDATE', 'data_change', 'prescriptions', 17, '{\"status\": \"approved\"}', '{\"status\": \"dispensed\"}', '172.18.0.1', '2026-05-29 08:47:12'),
(248, 9, 'UPDATE', 'data_change', 'prescriptions', 18, '{\"status\": \"paid\"}', '{\"status\": \"dispensed\"}', '127.0.0.1', '2026-05-29 08:56:24'),
(249, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 08:58:18'),
(250, 2, 'INSERT', 'data_change', 'medical_records', 12, NULL, '{\"diagnosis\": \"Viêm phế quản cấp cơ địa dị ứng.\", \"treatment\": \"Augmentin 1g: 02 viên/ngày (chia 2 lần sáng/tối, sau ăn).\", \"icd10_code\": null}', '172.18.0.1', '2026-05-29 08:58:58'),
(251, 13, 'LOGIN', 'auth', 'users', 13, NULL, '{\"email\": \"duocsi@benhvien.com\"}', '172.18.0.1', '2026-05-29 08:59:44'),
(252, 5, 'LOGIN', 'auth', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', '172.18.0.1', '2026-05-29 09:14:12'),
(253, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 09:20:16'),
(254, 1, 'INSERT', 'data_change', 'admissions', 4, NULL, '{\"bed_id\": null, \"patient_id\": \"7\"}', '127.0.0.1', '2026-05-29 09:36:40'),
(255, 1, 'UPDATE', 'data_change', 'admissions', 4, '{\"status\": \"pending\"}', '{\"bed_id\": \"2\", \"status\": \"active\"}', '127.0.0.1', '2026-05-29 09:36:40'),
(256, 1, 'DELETE', 'data_change', 'admissions', 4, '{\"diagnosis\": \"TEST DIAGNOSIS - PENDING FLOW\"}', NULL, '127.0.0.1', '2026-05-29 09:36:40'),
(257, 5, 'LOGIN', 'auth', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', '172.18.0.1', '2026-05-29 09:39:45'),
(258, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 09:40:43'),
(259, 2, 'INSERT', 'data_change', 'admissions', 5, NULL, '{\"bed_id\": null, \"patient_id\": \"6\"}', '172.18.0.1', '2026-05-29 09:43:01'),
(260, 5, 'UPDATE', 'data_change', 'admissions', 5, '{\"status\": \"pending\"}', '{\"bed_id\": \"2\", \"status\": \"active\"}', '172.18.0.1', '2026-05-29 09:43:55'),
(261, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 10:14:28'),
(262, 1, 'LOGIN', 'auth', 'users', 1, NULL, '{\"email\": \"admin@benhvien.com\"}', '172.18.0.1', '2026-05-29 10:51:35'),
(263, 14, 'LOGIN', 'auth', 'users', 14, NULL, '{\"email\": \"doctor4@benhvien.com\"}', '172.18.0.1', '2026-05-29 10:52:19'),
(264, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 10:54:53'),
(265, 5, 'LOGIN', 'auth', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', '172.18.0.1', '2026-05-29 11:11:30'),
(266, 14, 'LOGIN', 'auth', 'users', 14, NULL, '{\"email\": \"doctor4@benhvien.com\"}', '172.18.0.1', '2026-05-29 11:11:49'),
(267, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 11:12:34'),
(268, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 15:03:14'),
(269, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 15:31:38'),
(270, 2, 'INSERT', 'data_change', 'lab_orders', 2, NULL, '{\"test_name\": \"Xét nghiệm công thức máu\", \"patient_id\": \"6\"}', '172.18.0.1', '2026-05-29 15:45:25'),
(271, 2, 'UPDATE', 'data_change', 'lab_orders', 2, NULL, '{\"status\": \"cancelled\"}', '172.18.0.1', '2026-05-29 15:59:17'),
(272, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-29 18:24:25'),
(273, 12, 'INSERT', 'data_change', 'queue_tickets', 7, NULL, '{\"patient_id\": \"6\", \"department_id\": \"3\", \"ticket_number\": \"1\"}', '172.18.0.1', '2026-05-29 18:24:44'),
(274, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 18:24:53'),
(275, 2, 'UPDATE', 'data_change', 'queue_tickets', 7, NULL, '{\"status\": \"called\"}', '172.18.0.1', '2026-05-29 18:24:57'),
(276, 2, 'UPDATE', 'data_change', 'queue_tickets', 7, NULL, '{\"status\": \"in_progress\"}', '172.18.0.1', '2026-05-29 18:25:16'),
(277, 2, 'UPDATE', 'data_change', 'queue_tickets', 7, NULL, '{\"status\": \"completed\"}', '172.18.0.1', '2026-05-29 18:25:43'),
(278, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-29 18:50:49'),
(279, 12, 'INSERT', 'data_change', 'queue_tickets', 8, NULL, '{\"patient_id\": \"6\", \"department_id\": \"3\", \"ticket_number\": \"2\"}', '172.18.0.1', '2026-05-29 18:51:00'),
(280, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-29 18:51:09'),
(281, 2, 'UPDATE', 'data_change', 'queue_tickets', 8, NULL, '{\"status\": \"called\"}', '172.18.0.1', '2026-05-29 18:51:17'),
(282, 2, 'UPDATE', 'data_change', 'queue_tickets', 8, NULL, '{\"status\": \"in_progress\"}', '172.18.0.1', '2026-05-29 18:51:19'),
(283, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-30 11:25:28'),
(284, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-30 11:25:28'),
(285, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-30 11:25:53'),
(286, 12, 'INSERT', 'data_change', 'queue_tickets', 9, NULL, '{\"patient_id\": \"6\", \"department_id\": \"3\", \"ticket_number\": \"1\"}', '172.18.0.1', '2026-05-30 11:26:11'),
(287, 2, 'UPDATE', 'data_change', 'queue_tickets', 9, NULL, '{\"status\": \"called\"}', '172.18.0.1', '2026-05-30 11:26:21'),
(288, 2, 'UPDATE', 'data_change', 'queue_tickets', 9, NULL, '{\"status\": \"in_progress\"}', '172.18.0.1', '2026-05-30 11:52:21'),
(289, 2, 'UPDATE', 'data_change', 'queue_tickets', 9, NULL, '{\"status\": \"completed\"}', '172.18.0.1', '2026-05-30 11:52:40'),
(290, 7, 'LOGIN', 'auth', 'users', 7, NULL, '{\"email\": \"benhnhan1@gmail.com\"}', '172.18.0.1', '2026-05-30 12:10:21'),
(291, 10, 'LOGIN', 'auth', 'users', 10, NULL, '{\"email\": \"benhnhan4@gmail.com\"}', '172.18.0.1', '2026-05-30 12:10:39'),
(292, 5, 'LOGIN', 'auth', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', '172.18.0.1', '2026-05-30 12:13:53'),
(293, 7, 'LOGIN', 'auth', 'users', 7, NULL, '{\"email\": \"benhnhan1@gmail.com\"}', '172.18.0.1', '2026-05-30 12:14:02'),
(294, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-30 12:34:36'),
(295, 5, 'LOGIN', 'auth', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', '172.18.0.1', '2026-05-30 17:01:32'),
(296, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-30 19:51:10'),
(297, 3, 'LOGIN', 'auth', 'users', 3, NULL, '{\"email\": \"doctor2@benhvien.com\"}', '172.18.0.1', '2026-05-30 20:20:02'),
(298, 3, 'LOGIN', 'auth', 'users', 3, NULL, '{\"email\": \"doctor2@benhvien.com\"}', '172.18.0.1', '2026-05-30 20:20:34'),
(299, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-30 20:20:50'),
(300, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-31 03:08:57'),
(301, 5, 'LOGIN', 'auth', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', '172.18.0.1', '2026-05-31 03:38:31'),
(302, 2, 'LOGIN', 'auth', 'users', 2, NULL, '{\"email\": \"doctor1@benhvien.com\"}', '172.18.0.1', '2026-05-31 03:39:17'),
(303, 2, 'INSERT', 'data_change', 'admissions', 6, NULL, '{\"bed_id\": null, \"patient_id\": \"6\"}', '172.18.0.1', '2026-05-31 03:39:48'),
(304, 5, 'LOGIN', 'auth', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', '172.18.0.1', '2026-05-31 03:39:59'),
(305, 5, 'UPDATE', 'data_change', 'admissions', 6, '{\"status\": \"pending\"}', '{\"bed_id\": \"3\", \"status\": \"active\"}', '172.18.0.1', '2026-05-31 03:40:26'),
(306, 5, 'UPDATE', 'data_change', 'admissions', 6, '{\"status\": \"active\"}', '{\"status\": \"discharged\"}', '172.18.0.1', '2026-05-31 03:42:56'),
(307, 12, 'LOGIN', 'auth', 'users', 12, NULL, '{\"email\": \"letan@benhvien.com\"}', '172.18.0.1', '2026-05-31 03:45:20'),
(308, 5, 'LOGIN', 'auth', 'users', 5, NULL, '{\"email\": \"nurse1@benhvien.com\"}', '172.18.0.1', '2026-05-31 03:52:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `beds`
--

CREATE TABLE `beds` (
  `id` int NOT NULL,
  `room_id` int DEFAULT NULL,
  `bed_number` varchar(20) DEFAULT NULL,
  `status` enum('available','occupied','maintenance') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `beds`
--

INSERT INTO `beds` (`id`, `room_id`, `bed_number`, `status`) VALUES
(1, 1, 'G1', 'occupied'),
(2, 1, 'G2', 'occupied'),
(3, 2, 'G1', 'available'),
(4, 2, 'G2', 'available'),
(5, 3, 'G1', 'available'),
(6, 3, 'G2', 'available'),
(7, 4, 'G1', 'available'),
(8, 4, 'G2', 'available'),
(9, 5, 'G1', 'available'),
(10, 5, 'G2', 'available'),
(11, 5, 'G3', 'available');

--
-- Bẫy `beds`
--
DELIMITER $$
CREATE TRIGGER `trg_bed_after_update` AFTER UPDATE ON `beds` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `departments`
--

CREATE TABLE `departments` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`) VALUES
(1, 'Tim mạch', 'Chẩn đoán và điều trị bệnh tim'),
(2, 'Thần kinh', 'Điều trị bệnh liên quan đến não và hệ thần kinh'),
(3, 'Nhi khoa', 'Khám và điều trị cho trẻ em'),
(4, 'Nội tổng quát', 'Khám và điều trị bệnh tổng quát'),
(5, 'Lâm Sàng', 'Khoa Khám Bệnh là cửa ngõ đón tiếp và chăm sóc sức khỏe ban đầu cho mọi khách hàng khi đến với bệnh viện. Với quy trình tiếp đón tối ưu, ứng dụng công nghệ hiện đại trong đặt lịch và trả kết quả, khoa cam kết giảm thiểu tối đa thời gian chờ đợi của người bệnh. Tại đây quy tụ đội ngũ bác sĩ nội, ngoại và chuyên khoa có trình độ chuyên môn cao, giàu kinh nghiệm, trực tiếp thăm khám và tư vấn phác đồ điều trị chính xác. Hệ thống phòng khám khang trang, sạch sẽ cùng sự chỉ dẫn tận tình của đội ngũ điều dưỡng mang lại cảm giác an tâm, thoải mái cho người bệnh ngay từ những bước chân đầu tiên.'),
(6, 'Khoa Cận Lâm Sàng', 'là khối các khoa phòng thực hiện các kỹ thuật, thủ thuật hiện đại nhằm cung cấp bằng chứng y khoa để hỗ trợ bác sĩ lâm sàng chẩn đoán, theo dõi và điều trị bệnh lý chuẩn xác nhất.');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `doctors`
--

CREATE TABLE `doctors` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `specialty` varchar(100) DEFAULT NULL,
  `experience_years` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_head` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `department_id`, `specialty`, `experience_years`, `deleted_at`, `is_head`) VALUES
(1, 2, 3, 'Chuyên khoa tim mạch', 10, NULL, 1),
(2, 3, 6, 'Chuyên khoa thần kinh', 8, NULL, 1),
(3, 4, 4, 'Bác sĩ đa khoa', 5, NULL, 0),
(4, 14, 3, 'Chuyên khoa nhi', 7, NULL, 0),
(5, 15, 6, 'Phẫu thuật tim mạch', 12, NULL, 0),
(6, 30, 6, 'chuyên khoa thực hiện xét nghiệm,', 3, NULL, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `doctor_departments`
--

CREATE TABLE `doctor_departments` (
  `id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `department_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `doctor_departments`
--

INSERT INTO `doctor_departments` (`id`, `doctor_id`, `department_id`) VALUES
(1, 1, 3),
(4, 2, 6),
(3, 3, 4),
(2, 4, 3),
(15, 5, 6),
(16, 6, 6);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `doctor_shifts`
--

CREATE TABLE `doctor_shifts` (
  `id` int NOT NULL,
  `doctor_id` int DEFAULT NULL,
  `shift_id` int DEFAULT NULL,
  `status` enum('pending','approved','rejected','assigned') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `doctor_shifts`
--

INSERT INTO `doctor_shifts` (`id`, `doctor_id`, `shift_id`, `status`) VALUES
(1, 1, 1, 'pending'),
(3, 2, 3, 'pending'),
(4, 3, 4, 'pending'),
(7, 1, 2, 'pending'),
(9, 4, 7, 'approved'),
(10, 4, 8, 'approved'),
(11, 4, 9, 'approved'),
(12, 4, 22, 'approved');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `equipment`
--

CREATE TABLE `equipment` (
  `id` int NOT NULL,
  `equipment_name` varchar(150) NOT NULL,
  `quantity` int DEFAULT '0',
  `status` enum('available','maintenance') DEFAULT 'available',
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `equipment`
--

INSERT INTO `equipment` (`id`, `equipment_name`, `quantity`, `status`, `description`) VALUES
(1, 'Máy theo dõi bệnh nhân', 10, 'available', 'Theo dõi nhịp tim, huyết áp, SpO2'),
(2, 'Máy thở', 5, 'maintenance', 'Hỗ trợ hô hấp cấp cứu'),
(3, 'Xe đẩy cấp cứu', 8, 'available', 'Trang bị đầy đủ thuốc và dụng cụ cấp cứu'),
(4, 'Bơm tiêm điện', 15, 'available', 'Truyền thuốc tự động theo liều lượng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `examination_rooms`
--

CREATE TABLE `examination_rooms` (
  `id` int NOT NULL,
  `room_name` varchar(50) NOT NULL,
  `department_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `capacity` int DEFAULT '1',
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `notes` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `examination_rooms`
--

INSERT INTO `examination_rooms` (`id`, `room_name`, `department_id`, `doctor_id`, `capacity`, `status`, `notes`) VALUES
(1, 'Phòng 1', 1, 1, 1, 'active', 'Phòng khám Tim mạch'),
(2, 'Phòng 2', 2, 2, 1, 'active', 'Phòng khám Thần kinh'),
(3, 'Phòng 3', 4, 3, 1, 'active', 'Phòng khám Nội tổng quát'),
(4, 'Phòng 4', 3, 4, 1, 'active', 'Phòng khám Nhi khoa'),
(5, 'Phòng 5', 1, 5, 1, 'active', 'Phòng khám Tim mạch 2');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `icd10_codes`
--

CREATE TABLE `icd10_codes` (
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `icd10_codes`
--

INSERT INTO `icd10_codes` (`code`, `name`, `name_en`, `category`) VALUES
('A09', 'Tiêu chảy và viêm dạ dày ruột do nhiễm trùng', 'Diarrhoea and gastroenteritis of infectious origin', 'Bệnh đường ruột'),
('A37', 'Ho gà', 'Whooping cough', 'Nhiễm trùng hô hấp'),
('B01', 'Thủy đậu', 'Varicella (chickenpox)', 'Nhiễm virus'),
('B18', 'Viêm gan virus mạn tính', 'Chronic viral hepatitis', 'Bệnh gan'),
('E03', 'Suy giáp khác', 'Other hypothyroidism', 'Nội tiết'),
('E11', 'Đái tháo đường không phụ thuộc insulin (Type 2)', 'Type 2 diabetes mellitus', 'Nội tiết'),
('E66', 'Béo phì', 'Obesity', 'Nội tiết'),
('F32', 'Giai đoạn trầm cảm', 'Depressive episode', 'Tâm thần'),
('G43', 'Đau nửa đầu (Migraine)', 'Migraine', 'Thần kinh'),
('G47', 'Rối loạn giấc ngủ (Mất ngủ)', 'Sleep disorders', 'Thần kinh'),
('H10', 'Viêm kết mạc (Đau mắt đỏ)', 'Conjunctivitis', 'Mắt'),
('I10', 'Tăng huyết áp vô căn (nguyên phát)', 'Essential (primary) hypertension', 'Tim mạch'),
('I20', 'Cơn đau thắt ngực', 'Angina pectoris', 'Tim mạch'),
('I25', 'Bệnh tim thiếu máu cục bộ mạn tính', 'Chronic ischemic heart disease', 'Tim mạch'),
('I63', 'Nhồi máu não', 'Cerebral infarction', 'Tim mạch'),
('J00', 'Viêm mũi họng cấp (Cảm thường)', 'Acute nasopharyngitis (common cold)', 'Hô hấp'),
('J02', 'Viêm họng cấp', 'Acute pharyngitis', 'Hô hấp'),
('J03', 'Viêm amidan cấp', 'Acute tonsillitis', 'Hô hấp'),
('J06', 'Nhiễm khuẩn hô hấp trên cấp tính nhiều nơi/không xác định', 'Acute upper respiratory infections of multiple and unspecified sites', 'Hô hấp'),
('J20', 'Viêm phế quản cấp', 'Acute bronchitis', 'Hô hấp'),
('J30', 'Viêm mũi dị ứng và viêm mũi vận mạch', 'Vasomotor and allergic rhinitis', 'Hô hấp'),
('J45', 'Hen phế quản', 'Asthma', 'Hô hấp'),
('K21', 'Bệnh trào ngược dạ dày - thực quản (GERD)', 'Gastro-esophageal reflux disease', 'Tiêu hóa'),
('K29', 'Viêm dạ dày và tá tràng', 'Gastritis and duodenitis', 'Tiêu hóa'),
('K58', 'Hội chứng ruột kích thích (IBS)', 'Irritable bowel syndrome', 'Tiêu hóa'),
('L20', 'Viêm da cơ địa', 'Atopic dermatitis', 'Da liễu'),
('M45', 'Viêm cột sống dính khớp', 'Ankylosing spondylitis', 'Cơ xương khớp'),
('M54', 'Đau lưng', 'Dorsalgia', 'Cơ xương khớp'),
('M81', 'Loãng xương không có gãy xương bệnh lý', 'Osteoporosis without pathological fracture', 'Cơ xương khớp'),
('N30', 'Viêm bàng quang', 'Cystitis', 'Thận tiết niệu'),
('N39', 'Các rối loạn khác của hệ tiết niệu (Nhiễm trùng tiểu)', 'Other disorders of urinary system', 'Thận tiết niệu'),
('R50', 'Sốt không rõ nguyên nhân', 'Fever of other and unknown origin', 'Triệu chứng chung'),
('R51', 'Đau đầu', 'Headache', 'Triệu chứng chung'),
('R52', 'Đau, chưa phân loại nơi khác', 'Pain, not elsewhere classified', 'Triệu chứng chung'),
('Z00', 'Khám sức khỏe tổng quát', 'General examination and investigation of persons without complaint', 'Khám kiểm tra');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `invoices`
--

CREATE TABLE `invoices` (
  `id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `appointment_id` int DEFAULT NULL,
  `admission_id` int DEFAULT NULL,
  `prescription_id` int DEFAULT NULL,
  `insurance_number` varchar(50) DEFAULT NULL,
  `insurance_rate` decimal(5,2) DEFAULT '0.00',
  `insurance_coverage` decimal(12,2) DEFAULT '0.00',
  `patient_payment` decimal(12,2) DEFAULT '0.00',
  `total_amount` decimal(12,2) DEFAULT '0.00',
  `discount` decimal(12,2) DEFAULT '0.00',
  `final_amount` decimal(12,2) DEFAULT '0.00',
  `payment_method` enum('cash','card','momo','vnpay','transfer') DEFAULT 'cash',
  `vnpay_txn_ref` varchar(100) DEFAULT NULL,
  `vnpay_transaction_no` varchar(50) DEFAULT NULL,
  `vnpay_response_code` varchar(10) DEFAULT NULL,
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `notes` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `invoices`
--

INSERT INTO `invoices` (`id`, `patient_id`, `appointment_id`, `admission_id`, `prescription_id`, `insurance_number`, `insurance_rate`, `insurance_coverage`, `patient_payment`, `total_amount`, `discount`, `final_amount`, `payment_method`, `vnpay_txn_ref`, `vnpay_transaction_no`, `vnpay_response_code`, `status`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, NULL, NULL, NULL, 0.00, 0.00, 0.00, 705000.00, 0.00, 705000.00, 'cash', NULL, NULL, NULL, 'paid', 'Khám tim mạch + xét nghiệm', 12, NULL, '2026-03-10 11:00:00', '2026-05-02 17:12:28', NULL),
(2, 2, 2, NULL, NULL, NULL, 0.00, 0.00, 0.00, 500000.00, 50000.00, 450000.00, 'momo', NULL, NULL, NULL, 'paid', 'Khám thần kinh + chụp X-quang', 12, NULL, '2026-03-11 16:00:00', '2026-05-02 17:12:28', NULL),
(3, 6, 7, NULL, NULL, NULL, 0.00, 0.00, 0.00, 450000.00, 0.00, 450000.00, 'card', NULL, NULL, NULL, 'paid', 'Đo điện tim + Khám tổng quát', 12, NULL, '2026-05-06 09:30:00', '2026-05-02 17:12:28', NULL),
(4, 7, 8, NULL, NULL, NULL, 0.00, 0.00, 0.00, 300000.00, 0.00, 300000.00, 'transfer', NULL, NULL, NULL, 'pending', 'Khám tổng quát', 12, NULL, '2026-05-06 14:00:00', '2026-05-02 17:12:28', NULL),
(5, 4, 4, 1, NULL, NULL, 0.00, 0.00, 0.00, 3500000.00, 500000.00, 3000000.00, 'cash', NULL, NULL, NULL, 'paid', 'Phí tạm ứng nhập viện và lưu viện ban đầu', 12, NULL, '2026-03-13 17:30:00', '2026-05-02 17:12:28', NULL),
(6, 1, NULL, NULL, 6, NULL, 0.00, 0.00, 0.00, 37500.00, 0.00, 37500.00, 'cash', NULL, NULL, NULL, 'paid', 'Test invoice for prescription', 1, 1, '2026-05-25 09:27:17', '2026-05-25 09:27:17', NULL),
(7, 1, NULL, NULL, 7, NULL, 0.00, 0.00, 0.00, 37500.00, 0.00, 37500.00, 'cash', NULL, NULL, NULL, 'paid', 'Test invoice for prescription', 1, 1, '2026-05-25 09:27:39', '2026-05-25 09:27:39', NULL),
(8, 1, NULL, NULL, 8, NULL, 0.00, 0.00, 0.00, 37500.00, 0.00, 37500.00, 'cash', NULL, NULL, NULL, 'paid', 'Test invoice for prescription', 1, 1, '2026-05-25 09:27:52', '2026-05-25 09:27:52', NULL),
(9, 1, NULL, NULL, 10, NULL, 0.00, 0.00, 0.00, 37500.00, 0.00, 37500.00, 'cash', NULL, NULL, NULL, 'paid', 'Test invoice for prescription', 1, 1, '2026-05-25 09:29:12', '2026-05-25 09:29:12', NULL),
(10, 6, NULL, NULL, 12, NULL, 0.00, 0.00, 0.00, 3500.00, 0.00, 3500.00, 'cash', NULL, NULL, NULL, 'paid', '', 12, 12, '2026-05-25 10:13:18', '2026-05-25 10:29:46', NULL),
(12, 6, NULL, NULL, 16, NULL, 0.00, 0.00, 5000.00, 5000.00, 0.00, 5000.00, 'vnpay', '12_1780043493', NULL, NULL, 'paid', '', 29, 29, '2026-05-29 08:31:21', '2026-05-29 08:31:44', NULL),
(13, 6, NULL, NULL, 17, NULL, 0.00, 0.00, 4500.00, 4500.00, 0.00, 4500.00, 'momo', '13_1780044227', NULL, NULL, 'paid', '', 29, 29, '2026-05-29 08:43:32', '2026-05-29 08:44:05', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int NOT NULL,
  `invoice_id` int DEFAULT NULL,
  `service_id` int DEFAULT NULL,
  `medicine_id` int DEFAULT NULL,
  `room_id` int DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `quantity` int DEFAULT '1',
  `unit_price` decimal(12,2) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `invoice_items`
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
(13, 5, NULL, NULL, NULL, 'Phí sinh hoạt khác', 1, 425000.00, 425000.00),
(14, 10, NULL, 8, NULL, 'Omeprazole 20mg', 1, 3500.00, 3500.00),
(16, 12, NULL, 2, NULL, 'Amoxicillin 500mg', 1, 5000.00, 5000.00),
(17, 13, NULL, 6, NULL, 'Enalapril 5mg', 1, 4500.00, 4500.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lab_orders`
--

CREATE TABLE `lab_orders` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `appointment_id` int DEFAULT NULL,
  `order_type` enum('lab_test','imaging') NOT NULL,
  `test_name` varchar(200) NOT NULL,
  `priority` enum('normal','urgent') DEFAULT 'normal',
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `notes` text,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `lab_orders`
--

INSERT INTO `lab_orders` (`id`, `patient_id`, `doctor_id`, `appointment_id`, `order_type`, `test_name`, `priority`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 9, 1, NULL, 'imaging', 'Xét nghiệm công thức máu', 'normal', 'completed', 'lâm sàng', 2, '2026-05-27 16:28:01', '2026-05-27 16:33:16'),
(2, 6, 1, NULL, 'imaging', 'Xét nghiệm công thức máu', 'urgent', 'cancelled', '', 2, '2026-05-29 15:45:25', '2026-05-29 15:59:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lab_results`
--

CREATE TABLE `lab_results` (
  `id` int NOT NULL,
  `lab_order_id` int NOT NULL,
  `technician_id` int NOT NULL,
  `result_text` text,
  `result_value` varchar(100) DEFAULT NULL,
  `normal_range` varchar(100) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `conclusion` enum('normal','abnormal','critical') DEFAULT 'normal',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `lab_results`
--

INSERT INTO `lab_results` (`id`, `lab_order_id`, `technician_id`, `result_text`, `result_value`, `normal_range`, `unit`, `conclusion`, `completed_at`, `created_at`, `image_path`) VALUES
(1, 1, 27, 'Phế trường hai bên sáng, không thấy bóng tim to. Xuất hiện đám mờ rải rác vùng hạ đòn bên phải, nghi ngờ tổn thương viêm nhiễm tiến triển. Không thấy tràn dịch màng phổi hai bên.', 'Có đám mờ hạ đòn phải', 'Không phát hiện tổn thương', '', 'critical', '2026-05-27 16:33:16', '2026-05-27 16:33:16', 'uploads/lab_results/bbc28175f8f2c32bb781112ce10d88d3.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `medical_devices`
--

CREATE TABLE `medical_devices` (
  `id` int NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `device_code` varchar(100) DEFAULT NULL,
  `status` enum('available','in_use','maintenance') DEFAULT 'available',
  `department_id` int DEFAULT NULL,
  `purchase_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `medical_devices`
--

INSERT INTO `medical_devices` (`id`, `name`, `device_code`, `status`, `department_id`, `purchase_date`) VALUES
(1, 'Máy đo điện tim (ECG)', 'DEV-TM-001', 'in_use', 1, '2023-05-01'),
(2, 'Máy chụp MRI 1.5T', 'DEV-TK-001', 'in_use', 2, '2022-08-15'),
(3, 'Máy chụp X-quang kỹ thuật số', 'DEV-NK-001', 'available', 4, '2021-10-10'),
(4, 'Máy siêu âm 4D', 'DEV-NK-002', 'available', 3, '2024-01-20'),
(5, 'Máy xét nghiệm huyết học tự động', 'DEV-XN-001', 'available', 4, '2023-11-05');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `appointment_id` int DEFAULT NULL,
  `icd10_code` varchar(10) DEFAULT NULL,
  `diagnosis` text,
  `treatment` text,
  `notes` text,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `medical_records`
--

INSERT INTO `medical_records` (`id`, `patient_id`, `doctor_id`, `appointment_id`, `icd10_code`, `diagnosis`, `treatment`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, NULL, 'Rối loạn nhịp tim nhẹ (Loạn nhịp xoang)', 'Theo dõi Holter 24h, hạn chế caffeine và rượu bia', 'Bệnh nhân có tiền sử dị ứng Penicillin. Hẹn tái khám sau 2 tuần.', NULL, NULL, '2026-03-10 10:00:00', '2026-05-02 17:12:28', NULL),
(2, 3, 3, 3, NULL, 'Sức khỏe tổng quát bình thường', 'Không cần điều trị. Bổ sung Vitamin tổng hợp.', 'Các chỉ số xét nghiệm trong giới hạn bình thường. Hẹn khám lại sau 6 tháng.', NULL, NULL, '2026-03-12 11:00:00', '2026-05-02 17:12:28', NULL),
(3, 4, 1, 4, NULL, 'Suy tim độ II (NYHA)', 'Furosemide 40mg/ngày, Enalapril 5mg x 2 lần/ngày, hạn chế muối', 'Bệnh nhân phù 2 chi dưới, khó thở khi nằm. Cần nhập viện theo dõi.', NULL, NULL, '2026-03-13 16:30:00', '2026-05-02 17:12:28', NULL),
(4, 2, 2, 2, NULL, 'Đau nửa đầu Migraine', 'Paracetamol 1g khi đau, Topiramate 25mg/ngày phòng ngừa', 'Tiền sử gia đình có người bị Migraine. Tránh stress và thiếu ngủ.', NULL, NULL, '2026-03-11 15:30:00', '2026-05-02 17:12:28', NULL),
(5, 6, 1, 7, NULL, 'Cơn nhịp nhanh trên thất', 'Theo dõi ngoại trú, kê đơn chống loạn nhịp', 'Khuyên bệnh nhân ngừng hút thuốc', NULL, NULL, '2026-05-06 09:00:00', '2026-05-02 17:12:28', NULL),
(6, 7, 3, 8, NULL, 'Viêm loét dạ dày - tá tràng', 'Omeprazole 20mg x 2 lần/ngày, thay đổi thói quen ăn uống', 'Tránh đồ ăn cay nóng, chua, thức khuya', NULL, NULL, '2026-05-06 13:45:00', '2026-05-02 17:12:28', NULL),
(7, 9, 2, 10, NULL, 'Thiểu năng tuần hoàn não', 'Cải thiện tuần hoàn não, nghỉ ngơi', 'Nghi ngờ liên quan đến loãng xương cổ', NULL, NULL, '2026-05-07 15:00:00', '2026-05-02 17:12:28', NULL),
(8, 10, 3, 11, NULL, 'Cơn Gout cấp', 'Colchicine 1mg, tăng liều Allopurinol', 'Chườm lạnh, kiêng đạm động vật', NULL, NULL, '2026-05-08 10:30:00', '2026-05-02 17:12:28', NULL),
(10, 6, 1, NULL, NULL, 'Tăng huyết áp vô căn giai đoạn 2. Triệu chứng: Đau đầu nhẹ, chóng mặt, nặng ngực khi gắng sức. Huyết áp phòng khám: 155/95 mmHg', 'Amlodipin 5mg: 01 viên/ngày (uống sáng).\r\n\r\nLosartan 50mg: 01 viên/ngày (uống sáng).\r\n\r\nChỉ định: Đo điện tâm đồ (ECG), siêu âm tim.', 'Ăn nhạt (giảm muối), hạn chế mỡ động vật. Tránh thức khuya, stress. Tập thể dục nhẹ nhàng.', 2, NULL, '2026-05-29 07:57:22', '2026-05-29 07:57:22', NULL),
(11, 6, 1, NULL, NULL, 'Viêm phế quản cấp cơ địa dị ứng. Triệu chứng: Ho khạc đờm trắng đục, sốt nhẹ về chiều (38°C), rát họng, mệt mỏi. Phổi thô, có ít rale ẩm rải rác.', 'Glucophage 850mg (Metformin): 02 viên/ngày (chia 2 lần, uống ngay sau ăn sáng/tối).\r\nDiamicron MR 60mg: 01 viên/ngày (uống trước ăn sáng).\r\nChỉ định: Xét nghiệm bộ mỡ máu, HbA1c định kỳ.', 'Uống nhiều nước ấm (1.5 - 2 lít/ngày). Giữ ấm cổ họng, súc miệng nước muối sinh lý 3 lần/ngày. Kiêng nước đá, đồ ăn lạnh.', 2, NULL, '2026-05-29 08:41:46', '2026-05-29 08:41:46', NULL),
(12, 6, 1, NULL, NULL, 'Viêm phế quản cấp cơ địa dị ứng.', 'Augmentin 1g: 02 viên/ngày (chia 2 lần sáng/tối, sau ăn).', 'Uống nhiều nước ấm', 2, NULL, '2026-05-29 08:58:58', '2026-05-29 08:58:58', NULL);

--
-- Bẫy `medical_records`
--
DELIMITER $$
CREATE TRIGGER `trg_medical_records_after_update` AFTER UPDATE ON `medical_records` FOR EACH ROW BEGIN
    INSERT INTO `audit_logs` (`user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`)
    VALUES (
        NEW.updated_by, 
        'UPDATE', 
        'medical_records', 
        NEW.id, 
        JSON_OBJECT('diagnosis', OLD.diagnosis, 'treatment', OLD.treatment), 
        JSON_OBJECT('diagnosis', NEW.diagnosis, 'treatment', NEW.treatment)
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `medicines`
--

CREATE TABLE `medicines` (
  `id` int NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text,
  `quantity` int DEFAULT '0',
  `reserved` int DEFAULT '0',
  `expiry_date` date DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `description`, `quantity`, `reserved`, `expiry_date`, `price`) VALUES
(1, 'Paracetamol 500mg', 'Thuốc giảm đau hạ sốt, dùng phổ biến', 469, 39, '2027-05-01', 2500.00),
(2, 'Amoxicillin 500mg', 'Kháng sinh nhóm Beta-lactam, điều trị nhiễm khuẩn', 299, 0, '2026-12-01', 5000.00),
(3, 'Aspirin 81mg', 'Thuốc chống kết tập tiểu cầu, hỗ trợ tim mạch', 200, 1, '2027-03-15', 3500.00),
(4, 'Vitamin C 1000mg', 'Tăng cường sức đề kháng, chống oxy hóa', 600, 0, '2027-08-01', 2000.00),
(5, 'Furosemide 40mg', 'Thuốc lợi tiểu, điều trị suy tim và phù', 150, 1, '2027-06-01', 3000.00),
(6, 'Enalapril 5mg', 'Thuốc ức chế ACE, điều trị tăng huyết áp và suy tim', 199, 0, '2027-04-15', 4500.00),
(7, 'Topiramate 25mg', 'Thuốc chống động kinh, phòng ngừa Migraine', 8, 0, '2027-01-20', 8000.00),
(8, 'Omeprazole 20mg', 'Thuốc ức chế bơm proton, điều trị viêm loét dạ dày', 349, 0, '2027-09-01', 3500.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `message` text,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `status`, `created_at`) VALUES
(1, 7, 'Nhắc lịch khám', 'Bạn có lịch khám vào ngày 10/03/2026 lúc 09:00 với BS. Nguyễn Văn An (Tim mạch).', 'unread', '2026-03-09 08:00:00'),
(2, 8, 'Xác nhận lịch khám', 'Lịch khám của bạn ngày 11/03/2026 với BS. Trần Thị Bình (Thần kinh) đã được đặt thành công.', 'unread', '2026-03-05 08:36:41'),
(3, 9, 'Kết quả xét nghiệm', 'Kết quả xét nghiệm máu ngày 12/03/2026 của bạn đã có. Vui lòng liên hệ lễ tân để nhận kết quả.', 'read', '2026-03-13 09:00:00'),
(4, 2, '🌙 Nhắc nhở ca trực đêm', 'Lưu ý: Bạn mới chỉ đăng ký 0/2 ca trực đêm tối thiểu cho tuần này. Vui lòng vào phân hệ ca trực để đăng ký thêm để tránh bị Trưởng khoa chỉ định trực.', 'unread', '2026-05-29 10:36:43'),
(5, 14, '🌙 Nhắc nhở ca trực đêm', 'Lưu ý: Bạn mới chỉ đăng ký 1/2 ca trực đêm tối thiểu cho tuần này. Vui lòng vào phân hệ ca trực để đăng ký thêm để tránh bị Trưởng khoa chỉ định trực.', 'unread', '2026-05-29 10:52:19'),
(6, 5, '🌙 Nhắc nhở ca trực đêm', 'Lưu ý: Bạn mới chỉ đăng ký 0/2 ca trực đêm tối thiểu cho tuần này. Vui lòng vào phân hệ ca trực để đăng ký thêm để tránh bị Điều dưỡng trưởng chỉ định trực.', 'unread', '2026-05-29 11:11:30'),
(7, 3, '🌙 Nhắc nhở ca trực đêm', 'Lưu ý: Bạn mới chỉ đăng ký 0/2 ca trực đêm tối thiểu cho tuần này. Vui lòng vào phân hệ ca trực để đăng ký thêm để tránh bị Trưởng khoa chỉ định trực.', 'unread', '2026-05-30 20:20:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nurses`
--

CREATE TABLE `nurses` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `is_head` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `nurses`
--

INSERT INTO `nurses` (`id`, `user_id`, `department_id`, `is_head`) VALUES
(1, 5, 6, 1),
(2, 6, NULL, 0),
(3, 16, 2, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nurse_shifts`
--

CREATE TABLE `nurse_shifts` (
  `id` int NOT NULL,
  `nurse_id` int NOT NULL,
  `shift_id` int NOT NULL,
  `status` enum('pending','approved','rejected','assigned') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nursing_records`
--

CREATE TABLE `nursing_records` (
  `id` int NOT NULL,
  `admission_id` int NOT NULL,
  `nurse_id` int NOT NULL,
  `record_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `temperature` decimal(4,1) DEFAULT NULL,
  `blood_pressure_sys` int DEFAULT NULL,
  `blood_pressure_dia` int DEFAULT NULL,
  `heart_rate` int DEFAULT NULL,
  `respiratory_rate` int DEFAULT NULL,
  `spo2` int DEFAULT NULL,
  `care_notes` text,
  `medication_given` text,
  `diet_notes` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `online_meetings`
--

CREATE TABLE `online_meetings` (
  `id` int NOT NULL,
  `appointment_id` int DEFAULT NULL,
  `meeting_id` varchar(100) DEFAULT NULL,
  `meeting_link` varchar(255) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `online_meetings`
--

INSERT INTO `online_meetings` (`id`, `appointment_id`, `meeting_id`, `meeting_link`, `start_time`, `status`) VALUES
(1, 1, 'NC-A1B2C3D4', 'https://meet.novacare.com/NC-A1B2C3D4', '2026-03-10 09:00:00', 'completed'),
(2, 2, 'NC-E5F6G7H8', 'https://meet.novacare.com/NC-E5F6G7H8', '2026-03-11 14:00:00', 'scheduled'),
(3, 5, 'NC-J9K0L1M2', 'https://meet.novacare.com/NC-J9K0L1M2', '2026-04-15 09:30:00', 'completed'),
(4, 6, 'NC-N3P4Q5R6', 'https://meet.novacare.com/NC-N3P4Q5R6', '2026-05-05 10:00:00', 'scheduled'),
(5, 7, 'NC-24B80EB3', 'index.php?page=consultations&action=room&id=NC-24B80EB3', '2026-05-06 08:30:00', 'scheduled'),
(6, 14, 'NC-578ECD42', 'index.php?page=consultations&action=room&id=NC-578ECD42', '2026-05-08 21:43:00', 'scheduled'),
(7, 15, 'NC-207EC1F1', 'index.php?page=consultations&action=room&id=NC-207EC1F1', '2026-05-07 21:49:00', 'scheduled');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `patients`
--

CREATE TABLE `patients` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text,
  `blood_type` varchar(5) DEFAULT NULL,
  `medical_history` text,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `insurance_number` varchar(50) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `patients`
--

INSERT INTO `patients` (`id`, `user_id`, `date_of_birth`, `gender`, `address`, `blood_type`, `medical_history`, `emergency_contact`, `insurance_number`, `deleted_at`) VALUES
(1, 7, '1998-05-20', 'male', '123 Nguyễn Trãi, P. Bến Thành, Q.1, TP.HCM', 'O+', 'Dị ứng Penicillin. Tiền sử tăng huyết áp nhẹ.', NULL, 'GD4797931300001', NULL),
(2, 8, '2000-11-10', 'female', '45 Đại lộ Bình Dương, P. Hiệp Thành, TX. Thủ Dầu Một, Bình Dương', 'A+', 'Tiền sử gia đình: Migraine. Không dị ứng thuốc.', NULL, NULL, NULL),
(3, 9, '1995-02-14', 'male', '78 Trần Phú, P. Bửu Long, TP. Biên Hòa, Đồng Nai', 'B+', 'Không có bệnh lý nền. Sức khỏe tốt.', NULL, NULL, NULL),
(4, 10, '2001-07-08', 'female', '256 Võ Văn Kiệt, P. Cầu Kho, Q.1, TP.HCM', 'AB+', 'Tiền sử bệnh tim bẩm sinh (nhẹ). Đang theo dõi.', NULL, NULL, NULL),
(5, 11, '2014-02-20', 'female', '32/5 Nguyễn Văn Ngọi, P.7, Q. Gò Vấp, TP.HCM', 'B-', 'Rối loạn phổ tự kỉ (ASD). Đang điều trị tại khoa Nhi.', NULL, NULL, '2026-05-02 17:59:27'),
(6, 17, '1985-09-12', 'male', '110 Nam Kỳ Khởi Nghĩa, Q.3, TP.HCM', 'O+', 'Đái tháo đường type 2 (phát hiện 2 năm).', NULL, NULL, NULL),
(7, 18, '1990-03-25', 'male', '55 Lê Lợi, TP. Vũng Tàu', 'A-', 'Trào ngược dạ dày thực quản (GERD).', NULL, NULL, NULL),
(8, 19, '1993-11-05', 'female', '12 Nguyễn Huệ, TP. Huế', 'B+', 'Viêm xoang mạn tính.', NULL, NULL, NULL),
(9, 20, '1978-01-31', 'female', '88 Phan Đăng Lưu, Phú Nhuận, TP.HCM', 'O-', 'Rối loạn tiền đình, loãng xương mức độ nhẹ.', NULL, NULL, NULL),
(10, 21, '1982-08-14', 'male', '44 Trần Hưng Đạo, Q.5, TP.HCM', 'AB-', 'Gout. Đang dùng Allopurinol.', NULL, NULL, NULL),
(11, 22, '2005-06-18', 'female', '101 Cách Mạng Tháng Tám, Tân Bình, TP.HCM', 'O+', 'Dị ứng hải sản.', NULL, NULL, NULL),
(12, 23, '1965-12-02', 'male', '23 Nguyễn Đình Chiểu, Q.3, TP.HCM', 'A+', 'Tăng huyết áp vô căn, suy thận độ 2.', NULL, NULL, NULL),
(13, 24, '1970-04-19', 'female', '9 Lê Duẩn, Q.1, TP.HCM', 'B+', 'Thoái hóa khớp gối 2 bên.', NULL, NULL, NULL),
(14, 26, '2005-07-14', 'male', 'TP Hồ Chí Minh, Việt Nam', 'A+', 'Viêm Gan B', NULL, NULL, NULL),
(15, 31, '2004-07-28', 'male', 'TP Hồ Chí Minh, Việt Nam', 'B-', 'Lao phổi', NULL, NULL, NULL),
(16, 32, '2005-06-03', 'female', 'TP Hồ Chí Minh, Việt Nam', 'A+', 'ho lao', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `patient_services`
--

CREATE TABLE `patient_services` (
  `id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `service_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `result` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `patient_services`
--

INSERT INTO `patient_services` (`id`, `patient_id`, `service_id`, `doctor_id`, `service_date`, `result`) VALUES
(1, 1, 1, 1, '2026-03-10', 'Chỉ số máu bình thường'),
(2, 2, 2, 2, '2026-03-11', 'Không phát hiện bất thường'),
(3, 3, 1, 3, '2026-03-12', 'Kết quả bình thường');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int NOT NULL,
  `medical_record_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `pharmacist_notes` text,
  `status` enum('draft','paid','approved','dispensed','cancelled') DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `medical_record_id`, `doctor_id`, `approved_by`, `approved_at`, `pharmacist_notes`, `status`, `created_at`) VALUES
(1, 1, 1, NULL, NULL, NULL, 'draft', '2026-03-10 10:30:00'),
(2, 3, 1, NULL, NULL, NULL, 'draft', '2026-03-13 17:00:00'),
(3, 4, 2, NULL, NULL, NULL, 'draft', '2026-03-11 16:00:00'),
(4, 6, 3, NULL, NULL, NULL, 'draft', '2026-05-06 14:00:00'),
(5, 8, 3, NULL, NULL, NULL, 'draft', '2026-05-08 10:45:00'),
(6, 1, 1, NULL, NULL, NULL, 'dispensed', '2026-05-25 09:27:17'),
(7, 1, 1, NULL, NULL, NULL, 'dispensed', '2026-05-25 09:27:39'),
(8, 1, 1, NULL, NULL, NULL, 'dispensed', '2026-05-25 09:27:52'),
(9, 1, 1, NULL, NULL, NULL, 'cancelled', '2026-05-25 09:27:53'),
(10, 1, 1, NULL, NULL, NULL, 'dispensed', '2026-05-25 09:29:12'),
(11, 1, 1, NULL, NULL, NULL, 'cancelled', '2026-05-25 09:29:12'),
(12, 5, 1, NULL, NULL, NULL, 'dispensed', '2026-05-25 10:01:18'),
(13, 5, 1, NULL, NULL, NULL, 'draft', '2026-05-26 08:58:09'),
(16, 10, 1, 13, '2026-05-29 08:33:27', '', 'dispensed', '2026-05-29 08:00:25'),
(17, 11, 1, 13, '2026-05-29 08:46:59', '', 'dispensed', '2026-05-29 08:42:19'),
(18, 11, 1, NULL, NULL, NULL, 'dispensed', '2026-05-29 08:56:24'),
(19, 12, 1, NULL, NULL, NULL, 'draft', '2026-05-29 08:59:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `prescription_items`
--

CREATE TABLE `prescription_items` (
  `id` int NOT NULL,
  `prescription_id` int DEFAULT NULL,
  `medicine_id` int DEFAULT NULL,
  `quantity` int DEFAULT '1',
  `dosage` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `instructions` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `prescription_items`
--

INSERT INTO `prescription_items` (`id`, `prescription_id`, `medicine_id`, `quantity`, `dosage`, `duration`, `instructions`) VALUES
(1, 1, 1, 15, '500mg x 3 lần/ngày', '5 ngày', 'Uống sau khi ăn no, cách nhau 6 tiếng'),
(2, 1, 3, 30, '81mg x 1 lần/ngày', '30 ngày', 'Uống vào buổi sáng sau ăn'),
(3, 2, 5, 14, '40mg x 1 lần/ngày', '14 ngày', 'Uống buổi sáng, theo dõi lượng nước tiểu'),
(4, 2, 6, 60, '5mg x 2 lần/ngày', '30 ngày', 'Uống sáng-tối, theo dõi huyết áp'),
(5, 3, 1, 10, '1g khi đau', 'Khi cần', 'Không quá 4g/ngày. Uống khi cơn đau xuất hiện.'),
(6, 3, 7, 60, '25mg x 1 lần/ngày', '60 ngày', 'Uống tối trước khi ngủ, phòng ngừa Migraine'),
(7, 4, 8, 28, '20mg x 2 lần/ngày', '14 ngày', 'Uống trước ăn sáng và tối 30 phút'),
(8, 5, 1, 6, '500mg x 2 lần/ngày', '3 ngày', 'Giảm đau chống viêm'),
(9, 5, 4, 10, '1000mg x 1 lần/ngày', '10 ngày', 'Uống buổi sáng'),
(10, 6, 1, 1, '500mg x 3', '5 days', 'After meal'),
(11, 7, 1, 1, '500mg x 3', '5 days', 'After meal'),
(12, 8, 1, 15, '500mg x 3', '5 days', 'After meal'),
(13, 10, 1, 15, '500mg x 3', '5 days', 'After meal'),
(14, 11, 1, 10, '500mg x 3', '5 days', 'After meal'),
(15, 12, 8, 1, '5mg', '3', 'Uống sau khi ăn'),
(16, 13, 5, 1, '50mg', '3', 'Uống sau khi ăn'),
(19, 16, 2, 1, '500mg', '3 ngày', 'Uống sau khi ăn'),
(20, 17, 6, 1, '500mg', '3 ngày', 'Uống sau khi ăn'),
(21, 19, 3, 1, '500mg', '3 ngày', 'Uống sau khi ăn');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `queue_tickets`
--

CREATE TABLE `queue_tickets` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `ticket_number` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `examination_room_id` int DEFAULT NULL,
  `appointment_id` int DEFAULT NULL,
  `status` enum('waiting','called','in_progress','completed','cancelled') DEFAULT 'waiting',
  `priority` enum('normal','priority','emergency') DEFAULT 'normal',
  `queue_date` date NOT NULL,
  `check_in_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `called_at` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `insurance_verified` tinyint(1) DEFAULT '0',
  `notes` varchar(200) DEFAULT NULL,
  `created_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `queue_tickets`
--

INSERT INTO `queue_tickets` (`id`, `patient_id`, `ticket_number`, `department_id`, `doctor_id`, `examination_room_id`, `appointment_id`, `status`, `priority`, `queue_date`, `check_in_at`, `called_at`, `started_at`, `completed_at`, `insurance_verified`, `notes`, `created_by`) VALUES
(1, 9, 1, 6, 1, 1, NULL, 'completed', 'normal', '2026-05-28', '2026-05-28 08:45:12', '2026-05-28 08:49:31', '2026-05-28 08:49:39', '2026-05-28 08:49:42', 0, 'Đo huyết áp nhịp tim', 12),
(2, 9, 2, 6, NULL, 1, NULL, 'cancelled', 'normal', '2026-05-28', '2026-05-28 08:45:27', NULL, NULL, NULL, 0, 'Đo huyết áp nhịp tim', 12),
(3, 15, 3, 6, NULL, 1, NULL, 'cancelled', 'normal', '2026-05-28', '2026-05-28 09:13:53', NULL, NULL, NULL, 0, 'Ho lao', 12),
(4, 15, 4, 5, NULL, 1, NULL, 'cancelled', 'normal', '2026-05-28', '2026-05-28 09:15:17', NULL, NULL, NULL, 0, 'Ho lao', 12),
(5, 15, 5, 3, 1, 4, NULL, 'completed', 'normal', '2026-05-28', '2026-05-28 09:20:22', '2026-05-28 09:20:29', '2026-05-28 09:20:32', '2026-05-28 09:20:35', 0, 'Ho nhiều', 12),
(6, 16, 6, 3, NULL, 4, NULL, 'completed', 'normal', '2026-05-28', '2026-05-28 09:24:45', '2026-05-28 09:25:19', '2026-05-28 09:25:21', '2026-05-28 09:25:22', 0, 'aaa', 12),
(7, 6, 1, 3, NULL, 4, NULL, 'completed', 'normal', '2026-05-29', '2026-05-29 18:24:44', '2026-05-29 18:24:57', '2026-05-29 18:25:16', '2026-05-29 18:25:43', 0, '', 12),
(8, 6, 2, 3, NULL, 4, NULL, 'in_progress', 'normal', '2026-05-29', '2026-05-29 18:51:00', '2026-05-29 18:51:17', '2026-05-29 18:51:19', NULL, 0, '', 12),
(9, 6, 1, 3, NULL, 4, NULL, 'completed', 'normal', '2026-05-30', '2026-05-30 11:26:11', '2026-05-30 11:26:21', '2026-05-30 11:52:21', '2026-05-30 11:52:40', 0, '', 12);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rooms`
--

CREATE TABLE `rooms` (
  `id` int NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `department_id` int DEFAULT NULL,
  `room_type` enum('standard','vip','icu') DEFAULT 'standard',
  `price_per_day` decimal(12,2) DEFAULT '0.00',
  `status` enum('available','full','maintenance') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `rooms`
--

INSERT INTO `rooms` (`id`, `room_number`, `department_id`, `room_type`, `price_per_day`, `status`) VALUES
(1, 'P101', 1, 'standard', 500000.00, 'full'),
(2, 'P102', 1, 'vip', 1500000.00, 'available'),
(3, 'P201', 2, 'standard', 500000.00, 'available'),
(4, 'P202', 4, 'icu', 3000000.00, 'available'),
(5, 'P301', 3, 'standard', 500000.00, 'available');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `services`
--

CREATE TABLE `services` (
  `id` int NOT NULL,
  `service_name` varchar(150) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `services`
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
-- Cấu trúc bảng cho bảng `shifts`
--

CREATE TABLE `shifts` (
  `id` int NOT NULL,
  `shift_date` date DEFAULT NULL,
  `shift_type` enum('day','night') DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `required_doctors` int DEFAULT '0',
  `required_nurses` int DEFAULT '0',
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `shifts`
--

INSERT INTO `shifts` (`id`, `shift_date`, `shift_type`, `department_id`, `name`, `start_time`, `end_time`, `required_doctors`, `required_nurses`, `notes`) VALUES
(1, '2026-05-05', 'day', NULL, NULL, NULL, NULL, 0, 0, NULL),
(2, '2026-05-05', 'night', NULL, NULL, NULL, NULL, 0, 0, NULL),
(3, '2026-05-06', 'day', NULL, NULL, NULL, NULL, 0, 0, NULL),
(4, '2026-05-06', 'night', NULL, NULL, NULL, NULL, 0, 0, NULL),
(5, '2026-05-07', 'day', NULL, NULL, NULL, NULL, 0, 0, NULL),
(6, '2026-05-07', 'night', NULL, NULL, NULL, NULL, 0, 0, NULL),
(7, '2026-05-26', 'night', 3, 'Ca trực cấp cứu', '00:00:00', '22:00:00', 5, 5, ''),
(8, '2026-06-01', 'night', 3, 'Ca Đêm Thường Quy - Khoa Nội', '23:00:00', '04:00:00', 2, 2, ''),
(9, '2026-06-02', 'night', 3, 'Ca Đêm Thường Quy - Khoa Nội', '23:00:00', '04:00:00', 2, 2, ''),
(10, '2026-06-03', 'night', 3, 'Ca Đêm Thường Quy - Khoa Nội', '23:00:00', '04:00:00', 2, 2, ''),
(11, '2026-06-04', 'night', 3, 'Ca Đêm Thường Quy - Khoa Nội', '23:00:00', '04:00:00', 2, 2, ''),
(12, '2026-06-05', 'night', 3, 'Ca Đêm Thường Quy - Khoa Nội', '23:00:00', '04:00:00', 2, 2, ''),
(13, '2026-06-06', 'night', 3, 'Ca Đêm Thường Quy - Khoa Nội', '23:00:00', '04:00:00', 2, 2, ''),
(14, '2026-06-07', 'night', 3, 'Ca Đêm Thường Quy - Khoa Nội', '23:00:00', '04:00:00', 2, 2, ''),
(15, '2026-06-01', 'day', 3, 'Ca trực ngày', '19:00:00', '17:00:00', 2, 2, ''),
(16, '2026-06-02', 'day', 3, 'Ca trực ngày', '19:00:00', '17:00:00', 2, 2, ''),
(17, '2026-06-03', 'day', 3, 'Ca trực ngày', '19:00:00', '17:00:00', 2, 2, ''),
(18, '2026-06-04', 'day', 3, 'Ca trực ngày', '19:00:00', '17:00:00', 2, 2, ''),
(19, '2026-06-05', 'day', 3, 'Ca trực ngày', '19:00:00', '17:00:00', 2, 2, ''),
(20, '2026-06-06', 'day', 3, 'Ca trực ngày', '19:00:00', '17:00:00', 2, 2, ''),
(21, '2026-06-07', 'day', 3, 'Ca trực ngày', '19:00:00', '17:00:00', 2, 2, ''),
(22, '2026-06-01', 'day', 3, 'Ca chiều thường', '12:00:00', '18:00:00', 1, 1, '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `technicians`
--

CREATE TABLE `technicians` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `specialty` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `technicians`
--

INSERT INTO `technicians` (`id`, `user_id`, `department_id`, `specialty`) VALUES
(1, 27, 4, 'Xét nghiệm & Chẩn đoán hình ảnh');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','doctor','nurse','patient','receptionist','pharmacist','technician','director','cashier') NOT NULL DEFAULT 'patient',
  `status` enum('active','locked','suspended') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Quản trị hệ thống', 'admin@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000001', 'admin', 'active', '2026-03-05 08:36:40', '2026-05-27 15:13:00', NULL),
(2, 'Bác sĩ Nguyễn Văn An', 'doctor1@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000002', 'doctor', 'active', '2026-03-05 08:36:40', '2026-05-27 15:13:00', NULL),
(3, 'Bác sĩ Trần Thị Bình', 'doctor2@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000003', 'doctor', 'active', '2026-03-05 08:36:40', '2026-05-27 15:13:00', NULL),
(4, 'Bác sĩ Phạm Văn Hùng', 'doctor3@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000004', 'doctor', 'active', '2026-03-05 08:36:40', '2026-05-27 15:13:00', NULL),
(5, 'Y tá Lê Thị Chi', 'nurse1@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '090000003', 'nurse', 'active', '2026-03-05 08:36:40', '2026-05-27 17:05:54', NULL),
(6, 'Y tá Nguyễn Thị Lan', 'nurse2@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000006', 'nurse', 'active', '2026-03-05 08:36:40', '2026-05-27 15:13:00', NULL),
(7, 'Phạm Minh Đức', 'benhnhan1@gmail.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000007', 'patient', 'active', '2026-03-05 08:36:40', '2026-05-27 15:13:00', NULL),
(8, 'Nguyễn Thị Hoa', 'benhnhan2@gmail.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000008', 'patient', 'active', '2026-03-05 08:36:40', '2026-05-27 15:13:00', NULL),
(9, 'Trần Văn Nam', 'benhnhan3@gmail.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000009', 'patient', 'active', '2026-03-05 08:36:40', '2026-05-27 15:13:00', NULL),
(10, 'Lê Thị Mai', 'benhnhan4@gmail.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000010', 'patient', 'active', '2026-03-05 08:36:40', '2026-05-27 15:13:00', NULL),
(11, 'Trịnh Văn Vũ', 'vu@gmail.com', '$2y$12$AQKbxupa.fzqXf/j8aY0OukcNV8N0VdEmeLV/n58uhuk8EY4WQm8G', '0356478461', 'patient', 'active', '2026-03-06 10:43:26', '2026-05-02 17:59:27', '2026-05-02 17:59:27'),
(12, 'Lễ tân Nguyễn Thị Hương', 'letan@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000012', 'receptionist', 'active', '2026-05-02 16:17:50', '2026-05-27 15:13:00', NULL),
(13, 'Dược sĩ Trần Văn Minh', 'duocsi@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000013', 'pharmacist', 'active', '2026-05-02 16:17:50', '2026-05-27 15:13:00', NULL),
(14, 'Bác sĩ Lê Quang Bảo', 'doctor4@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000014', 'doctor', 'active', '2026-05-02 16:18:00', '2026-05-27 15:13:00', NULL),
(15, 'Bác sĩ Đinh Thu Trà', 'doctor5@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000015', 'doctor', 'active', '2026-05-02 16:18:00', '2026-05-27 15:13:00', NULL),
(16, 'Y tá Trần Quang Đại', 'nurse3@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000016', 'nurse', 'active', '2026-05-02 16:18:00', '2026-05-27 15:13:00', NULL),
(17, 'Lâm Gia Khang', 'benhnhan5@gmail.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000017', 'patient', 'active', '2026-05-02 16:18:00', '2026-05-27 15:13:00', NULL),
(18, 'Nguyễn Minh Tuấn', 'benhnhan6@gmail.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000018', 'patient', 'active', '2026-05-02 16:18:00', '2026-05-27 15:13:00', NULL),
(19, 'Vũ Thanh Thảo', 'benhnhan7@gmail.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000019', 'patient', 'active', '2026-05-02 16:18:00', '2026-05-27 15:13:00', NULL),
(20, 'Đặng Hoàng Yến', 'benhnhan8@gmail.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '090000201', 'patient', 'active', '2026-05-02 16:18:00', '2026-05-27 15:58:28', NULL),
(21, 'Hoàng Trọng Nghĩa', 'benhnhan9@gmail.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000021', 'patient', 'active', '2026-05-02 16:18:00', '2026-05-27 15:13:00', NULL),
(22, 'Phan Bích Ngọc', 'benhnhan10@gmail.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000022', 'patient', 'active', '2026-05-02 16:18:00', '2026-05-27 15:13:00', NULL),
(23, 'Đoàn Hữu Phước', 'benhnhan11@gmail.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000023', 'patient', 'active', '2026-05-02 16:18:00', '2026-05-27 15:13:00', NULL),
(24, 'Trương Thị Hà', 'benhnhan12@gmail.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000024', 'patient', 'active', '2026-05-02 16:18:00', '2026-05-27 15:13:00', NULL),
(26, 'Nguyễn Văn Vũ', 'trinhvanvu2018v@gmail.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0817331013', 'patient', 'active', '2026-05-07 14:41:41', '2026-05-27 15:13:00', NULL),
(27, 'KTV Nguyễn Văn Tâm', 'ktv@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000030', 'technician', 'active', '2026-05-27 14:49:42', '2026-05-27 17:13:37', NULL),
(28, 'GĐ Trần Quốc Việt', 'giamdoc@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0900000031', 'director', 'active', '2026-05-27 14:49:42', '2026-05-27 17:13:37', NULL),
(29, 'Thu ngân Lê Thị Hương', 'thungan@benhvien.com', '$2y$10$ZZQDi1BIQpZ3ueCU2VKA2.EuTd20pFix1wxAaqWxggozeXSNFVk4e', '0912345678', 'cashier', 'active', '2026-05-27 15:09:11', '2026-05-27 17:13:37', NULL),
(30, 'Phan Tấn Trung', 'phantantrung@gmail.com', '$2y$12$w7TfYBfOdTm8MYcgAFSSLeI338aXnkHh.nWiwH6v2GesrldO0NXmW', '21321321', 'doctor', 'active', '2026-05-27 16:12:40', '2026-05-27 16:12:40', NULL),
(31, 'Nguyễn Chí Thuận', 'thuanngu@gmail.com', '$2y$12$NnvBY9Efa3dr0XR5c8uJh.94bHlantTOMKZv929pw8WJcBtqoYd3C', '0817331018', 'patient', 'active', '2026-05-28 09:13:24', '2026-05-28 09:13:24', NULL),
(32, 'Nguyễn Chí Thuận 1', 'thuangu1@gmail.com', '$2y$12$owSSBnPVt2yTr32ZiswtFepxsLVGzNiDIyqV6aFxML2z7GG.6sWD.', '21321321', 'patient', 'active', '2026-05-28 09:24:24', '2026-05-28 09:24:24', NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admissions`
--
ALTER TABLE `admissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `bed_id` (`bed_id`),
  ADD KEY `status` (`status`),
  ADD KEY `fk_adm_created_by` (`created_by`),
  ADD KEY `fk_adm_updated_by` (`updated_by`);

--
-- Chỉ mục cho bảng `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_doc_appt` (`doctor_id`,`appointment_date`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `appointment_date` (`appointment_date`),
  ADD KEY `status` (`status`),
  ADD KEY `doctor_status` (`doctor_id`,`status`),
  ADD KEY `fk_appt_created_by` (`created_by`),
  ADD KEY `fk_appt_updated_by` (`updated_by`);

--
-- Chỉ mục cho bảng `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `table_name` (`table_name`);

--
-- Chỉ mục cho bảng `beds`
--
ALTER TABLE `beds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Chỉ mục cho bảng `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Chỉ mục cho bảng `doctor_departments`
--
ALTER TABLE `doctor_departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_doc_dept` (`doctor_id`,`department_id`),
  ADD KEY `fk_dd_dept` (`department_id`);

--
-- Chỉ mục cho bảng `doctor_shifts`
--
ALTER TABLE `doctor_shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Chỉ mục cho bảng `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `examination_rooms`
--
ALTER TABLE `examination_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Chỉ mục cho bảng `icd10_codes`
--
ALTER TABLE `icd10_codes`
  ADD PRIMARY KEY (`code`);

--
-- Chỉ mục cho bảng `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `status` (`status`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `fk_inv_admission` (`admission_id`),
  ADD KEY `fk_inv_creator` (`created_by`),
  ADD KEY `fk_inv_updated_by` (`updated_by`),
  ADD KEY `fk_invoices_prescriptions` (`prescription_id`);

--
-- Chỉ mục cho bảng `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Chỉ mục cho bảng `lab_orders`
--
ALTER TABLE `lab_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `status` (`status`);

--
-- Chỉ mục cho bảng `lab_results`
--
ALTER TABLE `lab_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_order_id` (`lab_order_id`),
  ADD KEY `technician_id` (`technician_id`);

--
-- Chỉ mục cho bảng `medical_devices`
--
ALTER TABLE `medical_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Chỉ mục cho bảng `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `fk_mr_created_by` (`created_by`),
  ADD KEY `fk_mr_updated_by` (`updated_by`),
  ADD KEY `fk_medical_records_icd10` (`icd10_code`);

--
-- Chỉ mục cho bảng `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Chỉ mục cho bảng `nurses`
--
ALTER TABLE `nurses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Chỉ mục cho bảng `nurse_shifts`
--
ALTER TABLE `nurse_shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_nurse_shift` (`nurse_id`,`shift_id`),
  ADD KEY `fk_ns_shift` (`shift_id`);

--
-- Chỉ mục cho bảng `nursing_records`
--
ALTER TABLE `nursing_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admission_id` (`admission_id`),
  ADD KEY `nurse_id` (`nurse_id`);

--
-- Chỉ mục cho bảng `online_meetings`
--
ALTER TABLE `online_meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `meeting_id` (`meeting_id`),
  ADD KEY `start_time` (`start_time`);

--
-- Chỉ mục cho bảng `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `date_of_birth` (`date_of_birth`);

--
-- Chỉ mục cho bảng `patient_services`
--
ALTER TABLE `patient_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `service_date` (`service_date`);

--
-- Chỉ mục cho bảng `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medical_record_id` (`medical_record_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `fk_pres_approved_by` (`approved_by`);

--
-- Chỉ mục cho bảng `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescription_id` (`prescription_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Chỉ mục cho bảng `queue_tickets`
--
ALTER TABLE `queue_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ticket_date` (`ticket_number`,`queue_date`),
  ADD KEY `fk_qt_patient` (`patient_id`),
  ADD KEY `fk_qt_dept` (`department_id`),
  ADD KEY `fk_qt_doctor` (`doctor_id`),
  ADD KEY `fk_qt_exroom` (`examination_room_id`),
  ADD KEY `fk_qt_appt` (`appointment_id`),
  ADD KEY `fk_qt_creator` (`created_by`),
  ADD KEY `idx_queue_date_status` (`queue_date`,`status`),
  ADD KEY `idx_department_date` (`department_id`,`queue_date`);

--
-- Chỉ mục cho bảng `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Chỉ mục cho bảng `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shift_date` (`shift_date`),
  ADD KEY `department_id` (`department_id`);

--
-- Chỉ mục cho bảng `technicians`
--
ALTER TABLE `technicians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `phone` (`phone`),
  ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admissions`
--
ALTER TABLE `admissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=309;

--
-- AUTO_INCREMENT cho bảng `beds`
--
ALTER TABLE `beds`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `doctor_departments`
--
ALTER TABLE `doctor_departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `doctor_shifts`
--
ALTER TABLE `doctor_shifts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `examination_rooms`
--
ALTER TABLE `examination_rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `lab_orders`
--
ALTER TABLE `lab_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `lab_results`
--
ALTER TABLE `lab_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `medical_devices`
--
ALTER TABLE `medical_devices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `nurses`
--
ALTER TABLE `nurses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `nurse_shifts`
--
ALTER TABLE `nurse_shifts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nursing_records`
--
ALTER TABLE `nursing_records`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `online_meetings`
--
ALTER TABLE `online_meetings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `patient_services`
--
ALTER TABLE `patient_services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `prescription_items`
--
ALTER TABLE `prescription_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `queue_tickets`
--
ALTER TABLE `queue_tickets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `technicians`
--
ALTER TABLE `technicians`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `admissions`
--
ALTER TABLE `admissions`
  ADD CONSTRAINT `fk_adm_bed` FOREIGN KEY (`bed_id`) REFERENCES `beds` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adm_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adm_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adm_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adm_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appt_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appt_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appt_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appt_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `beds`
--
ALTER TABLE `beds`
  ADD CONSTRAINT `fk_beds_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `fk_doctors_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doctors_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `doctor_departments`
--
ALTER TABLE `doctor_departments`
  ADD CONSTRAINT `fk_dd_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dd_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `doctor_shifts`
--
ALTER TABLE `doctor_shifts`
  ADD CONSTRAINT `fk_ds_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ds_shift` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `examination_rooms`
--
ALTER TABLE `examination_rooms`
  ADD CONSTRAINT `fk_exroom_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_exroom_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_inv_admission` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inv_appt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inv_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inv_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inv_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invoices_prescriptions` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `fk_ii_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ii_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ii_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ii_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `lab_orders`
--
ALTER TABLE `lab_orders`
  ADD CONSTRAINT `fk_lo_appt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lo_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lo_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `lab_results`
--
ALTER TABLE `lab_results`
  ADD CONSTRAINT `fk_lr_order` FOREIGN KEY (`lab_order_id`) REFERENCES `lab_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lr_tech` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `medical_devices`
--
ALTER TABLE `medical_devices`
  ADD CONSTRAINT `fk_mdev_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `fk_medical_records_icd10` FOREIGN KEY (`icd10_code`) REFERENCES `icd10_codes` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mr_appt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mr_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mr_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mr_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mr_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `nurses`
--
ALTER TABLE `nurses`
  ADD CONSTRAINT `fk_nurses_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nurses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `nurse_shifts`
--
ALTER TABLE `nurse_shifts`
  ADD CONSTRAINT `fk_ns_nurse` FOREIGN KEY (`nurse_id`) REFERENCES `nurses` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ns_shift` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `nursing_records`
--
ALTER TABLE `nursing_records`
  ADD CONSTRAINT `fk_nr_admission` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nr_nurse` FOREIGN KEY (`nurse_id`) REFERENCES `nurses` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `online_meetings`
--
ALTER TABLE `online_meetings`
  ADD CONSTRAINT `fk_om_appt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patients_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `patient_services`
--
ALTER TABLE `patient_services`
  ADD CONSTRAINT `fk_ps_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ps_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ps_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `fk_pres_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pres_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pres_record` FOREIGN KEY (`medical_record_id`) REFERENCES `medical_records` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD CONSTRAINT `fk_pi_med` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pi_pres` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `queue_tickets`
--
ALTER TABLE `queue_tickets`
  ADD CONSTRAINT `fk_qt_appt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_qt_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_qt_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_qt_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_qt_exroom` FOREIGN KEY (`examination_room_id`) REFERENCES `examination_rooms` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_qt_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_rooms_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `fk_shifts_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `technicians`
--
ALTER TABLE `technicians`
  ADD CONSTRAINT `fk_tech_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tech_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
