-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2025 at 05:00 AM
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
-- Database: `pond_monitoring`
--

-- --------------------------------------------------------

--
-- Table structure for table `threshold_notif`
--

CREATE TABLE `threshold_notif` (
  `id` int(11) NOT NULL,
  `sensor` varchar(32) NOT NULL,
  `value` varchar(8) NOT NULL,
  `status` varchar(8) NOT NULL,
  `timestamp` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `threshold_notif`
--

INSERT INTO `threshold_notif` (`id`, `sensor`, `value`, `status`, `timestamp`) VALUES
(1, 'DO', '16', 'High', '2025-08-13 02:45:09'),
(2, 'DO', '15.2', 'High', '2025-08-13 02:45:29'),
(3, 'pH', '8.5', 'High', '2025-08-13 02:46:35'),
(4, 'pH', '8.1', 'High', '2025-08-13 02:48:41'),
(5, 'pH', '8.2', 'High', '2025-08-13 02:49:03'),
(6, 'pH', '8.3', 'High', '2025-08-13 02:50:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `threshold_notif`
--
ALTER TABLE `threshold_notif`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `threshold_notif`
--
ALTER TABLE `threshold_notif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
