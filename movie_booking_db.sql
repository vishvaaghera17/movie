-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2026 at 07:29 AM
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
-- Database: `movie_booking_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `booked_seats`
--

CREATE TABLE `booked_seats` (
  `booking_seat_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `seat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booked_seats`
--

INSERT INTO `booked_seats` (`booking_seat_id`, `booking_id`, `seat_id`) VALUES
(3, 2, 82),
(4, 2, 83),
(14, 4, 159),
(15, 4, 158),
(16, 5, 84),
(17, 6, 92),
(18, 6, 93),
(19, 7, 355),
(20, 8, 85),
(21, 9, 85),
(22, 10, 86),
(23, 11, 321);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `show_id` int(11) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_seats` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('confirmed','cancelled','pending') DEFAULT 'pending',
  `payment_status` enum('paid','unpaid','refunded') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `show_id`, `booking_date`, `total_seats`, `total_amount`, `status`, `payment_status`) VALUES
(1, 4, 6, '2026-02-28 18:40:20', 2, 500.00, 'cancelled', 'refunded'),
(2, 4, 8, '2026-03-01 15:45:20', 2, 500.00, 'confirmed', 'paid'),
(3, 7, 8, '2026-03-02 03:48:37', 9, 2250.00, 'cancelled', 'refunded'),
(4, 4, 8, '2026-03-02 05:01:10', 2, 500.00, 'confirmed', 'paid'),
(5, 4, 8, '2026-03-05 03:54:25', 1, 250.00, 'confirmed', 'paid'),
(6, 4, 8, '2026-03-07 03:10:01', 2, 500.00, 'confirmed', 'paid'),
(7, 4, 6, '2026-03-11 04:21:13', 1, 250.00, 'confirmed', 'paid'),
(8, 4, 8, '2026-03-12 03:35:45', 1, 250.00, 'confirmed', 'paid'),
(9, 4, 8, '2026-03-12 03:36:33', 1, 250.00, 'confirmed', 'paid'),
(10, 4, 8, '2026-03-12 04:48:25', 1, 250.00, 'confirmed', 'paid'),
(11, 4, 6, '2026-03-12 05:09:16', 1, 250.00, 'confirmed', 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `movie_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `status` enum('now_showing','coming_soon','ended') DEFAULT 'now_showing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`movie_id`, `title`, `description`, `duration`, `genre`, `language`, `release_date`, `rating`, `image_url`, `status`, `created_at`) VALUES
(1, 'Drishyam 3', 'continues Georgekutty’s fight to protect his family as hidden secrets and new investigations threaten to expose the truth.', 140, 'Thriller', 'Hindi', '2026-10-02', 8.5, 'uploads/drishyam3.jpg', 'now_showing', '2026-02-28 08:36:35'),
(2, 'King', 'King follows a powerful and mysterious man who enters the criminal underworld to protect his loved ones and take revenge against his enemies.', 120, 'Action', 'Hindi', '2026-04-15', 7.8, 'uploads/king.jpg', 'now_showing', '2026-02-28 08:36:35'),
(3, 'Bhoot Bangla', 'Bhoot Bangla is a horror-comedy about a man who enters a haunted mansion and faces funny yet frightening supernatural events.', 135, 'Horror', 'Hindi', '2026-05-15', 8.9, 'uploads/bhootbangla.jpg', 'now_showing', '2026-02-28 08:36:35'),
(4, 'Mahavtar', 'A beautiful love story', 110, 'Animation', 'Hindi', '2026-08-10', 7.5, 'uploads/mahavtar.jpg', 'now_showing', '2026-02-28 08:36:35'),
(5, 'Galwan', 'Galwan is based on the real-life clash between Indian and Chinese soldiers in the Galwan Valley, highlighting bravery, sacrifice, and patriotism.', 150, 'War', 'Hindi', '2026-07-15', 9.0, 'uploads/galwan.jpg', 'now_showing', '2026-02-28 08:36:35'),
(6, 'Dhurandhar', 'Dhurandhar is a high-octane action thriller about a skilled undercover agent who enters dangerous missions to expose powerful enemies and protect the nation.', 150, 'Action', 'Hindi', '2026-03-02', 8.5, 'uploads/1772296576_dhurandhar.jpg', 'now_showing', '2026-02-28 16:36:16'),
(8, 'Border 2', 'Border 2 portrays the courage and sacrifice of Indian soldiers as they defend the nation’s borders against overwhelming odds.', 150, 'Action', 'Hindi', '2026-03-12', 8.2, 'uploads/1772383448_border2.jpg', 'coming_soon', '2026-03-01 16:44:08');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `razorpay_order_id` varchar(100) DEFAULT NULL,
  `razorpay_payment_id` varchar(100) DEFAULT NULL,
  `razorpay_signature` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `user_id`, `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature`, `amount`, `payment_method`, `payment_status`, `payment_date`) VALUES
(1, 8, 4, NULL, 'pay_SQAO3iLTnzO3B2', NULL, 250.00, 'razorpay', 'paid', '2026-03-12 03:35:45'),
(2, 9, 4, NULL, 'pay_SQATte4Tej59qJ', NULL, 250.00, 'razorpay', 'paid', '2026-03-12 03:36:33'),
(3, 10, 4, NULL, 'pay_SQBepAwGZ3JtBC', NULL, 250.00, 'razorpay', 'paid', '2026-03-12 04:48:25'),
(4, 11, 4, '', 'pay_SQC3nKbYjdBWu4', '0', 250.00, 'razorpay', 'paid', '2026-03-12 05:09:16');

-- --------------------------------------------------------

--
-- Table structure for table `screens`
--

CREATE TABLE `screens` (
  `screen_id` int(11) NOT NULL,
  `theater_id` int(11) DEFAULT NULL,
  `screen_name` varchar(50) DEFAULT NULL,
  `total_seats` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `screens`
--

INSERT INTO `screens` (`screen_id`, `theater_id`, `screen_name`, `total_seats`) VALUES
(1, 1, 'Screen 1', 100),
(2, 1, 'Screen 2', 120),
(3, 2, 'Screen 1', 150),
(4, 2, 'Screen 2', 100),
(5, 3, 'Screen 1', 80);

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `seat_id` int(11) NOT NULL,
  `screen_id` int(11) DEFAULT NULL,
  `seat_number` varchar(10) DEFAULT NULL,
  `seat_row` char(2) DEFAULT NULL,
  `seat_type` enum('regular','premium','vip') DEFAULT 'regular'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`seat_id`, `screen_id`, `seat_number`, `seat_row`, `seat_type`) VALUES
(1, 4, '1', 'A', 'regular'),
(2, 4, '2', 'A', 'regular'),
(3, 4, '3', 'A', 'regular'),
(4, 4, '4', 'A', 'regular'),
(5, 4, '5', 'A', 'regular'),
(6, 4, '6', 'A', 'regular'),
(7, 4, '7', 'A', 'regular'),
(8, 4, '8', 'A', 'regular'),
(9, 4, '9', 'A', 'regular'),
(10, 4, '10', 'A', 'regular'),
(11, 4, '1', 'B', 'regular'),
(12, 4, '2', 'B', 'regular'),
(13, 4, '3', 'B', 'regular'),
(14, 4, '4', 'B', 'regular'),
(15, 4, '5', 'B', 'regular'),
(16, 4, '6', 'B', 'regular'),
(17, 4, '7', 'B', 'regular'),
(18, 4, '8', 'B', 'regular'),
(19, 4, '9', 'B', 'regular'),
(20, 4, '10', 'B', 'regular'),
(21, 4, '1', 'C', 'regular'),
(22, 4, '2', 'C', 'regular'),
(23, 4, '3', 'C', 'regular'),
(24, 4, '4', 'C', 'regular'),
(25, 4, '5', 'C', 'regular'),
(26, 4, '6', 'C', 'regular'),
(27, 4, '7', 'C', 'regular'),
(28, 4, '8', 'C', 'regular'),
(29, 4, '9', 'C', 'regular'),
(30, 4, '10', 'C', 'regular'),
(31, 4, '1', 'D', 'regular'),
(32, 4, '2', 'D', 'regular'),
(33, 4, '3', 'D', 'regular'),
(34, 4, '4', 'D', 'regular'),
(35, 4, '5', 'D', 'regular'),
(36, 4, '6', 'D', 'regular'),
(37, 4, '7', 'D', 'regular'),
(38, 4, '8', 'D', 'regular'),
(39, 4, '9', 'D', 'regular'),
(40, 4, '10', 'D', 'regular'),
(41, 4, '1', 'E', 'regular'),
(42, 4, '2', 'E', 'regular'),
(43, 4, '3', 'E', 'regular'),
(44, 4, '4', 'E', 'regular'),
(45, 4, '5', 'E', 'regular'),
(46, 4, '6', 'E', 'regular'),
(47, 4, '7', 'E', 'regular'),
(48, 4, '8', 'E', 'regular'),
(49, 4, '9', 'E', 'regular'),
(50, 4, '10', 'E', 'regular'),
(51, 4, '1', 'F', 'regular'),
(52, 4, '2', 'F', 'regular'),
(53, 4, '3', 'F', 'regular'),
(54, 4, '4', 'F', 'regular'),
(55, 4, '5', 'F', 'regular'),
(56, 4, '6', 'F', 'regular'),
(57, 4, '7', 'F', 'regular'),
(58, 4, '8', 'F', 'regular'),
(59, 4, '9', 'F', 'regular'),
(60, 4, '10', 'F', 'regular'),
(61, 4, '1', 'G', 'regular'),
(62, 4, '2', 'G', 'regular'),
(63, 4, '3', 'G', 'regular'),
(64, 4, '4', 'G', 'regular'),
(65, 4, '5', 'G', 'regular'),
(66, 4, '6', 'G', 'regular'),
(67, 4, '7', 'G', 'regular'),
(68, 4, '8', 'G', 'regular'),
(69, 4, '9', 'G', 'regular'),
(70, 4, '10', 'G', 'regular'),
(71, 4, '1', 'H', 'regular'),
(72, 4, '2', 'H', 'regular'),
(73, 4, '3', 'H', 'regular'),
(74, 4, '4', 'H', 'regular'),
(75, 4, '5', 'H', 'regular'),
(76, 4, '6', 'H', 'regular'),
(77, 4, '7', 'H', 'regular'),
(78, 4, '8', 'H', 'regular'),
(79, 4, '9', 'H', 'regular'),
(80, 4, '10', 'H', 'regular'),
(81, 5, '1', 'A', 'regular'),
(82, 5, '2', 'A', 'regular'),
(83, 5, '3', 'A', 'regular'),
(84, 5, '4', 'A', 'regular'),
(85, 5, '5', 'A', 'regular'),
(86, 5, '6', 'A', 'regular'),
(87, 5, '7', 'A', 'regular'),
(88, 5, '8', 'A', 'regular'),
(89, 5, '9', 'A', 'regular'),
(90, 5, '10', 'A', 'regular'),
(91, 5, '1', 'B', 'regular'),
(92, 5, '2', 'B', 'regular'),
(93, 5, '3', 'B', 'regular'),
(94, 5, '4', 'B', 'regular'),
(95, 5, '5', 'B', 'regular'),
(96, 5, '6', 'B', 'regular'),
(97, 5, '7', 'B', 'regular'),
(98, 5, '8', 'B', 'regular'),
(99, 5, '9', 'B', 'regular'),
(100, 5, '10', 'B', 'regular'),
(101, 5, '1', 'C', 'regular'),
(102, 5, '2', 'C', 'regular'),
(103, 5, '3', 'C', 'regular'),
(104, 5, '4', 'C', 'regular'),
(105, 5, '5', 'C', 'regular'),
(106, 5, '6', 'C', 'regular'),
(107, 5, '7', 'C', 'regular'),
(108, 5, '8', 'C', 'regular'),
(109, 5, '9', 'C', 'regular'),
(110, 5, '10', 'C', 'regular'),
(111, 5, '1', 'D', 'regular'),
(112, 5, '2', 'D', 'regular'),
(113, 5, '3', 'D', 'regular'),
(114, 5, '4', 'D', 'regular'),
(115, 5, '5', 'D', 'regular'),
(116, 5, '6', 'D', 'regular'),
(117, 5, '7', 'D', 'regular'),
(118, 5, '8', 'D', 'regular'),
(119, 5, '9', 'D', 'regular'),
(120, 5, '10', 'D', 'regular'),
(121, 5, '1', 'E', 'regular'),
(122, 5, '2', 'E', 'regular'),
(123, 5, '3', 'E', 'regular'),
(124, 5, '4', 'E', 'regular'),
(125, 5, '5', 'E', 'regular'),
(126, 5, '6', 'E', 'regular'),
(127, 5, '7', 'E', 'regular'),
(128, 5, '8', 'E', 'regular'),
(129, 5, '9', 'E', 'regular'),
(130, 5, '10', 'E', 'regular'),
(131, 5, '1', 'F', 'regular'),
(132, 5, '2', 'F', 'regular'),
(133, 5, '3', 'F', 'regular'),
(134, 5, '4', 'F', 'regular'),
(135, 5, '5', 'F', 'regular'),
(136, 5, '6', 'F', 'regular'),
(137, 5, '7', 'F', 'regular'),
(138, 5, '8', 'F', 'regular'),
(139, 5, '9', 'F', 'regular'),
(140, 5, '10', 'F', 'regular'),
(141, 5, '1', 'G', 'regular'),
(142, 5, '2', 'G', 'regular'),
(143, 5, '3', 'G', 'regular'),
(144, 5, '4', 'G', 'regular'),
(145, 5, '5', 'G', 'regular'),
(146, 5, '6', 'G', 'regular'),
(147, 5, '7', 'G', 'regular'),
(148, 5, '8', 'G', 'regular'),
(149, 5, '9', 'G', 'regular'),
(150, 5, '10', 'G', 'regular'),
(151, 5, '1', 'H', 'regular'),
(152, 5, '2', 'H', 'regular'),
(153, 5, '3', 'H', 'regular'),
(154, 5, '4', 'H', 'regular'),
(155, 5, '5', 'H', 'regular'),
(156, 5, '6', 'H', 'regular'),
(157, 5, '7', 'H', 'regular'),
(158, 5, '8', 'H', 'regular'),
(159, 5, '9', 'H', 'regular'),
(160, 5, '10', 'H', 'regular'),
(161, 3, '1', 'A', 'regular'),
(162, 3, '2', 'A', 'regular'),
(163, 3, '3', 'A', 'regular'),
(164, 3, '4', 'A', 'regular'),
(165, 3, '5', 'A', 'regular'),
(166, 3, '6', 'A', 'regular'),
(167, 3, '7', 'A', 'regular'),
(168, 3, '8', 'A', 'regular'),
(169, 3, '9', 'A', 'regular'),
(170, 3, '10', 'A', 'regular'),
(171, 3, '1', 'B', 'regular'),
(172, 3, '2', 'B', 'regular'),
(173, 3, '3', 'B', 'regular'),
(174, 3, '4', 'B', 'regular'),
(175, 3, '5', 'B', 'regular'),
(176, 3, '6', 'B', 'regular'),
(177, 3, '7', 'B', 'regular'),
(178, 3, '8', 'B', 'regular'),
(179, 3, '9', 'B', 'regular'),
(180, 3, '10', 'B', 'regular'),
(181, 3, '1', 'C', 'regular'),
(182, 3, '2', 'C', 'regular'),
(183, 3, '3', 'C', 'regular'),
(184, 3, '4', 'C', 'regular'),
(185, 3, '5', 'C', 'regular'),
(186, 3, '6', 'C', 'regular'),
(187, 3, '7', 'C', 'regular'),
(188, 3, '8', 'C', 'regular'),
(189, 3, '9', 'C', 'regular'),
(190, 3, '10', 'C', 'regular'),
(191, 3, '1', 'D', 'regular'),
(192, 3, '2', 'D', 'regular'),
(193, 3, '3', 'D', 'regular'),
(194, 3, '4', 'D', 'regular'),
(195, 3, '5', 'D', 'regular'),
(196, 3, '6', 'D', 'regular'),
(197, 3, '7', 'D', 'regular'),
(198, 3, '8', 'D', 'regular'),
(199, 3, '9', 'D', 'regular'),
(200, 3, '10', 'D', 'regular'),
(201, 3, '1', 'E', 'regular'),
(202, 3, '2', 'E', 'regular'),
(203, 3, '3', 'E', 'regular'),
(204, 3, '4', 'E', 'regular'),
(205, 3, '5', 'E', 'regular'),
(206, 3, '6', 'E', 'regular'),
(207, 3, '7', 'E', 'regular'),
(208, 3, '8', 'E', 'regular'),
(209, 3, '9', 'E', 'regular'),
(210, 3, '10', 'E', 'regular'),
(211, 3, '1', 'F', 'regular'),
(212, 3, '2', 'F', 'regular'),
(213, 3, '3', 'F', 'regular'),
(214, 3, '4', 'F', 'regular'),
(215, 3, '5', 'F', 'regular'),
(216, 3, '6', 'F', 'regular'),
(217, 3, '7', 'F', 'regular'),
(218, 3, '8', 'F', 'regular'),
(219, 3, '9', 'F', 'regular'),
(220, 3, '10', 'F', 'regular'),
(221, 3, '1', 'G', 'regular'),
(222, 3, '2', 'G', 'regular'),
(223, 3, '3', 'G', 'regular'),
(224, 3, '4', 'G', 'regular'),
(225, 3, '5', 'G', 'regular'),
(226, 3, '6', 'G', 'regular'),
(227, 3, '7', 'G', 'regular'),
(228, 3, '8', 'G', 'regular'),
(229, 3, '9', 'G', 'regular'),
(230, 3, '10', 'G', 'regular'),
(231, 3, '1', 'H', 'regular'),
(232, 3, '2', 'H', 'regular'),
(233, 3, '3', 'H', 'regular'),
(234, 3, '4', 'H', 'regular'),
(235, 3, '5', 'H', 'regular'),
(236, 3, '6', 'H', 'regular'),
(237, 3, '7', 'H', 'regular'),
(238, 3, '8', 'H', 'regular'),
(239, 3, '9', 'H', 'regular'),
(240, 3, '10', 'H', 'regular'),
(241, 2, '1', 'A', 'regular'),
(242, 2, '2', 'A', 'regular'),
(243, 2, '3', 'A', 'regular'),
(244, 2, '4', 'A', 'regular'),
(245, 2, '5', 'A', 'regular'),
(246, 2, '6', 'A', 'regular'),
(247, 2, '7', 'A', 'regular'),
(248, 2, '8', 'A', 'regular'),
(249, 2, '9', 'A', 'regular'),
(250, 2, '10', 'A', 'regular'),
(251, 2, '1', 'B', 'regular'),
(252, 2, '2', 'B', 'regular'),
(253, 2, '3', 'B', 'regular'),
(254, 2, '4', 'B', 'regular'),
(255, 2, '5', 'B', 'regular'),
(256, 2, '6', 'B', 'regular'),
(257, 2, '7', 'B', 'regular'),
(258, 2, '8', 'B', 'regular'),
(259, 2, '9', 'B', 'regular'),
(260, 2, '10', 'B', 'regular'),
(261, 2, '1', 'C', 'regular'),
(262, 2, '2', 'C', 'regular'),
(263, 2, '3', 'C', 'regular'),
(264, 2, '4', 'C', 'regular'),
(265, 2, '5', 'C', 'regular'),
(266, 2, '6', 'C', 'regular'),
(267, 2, '7', 'C', 'regular'),
(268, 2, '8', 'C', 'regular'),
(269, 2, '9', 'C', 'regular'),
(270, 2, '10', 'C', 'regular'),
(271, 2, '1', 'D', 'regular'),
(272, 2, '2', 'D', 'regular'),
(273, 2, '3', 'D', 'regular'),
(274, 2, '4', 'D', 'regular'),
(275, 2, '5', 'D', 'regular'),
(276, 2, '6', 'D', 'regular'),
(277, 2, '7', 'D', 'regular'),
(278, 2, '8', 'D', 'regular'),
(279, 2, '9', 'D', 'regular'),
(280, 2, '10', 'D', 'regular'),
(281, 2, '1', 'E', 'regular'),
(282, 2, '2', 'E', 'regular'),
(283, 2, '3', 'E', 'regular'),
(284, 2, '4', 'E', 'regular'),
(285, 2, '5', 'E', 'regular'),
(286, 2, '6', 'E', 'regular'),
(287, 2, '7', 'E', 'regular'),
(288, 2, '8', 'E', 'regular'),
(289, 2, '9', 'E', 'regular'),
(290, 2, '10', 'E', 'regular'),
(291, 2, '1', 'F', 'regular'),
(292, 2, '2', 'F', 'regular'),
(293, 2, '3', 'F', 'regular'),
(294, 2, '4', 'F', 'regular'),
(295, 2, '5', 'F', 'regular'),
(296, 2, '6', 'F', 'regular'),
(297, 2, '7', 'F', 'regular'),
(298, 2, '8', 'F', 'regular'),
(299, 2, '9', 'F', 'regular'),
(300, 2, '10', 'F', 'regular'),
(301, 2, '1', 'G', 'regular'),
(302, 2, '2', 'G', 'regular'),
(303, 2, '3', 'G', 'regular'),
(304, 2, '4', 'G', 'regular'),
(305, 2, '5', 'G', 'regular'),
(306, 2, '6', 'G', 'regular'),
(307, 2, '7', 'G', 'regular'),
(308, 2, '8', 'G', 'regular'),
(309, 2, '9', 'G', 'regular'),
(310, 2, '10', 'G', 'regular'),
(311, 2, '1', 'H', 'regular'),
(312, 2, '2', 'H', 'regular'),
(313, 2, '3', 'H', 'regular'),
(314, 2, '4', 'H', 'regular'),
(315, 2, '5', 'H', 'regular'),
(316, 2, '6', 'H', 'regular'),
(317, 2, '7', 'H', 'regular'),
(318, 2, '8', 'H', 'regular'),
(319, 2, '9', 'H', 'regular'),
(320, 2, '10', 'H', 'regular'),
(321, 1, '1', 'A', 'regular'),
(322, 1, '2', 'A', 'regular'),
(323, 1, '3', 'A', 'regular'),
(324, 1, '4', 'A', 'regular'),
(325, 1, '5', 'A', 'regular'),
(326, 1, '6', 'A', 'regular'),
(327, 1, '7', 'A', 'regular'),
(328, 1, '8', 'A', 'regular'),
(329, 1, '9', 'A', 'regular'),
(330, 1, '10', 'A', 'regular'),
(331, 1, '1', 'B', 'regular'),
(332, 1, '2', 'B', 'regular'),
(333, 1, '3', 'B', 'regular'),
(334, 1, '4', 'B', 'regular'),
(335, 1, '5', 'B', 'regular'),
(336, 1, '6', 'B', 'regular'),
(337, 1, '7', 'B', 'regular'),
(338, 1, '8', 'B', 'regular'),
(339, 1, '9', 'B', 'regular'),
(340, 1, '10', 'B', 'regular'),
(341, 1, '1', 'C', 'regular'),
(342, 1, '2', 'C', 'regular'),
(343, 1, '3', 'C', 'regular'),
(344, 1, '4', 'C', 'regular'),
(345, 1, '5', 'C', 'regular'),
(346, 1, '6', 'C', 'regular'),
(347, 1, '7', 'C', 'regular'),
(348, 1, '8', 'C', 'regular'),
(349, 1, '9', 'C', 'regular'),
(350, 1, '10', 'C', 'regular'),
(351, 1, '1', 'D', 'regular'),
(352, 1, '2', 'D', 'regular'),
(353, 1, '3', 'D', 'regular'),
(354, 1, '4', 'D', 'regular'),
(355, 1, '5', 'D', 'regular'),
(356, 1, '6', 'D', 'regular'),
(357, 1, '7', 'D', 'regular'),
(358, 1, '8', 'D', 'regular'),
(359, 1, '9', 'D', 'regular'),
(360, 1, '10', 'D', 'regular'),
(361, 1, '1', 'E', 'regular'),
(362, 1, '2', 'E', 'regular'),
(363, 1, '3', 'E', 'regular'),
(364, 1, '4', 'E', 'regular'),
(365, 1, '5', 'E', 'regular'),
(366, 1, '6', 'E', 'regular'),
(367, 1, '7', 'E', 'regular'),
(368, 1, '8', 'E', 'regular'),
(369, 1, '9', 'E', 'regular'),
(370, 1, '10', 'E', 'regular'),
(371, 1, '1', 'F', 'regular'),
(372, 1, '2', 'F', 'regular'),
(373, 1, '3', 'F', 'regular'),
(374, 1, '4', 'F', 'regular'),
(375, 1, '5', 'F', 'regular'),
(376, 1, '6', 'F', 'regular'),
(377, 1, '7', 'F', 'regular'),
(378, 1, '8', 'F', 'regular'),
(379, 1, '9', 'F', 'regular'),
(380, 1, '10', 'F', 'regular'),
(381, 1, '1', 'G', 'regular'),
(382, 1, '2', 'G', 'regular'),
(383, 1, '3', 'G', 'regular'),
(384, 1, '4', 'G', 'regular'),
(385, 1, '5', 'G', 'regular'),
(386, 1, '6', 'G', 'regular'),
(387, 1, '7', 'G', 'regular'),
(388, 1, '8', 'G', 'regular'),
(389, 1, '9', 'G', 'regular'),
(390, 1, '10', 'G', 'regular'),
(391, 1, '1', 'H', 'regular'),
(392, 1, '2', 'H', 'regular'),
(393, 1, '3', 'H', 'regular'),
(394, 1, '4', 'H', 'regular'),
(395, 1, '5', 'H', 'regular'),
(396, 1, '6', 'H', 'regular'),
(397, 1, '7', 'H', 'regular'),
(398, 1, '8', 'H', 'regular'),
(399, 1, '9', 'H', 'regular'),
(400, 1, '10', 'H', 'regular');

-- --------------------------------------------------------

--
-- Table structure for table `shows`
--

CREATE TABLE `shows` (
  `show_id` int(11) NOT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `screen_id` int(11) DEFAULT NULL,
  `show_date` date DEFAULT NULL,
  `show_time` time DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `available_seats` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shows`
--

INSERT INTO `shows` (`show_id`, `movie_id`, `screen_id`, `show_date`, `show_time`, `price`, `available_seats`) VALUES
(6, 1, 1, '2026-10-29', '22:00:00', 250.00, 98),
(7, 3, 2, '2026-05-15', '21:00:00', 280.00, 100),
(8, 1, 5, '2026-10-29', '06:00:00', 250.00, 93),
(9, 2, 4, '2026-04-15', '09:00:00', 250.00, 100);

-- --------------------------------------------------------

--
-- Table structure for table `theaters`
--

CREATE TABLE `theaters` (
  `theater_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `total_screens` int(11) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theaters`
--

INSERT INTO `theaters` (`theater_id`, `name`, `address`, `city`, `total_screens`, `contact_number`, `created_at`) VALUES
(1, 'Rajhans', 'Italva', 'Navsari', 4, '022-12345678', '2026-02-28 08:36:35'),
(2, 'Royal', 'staion , Road', 'Navsari', 5, '011-87654321', '2026-02-28 08:36:35'),
(3, 'Mukta', 'Station road ', 'Navsari', 3, '080-11223344', '2026-02-28 08:36:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `user_type` enum('admin','user','staff') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `full_name`, `phone`, `user_type`, `created_at`, `status`) VALUES
(4, 'v_17', 'v@gmail.com', '$2y$10$9DWQdOLJuloek/7Caf4baugQzTqe5w4ud4Pz28wOSqwPOkgAN7bq6', 'Vishva', '9876543212', 'user', '2026-02-28 09:20:05', 'active'),
(5, 'vishu_17', 'vishu@gmail.com', '$2y$10$V7X2lr8zQX9omnlPFeTMmOOk4Rk/i1J7hb6UfAOZJAGNw81QN.PSy', 'Vishva Aghera', '9876543210', 'admin', '2026-02-28 09:26:30', 'active'),
(6, 'v_1711', 'v17@gmail.com', '$2y$10$6ZP0OdBb1wzxfJZiC6u7WuI1TYjHr0Y/dY8tIUXqhizpEcEiidT0O', 'Vishva', '9876543212', 'staff', '2026-02-28 12:05:37', 'active'),
(7, 'nilenpatel', 'nilenp22@gmail.com', '$2y$10$45mlS6hL8IpuCMVWjH/.8OGG0.4iwsautZPhVq.daDcHfFlk11uU2', 'Nilen Patel', '8238258991', 'admin', '2026-03-02 03:47:40', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booked_seats`
--
ALTER TABLE `booked_seats`
  ADD PRIMARY KEY (`booking_seat_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `seat_id` (`seat_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `show_id` (`show_id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`movie_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `screens`
--
ALTER TABLE `screens`
  ADD PRIMARY KEY (`screen_id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`seat_id`),
  ADD KEY `screen_id` (`screen_id`);

--
-- Indexes for table `shows`
--
ALTER TABLE `shows`
  ADD PRIMARY KEY (`show_id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `screen_id` (`screen_id`);

--
-- Indexes for table `theaters`
--
ALTER TABLE `theaters`
  ADD PRIMARY KEY (`theater_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booked_seats`
--
ALTER TABLE `booked_seats`
  MODIFY `booking_seat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `movie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `screens`
--
ALTER TABLE `screens`
  MODIFY `screen_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `seat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=401;

--
-- AUTO_INCREMENT for table `shows`
--
ALTER TABLE `shows`
  MODIFY `show_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `theaters`
--
ALTER TABLE `theaters`
  MODIFY `theater_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booked_seats`
--
ALTER TABLE `booked_seats`
  ADD CONSTRAINT `booked_seats_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booked_seats_ibfk_2` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`seat_id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`show_id`) REFERENCES `shows` (`show_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `screens`
--
ALTER TABLE `screens`
  ADD CONSTRAINT `screens_ibfk_1` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`theater_id`) ON DELETE CASCADE;

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`screen_id`) REFERENCES `screens` (`screen_id`) ON DELETE CASCADE;

--
-- Constraints for table `shows`
--
ALTER TABLE `shows`
  ADD CONSTRAINT `shows_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shows_ibfk_2` FOREIGN KEY (`screen_id`) REFERENCES `screens` (`screen_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
