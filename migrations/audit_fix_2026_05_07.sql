-- =========================================================
-- NovaCare System Audit — Database Migration
-- Ngày: 2026-05-07
-- =========================================================

SET FOREIGN_KEY_CHECKS=0;

-- 1. FIX: Thêm 'emergency' vào enum appointments.status
ALTER TABLE `appointments` MODIFY COLUMN `status` 
  ENUM('pending','confirmed','cancelled','completed','emergency') DEFAULT 'pending';

-- 2. NEW: Bảng nurse_shifts
CREATE TABLE IF NOT EXISTS `nurse_shifts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nurse_id` INT NOT NULL,
  `shift_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_nurse_shift` (`nurse_id`, `shift_id`),
  CONSTRAINT `fk_ns_nurse` FOREIGN KEY (`nurse_id`) REFERENCES `nurses`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_ns_shift` FOREIGN KEY (`shift_id`) REFERENCES `shifts`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 3. ADD: status cho users
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='hospital_management' AND TABLE_NAME='users' AND COLUMN_NAME='status');
SET @sql = IF(@col_exists = 0, "ALTER TABLE `users` ADD COLUMN `status` ENUM('active','locked','suspended') DEFAULT 'active' AFTER `role`", 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. ADD: status cho prescriptions
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='hospital_management' AND TABLE_NAME='prescriptions' AND COLUMN_NAME='status');
SET @sql = IF(@col_exists = 0, "ALTER TABLE `prescriptions` ADD COLUMN `status` ENUM('draft','dispensed','cancelled') DEFAULT 'draft' AFTER `doctor_id`", 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5. ADD: emergency_contact cho patients
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='hospital_management' AND TABLE_NAME='patients' AND COLUMN_NAME='emergency_contact');
SET @sql = IF(@col_exists = 0, "ALTER TABLE `patients` ADD COLUMN `emergency_contact` VARCHAR(100) DEFAULT NULL AFTER `medical_history`", 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 6. ADD: insurance_number cho patients
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='hospital_management' AND TABLE_NAME='patients' AND COLUMN_NAME='insurance_number');
SET @sql = IF(@col_exists = 0, "ALTER TABLE `patients` ADD COLUMN `insurance_number` VARCHAR(50) DEFAULT NULL AFTER `emergency_contact`", 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 7. ADD: quantity vào prescription_items
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='hospital_management' AND TABLE_NAME='prescription_items' AND COLUMN_NAME='quantity');
SET @sql = IF(@col_exists = 0, "ALTER TABLE `prescription_items` ADD COLUMN `quantity` INT DEFAULT 1 AFTER `medicine_id`", 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 8. Backfill prescription_items quantity
UPDATE `prescription_items` SET `quantity` = 15 WHERE `id` = 1 AND `quantity` = 1;
UPDATE `prescription_items` SET `quantity` = 30 WHERE `id` = 2 AND `quantity` = 1;
UPDATE `prescription_items` SET `quantity` = 14 WHERE `id` = 3 AND `quantity` = 1;
UPDATE `prescription_items` SET `quantity` = 60 WHERE `id` = 4 AND `quantity` = 1;
UPDATE `prescription_items` SET `quantity` = 10 WHERE `id` = 5 AND `quantity` = 1;
UPDATE `prescription_items` SET `quantity` = 60 WHERE `id` = 6 AND `quantity` = 1;
UPDATE `prescription_items` SET `quantity` = 28 WHERE `id` = 7 AND `quantity` = 1;
UPDATE `prescription_items` SET `quantity` = 6  WHERE `id` = 8 AND `quantity` = 1;
UPDATE `prescription_items` SET `quantity` = 10 WHERE `id` = 9 AND `quantity` = 1;

SET FOREIGN_KEY_CHECKS=1;
