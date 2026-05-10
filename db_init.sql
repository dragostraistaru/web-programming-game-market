-- DB initialization for GameMarket application
-- Run this in phpMyAdmin or mysql CLI to create the database and users table

CREATE DATABASE IF NOT EXISTS `jocuri_app` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `jocuri_app`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('user','admin','vanzator','cumparator','ambele') NOT NULL DEFAULT 'cumparator',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `remember_token` VARCHAR(128) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Example seed user (password: TestPass123)
-- To insert use the hash produced by PHP's password_hash. Example shown here is placeholder.
-- INSERT INTO users (username, email, password_hash, role) VALUES ('testuser', 'test@example.com', '$2y$10$...hash...', 'cumparator');

-- If the users table already exists, run this once:
-- ALTER TABLE users MODIFY role ENUM('user','admin','vanzator','cumparator','ambele') NOT NULL DEFAULT 'cumparator';

