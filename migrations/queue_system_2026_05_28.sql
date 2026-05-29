-- =========================================================
-- NovaCare — Phase 1: Hệ thống Số thứ tự + Phòng khám
-- Ngày: 2026-05-28
-- Mô tả: Tạo bảng examination_rooms và queue_tickets
-- =========================================================

SET FOREIGN_KEY_CHECKS=0;

-- =============================================
-- 1. BẢNG MỚI: examination_rooms (Phòng khám ngoại trú)
-- =============================================
CREATE TABLE IF NOT EXISTS `examination_rooms` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `room_name` VARCHAR(50) NOT NULL,
  `department_id` INT DEFAULT NULL,
  `doctor_id` INT DEFAULT NULL,
  `capacity` INT DEFAULT 1,
  `status` ENUM('active','inactive','maintenance') DEFAULT 'active',
  `notes` VARCHAR(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),
  KEY `doctor_id` (`doctor_id`),
  CONSTRAINT `fk_exroom_dept` FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_exroom_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =============================================
-- 2. BẢNG MỚI: queue_tickets (Phiếu số thứ tự)
-- =============================================
CREATE TABLE IF NOT EXISTS `queue_tickets` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `patient_id` INT NOT NULL,
  `ticket_number` INT NOT NULL,
  `department_id` INT DEFAULT NULL,
  `doctor_id` INT DEFAULT NULL,
  `examination_room_id` INT DEFAULT NULL,
  `appointment_id` INT DEFAULT NULL,
  `status` ENUM('waiting','called','in_progress','completed','cancelled') DEFAULT 'waiting',
  `priority` ENUM('normal','priority','emergency') DEFAULT 'normal',
  `queue_date` DATE NOT NULL,
  `check_in_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `called_at` DATETIME DEFAULT NULL,
  `started_at` DATETIME DEFAULT NULL,
  `completed_at` DATETIME DEFAULT NULL,
  `insurance_verified` TINYINT(1) DEFAULT 0,
  `notes` VARCHAR(200) DEFAULT NULL,
  `created_by` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_qt_patient` (`patient_id`),
  KEY `fk_qt_dept` (`department_id`),
  KEY `fk_qt_doctor` (`doctor_id`),
  KEY `fk_qt_exroom` (`examination_room_id`),
  KEY `fk_qt_appt` (`appointment_id`),
  KEY `fk_qt_creator` (`created_by`),
  KEY `idx_queue_date_status` (`queue_date`, `status`),
  KEY `idx_department_date` (`department_id`, `queue_date`),
  UNIQUE KEY `uk_ticket_date` (`ticket_number`, `queue_date`),
  CONSTRAINT `fk_qt_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_qt_dept` FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_qt_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_qt_exroom` FOREIGN KEY (`examination_room_id`) REFERENCES `examination_rooms`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_qt_appt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_qt_creator` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =============================================
-- 3. DỮ LIỆU MẪU: Phòng khám ngoại trú
-- =============================================
INSERT INTO `examination_rooms` (`room_name`, `department_id`, `doctor_id`, `notes`) VALUES
('Phòng 1', 1, 1, 'Phòng khám Tim mạch'),
('Phòng 2', 2, 2, 'Phòng khám Thần kinh'),
('Phòng 3', 4, 3, 'Phòng khám Nội tổng quát'),
('Phòng 4', 3, 4, 'Phòng khám Nhi khoa'),
('Phòng 5', 1, 5, 'Phòng khám Tim mạch 2');

SET FOREIGN_KEY_CHECKS=1;
