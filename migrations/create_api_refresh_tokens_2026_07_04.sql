-- SQL Migration: Create api_refresh_tokens table
-- Created At: 2026-07-04

CREATE TABLE IF NOT EXISTS `api_refresh_tokens` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `refresh_token_hash` VARCHAR(64) NOT NULL UNIQUE,
    `device_name` VARCHAR(255) DEFAULT NULL,
    `user_agent` VARCHAR(255) DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `expires_at` TIMESTAMP NOT NULL,
    `revoked_at` TIMESTAMP NULL DEFAULT NULL,
    `last_used_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `replaced_by_token_id` INT DEFAULT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_art_user_id` (`user_id`),
    INDEX `idx_art_hash` (`refresh_token_hash`),
    INDEX `idx_art_expires_at` (`expires_at`),
    INDEX `idx_art_revoked_at` (`revoked_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
