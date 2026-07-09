-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 24, 2026 at 01:06 PM
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
-- Database: `course_lessons_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `course_lessons`
--

CREATE TABLE `course_lessons` (
  `lesson_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `lesson_order` int(11) NOT NULL,
  `deity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `course_lessons`
--

INSERT INTO `course_lessons` (`lesson_id`, `title`, `content`, `lesson_order`, `deity_id`) VALUES
(1, 'Introduction to Shiva', 'This lesson introduces Shiva as one of the major deities in Hinduism.', 1, 1),
(2, 'Shiva and Transformation', 'This lesson explains Shiva’s role as the destroyer and transformer within the Trimurti.', 2, 1),
(3, 'Shiva in Mythology', 'This lesson explores simple mythology connected to Shiva, meditation, destruction, and renewal.', 3, 1),
(4, 'Introduction to Vishnu', 'This lesson introduces Vishnu as the preserver and protector within the Trimurti.', 1, 2),
(5, 'Vishnu and Balance', 'This lesson explains Vishnu’s role in maintaining balance and protecting dharma.', 2, 2),
(6, 'Vishnu in Mythology', 'This lesson explores simple mythology connected to Vishnu and his divine incarnations.', 3, 2),
(7, 'Introduction to Ganesha', 'This lesson introduces Ganesha as the remover of obstacles and god of wisdom.', 1, 3),
(8, 'Ganesha and New Beginnings', 'This lesson explains Ganesha’s connection to knowledge, success, and new beginnings.', 2, 3),
(9, 'Ganesha in Mythology', 'This lesson explores simple mythology connected to Ganesha and his symbolic meaning.', 3, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course_lessons`
--
ALTER TABLE `course_lessons`
  ADD PRIMARY KEY (`lesson_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course_lessons`
--
ALTER TABLE `course_lessons`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
