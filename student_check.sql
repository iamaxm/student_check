-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2024 at 12:53 PM
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
(2, 'tesr', '12345678', '2024-10-26 07:40:32');

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
(3, 'ป.2', '2024-10-26 08:21:00'),
(4, 'ป.3', '2024-10-26 08:21:46');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `name`, `created_at`) VALUES
(3, '1/1', '2024-10-28 09:18:40'),
(4, '1/2', '2024-10-28 09:18:45');

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
(1, 'นางสาว', 'อัญชลี', 'สุทธิรัตน์', NULL, 3, 2, 1, 4, '2024-10-28 11:19:29'),
(2, 'เด็กหญิง', 'มานี', 'น่ารัก', NULL, 3, 2, 1, 4, '2024-10-28 11:20:51');

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
(2, 'สมจิต', 'ใจดี', 4, 1, '2024-10-28 10:20:16'),
(5, 'สมชาย', 'รักโลก', 3, 1, '2024-10-28 11:02:12');

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `grade_level`
--
ALTER TABLE `grade_level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `time_inout`
--
ALTER TABLE `time_inout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

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
