-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 26, 2024 at 07:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_check`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '12345678', '2024-10-25 13:11:25'),
(38, 'admin2', '12345678', '2024-11-28 03:34:42');

-- --------------------------------------------------------

--
-- Table structure for table `check_in`
--

CREATE TABLE `check_in` (
  `id` int(11) NOT NULL,
  `id_student` int(11) NOT NULL,
  `in_at` timestamp NULL DEFAULT NULL,
  `out_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `check_in`
--

INSERT INTO `check_in` (`id`, `id_student`, `in_at`, `out_at`, `created_at`) VALUES
(5, 1, '2024-11-18 01:00:00', '2024-11-18 10:00:00', '2024-11-18 05:48:54'),
(7, 1, '2024-11-19 06:44:26', NULL, '2024-11-19 06:44:04'),
(9, 1, '2024-11-20 03:26:46', NULL, '2024-11-20 03:26:43'),
(11, 1, '2024-11-21 01:05:00', '2024-11-21 09:13:49', '2024-11-21 04:50:51'),
(16, 1, '2024-12-01 01:00:00', '2024-12-01 14:06:23', '2024-12-01 11:54:57'),
(17, 2, '2024-12-01 11:55:08', '2024-12-01 14:06:26', '2024-12-01 11:54:57'),
(18, 3, NULL, NULL, '2024-12-01 11:54:57'),
(19, 4, NULL, NULL, '2024-12-01 11:54:57'),
(20, 5, '2024-12-01 11:55:16', '2024-12-01 14:06:33', '2024-12-01 11:54:57'),
(21, 6, '2024-12-01 11:59:36', NULL, '2024-12-01 11:54:57'),
(22, 7, '2024-12-01 11:55:22', NULL, '2024-12-01 11:54:57'),
(23, 8, NULL, NULL, '2024-12-01 11:54:57'),
(24, 9, NULL, NULL, '2024-12-01 11:54:57'),
(25, 10, NULL, NULL, '2024-12-01 11:54:57'),
(27, 10, NULL, NULL, '2024-12-05 05:45:27'),
(28, 1, '2024-12-05 05:45:41', '2024-12-05 10:21:02', '2024-12-05 05:45:27'),
(29, 2, '2024-12-05 08:29:31', '2024-12-05 10:41:09', '2024-12-05 05:45:27'),
(30, 3, '2024-12-05 08:29:34', '2024-12-05 10:33:16', '2024-12-05 05:45:27'),
(31, 4, NULL, NULL, '2024-12-05 05:45:27'),
(32, 5, NULL, NULL, '2024-12-05 05:45:27'),
(33, 6, NULL, NULL, '2024-12-05 05:45:27'),
(34, 7, NULL, NULL, '2024-12-05 05:45:27'),
(35, 8, NULL, NULL, '2024-12-05 05:45:27'),
(36, 9, NULL, NULL, '2024-12-05 05:45:27'),
(37, 10, '2024-12-06 06:57:55', NULL, '2024-12-06 00:57:27'),
(38, 12, '2024-12-06 06:58:01', NULL, '2024-12-06 00:57:27'),
(39, 1, '2024-12-06 00:57:53', NULL, '2024-12-06 00:57:27'),
(40, 2, '2024-12-06 00:57:46', NULL, '2024-12-06 00:57:27'),
(41, 3, NULL, NULL, '2024-12-06 00:57:27'),
(42, 4, '2024-12-06 01:00:06', NULL, '2024-12-06 00:57:27'),
(43, 5, '2024-12-06 06:58:16', NULL, '2024-12-06 00:57:27'),
(44, 6, '2024-12-06 00:59:53', NULL, '2024-12-06 00:57:27'),
(45, 7, '2024-12-06 00:58:00', NULL, '2024-12-06 00:57:27'),
(46, 8, '2024-12-06 00:58:28', NULL, '2024-12-06 00:57:27'),
(47, 9, '2024-12-06 06:58:25', NULL, '2024-12-06 00:57:27'),
(48, 10, NULL, NULL, '2024-12-08 06:57:22'),
(49, 12, NULL, NULL, '2024-12-08 06:57:22'),
(50, 1, NULL, NULL, '2024-12-08 06:57:22'),
(51, 2, NULL, NULL, '2024-12-08 06:57:22'),
(52, 3, NULL, NULL, '2024-12-08 06:57:22'),
(53, 4, NULL, NULL, '2024-12-08 06:57:22'),
(54, 5, NULL, NULL, '2024-12-08 06:57:22'),
(55, 6, NULL, NULL, '2024-12-08 06:57:22'),
(56, 7, NULL, NULL, '2024-12-08 06:57:22'),
(57, 8, NULL, NULL, '2024-12-08 06:57:22'),
(58, 9, NULL, NULL, '2024-12-08 06:57:22');

-- --------------------------------------------------------

--
-- Table structure for table `check_time_settings`
--

CREATE TABLE `check_time_settings` (
  `id` int(11) NOT NULL,
  `check_in_start` time NOT NULL,
  `check_in_end` time NOT NULL,
  `check_out_start` time NOT NULL,
  `check_out_end` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `check_time_settings`
--

INSERT INTO `check_time_settings` (`id`, `check_in_start`, `check_in_end`, `check_out_start`, `check_out_end`) VALUES
(1, '06:00:00', '08:00:00', '16:00:00', '17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `grade_level`
--

CREATE TABLE `grade_level` (
  `id` int(11) NOT NULL,
  `grade_level` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_level`
--

INSERT INTO `grade_level` (`id`, `grade_level`, `created_at`) VALUES
(1, 'ป.1', '2024-11-14 05:17:12'),
(2, 'ป.2', '2024-11-14 05:17:25'),
(3, 'ป.3', '2024-11-14 05:17:31'),
(4, 'ป.4', '2024-11-14 05:17:37'),
(5, 'ป.5', '2024-11-14 05:17:42'),
(6, 'ป.6', '2024-11-14 05:17:53'),
(7, 'ม.1', '2024-11-14 05:18:01'),
(8, 'ม.2', '2024-11-14 05:18:07'),
(9, 'ม.3', '2024-11-14 05:18:13');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `grade_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `name`, `grade_id`, `created_at`) VALUES
(1, '1/1', 1, '2024-11-14 05:21:23'),
(2, '1/2', 1, '2024-11-14 05:21:29'),
(3, '2/1', 2, '2024-11-14 05:21:37'),
(4, '2/2', 2, '2024-11-14 05:21:43'),
(5, '3/1', 3, '2024-11-14 15:21:04'),
(6, 'ม.3/1', 9, '2024-11-23 04:48:49');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `prefix` varchar(255) NOT NULL COMMENT 'คำนำหน้าชื่อ',
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `id_finger` varchar(255) DEFAULT NULL,
  `id_room` int(11) DEFAULT NULL,
  `id_teacher` int(11) DEFAULT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `id_grade` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `prefix`, `name`, `surname`, `id_finger`, `id_room`, `id_teacher`, `id_admin`, `id_grade`, `created_at`) VALUES
(1, 'นาย', 'สมชาย', 'ใจดี', NULL, 6, 5, 1, 9, '2024-12-01 11:41:37'),
(2, 'นางสาว', 'สมหญิง', 'ใจงาม', NULL, 6, 5, 1, 9, '2024-12-01 11:41:37'),
(3, 'เด็กชาย', 'ปกรณ์', 'รักเรียน', NULL, 6, 5, 1, 9, '2024-12-01 11:41:37'),
(4, 'เด็กหญิง', 'ปรียา', 'ใจสู้', NULL, 6, 5, 1, 9, '2024-12-01 11:41:37'),
(5, 'นาย', 'ชัชวาล', 'ตั้งใจ', NULL, 6, 5, 1, 9, '2024-12-01 11:41:37'),
(6, 'นางสาว', 'วราภรณ์', 'พัฒนกิจ', NULL, 6, 5, 1, 9, '2024-12-01 11:41:37'),
(7, 'เด็กชาย', 'อนันต์', 'เจริญสุข', NULL, 6, 5, 1, 9, '2024-12-01 11:41:37'),
(8, 'เด็กหญิง', 'อัญชลี', 'เพิ่มพูน', NULL, 6, 5, 1, 9, '2024-12-01 11:41:37'),
(9, 'นาย', 'ณัฐวุฒิ', 'มั่งมี', NULL, 6, 5, 1, 9, '2024-12-01 11:41:37'),
(10, 'นางสาว', 'วิไลลักษณ์', 'ยั่งยืน', NULL, 1, 1, 1, 1, '2024-12-04 13:29:04'),
(12, 'เด็กหญิง', 'กุหลาบ', 'งามดี', NULL, 4, 4, 1, 2, '2024-12-05 07:03:06');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `id_room` int(11) DEFAULT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`id`, `name`, `surname`, `id_room`, `id_admin`, `created_at`) VALUES
(1, 'สมจิต', 'ใจดี', 1, 1, '2024-11-14 05:22:18'),
(2, 'สมร', 'ใจดี', 2, 1, '2024-11-14 05:22:36'),
(3, 'อำนวย', 'มานะ', 3, 1, '2024-11-14 05:22:57'),
(4, 'สมจิต', 'ใจดำ', 4, 1, '2024-11-14 05:23:22'),
(5, 'กุหลาบ', 'งามดี', 6, 1, '2024-11-23 04:49:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `check_in`
--
ALTER TABLE `check_in`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foreign key id_student` (`id_student`);

--
-- Indexes for table `check_time_settings`
--
ALTER TABLE `check_time_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grade_level`
--
ALTER TABLE `grade_level`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_grade_id` (`grade_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_room` (`id_room`),
  ADD KEY `id_teacher` (`id_teacher`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_grade` (`id_grade`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_room` (`id_room`),
  ADD KEY `id_admin` (`id_admin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `check_in`
--
ALTER TABLE `check_in`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `check_time_settings`
--
ALTER TABLE `check_time_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grade_level`
--
ALTER TABLE `grade_level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `check_in`
--
ALTER TABLE `check_in`
  ADD CONSTRAINT `foreign key id_student` FOREIGN KEY (`id_student`) REFERENCES `student` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `fk_grade_id` FOREIGN KEY (`grade_id`) REFERENCES `grade_level` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`id_room`) REFERENCES `room` (`id`),
  ADD CONSTRAINT `student_ibfk_2` FOREIGN KEY (`id_teacher`) REFERENCES `teacher` (`id`),
  ADD CONSTRAINT `student_ibfk_3` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id`),
  ADD CONSTRAINT `student_ibfk_4` FOREIGN KEY (`id_grade`) REFERENCES `grade_level` (`id`);

--
-- Constraints for table `teacher`
--
ALTER TABLE `teacher`
  ADD CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`id_room`) REFERENCES `room` (`id`),
  ADD CONSTRAINT `teacher_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
