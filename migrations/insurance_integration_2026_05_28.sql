-- =========================================================
-- NovaCare — Phase 2: Tích hợp BHYT vào Hóa đơn
-- Ngày: 2026-05-28
-- Mô tả: Thêm các trường BHYT vào bảng invoices
-- =========================================================

ALTER TABLE `invoices` 
    ADD COLUMN `insurance_number` VARCHAR(50) DEFAULT NULL AFTER `prescription_id`,
    ADD COLUMN `insurance_rate` DECIMAL(5,2) DEFAULT 0.00 AFTER `insurance_number`,
    ADD COLUMN `insurance_coverage` DECIMAL(12,2) DEFAULT 0.00 AFTER `insurance_rate`,
    ADD COLUMN `patient_payment` DECIMAL(12,2) DEFAULT 0.00 AFTER `insurance_coverage`;
