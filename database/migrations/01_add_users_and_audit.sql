CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `google_id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `profile_picture` text DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `google_id` (`google_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admin_audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `target_type` varchar(50) NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `payload_json` text DEFAULT NULL,
  `created_ip` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `discount_records` 
  ADD COLUMN `user_id` int(11) DEFAULT NULL AFTER `id`,
  ADD KEY `user_id` (`user_id`);

-- Add foreign key constraints if needed (optional for shared host, but good for integrity)
-- ALTER TABLE `users` ADD CONSTRAINT `fk_users_department` FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL;
-- ALTER TABLE `discount_records` ADD CONSTRAINT `fk_records_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL;
