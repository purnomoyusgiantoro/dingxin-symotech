-- ==========================================================
-- DINGXIN MULTI DISTRIBUSI - PRODUCTION DATABASE EXPORT (MySQL)
-- Target Domain : dingxin.symotech.web.id
-- Generated     : 2026-10-13 22:37:57
-- Engine        : MySQL 8.x / MariaDB 10.x compatible
-- ==========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";


-- ---------------------------------------------------------
-- Table structure for `users`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'driver',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `users` (20 records)
INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'General Manager', 'gm', 'gm@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'gm', 1, 'hJoBS6qnGhto6qfJULDNOXVUjBFbAAfeqsvXh4LFM9vK63zwiptUGicDzBqJ', '2026-10-03 16:08:48', '2026-10-06 20:20:43'),
(2, 'Kasir Utama', 'kasir', 'kasir@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'cashier', 1, '7cRIcYaZbliI6ORH5TNjzb18Rkxpu1bIUNIzGpiwlwPn0gpemN9vUrDAuTam', '2026-10-03 16:08:48', '2026-10-06 20:20:43'),
(3, 'Admin Penjualan Utama', 'admin', 'admin@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'sales_admin', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(4, 'Admin Penjualan 1', 'admin1', 'admin1@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'sales_admin', 1, 'YgZtQ79G8stAHUPpKYG2yu9ZsmwgVDKhYKidGc3wVxUFMRFXXHZzU9UBHtkO', '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(5, 'Admin Penjualan 2', 'admin2', 'admin2@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'sales_admin', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(6, 'Admin Penjualan 3', 'admin3', 'admin3@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'sales_admin', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(7, 'Sopir TGL1.2', 'TGL1.2', 'tgl1.2@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, 'J7FUR5zLPMTs0kSIIluGy1n0Ws9ivfNdWAHR4aI7ubRf3woxFWDg5PEbru1C', '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(8, 'Sopir TGL3.4', 'TGL3.4', 'tgl3.4@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(9, 'Sopir TGL5.6', 'TGL5.6', 'tgl5.6@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(10, 'Sopir TGL7.8', 'TGL7.8', 'tgl7.8@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(11, 'Sopir TGL9.10', 'TGL9.10', 'tgl9.10@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(12, 'Sopir TGL11.12', 'TGL11.12', 'tgl11.12@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(13, 'Sopir TGL13.14', 'TGL13.14', 'tgl13.14@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(14, 'Sopir BRS1.2', 'BRS1.2', 'brs1.2@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(15, 'Sopir BRS3.4', 'BRS3.4', 'brs3.4@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(16, 'Sopir BRS5.6', 'BRS5.6', 'brs5.6@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(17, 'Sopir BRS7.8', 'BRS7.8', 'brs7.8@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(18, 'Sopir BRS9.10', 'BRS9.10', 'brs9.10@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(19, 'Sopir BRS11.12', 'BRS11.12', 'brs11.12@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44'),
(20, 'Sopir BRS13.14', 'BRS13.14', 'brs13.14@symotech.web.id', NULL, '$2y$12$w5.d9Ht5P/1t1nK.thdFzO673A9rmCb.FSIu9J8BUUJy5twas3QRy', 'driver', 1, NULL, '2026-10-03 16:08:48', '2026-10-06 20:20:44');


-- ---------------------------------------------------------
-- Table structure for `password_reset_tokens`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------
-- Table structure for `sessions`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `sessions` (32 records)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Lp4ONQrYfiQgetNWu7jvLyXEkksEQGGKCVbqDws0', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiTDNzbmFGVnUyTFdvNEoxbkw5QlVzYmtPRk1jN2cyT3lhaXVOUXJzaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9yZXR1cm4taXRlbXMvMTIvZWRpdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQ7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiR3NS5kOUh0NVAvMXQxbksudGhkRnpPNjczQTlybUNiLkZTSXU5SjhCVVVKeTV0d2FzM1FSeSI7czo4OiJmaWxhbWVudCI7YTowOnt9fQ==', 1791555870),
('0cDW1FJQ98z8DdQMKKBfrMXKth9te6fVzXdQbAv1', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidllBVHZYSDZIWXNwYmpya0F3U1dJNmQ2M21GcktRcFNUR2NsWnpmbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kcml2ZXIvaGlzdG9yeSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjc7fQ==', 1791555878),
('wjAQixUClJ76I46vBrKaCx7ktfmFMfTtKMubQ8s3', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoicklXMVNqendpWFFIN0JJSUFmYzZTT3N3NERBcFBqZ0JhNkVLQ3hXRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYWlseS1zZXR0bGVtZW50LXN1bW1hcnkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkdzUuZDlIdDVQLzF0MW5LLnRoZEZ6TzY3M0E5cm1DYi5GU0l1OUo4QlVVSnk1dHdhczNRUnkiO3M6ODoiZmlsYW1lbnQiO2E6MDp7fX0=', 1791555903),
('XwwmoWyOq7yyqCNaK9cQD4NfCbK3j4CL1TOSBRRg', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiR1BOY0I2VGZCWkYwNVN0WlR4c2VEc2xXQzlGZ1ZHaXhFdjJDMzZPNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kcml2ZXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJHc1LmQ5SHQ1UC8xdDFuSy50aGRGek82NzNBOXJtQ2IuRlNJdTlKOEJVVUp5NXR3YXMzUVJ5Ijt9', 1791555909),
('lbvUbqjbWx8zXcqtCXE9VWD4L3pJhgGa6SRDjuR9', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTVB4UmNzNG1nY29vMDZzNkk5ZWVZQ3cyODk0Y0RheUMzTW5rU1FOZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kcml2ZXIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo3O30=', 1791555912),
('b3ltCx0kaSKUfwqQ7NiBGdVLllOGux4zeuJMAIL2', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSGpwSExLbmVvSEZVVncyZjhOb0tHc0lNSnRvaEwwbUR0WEV5VUpFViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiR3NS5kOUh0NVAvMXQxbksudGhkRnpPNjczQTlybUNiLkZTSXU5SjhCVVVKeTV0d2FzM1FSeSI7fQ==', 1791555914),
('NFqamYAKywzbpwpZscQTWndCyTqenrwKV5g6W6KG', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRm4zekJMajdKQWxjUkc4STJUMjl6aUFRWG5MdzE2QjRBSGpBaGFUUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1791555916),
('V47OsVoFx4ub0MMNPKitHdGT1dQylLW7ag0dZNtW', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiVTMwelNkVXFLa3JsS20yc1lwd3RUTVpTOUhockRta2QyYkpSc0pacSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9yZXR1cm4taXRlbXMvMTMvZWRpdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQ7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiR3NS5kOUh0NVAvMXQxbksudGhkRnpPNjczQTlybUNiLkZTSXU5SjhCVVVKeTV0d2FzM1FSeSI7czo4OiJmaWxhbWVudCI7YTowOnt9fQ==', 1791556284),
('op33hZozdEdPvG7qXxZ7PSSvX0BtqggQvvrVaSTf', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidG95bFkyQVlvY0hwZHh4OTBHYmk1WHNvbnpHeDRqZU5BMTA0VzFDUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kcml2ZXIvaGlzdG9yeSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjc7fQ==', 1791556292),
('7XKV3DHdzUHU6Xhf3CaAnzNfHv2vq3GhjPZUDLw9', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiTDZ1RGRMblg1clBYZHJ4eE9ZWTVPd1g4TmNhUWw5c2l6MWlVOFZMNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYWlseS1zZXR0bGVtZW50LXN1bW1hcnkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkdzUuZDlIdDVQLzF0MW5LLnRoZEZ6TzY3M0E5cm1DYi5GU0l1OUo4QlVVSnk1dHdhczNRUnkiO3M6ODoiZmlsYW1lbnQiO2E6MDp7fX0=', 1791556302),
('BLX6M1y32LxcUxRYGZHdA88Rk6ioPP9bhZJk6MwC', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicW5jNEFMSU1PckFaVHFHU3ptTjdOTG8zNWdTSlRYT1EyOE9PUW9zOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kcml2ZXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJHc1LmQ5SHQ1UC8xdDFuSy50aGRGek82NzNBOXJtQ2IuRlNJdTlKOEJVVUp5NXR3YXMzUVJ5Ijt9', 1791556307),
('QWeTvvUMj1Q3abu61tt8nruM1HIYDLnUkEUOZIuU', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRUhQcVY3a0kzZnhEbU5TZlpsVjl5MVAxckp6UElHcENPUVlOZHZKTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kcml2ZXIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo3O30=', 1791556310),
('GXdgoDvmi0xzypvi70FUCuN2E1q8SIBzQWbvB214', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicGxEREw5cEdnendmaFBCRU1WRkFsdk1ySFBCT2lSemdya1EzbUtxVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiR3NS5kOUh0NVAvMXQxbksudGhkRnpPNjczQTlybUNiLkZTSXU5SjhCVVVKeTV0d2FzM1FSeSI7fQ==', 1791556312),
('qeB6LRF9H6FWaRGlRC6PhZduE5xYnSs5hdFhZYBq', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaHVpbmZGTkhGN2pZaFVRaHBueExKZ0xIYjA1RlZtanRhYm9RWXNvOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1791556314),
('nVslFMsfM9KFqKErR3UtEgdGmh00hMhf6j5IhWOY', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiRlFqTGxMdm5DaHVZS3FPVURrZlZBclU4SEQ5dWxmZ0FWY1AyZXVWYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9yZXR1cm4taXRlbXMvMTQvZWRpdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQ7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiR3NS5kOUh0NVAvMXQxbksudGhkRnpPNjczQTlybUNiLkZTSXU5SjhCVVVKeTV0d2FzM1FSeSI7czo4OiJmaWxhbWVudCI7YTowOnt9fQ==', 1791556996),
('JsK0vClvrtv4Y4alrh9dx9hWo8iI0OcatwjC6EhN', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSTJKOTJ2TG5NdVhYekIzS1F2aUdDcGN4Qjh1SHNXRUtnWG1xRjBLVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kcml2ZXIvaGlzdG9yeSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjc7fQ==', 1791557004),
('C0eOXyYRg4Cka9Xjq5Qdgf47lVBnS3WOemT82f2m', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiQ01SR0U2V3hKcnR6N2kzNUp5U0ZFT0VHZmRHYkxFbFNKb2I1S3dGaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYWlseS1zZXR0bGVtZW50LXN1bW1hcnkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkdzUuZDlIdDVQLzF0MW5LLnRoZEZ6TzY3M0E5cm1DYi5GU0l1OUo4QlVVSnk1dHdhczNRUnkiO3M6ODoiZmlsYW1lbnQiO2E6MDp7fX0=', 1791557013),
('RrhXXiM8Qv3vHz4pLTqKJFFeWmPjd67gatVTGyk0', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibmU0VnVUVGlGTklmSFJqY0xVNmFCWlVqOUxEdHNlb2I4Y1RBdzU3cyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kcml2ZXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJHc1LmQ5SHQ1UC8xdDFuSy50aGRGek82NzNBOXJtQ2IuRlNJdTlKOEJVVUp5NXR3YXMzUVJ5Ijt9', 1791557018),
('vdLNuxy6ox179c8HQiC4XXASignafMv9AyBIitwY', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSEJYempXemY1YW9mMDIwSDRKa1lTNENlWE9MclNpYkw1dW5Ea2xQZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kcml2ZXIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo3O30=', 1791557020),
('Ct16qzUj1axC4HRbodcxF5MT2B21FSxu91n62R7S', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiekFjZFBOQU1pNmg5aW15YnNvOEUzSkpxSElhUUJLTjdObEQyczRrQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiR3NS5kOUh0NVAvMXQxbksudGhkRnpPNjczQTlybUNiLkZTSXU5SjhCVVVKeTV0d2FzM1FSeSI7fQ==', 1791557022),
('VygdSURWDnQGvqOo7iWhahs5ZuzbFeMXG4T7tiYB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQzVDNEZxM1ZDQ0dNU1J3RjVVUnk1OGNnYVdmMVFCd1NEYW1xYXlieiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1791557024),
('iihGDg39cUeORjs6Ky1lsLVRoygnFbNQmi2wflW8', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9168', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibmRlZmNEMDFLRnRiYmN3Rm5aeE1GMGNhVkt4RXhrQ0pETDlxWm9iNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1791557709),
('bjNAADXeFsl5SiFV19UU4YKBNf4OV7Jy0iyqTNxo', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9168', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOEg3UDBmTlpCN3dEeTFtMVRxbmNzelh4MjlqY0Z2cDU2Um1pV3BSViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1791557742),
('QrdB5aEvelHek7vKXtO5ZbFAPsLfAr63lQZgsu8e', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9168', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVVRnUXh0dDVWQnFBbWEyY2JBcFNTMm8xSXVlZzA2QmQ3dkpZN2FGTiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2FkbWluIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1791557742),
('P3hA3r5AlIqABhVEdD1BSQvwW6fBj92HLSoLWvoE', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiSlJuQU4zQURuOFd1ZTQ5R3pKVURGVTR3blM2V0hCcHMxd1ZMcGJFNyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYWlseS1kZWxpdmVyaWVzIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MztzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJHc1LmQ5SHQ1UC8xdDFuSy50aGRGek82NzNBOXJtQ2IuRlNJdTlKOEJVVUp5NXR3YXMzUVJ5IjtzOjY6InRhYmxlcyI7YToxOntzOjQ4OiIzM2I4YTA3Nzg4ZTgzYjFlZDFkNWRiNDM0NWY1YTVmY190b2dnbGVkX2NvbHVtbnMiO2E6MTp7czo4OiJ2ZXJpZmllciI7YToxOntzOjQ6Im5hbWUiO2I6MDt9fX19', 1791558684),
('5RdlbJWB4z8qJhwuzVBUjQ6wOny0SQAbM3SAEd1j', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoieklpclZnVnVWRlQ1bVJ5THhtMm5QbkpseVBTRER1U2pLWlIwUFpsUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9yZXR1cm4taXRlbXMvMTUvZWRpdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQ7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiR3NS5kOUh0NVAvMXQxbksudGhkRnpPNjczQTlybUNiLkZTSXU5SjhCVVVKeTV0d2FzM1FSeSI7czo4OiJmaWxhbWVudCI7YTowOnt9fQ==', 1791558496),
('RCxUJkkITimgM4IPeYxxEKPk5L52d7dqLG7Sa4pF', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZEFkY2RaN3VkY3NKZmlPWGpUSlR3WmJBb0NvazkxY3JabkJQbFN3byI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kcml2ZXIvaGlzdG9yeSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjc7fQ==', 1791558504),
('4QFHwJ0cipdVRGphm63dD5zxG9MwV2arZRTfJFWk', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiTDZHdXdiT3J2S2pLR0wwYzdIOEVYdzhpbmE5aGZvQ1dCc0N1amZrRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYWlseS1zZXR0bGVtZW50LXN1bW1hcnkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkdzUuZDlIdDVQLzF0MW5LLnRoZEZ6TzY3M0E5cm1DYi5GU0l1OUo4QlVVSnk1dHdhczNRUnkiO3M6ODoiZmlsYW1lbnQiO2E6MDp7fX0=', 1791558514),
('DPW8NcwJPstbLVacNWUa6AQQ1dvtmtOYGvXrJfd1', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQ1J0OGY0c1dSc1Yxcjd2c0ZxdjNkZ2FoNXU3S1RlMGNCZlg4RGNGcyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kcml2ZXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJHc1LmQ5SHQ1UC8xdDFuSy50aGRGek82NzNBOXJtQ2IuRlNJdTlKOEJVVUp5NXR3YXMzUVJ5Ijt9', 1791558519),
('47xCx3uoO0EUqFa5K5V0iTTcf3ezssxRSVt1Gz5z', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMHlRYmpUZ0VReUhoZXk3dzNHN0kyMW96bHhIQ1lpb1lTNnlobFZEVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kcml2ZXIiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo3O30=', 1791558521),
('5M0LfcCqOCmuNnt66MtYs8RYhMpLk7KbA9FzmGI5', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibUFDNHV5MFZod0NNeGhwUVE2SjNVdmhTZFlmYVF0MGl0Mk9ieVlIOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiR3NS5kOUh0NVAvMXQxbksudGhkRnpPNjczQTlybUNiLkZTSXU5SjhCVVVKeTV0d2FzM1FSeSI7fQ==', 1791558523),
('9DB0TKnILqXRR9FT2h4vM4CYgU7highY4KtTB2On', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSGJXU0VINDVWNENiR2JCT1pIRVBIT0tNdk5DeFF0aElUMjI4SThUUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1791558525);


-- ---------------------------------------------------------
-- Table structure for `cache`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `cache` (2 records)
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('livewire-rate-limiter:056fc329aaaa757d31db450f525da23fde4d1b36:timer', 'i:1791558551;', 1791558551),
('livewire-rate-limiter:056fc329aaaa757d31db450f525da23fde4d1b36', 'i:7;', 1791558551);


-- ---------------------------------------------------------
-- Table structure for `cache_locks`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------
-- Table structure for `jobs`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------
-- Table structure for `job_batches`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------
-- Table structure for `failed_jobs`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------
-- Table structure for `migrations`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `migrations` (4 records)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_10_03_000003_create_dingxin_tables', 1);


-- ---------------------------------------------------------
-- Table structure for `drivers`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `drivers`;
CREATE TABLE `drivers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `driver_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plate_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `cumulative_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drivers_driver_code_unique` (`driver_code`),
  KEY `drivers_user_id_foreign` (`user_id`),
  CONSTRAINT `drivers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `drivers` (14 records)
INSERT INTO `drivers` (`id`, `user_id`, `driver_code`, `area_code`, `phone_number`, `plate_number`, `is_active`, `cumulative_balance`, `created_at`, `updated_at`) VALUES
(1, 7, 'TGL1.2', 'TGL', '081202000000', 'G 8100 EZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(2, 8, 'TGL3.4', 'TGL', '081202000001', 'G 8101 EZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(3, 9, 'TGL5.6', 'TGL', '081202000002', 'G 8102 EZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(4, 10, 'TGL7.8', 'TGL', '081202000003', 'G 8103 EZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(5, 11, 'TGL9.10', 'TGL', '081202000004', 'G 8104 EZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(6, 12, 'TGL11.12', 'TGL', '081202000005', 'G 8105 EZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(7, 13, 'TGL13.14', 'TGL', '081202000006', 'G 8106 EZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(8, 14, 'BRS1.2', 'BRS', '081303000000', 'G 9100 BZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(9, 15, 'BRS3.4', 'BRS', '081303000001', 'G 9101 BZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(10, 16, 'BRS5.6', 'BRS', '081303000002', 'G 9102 BZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(11, 17, 'BRS7.8', 'BRS', '081303000003', 'G 9103 BZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(12, 18, 'BRS9.10', 'BRS', '081303000004', 'G 9104 BZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(13, 19, 'BRS11.12', 'BRS', '081303000005', 'G 9105 BZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(14, 20, 'BRS13.14', 'BRS', '081303000006', 'G 9106 BZ', 1, 0, '2026-10-03 16:08:48', '2026-10-03 16:08:48');


-- ---------------------------------------------------------
-- Table structure for `daily_deliveries`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `daily_deliveries`;
CREATE TABLE `daily_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `route_notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_deliveries_driver_id_date_unique` (`driver_id`,`date`),
  KEY `daily_deliveries_created_by_foreign` (`created_by`),
  KEY `daily_deliveries_driver_id_foreign` (`driver_id`),
  CONSTRAINT `daily_deliveries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `daily_deliveries_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `daily_deliveries` (8 records)
INSERT INTO `daily_deliveries` (`id`, `driver_id`, `date`, `amount`, `route_notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-10-03 00:00:00', 10000000, 'Rute Margadana - Kramat', 4, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(2, 2, '2026-10-03 00:00:00', 8000000, 'Rute Mejasem - Suradadi', 5, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(3, 8, '2026-10-03 00:00:00', 12000000, 'Rute Brebes Kota - Jatibarang', 4, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(11, 1, '2026-10-05 00:00:00', 10000000, 'Rute E2E Margadana - Kramat', NULL, '2026-10-05 11:13:22', '2026-10-05 11:13:22'),
(13, 2, '2026-10-06 00:00:00', 8000000, 'Rute Mejasem - Suradadi', 5, '2026-10-06 20:20:44', '2026-10-06 20:20:44'),
(14, 8, '2026-10-06 00:00:00', 12000000, 'Rute Brebes Kota - Jatibarang', 4, '2026-10-06 20:20:44', '2026-10-06 20:20:44'),
(16, 1, '2026-10-06 00:00:00', 10000000, 'Rute E2E Margadana - Kramat', NULL, '2026-10-06 21:14:41', '2026-10-06 21:14:41'),
(20, 1, '2026-10-09 00:00:00', 10000000, 'Rute E2E Margadana - Kramat', NULL, '2026-10-09 22:08:14', '2026-10-09 22:08:14');


-- ---------------------------------------------------------
-- Table structure for `return_items`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `return_items`;
CREATE TABLE `return_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `return_items_driver_id_foreign` (`driver_id`),
  KEY `return_items_created_by_foreign` (`created_by`),
  CONSTRAINT `return_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `return_items_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `return_items` (4 records)
INSERT INTO `return_items` (`id`, `driver_id`, `date`, `amount`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-10-03 00:00:00', 500000, 'Barang rusak/kemasan bocor', 4, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(8, 1, '2026-10-05 00:00:00', 500000, 'Dus penyok kemasan rusak uji E2E', 4, '2026-10-05 11:13:24', '2026-10-05 11:13:24'),
(11, 1, '2026-10-06 00:00:00', 500000, 'Dus penyok kemasan rusak uji E2E', 4, '2026-10-06 21:14:44', '2026-10-06 21:14:44'),
(15, 1, '2026-10-09 00:00:00', 500000, 'Dus penyok kemasan rusak uji E2E', 4, '2026-10-09 22:08:16', '2026-10-09 22:08:16');


-- ---------------------------------------------------------
-- Table structure for `transfer_payments`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `transfer_payments`;
CREATE TABLE `transfer_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `store_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `claimed_amount` decimal(15,2) NOT NULL,
  `verified_amount` decimal(15,2) DEFAULT NULL,
  `proof_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfer_payments_driver_id_foreign` (`driver_id`),
  KEY `transfer_payments_verified_by_foreign` (`verified_by`),
  CONSTRAINT `transfer_payments_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transfer_payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `transfer_payments` (8 records)
INSERT INTO `transfer_payments` (`id`, `driver_id`, `date`, `store_name`, `claimed_amount`, `verified_amount`, `proof_image_path`, `status`, `verified_by`, `verified_at`, `notes`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-10-03 00:00:00', 'Toko Berkah Abadi', 2000000, 2000000, NULL, 'approved', 2, '2026-10-03 16:08:48', 'Mutasi BCA masuk valid', NULL, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(2, 2, '2026-10-03 00:00:00', 'Toko Sinar Jaya', 1000000, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(3, 8, '2026-10-03 00:00:00', 'Toko Barokah Brebes', 4000000, 4000000, NULL, 'approved', 2, '2026-10-03 16:08:48', NULL, NULL, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(8, 1, '2026-10-05 00:00:00', 'Toko Sinar Jaya', 2000000, 2000000, 'transfers/GUc32gZaXbCBJqNZGzwRtxAqJhlLN0CBZpuX5lX9.png', 'approved', 2, '2026-10-05 11:13:42', 'Transfer via BCA Rekening Kantor', NULL, '2026-10-05 11:13:34', '2026-10-05 11:13:42'),
(10, 2, '2026-10-06 00:00:00', 'Toko Sinar Jaya', 1000000, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-10-06 20:20:44', '2026-10-06 20:20:44'),
(11, 8, '2026-10-06 00:00:00', 'Toko Barokah Brebes', 4000000, 4000000, NULL, 'approved', 2, '2026-10-06 20:20:44', NULL, NULL, '2026-10-06 20:20:44', '2026-10-06 20:20:44'),
(13, 1, '2026-10-06 00:00:00', 'Toko Sinar Jaya', 2000000, 2000000, 'transfers/axn1fjYIcQCtvznyUFCGzWDEBlfXgEhTWaRAPzY9.png', 'approved', 2, '2026-10-06 21:14:59', 'Transfer via BCA Rekening Kantor', NULL, '2026-10-06 21:14:50', '2026-10-06 21:14:59'),
(17, 1, '2026-10-09 00:00:00', 'Toko Sinar Jaya', 2000000, 2000000, 'transfers/twVdVva5oTtbzM343H4TirbV9hq38lKjPAHrRXHr.png', 'approved', 2, '2026-10-09 22:08:29', 'Transfer via BCA Rekening Kantor', NULL, '2026-10-09 22:08:21', '2026-10-09 22:08:29');


-- ---------------------------------------------------------
-- Table structure for `credit_deliveries`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `credit_deliveries`;
CREATE TABLE `credit_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `store_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `invoice_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_deliveries_driver_id_foreign` (`driver_id`),
  CONSTRAINT `credit_deliveries_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `credit_deliveries` (8 records)
INSERT INTO `credit_deliveries` (`id`, `driver_id`, `date`, `store_name`, `amount`, `invoice_photo_path`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-10-03 00:00:00', 'Toko Jaya Mandiri', 1500000, NULL, 'Tempo 14 hari', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(2, 2, '2026-10-03 00:00:00', 'Toko Rejeki', 1000000, NULL, 'Faktur No. FK-0829', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(3, 8, '2026-10-03 00:00:00', 'Toko Mulia', 2000000, NULL, NULL, '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(7, 1, '2026-10-05 00:00:00', 'Toko Berkah Abadi', 1500000, 'credit_invoices/Z0McJ6dKKbvo5hjpppSXJBqxl7jLdIZ0iXKIZPOy.png', 'Bon tempo 7 hari stempel basah', '2026-10-05 11:13:36', '2026-10-05 11:13:36'),
(9, 2, '2026-10-06 00:00:00', 'Toko Rejeki', 1000000, NULL, 'Faktur No. FK-0829', '2026-10-06 20:20:44', '2026-10-06 20:20:44'),
(10, 8, '2026-10-06 00:00:00', 'Toko Mulia', 2000000, NULL, NULL, '2026-10-06 20:20:44', '2026-10-06 20:20:44'),
(12, 1, '2026-10-06 00:00:00', 'Toko Berkah Abadi', 1500000, 'credit_invoices/mGH02G9wnDQGsoDtiqKAcCYaft3TpSvXWJ6xYBGE.png', 'Bon tempo 7 hari stempel basah', '2026-10-06 21:14:52', '2026-10-06 21:14:52'),
(16, 1, '2026-10-09 00:00:00', 'Toko Berkah Abadi', 1500000, 'credit_invoices/o19TgwItsEIfSFhxDllor3tOyvgwOuGPf2uOUBBQ.png', 'Bon tempo 7 hari stempel basah', '2026-10-09 22:08:23', '2026-10-09 22:08:23');


-- ---------------------------------------------------------
-- Table structure for `cash_deposits`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `cash_deposits`;
CREATE TABLE `cash_deposits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `amount_received` decimal(15,2) NOT NULL,
  `deposit_phase` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `cashier_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_deposits_driver_id_foreign` (`driver_id`),
  KEY `cash_deposits_cashier_id_foreign` (`cashier_id`),
  CONSTRAINT `cash_deposits_cashier_id_foreign` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_deposits_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `cash_deposits` (8 records)
INSERT INTO `cash_deposits` (`id`, `driver_id`, `date`, `amount_received`, `deposit_phase`, `cashier_id`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-10-03 00:00:00', 5000000, '1', 2, 'Setoran tunai tahap 1 sore', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(2, 2, '2026-10-03 00:00:00', 4000000, '1', 2, 'Setoran tunai shift 1', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(3, 8, '2026-10-03 00:00:00', 6000000, '1', 2, 'Lunas tepat', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(6, 1, '2026-10-05 00:00:00', 6000000, '1', 2, 'Pelunasan tunai shift sore TGL1.2', '2026-10-05 11:13:45', '2026-10-05 11:13:45'),
(8, 2, '2026-10-06 00:00:00', 4000000, '1', 2, 'Setoran tunai shift 1', '2026-10-06 20:20:44', '2026-10-06 20:20:44'),
(9, 8, '2026-10-06 00:00:00', 6000000, '1', 2, 'Lunas tepat', '2026-10-06 20:20:44', '2026-10-06 20:20:44'),
(10, 1, '2026-10-06 00:00:00', 6000000, '1', 2, 'Pelunasan tunai shift sore TGL1.2', '2026-10-06 21:15:05', '2026-10-06 21:15:05'),
(14, 1, '2026-10-09 00:00:00', 6000000, '1', 2, 'Pelunasan tunai shift sore TGL1.2', '2026-10-09 22:08:34', '2026-10-09 22:08:34');


-- ---------------------------------------------------------
-- Table structure for `daily_settlements`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `daily_settlements`;
CREATE TABLE `daily_settlements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `amount_carried` decimal(15,2) NOT NULL DEFAULT '0.00',
  `amount_returned` decimal(15,2) NOT NULL DEFAULT '0.00',
  `amount_transfer_approved` decimal(15,2) NOT NULL DEFAULT '0.00',
  `amount_credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `target_cash` decimal(15,2) NOT NULL DEFAULT '0.00',
  `actual_cash` decimal(15,2) NOT NULL DEFAULT '0.00',
  `difference` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('lunas','kurang_setor','lebih_setor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lunas',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_settlements_driver_id_date_unique` (`driver_id`,`date`),
  KEY `daily_settlements_driver_id_foreign` (`driver_id`),
  CONSTRAINT `daily_settlements_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `daily_settlements` (56 records)
INSERT INTO `daily_settlements` (`id`, `driver_id`, `date`, `amount_carried`, `amount_returned`, `amount_transfer_approved`, `amount_credit`, `target_cash`, `actual_cash`, `difference`, `status`, `created_at`, `updated_at`) VALUES
(1, 8, '2026-10-03 00:00:00', 12000000, 0, 4000000, 2000000, 6000000, 6000000, 0, 'lunas', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(2, 13, '2026-10-03 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(3, 14, '2026-10-03 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(4, 9, '2026-10-03 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(5, 10, '2026-10-03 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(6, 11, '2026-10-03 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(7, 12, '2026-10-03 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(8, 1, '2026-10-03 00:00:00', 10000000, 500000, 2000000, 1500000, 6000000, 5000000, 1000000, 'kurang_setor', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(9, 6, '2026-10-03 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-03 16:08:48', '2026-10-03 16:08:48'),
(10, 7, '2026-10-03 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-03 16:08:49', '2026-10-03 16:08:49'),
(11, 2, '2026-10-03 00:00:00', 8000000, 0, 0, 1000000, 7000000, 4000000, 3000000, 'kurang_setor', '2026-10-03 16:08:49', '2026-10-03 16:08:49'),
(12, 3, '2026-10-03 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-03 16:08:49', '2026-10-03 16:08:49'),
(13, 4, '2026-10-03 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-03 16:08:49', '2026-10-03 16:08:49'),
(14, 5, '2026-10-03 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-03 16:08:49', '2026-10-03 16:08:49'),
(16, 8, '2026-10-05 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-05 10:28:44', '2026-10-05 10:28:44'),
(17, 13, '2026-10-05 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-05 10:28:44', '2026-10-05 10:28:44'),
(18, 14, '2026-10-05 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-05 10:28:44', '2026-10-05 10:28:44'),
(19, 9, '2026-10-05 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-05 10:28:44', '2026-10-05 10:28:44'),
(20, 10, '2026-10-05 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-05 10:28:44', '2026-10-05 10:28:44'),
(21, 11, '2026-10-05 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-05 10:28:44', '2026-10-05 10:28:44'),
(22, 12, '2026-10-05 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-05 10:28:44', '2026-10-05 10:28:44'),
(23, 6, '2026-10-05 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-05 10:28:44', '2026-10-05 10:28:44'),
(24, 7, '2026-10-05 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-05 10:28:44', '2026-10-05 10:28:44'),
(25, 2, '2026-10-05 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-05 10:28:44', '2026-10-05 10:28:44'),
(26, 3, '2026-10-05 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-05 10:28:44', '2026-10-05 10:28:44'),
(27, 4, '2026-10-05 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-05 10:28:44', '2026-10-05 10:28:44'),
(28, 5, '2026-10-05 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-05 10:28:44', '2026-10-05 10:28:44'),
(35, 1, '2026-10-05 00:00:00', 10000000, 500000, 2000000, 1500000, 6000000, 6000000, 0, 'lunas', '2026-10-05 11:13:27', '2026-10-05 11:13:45'),
(36, 8, '2026-10-06 00:00:00', 12000000, 0, 4000000, 2000000, 6000000, 6000000, 0, 'lunas', '2026-10-06 19:48:35', '2026-10-06 20:20:44'),
(37, 13, '2026-10-06 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-06 19:48:35', '2026-10-06 19:48:35'),
(38, 14, '2026-10-06 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-06 19:48:35', '2026-10-06 19:48:35'),
(39, 9, '2026-10-06 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-06 19:48:35', '2026-10-06 19:48:35'),
(40, 10, '2026-10-06 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-06 19:48:35', '2026-10-06 19:48:35'),
(41, 11, '2026-10-06 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-06 19:48:35', '2026-10-06 19:48:35'),
(42, 12, '2026-10-06 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-06 19:48:35', '2026-10-06 19:48:35'),
(44, 6, '2026-10-06 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-06 19:48:35', '2026-10-06 19:48:35'),
(45, 7, '2026-10-06 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-06 19:48:35', '2026-10-06 19:48:35'),
(46, 2, '2026-10-06 00:00:00', 8000000, 0, 0, 1000000, 7000000, 4000000, 3000000, 'kurang_setor', '2026-10-06 19:48:35', '2026-10-06 20:20:44'),
(47, 3, '2026-10-06 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-06 19:48:35', '2026-10-06 19:48:35'),
(48, 4, '2026-10-06 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-06 19:48:35', '2026-10-06 19:48:35'),
(49, 5, '2026-10-06 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-06 19:48:35', '2026-10-06 19:48:35'),
(51, 1, '2026-10-06 00:00:00', 10000000, 500000, 2000000, 1500000, 6000000, 6000000, 0, 'lunas', '2026-10-06 21:14:47', '2026-10-06 21:15:05'),
(53, 8, '2026-10-09 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-09 21:25:00', '2026-10-09 21:25:00'),
(54, 13, '2026-10-09 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-09 21:25:00', '2026-10-09 21:25:00'),
(55, 14, '2026-10-09 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-09 21:25:00', '2026-10-09 21:25:00'),
(56, 9, '2026-10-09 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-09 21:25:00', '2026-10-09 21:25:00'),
(57, 10, '2026-10-09 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-09 21:25:00', '2026-10-09 21:25:00'),
(58, 11, '2026-10-09 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-09 21:25:00', '2026-10-09 21:25:00'),
(59, 12, '2026-10-09 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-09 21:25:00', '2026-10-09 21:25:00'),
(60, 6, '2026-10-09 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-09 21:25:00', '2026-10-09 21:25:00');
INSERT INTO `daily_settlements` (`id`, `driver_id`, `date`, `amount_carried`, `amount_returned`, `amount_transfer_approved`, `amount_credit`, `target_cash`, `actual_cash`, `difference`, `status`, `created_at`, `updated_at`) VALUES
(61, 7, '2026-10-09 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-09 21:25:00', '2026-10-09 21:25:00'),
(62, 2, '2026-10-09 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-09 21:25:00', '2026-10-09 21:25:00'),
(63, 3, '2026-10-09 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-09 21:25:00', '2026-10-09 21:25:00'),
(64, 4, '2026-10-09 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-09 21:25:00', '2026-10-09 21:25:00'),
(65, 5, '2026-10-09 00:00:00', 0, 0, 0, 0, 0, 0, 0, 'lunas', '2026-10-09 21:25:00', '2026-10-09 21:25:00'),
(68, 1, '2026-10-09 00:00:00', 10000000, 500000, 2000000, 1500000, 6000000, 6000000, 0, 'lunas', '2026-10-09 22:08:19', '2026-10-09 22:08:34');

-- ---------------------------------------------------------
-- Re-enable foreign key checks and commit
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
