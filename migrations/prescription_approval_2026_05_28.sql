-- =========================================================
-- NovaCare — Phase 3: Duyệt đơn thuốc của Dược sĩ
-- Ngày: 2026-05-28
-- Mô tả: Thêm cột và trạng thái approved cho đơn thuốc
-- =========================================================

-- Thay đổi cấu trúc Enum trạng thái
ALTER TABLE `prescriptions` MODIFY COLUMN `status` ENUM('draft','paid','approved','dispensed','cancelled') DEFAULT 'draft';

-- Thêm các cột lưu thông tin duyệt
ALTER TABLE `prescriptions`
    ADD COLUMN `approved_by` INT DEFAULT NULL AFTER `doctor_id`,
    ADD COLUMN `approved_at` DATETIME DEFAULT NULL AFTER `approved_by`,
    ADD COLUMN `pharmacist_notes` TEXT DEFAULT NULL AFTER `approved_at`;

-- Thêm khóa ngoại cho người duyệt (Dược sĩ)
ALTER TABLE `prescriptions`
    ADD CONSTRAINT `fk_pres_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
