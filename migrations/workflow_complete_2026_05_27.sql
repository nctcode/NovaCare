-- =========================================================
-- NovaCare — Workflow Complete Migration
-- Ngày: 2026-05-27
-- Mô tả: Thêm roles mới, sửa schema, tạo bảng mới
-- =========================================================

SET FOREIGN_KEY_CHECKS=0;

-- =============================================
-- 1. THÊM ROLE MỚI vào users.role ENUM
-- =============================================
ALTER TABLE `users` MODIFY COLUMN `role`
  ENUM('admin','doctor','nurse','patient','receptionist','pharmacist','technician','director') NOT NULL;

-- =============================================
-- 2. SỬA CỘT THIẾU: medicines.reserved
-- =============================================
SET @col = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='medicines' AND COLUMN_NAME='reserved');
SET @sql = IF(@col = 0, "ALTER TABLE `medicines` ADD COLUMN `reserved` INT DEFAULT 0 AFTER `quantity`", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- 3. SỬA CỘT THIẾU: invoices.prescription_id
-- =============================================
SET @col = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='invoices' AND COLUMN_NAME='prescription_id');
SET @sql = IF(@col = 0, "ALTER TABLE `invoices` ADD COLUMN `prescription_id` INT DEFAULT NULL AFTER `admission_id`", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- 4. SỬA CỘT THIẾU: doctors.is_head
-- =============================================
SET @col = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='doctors' AND COLUMN_NAME='is_head');
SET @sql = IF(@col = 0, "ALTER TABLE `doctors` ADD COLUMN `is_head` TINYINT(1) DEFAULT 0 AFTER `experience_years`", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- 5. SỬA CỘT THIẾU: nurses.is_head
-- =============================================
SET @col = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='nurses' AND COLUMN_NAME='is_head');
SET @sql = IF(@col = 0, "ALTER TABLE `nurses` ADD COLUMN `is_head` TINYINT(1) DEFAULT 0 AFTER `department_id`", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- 6. SỬA CỘT THIẾU: audit_logs.log_type
-- =============================================
SET @col = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='audit_logs' AND COLUMN_NAME='log_type');
SET @sql = IF(@col = 0, "ALTER TABLE `audit_logs` ADD COLUMN `log_type` VARCHAR(30) DEFAULT 'data_change' AFTER `user_id`", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- 7. SỬA CỘT THIẾU: audit_logs.ip_address
-- =============================================
SET @col = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='audit_logs' AND COLUMN_NAME='ip_address');
SET @sql = IF(@col = 0, "ALTER TABLE `audit_logs` ADD COLUMN `ip_address` VARCHAR(45) DEFAULT NULL AFTER `new_values`", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================
-- 8. SỬA prescriptions.status ENUM thêm 'paid'
-- =============================================
ALTER TABLE `prescriptions` MODIFY COLUMN `status`
  ENUM('draft','paid','dispensed','cancelled') DEFAULT 'draft';

-- =============================================
-- 9. BẢNG MỚI: technicians
-- =============================================
CREATE TABLE IF NOT EXISTS `technicians` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `department_id` INT DEFAULT NULL,
  `specialty` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `fk_tech_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_tech_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =============================================
-- 10. BẢNG MỚI: lab_orders (Chỉ định CLS)
-- =============================================
CREATE TABLE IF NOT EXISTS `lab_orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `patient_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `appointment_id` INT DEFAULT NULL,
  `order_type` ENUM('lab_test','imaging') NOT NULL,
  `test_name` VARCHAR(200) NOT NULL,
  `priority` ENUM('normal','urgent') DEFAULT 'normal',
  `status` ENUM('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `notes` TEXT,
  `created_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `appointment_id` (`appointment_id`),
  KEY `status` (`status`),
  CONSTRAINT `fk_lo_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_lo_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_lo_appt` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =============================================
-- 11. BẢNG MỚI: lab_results (Kết quả CLS)
-- =============================================
CREATE TABLE IF NOT EXISTS `lab_results` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `lab_order_id` INT NOT NULL,
  `technician_id` INT NOT NULL,
  `result_text` TEXT,
  `result_value` VARCHAR(100) DEFAULT NULL,
  `normal_range` VARCHAR(100) DEFAULT NULL,
  `unit` VARCHAR(50) DEFAULT NULL,
  `conclusion` ENUM('normal','abnormal','critical') DEFAULT 'normal',
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lab_order_id` (`lab_order_id`),
  KEY `technician_id` (`technician_id`),
  CONSTRAINT `fk_lr_order` FOREIGN KEY (`lab_order_id`) REFERENCES `lab_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_lr_tech` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =============================================
-- 12. BẢNG MỚI: nursing_records (Chăm sóc ĐD)
-- =============================================
CREATE TABLE IF NOT EXISTS `nursing_records` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `admission_id` INT NOT NULL,
  `nurse_id` INT NOT NULL,
  `record_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `temperature` DECIMAL(4,1) DEFAULT NULL,
  `blood_pressure_sys` INT DEFAULT NULL,
  `blood_pressure_dia` INT DEFAULT NULL,
  `heart_rate` INT DEFAULT NULL,
  `respiratory_rate` INT DEFAULT NULL,
  `spo2` INT DEFAULT NULL,
  `care_notes` TEXT,
  `medication_given` TEXT,
  `diet_notes` VARCHAR(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admission_id` (`admission_id`),
  KEY `nurse_id` (`nurse_id`),
  CONSTRAINT `fk_nr_admission` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nr_nurse` FOREIGN KEY (`nurse_id`) REFERENCES `nurses` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =============================================
-- 13. DỮ LIỆU MẪU: Tài khoản KTV & BGĐ
-- =============================================
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `role`, `status`) VALUES
('KTV Nguyễn Văn Tâm', 'ktv@benhvien.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000030', 'technician', 'active'),
('GĐ Trần Quốc Việt', 'giamdoc@benhvien.com', '$2y$12$Ss0F7b28oJl8dDUy1vFL2O8N.7uFGoZMqQm4LqNaCNj0o7rtpCbwG', '0900000031', 'director', 'active');

-- Thêm KTV vào bảng technicians
INSERT INTO `technicians` (`user_id`, `department_id`, `specialty`)
SELECT u.id, 4, 'Xét nghiệm & Chẩn đoán hình ảnh'
FROM `users` u WHERE u.email = 'ktv@benhvien.com' LIMIT 1;

SET FOREIGN_KEY_CHECKS=1;
