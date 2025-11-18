-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2025 at 04:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sesa_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `supervision_sessions`
--

CREATE TABLE `supervision_sessions` (
  `id` int(11) NOT NULL,
  `supervisor_p_id` varchar(13) NOT NULL COMMENT 'FK to supervisor.p_id',
  `teacher_t_pid` varchar(13) NOT NULL COMMENT 'FK to teacher.t_pid',
  `subject_code` varchar(50) DEFAULT NULL COMMENT 'รหัสวิชา',
  `subject_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อวิชา',
  `inspection_time` int(2) DEFAULT NULL COMMENT 'ครั้งที่นิเทศ',
  `inspection_date` date DEFAULT NULL COMMENT 'วันที่รับการนิเทศ',
  `overall_suggestion` text DEFAULT NULL COMMENT 'ข้อเสนอแนะเพิ่มเติม',
  `supervision_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `supervision_sessions`
--

INSERT INTO `supervision_sessions` (`id`, `supervisor_p_id`, `teacher_t_pid`, `subject_code`, `subject_name`, `inspection_time`, `inspection_date`, `overall_suggestion`, `supervision_date`) VALUES
(17, '1529900270499', '1509900989451', 'ท001', 'ภาษาไทย', 1, '2025-11-18', NULL, '2025-11-18 03:03:46'),
(18, '3210400040835', '3510300327129', 'ท001', 'ภาษาไทย', 2, '2025-11-18', NULL, '2025-11-18 03:05:18'),
(19, '3210400040835', '3510300327129', 'ท001', 'ภาษาไทย', 2, '2025-11-18', '', '2025-11-18 03:13:50'),
(20, '1529900270499', '1509900430437', 'ท001', 'ภาษาไทย', 1, '2025-11-18', '', '2025-11-18 03:15:17'),
(21, '3509900553730', '3510400265249', 'ท001', 'ภาษาไทย', 1, '2025-11-18', '-', '2025-11-18 03:22:49'),
(22, '3210400040835', '1509900989451', 'ท001', 'ภาษาไทย', 5, '2025-11-18', '-', '2025-11-18 03:24:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `supervision_sessions`
--
ALTER TABLE `supervision_sessions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `supervision_sessions`
--
ALTER TABLE `supervision_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
