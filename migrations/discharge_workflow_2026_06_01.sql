-- =========================================================
-- NovaCare — Tích hợp quy trình Xuất viện 3 bước
-- Ngày: 2026-06-01
-- Mô tả: Thêm cột discharge_ordered và discharge_ordered_at vào bảng admissions
-- =========================================================

ALTER TABLE `admissions` 
    ADD COLUMN `discharge_ordered` TINYINT(1) DEFAULT 0 AFTER `status`,
    ADD COLUMN `discharge_ordered_at` DATETIME DEFAULT NULL AFTER `discharge_ordered`;
