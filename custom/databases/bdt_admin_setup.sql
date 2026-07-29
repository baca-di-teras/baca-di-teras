CREATE TABLE IF NOT EXISTS `bdt_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password for 'admin' is 'admin123'
INSERT IGNORE INTO `bdt_admins` (`username`, `password_hash`, `name`, `role`) 
VALUES ('admin', '$2y$10$AGw.j9gBVkVCEbMkVpEPXeEzCPefukArzqSzPPLBfz7aU5w8GuO3.', 'Administrator', 'super_admin');
