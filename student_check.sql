-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2024 at 12:09 PM
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
(2, 'test2', '12345678', '2024-10-29 06:24:57'),
(3, 'test', '12345678', '2024-10-28 13:43:51'),
(36, 'admin2', '12345678', '2024-11-01 10:51:21'),
(37, 'testtest', '12345678', '2024-11-02 09:34:51');

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
(2, 'ป.1', '2024-10-26 08:20:30'),
(9, 'ป.4', '2024-10-30 15:43:02'),
(13, 'ป.2', '2024-11-01 10:53:06'),
(14, 'ป.3', '2024-11-01 10:53:14');

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
(3, '1/1', 2, '2024-10-29 03:39:13'),
(4, '1/2', 2, '2024-10-29 03:39:16'),
(11, '4/2', 9, '2024-11-01 11:20:14'),
(13, '3/1', 14, '2024-11-01 11:20:06'),
(14, '1', 9, '2024-11-01 11:20:36'),
(15, '2', 9, '2024-11-01 11:20:42'),
(16, '2', 14, '2024-11-01 11:29:08');

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
(5, 'นาย', 'สรอรรถ', 'จันทร์นนท์', NULL, 3, 6, 1, 2, '2024-10-29 04:03:08'),
(6, 'นางสาว', 'อัญชลี', 'สุทธิรัตน์', NULL, 4, 2, 1, 2, '2024-10-29 04:50:45'),
(7, 'นาย', 'มานะ', 'มานะ', NULL, 3, 6, 1, 2, '2024-10-29 07:57:03'),
(8, 'เด็กหญิง', 'มานี', 'มานี', NULL, 11, 7, 1, 9, '2024-10-30 15:47:41');

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
(2, 'สมจิต', 'ใจดี', 4, 1, '2024-10-29 04:48:18'),
(6, 'สมชาย', 'รักโลก', 3, 1, '2024-10-29 04:48:22'),
(7, 'สมร', 'มั่นคง', 11, 1, '2024-10-30 15:47:20'),
(8, 'มานี', 'มั่นคง', 11, 1, '2024-11-01 11:40:47'),
(9, 'มานี', 'ใจดี', 13, 1, '2024-11-01 11:43:41'),
(10, 'สมจิต', 'ดูดี', 3, 1, '2024-11-01 12:04:19');

-- --------------------------------------------------------

--
-- Table structure for table `time_inout`
--

CREATE TABLE `time_inout` (
  `id` int(11) NOT NULL,
  `id_student` int(11) DEFAULT NULL,
  `status` enum('in','out') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_inout`
--

INSERT INTO `time_inout` (`id`, `id_student`, `status`, `created_at`) VALUES
(1, 5, 'in', '2024-10-28 07:30:51'),
(2, 5, 'out', '2024-10-28 07:33:26'),
(3, 6, 'in', '2024-10-29 07:33:30'),
(4, 6, 'out', '2024-10-29 07:33:34'),
(5, 7, 'in', '2024-10-29 07:57:24'),
(6, 7, 'out', '2024-10-29 07:57:26'),
(7, 7, 'in', '2024-10-29 07:57:29'),
(8, 5, 'in', '2024-10-30 05:19:04'),
(9, 5, 'out', '2024-10-30 05:19:08'),
(10, 6, 'in', '2024-10-30 05:25:53'),
(11, 6, 'out', '2024-10-30 05:25:56'),
(12, 7, 'out', '2024-10-30 05:25:58'),
(13, 7, 'in', '2024-10-30 10:24:16'),
(29, 7, 'out', '2024-10-30 11:36:48'),
(30, 5, 'in', '2024-10-30 11:37:37'),
(31, 6, 'in', '2024-10-30 11:37:44'),
(33, 7, 'in', '2024-10-30 11:40:41'),
(34, 5, 'out', '2024-10-30 11:45:25'),
(36, 7, 'out', '2024-10-30 11:45:39'),
(38, 6, 'out', '2024-10-30 12:46:00'),
(39, 6, 'in', '2024-10-30 12:46:00'),
(40, 6, 'out', '2024-10-30 12:48:31'),
(41, 6, 'in', '2024-10-30 12:53:47'),
(42, 6, 'out', '2024-10-30 12:55:58'),
(43, 6, 'in', '2024-10-29 13:01:55'),
(44, 6, 'in', '2024-10-29 13:01:02'),
(45, 6, 'in', '2024-10-30 13:03:32'),
(46, 6, 'in', '2024-10-30 13:03:34'),
(47, 6, 'out', '2024-10-30 13:03:58'),
(48, 6, 'in', '2024-10-30 13:04:08'),
(49, 6, 'out', '2024-10-29 13:04:17'),
(50, 6, 'in', '2024-10-27 13:12:59'),
(51, 6, 'out', '2024-10-27 14:12:09'),
(52, 6, 'out', '2024-10-30 13:17:58'),
(53, 6, 'in', '2024-10-25 13:17:06'),
(54, 6, 'out', '2024-10-25 15:17:18'),
(55, 6, 'in', '2024-10-30 13:31:14'),
(56, 5, 'in', '2024-10-30 13:31:22'),
(57, 7, 'in', '2024-10-30 13:33:32'),
(58, 8, 'in', '2024-11-02 06:46:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
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
-- Indexes for table `time_inout`
--
ALTER TABLE `time_inout`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_student` (`id_student`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `grade_level`
--
ALTER TABLE `grade_level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `time_inout`
--
ALTER TABLE `time_inout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Constraints for dumped tables
--

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

--
-- Constraints for table `time_inout`
--
ALTER TABLE `time_inout`
  ADD CONSTRAINT `time_inout_ibfk_1` FOREIGN KEY (`id_student`) REFERENCES `student` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
