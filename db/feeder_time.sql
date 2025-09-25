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
-- Table structure for table `feeder_time`
--

CREATE TABLE `feeder_time` (
  `id` int(11) NOT NULL,
  `time1` varchar(16) NOT NULL,
  `interval1` varchar(8) NOT NULL,
  `time2` varchar(16) NOT NULL,
  `interval2` varchar(8) NOT NULL,
  `time3` varchar(16) NOT NULL,
  `interval3` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feeder_time`
--

INSERT INTO `feeder_time` (`id`, `time1`, `interval1`, `time2`, `interval2`, `time3`, `interval3`) VALUES
(1, '01:32', '10', '01:29', '15', '01:16', '10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feeder_time`
--
ALTER TABLE `feeder_time`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feeder_time`
--
ALTER TABLE `feeder_time`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
