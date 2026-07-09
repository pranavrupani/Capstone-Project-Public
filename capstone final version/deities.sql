-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 24, 2026 at 12:36 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `deities_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `deities`
--

CREATE TABLE `deities` (
  `deity_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `mythology` text NOT NULL,
  `image_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `deities`
--

INSERT INTO `deities` (`deity_id`, `name`, `description`, `mythology`, `image_name`) VALUES
(1, 'Shiva', 'Known as the destroyer and transformer within the Trimurti.', 'Associated with cosmic destruction, meditation, and renewal.', 'Shiva.jpg'),
(2, 'Vishnu', 'Known as the preserver and protector within the Trimurti.', 'Associated with balance, protection, and divine incarnations.', 'Vishnu.jpg'),
(3, 'Ganesha', 'Known as the remover of obstacles and the god of wisdom.', 'Associated with new beginnings, knowledge, and success.', 'Ganesha.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `deities`
--
ALTER TABLE `deities`
  ADD PRIMARY KEY (`deity_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `deities`
--
ALTER TABLE `deities`
  MODIFY `deity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;