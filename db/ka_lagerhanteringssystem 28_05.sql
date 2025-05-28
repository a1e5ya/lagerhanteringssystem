-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 28, 2025 at 05:13 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ka_lagerhanteringssystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

CREATE TABLE `author` (
  `author_id` int NOT NULL,
  `author_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `author`
--

INSERT INTO `author` (`author_id`, `author_name`) VALUES
(1, 'Väinö Linna'),
(2, 'Mika Waltari'),
(3, 'Aleksis Kivi'),
(4, 'Sofi Oksanen'),
(5, 'Arto Paasilinna'),
(6, 'Astrid Lindgren'),
(7, 'Stieg Larsson'),
(8, 'Henning Mankell'),
(9, 'Tove Jansson'),
(10, 'Agatha Christie'),
(11, 'Arthur Conan Doyle'),
(12, 'J.R.R. Tolkien'),
(13, 'George Orwell'),
(14, 'Jane Austen'),
(15, 'Charles Dickens'),
(16, 'William Shakespeare'),
(17, 'Ernest Hemingway'),
(18, 'F. Scott Fitzgerald'),
(19, 'Harper Lee'),
(20, 'Mark Twain'),
(21, 'Leo Tolstoy'),
(22, 'Fyodor Dostoevsky'),
(23, 'Franz Kafka'),
(24, 'Virginia Woolf'),
(25, 'James Joyce'),
(26, 'Roald Dahl'),
(27, 'Lewis Carroll'),
(28, 'C.S. Lewis'),
(29, 'Ray Bradbury'),
(30, 'Isaac Asimov'),
(31, 'Jean Sibelius'),
(32, 'The Beatles'),
(33, 'Elvis Presley'),
(34, 'Bob Dylan'),
(35, 'ABBA'),
(36, 'Pink Floyd'),
(37, 'Queen'),
(38, 'Led Zeppelin'),
(39, 'David Bowie'),
(40, 'The Rolling Stones'),
(41, 'Juice Leskinen'),
(42, 'Eppu Normaali'),
(43, 'Dingo'),
(44, 'Hassisen Kone'),
(45, 'Leevi and the Leavings'),
(46, 'Aki Kaurismäki'),
(47, 'Ingmar Bergman'),
(48, 'Steven Spielberg'),
(49, 'Christopher Nolan'),
(50, 'Alfred Hitchcock'),
(51, 'Hergé'),
(52, 'René Goscinny'),
(53, 'Albert Uderzo'),
(54, 'Carl Barks'),
(55, 'Don Rosa'),
(56, 'Stan Lee'),
(57, 'Jack Kirby'),
(58, 'Alan Moore'),
(59, 'Frank Miller'),
(60, 'Neil Gaiman'),
(61, 'Art Spiegelman'),
(62, 'Marjane Satrapi'),
(63, 'Mauri Kunnas'),
(64, 'Turk'),
(65, 'Peyo');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int NOT NULL,
  `category_sv_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category_fi_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_sv_name`, `category_fi_name`) VALUES
(1, 'Bok', 'Kirja'),
(5, 'CD', 'CD'),
(6, 'Vinyl', 'Vinyyli'),
(7, 'DVD', 'DVD'),
(8, 'Serier', 'Sarjakuva'),
(9, 'Samlarobjekt', 'Keräilyesine'),
(12, 'Spel', 'Pelit'),
(13, 'Tidningar', 'Lehdet');

-- --------------------------------------------------------

--
-- Table structure for table `condition`
--

CREATE TABLE `condition` (
  `condition_id` int NOT NULL,
  `condition_sv_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `condition_fi_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `condition_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `condition`
--

INSERT INTO `condition` (`condition_id`, `condition_sv_name`, `condition_fi_name`, `condition_code`) VALUES
(1, 'Nyskick', 'Erinomainen', 'K-4'),
(2, 'Mycket bra', 'Erittäin hyvä', 'K-3'),
(3, 'Bra', 'Hyvä', 'K-2'),
(4, 'Acceptabelt', 'Hyväksyttävä', 'K-1');

-- --------------------------------------------------------

--
-- Table structure for table `event_log`
--

CREATE TABLE `event_log` (
  `event_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `event_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `event_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `event_timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `product_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_log`
--

INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES
(1, NULL, 'create', 'Skapade produkt: Trollvinter', '2025-04-22 11:21:34', NULL),
(2, NULL, 'create', 'Skapade produkt: Muumipeikko ja pyrstötähti', '2025-04-22 11:21:34', NULL),
(3, NULL, 'update', 'Uppdaterade pris på: Harry Potter och De Vises Sten', '2025-04-22 11:21:34', NULL),
(4, NULL, 'create', 'Skapade produkt: Sibelius Symphony No. 2', '2025-04-22 11:21:34', NULL),
(5, NULL, 'login', 'Backdoor login used for admin', '2025-05-12 10:53:38', NULL),
(198, 1, 'logout', 'User logged out: admin', '2025-05-15 13:30:36', NULL),
(199, 1, 'login', 'Backdoor login used for admin', '2025-05-15 13:30:52', NULL),
(200, 1, 'logout', 'User logged out: admin', '2025-05-19 12:22:24', NULL),
(201, 1, 'login', 'Backdoor login used for admin', '2025-05-19 12:22:33', NULL),
(202, 1, 'logout', 'User logged out: admin', '2025-05-19 12:23:19', NULL),
(203, 1, 'logout', 'User logged out: Admin', '2025-05-27 08:58:34', NULL),
(204, 1, 'login', 'Successful login for user: Admin', '2025-05-27 10:41:47', NULL),
(205, 1, 'logout', 'User logged out: Admin', '2025-05-27 11:01:50', NULL),
(206, 1, 'login', 'Successful login for user: Admin', '2025-05-27 11:01:55', NULL),
(211, 1, 'batch_update_status', 'Batch operation: 30 produkter har fått ny status.', '2025-05-27 12:38:20', NULL),
(212, 1, 'batch_update_status', 'Batch operation: 30 produkter har fått ny status.', '2025-05-27 12:38:20', NULL),
(213, 1, 'batch_delete', 'Batch operation: 3 produkter har tagits bort.', '2025-05-27 12:38:33', NULL),
(214, 1, 'batch_delete', 'Batch operation: 3 produkter har tagits bort.', '2025-05-27 12:38:33', NULL),
(215, 1, 'batch_set_rare', 'Batch operation: 2 produkter markerade som sällsynta.', '2025-05-27 12:38:37', NULL),
(216, 1, 'batch_set_rare', 'Batch operation: 2 produkter markerade som sällsynta.', '2025-05-27 12:38:37', NULL),
(217, 1, 'batch_update_price', 'Batch operation: 6 produkter uppdaterade med nytt pris.', '2025-05-27 12:38:47', NULL),
(218, 1, 'batch_update_price', 'Batch operation: 6 produkter uppdaterade med nytt pris.', '2025-05-27 12:38:47', NULL),
(221, 1, 'logout', 'User logged out: Admin', '2025-05-27 12:52:55', NULL),
(222, 1, 'login', 'Successful login for user: Admin', '2025-05-27 12:53:00', NULL),
(224, 1, 'batch_set_rare', 'Batch operation: 21 produkter markerade som sällsynta.', '2025-05-27 12:54:23', NULL),
(225, 1, 'batch_set_rare', 'Batch operation: 21 produkter markerade som sällsynta.', '2025-05-27 12:54:23', NULL),
(226, 1, 'batch_set_rare', 'Batch operation: 21 produkter tog bort sällsynt-markeringen från.', '2025-05-27 12:54:28', NULL),
(227, 1, 'batch_set_rare', 'Batch operation: 21 produkter tog bort sällsynt-markeringen från.', '2025-05-27 12:54:28', NULL),
(228, 1, 'logout', 'User logged out: Admin', '2025-05-27 12:56:42', NULL),
(229, 1, 'login', 'Successful login for user: Admin', '2025-05-27 12:56:47', NULL),
(230, 1, 'update_subscriber', 'Newsletter subscriber deactivated (ID: 19)', '2025-05-27 12:59:46', NULL),
(231, 1, 'delete_subscriber', 'Newsletter subscriber deleted: lesya.maurin@gmail.com', '2025-05-27 13:00:19', NULL),
(232, 1, 'update_subscriber', 'Newsletter subscriber deactivated (ID: 1)', '2025-05-27 13:01:02', NULL),
(233, 1, 'create_author', 'Author created: Aleksandra Maurina', '2025-05-27 13:16:45', NULL),
(234, 1, 'update_author', 'Author updated: \'Aleksandra Maurina\' to \'Aleksandra Maurinaa\'', '2025-05-27 13:16:56', NULL),
(235, 1, 'update_author', 'Author updated: \'Aleksandra Maurinaa\' to \'Aleksandra Maurinaa\'', '2025-05-27 13:16:56', NULL),
(236, 1, 'create_author', 'Author created: Aleksandra Maurina', '2025-05-27 13:17:05', NULL),
(237, 1, 'delete_author', 'Author deleted: Aleksandra Maurina', '2025-05-27 13:17:19', NULL),
(238, 1, 'delete_author', 'Author deleted: Aleksandra Maurinaa', '2025-05-27 13:17:21', NULL),
(239, 1, 'create_author', 'Author created: Aleksandra Maurina', '2025-05-27 13:17:23', NULL),
(240, 1, 'update_author', 'Author updated: \'Aleksandra Maurina\' to \'Aleksandra Maurina\'', '2025-05-27 13:17:46', NULL),
(241, 1, 'update_author', 'Author updated: \'Aleksandra Maurina\' to \'Aleksandra Maurinaa\'', '2025-05-27 13:17:54', NULL),
(242, 1, 'create_author', 'Author created: Aleksandra Maurina', '2025-05-27 13:17:57', NULL),
(243, 1, 'delete_author', 'Author deleted: Aleksandra Maurinaa', '2025-05-27 13:18:01', NULL),
(244, 1, 'logout', 'User logged out: Admin', '2025-05-27 13:32:08', NULL),
(245, 3, 'login', 'Successful login for user: Redaktor', '2025-05-27 13:32:13', NULL),
(247, 3, 'logout', 'User logged out: Redaktor', '2025-05-27 13:32:28', NULL),
(248, 1, 'login', 'Successful login for user: Admin', '2025-05-27 13:32:34', NULL),
(249, 1, 'logout', 'User logged out: Admin', '2025-05-27 13:41:34', NULL),
(250, 3, 'login', 'Successful login for user: Redaktor', '2025-05-27 13:41:40', NULL),
(251, 3, 'logout', 'User logged out: Redaktor', '2025-05-27 13:42:14', NULL),
(252, 1, 'login', 'Successful login for user: Admin', '2025-05-27 13:42:19', NULL),
(256, 1, 'logout', 'User logged out: Admin', '2025-05-27 19:28:53', NULL),
(257, 1, 'login', 'Successful login for user: Admin', '2025-05-27 19:28:57', NULL),
(258, 1, 'create_author', 'Author created: Aleksandra Maurina 2', '2025-05-27 20:21:18', NULL),
(259, 1, 'delete_image', 'Raderade produktbild med ID: 4', '2025-05-27 20:41:05', NULL),
(267, 1, 'update', 'Uppdaterade produkt: 1984', '2025-05-28 00:37:59', 38),
(268, 1, 'update', 'Uppdaterade produkt: I, Robot', '2025-05-28 00:39:47', 64),
(269, 1, 'update', 'Uppdaterade produkt: Afrikan tähti', '2025-05-28 00:42:16', 142),
(270, 1, 'update', 'Uppdaterade produkt: Dungeons & Dragons Basic Set', '2025-05-28 00:42:52', 145),
(271, 1, 'update', 'Uppdaterade produkt: Monopol Helsingfors edition', '2025-05-28 00:43:47', 141),
(272, 1, 'update', 'Uppdaterade produkt: Trivial Pursuit Svenska', '2025-05-28 00:44:10', 143),
(273, 1, 'logout', 'User logged out: Admin', '2025-05-28 05:39:20', NULL),
(274, 1, 'login', 'Successful login for user: Admin', '2025-05-28 06:11:54', NULL),
(275, 1, 'create', 'Skapade produkt: AAAA', '2025-05-28 06:12:21', NULL),
(276, 1, 'batch_delete', 'Batch operation: 1 produkter har tagits bort.', '2025-05-28 06:13:33', NULL),
(277, 1, 'batch_set_special_price', 'Batch operation: 7 produkter markerade som rea.', '2025-05-28 06:24:11', NULL),
(278, 1, 'batch_set_special_price', 'Batch operation: 3 produkter markerade som rea.', '2025-05-28 06:24:31', NULL),
(279, 1, 'logout', 'User logged out: Admin', '2025-05-28 07:53:40', NULL),
(280, 1, 'login', 'Successful login for user: Admin', '2025-05-28 07:56:15', NULL),
(281, 1, 'update', 'Uppdaterade produkt: Farlig midsommar', '2025-05-28 07:57:05', 27),
(282, 1, 'update', 'Uppdaterade produkt: Pappan och havet', '2025-05-28 07:57:48', 29),
(283, 1, 'update', 'Uppdaterade produkt: Trollvinter', '2025-05-28 07:58:34', 28),
(284, 1, 'create', 'Skapade produkt: Product', '2025-05-28 08:06:45', NULL),
(285, 1, 'batch_delete', 'Batch operation: 1 produkter har tagits bort.', '2025-05-28 08:10:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE `genre` (
  `genre_id` int NOT NULL,
  `genre_sv_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `genre_fi_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genre`
--

INSERT INTO `genre` (`genre_id`, `genre_sv_name`, `genre_fi_name`) VALUES
(1, 'Romaner', 'Romaanit'),
(3, 'Historia', 'Historia'),
(4, 'Dikter', 'Runot'),
(5, 'Biografi', 'Elämäkerta'),
(6, 'Barnböcker', 'Lastenkirjat'),
(7, 'Rock', 'Rock'),
(8, 'Jazz', 'Jazz'),
(9, 'Klassisk', 'Klassinen'),
(10, 'Äventyr', 'Seikkailu'),
(11, '-', '-');

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE `image` (
  `image_id` int NOT NULL,
  `prod_id` int DEFAULT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `image_uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `image`
--

INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES
(1, 1, 'assets/uploads/products/ka-book-01.webp', '2025-05-28 06:35:00'),
(2, 2, 'assets/uploads/products/ka-book-02.webp', '2025-05-28 06:35:00'),
(3, 3, 'assets/uploads/products/ka-book-03.webp', '2025-05-28 06:35:00'),
(4, 4, 'assets/uploads/products/ka-book-04.webp', '2025-05-28 06:35:00'),
(5, 5, 'assets/uploads/products/ka-book-05.webp', '2025-05-28 06:35:00'),
(6, 6, 'assets/uploads/products/ka-book-06.webp', '2025-05-28 06:35:00'),
(7, 7, 'assets/uploads/products/ka-book-07.webp', '2025-05-28 06:35:00'),
(8, 8, 'assets/uploads/products/ka-book-08.webp', '2025-05-28 06:35:00'),
(9, 9, 'assets/uploads/products/ka-book-09.webp', '2025-05-28 06:35:00'),
(10, 10, 'assets/uploads/products/ka-book-10.webp', '2025-05-28 06:35:00'),
(11, 11, 'assets/uploads/products/ka-book-11.webp', '2025-05-28 06:35:00'),
(12, 12, 'assets/uploads/products/ka-book-12.webp', '2025-05-28 06:35:00'),
(13, 13, 'assets/uploads/products/ka-book-13.webp', '2025-05-28 06:35:00'),
(14, 14, 'assets/uploads/products/ka-book-14.webp', '2025-05-28 06:35:00'),
(15, 15, 'assets/uploads/products/ka-book-15.webp', '2025-05-28 06:35:00'),
(16, 16, 'assets/uploads/products/ka-child-01.webp', '2025-05-28 06:35:00'),
(17, 17, 'assets/uploads/products/ka-child-02.webp', '2025-05-28 06:35:00'),
(18, 18, 'assets/uploads/products/ka-child-03.webp', '2025-05-28 06:35:00'),
(19, 19, 'assets/uploads/products/ka-child-04.webp', '2025-05-28 06:35:00'),
(20, 20, 'assets/uploads/products/ka-book-16.webp', '2025-05-28 06:35:00'),
(21, 21, 'assets/uploads/products/ka-book-17.webp', '2025-05-28 06:35:00'),
(22, 22, 'assets/uploads/products/ka-book-18.webp', '2025-05-28 06:35:00'),
(23, 23, 'assets/uploads/products/ka-book-19.webp', '2025-05-28 06:35:00'),
(24, 24, 'assets/uploads/products/ka-book-20.webp', '2025-05-28 06:35:00'),
(25, 25, 'assets/uploads/products/ka-book-21.webp', '2025-05-28 06:35:00'),
(26, 26, 'assets/uploads/products/ka-child-05.webp', '2025-05-28 06:35:00'),
(30, 30, 'assets/uploads/products/ka-child-09.webp', '2025-05-28 06:35:00'),
(31, 31, 'assets/uploads/products/ka-book-22.webp', '2025-05-28 06:35:00'),
(32, 32, 'assets/uploads/products/ka-book-23.webp', '2025-05-28 06:35:00'),
(33, 33, 'assets/uploads/products/ka-book-24.webp', '2025-05-28 06:35:00'),
(34, 34, 'assets/uploads/products/ka-book-25.webp', '2025-05-28 06:35:00'),
(35, 35, 'assets/uploads/products/ka-book-26.webp', '2025-05-28 06:35:00'),
(36, 36, 'assets/uploads/products/ka-book-27.webp', '2025-05-28 06:35:00'),
(37, 37, 'assets/uploads/products/ka-book-28.webp', '2025-05-28 06:35:00'),
(38, 38, 'assets/uploads/products/ka-book-29.webp', '2025-05-28 06:35:00'),
(39, 39, 'assets/uploads/products/ka-book-30.webp', '2025-05-28 06:35:00'),
(40, 40, 'assets/uploads/products/ka-book-31.webp', '2025-05-28 06:35:00'),
(41, 41, 'assets/uploads/products/ka-book-32.webp', '2025-05-28 06:35:00'),
(42, 42, 'assets/uploads/products/ka-book-33.webp', '2025-05-28 06:35:00'),
(43, 43, 'assets/uploads/products/ka-book-34.webp', '2025-05-28 06:35:00'),
(44, 44, 'assets/uploads/products/ka-book-35.webp', '2025-05-28 06:35:00'),
(45, 45, 'assets/uploads/products/ka-book-36.webp', '2025-05-28 06:35:00'),
(46, 46, 'assets/uploads/products/ka-book-37.webp', '2025-05-28 06:35:00'),
(47, 47, 'assets/uploads/products/ka-book-38.webp', '2025-05-28 06:35:00'),
(48, 48, 'assets/uploads/products/ka-book-39.webp', '2025-05-28 06:35:00'),
(49, 49, 'assets/uploads/products/ka-random-01.webp', '2025-05-28 06:35:00'),
(50, 50, 'assets/uploads/products/ka-child-10.webp', '2025-05-28 06:35:00'),
(51, 51, 'assets/uploads/products/ka-random-02.webp', '2025-05-28 06:35:00'),
(52, 52, 'assets/uploads/products/ka-random-03.webp', '2025-05-28 06:35:00'),
(53, 53, 'assets/uploads/products/ka-random-04.webp', '2025-05-28 06:35:00'),
(54, 54, 'assets/uploads/products/ka-random-05.webp', '2025-05-28 06:35:00'),
(55, 55, 'assets/uploads/products/ka-random-06.webp', '2025-05-28 06:35:00'),
(56, 56, 'assets/uploads/products/ka-random-07.webp', '2025-05-28 06:35:00'),
(57, 57, 'assets/uploads/products/ka-child-11.webp', '2025-05-28 06:35:00'),
(58, 58, 'assets/uploads/products/ka-child-12.webp', '2025-05-28 06:35:00'),
(59, 59, 'assets/uploads/products/ka-child-13.webp', '2025-05-28 06:35:00'),
(60, 60, 'assets/uploads/products/ka-child-14.webp', '2025-05-28 06:35:00'),
(61, 61, 'assets/uploads/products/ka-random-08.webp', '2025-05-28 06:35:00'),
(62, 62, 'assets/uploads/products/ka-random-09.webp', '2025-05-28 06:35:00'),
(63, 63, 'assets/uploads/products/ka-random-10.webp', '2025-05-28 06:35:00'),
(64, 64, 'assets/uploads/products/ka-random-11.webp', '2025-05-28 06:35:00'),
(65, 65, 'assets/uploads/products/ka-random-12.webp', '2025-05-28 06:35:00'),
(66, 66, 'assets/uploads/products/ka-cd-01.webp', '2025-05-28 06:35:00'),
(67, 67, 'assets/uploads/products/ka-cd-02.webp', '2025-05-28 06:35:00'),
(68, 68, 'assets/uploads/products/ka-cd-03.webp', '2025-05-28 06:35:00'),
(69, 69, 'assets/uploads/products/ka-cd-04.webp', '2025-05-28 06:35:00'),
(70, 70, 'assets/uploads/products/ka-cd-05.webp', '2025-05-28 06:35:00'),
(71, 71, 'assets/uploads/products/ka-cd-06.webp', '2025-05-28 06:35:00'),
(72, 72, 'assets/uploads/products/ka-cd-07.webp', '2025-05-28 06:35:00'),
(73, 73, 'assets/uploads/products/ka-cd-08.webp', '2025-05-28 06:35:00'),
(74, 74, 'assets/uploads/products/ka-cd-09.webp', '2025-05-28 06:35:00'),
(75, 75, 'assets/uploads/products/ka-cd-10.webp', '2025-05-28 06:35:00'),
(76, 76, 'assets/uploads/products/ka-cd-11.webp', '2025-05-28 06:35:00'),
(77, 77, 'assets/uploads/products/ka-cd-12.webp', '2025-05-28 06:35:00'),
(78, 78, 'assets/uploads/products/ka-cd-13.webp', '2025-05-28 06:35:00'),
(79, 79, 'assets/uploads/products/ka-cd-14.webp', '2025-05-28 06:35:00'),
(80, 80, 'assets/uploads/products/ka-cd-15.webp', '2025-05-28 06:35:00'),
(81, 81, 'assets/uploads/products/ka-cd-16.webp', '2025-05-28 06:35:00'),
(82, 82, 'assets/uploads/products/ka-cd-17.webp', '2025-05-28 06:35:00'),
(83, 83, 'assets/uploads/products/ka-cd-18.webp', '2025-05-28 06:35:00'),
(84, 84, 'assets/uploads/products/ka-cd-19.webp', '2025-05-28 06:35:00'),
(85, 85, 'assets/uploads/products/ka-random-13.webp', '2025-05-28 06:35:00'),
(86, 86, 'assets/uploads/products/ka-random-14.webp', '2025-05-28 06:35:00'),
(87, 87, 'assets/uploads/products/ka-random-15.webp', '2025-05-28 06:35:00'),
(88, 88, 'assets/uploads/products/ka-random-16.webp', '2025-05-28 06:35:00'),
(89, 89, 'assets/uploads/products/ka-random-17.webp', '2025-05-28 06:35:00'),
(90, 90, 'assets/uploads/products/ka-random-18.webp', '2025-05-28 06:35:00'),
(91, 91, 'assets/uploads/products/ka-random-19.webp', '2025-05-28 06:35:00'),
(92, 92, 'assets/uploads/products/ka-random-20.webp', '2025-05-28 06:35:00'),
(93, 93, 'assets/uploads/products/ka-random-21.webp', '2025-05-28 06:35:00'),
(94, 94, 'assets/uploads/products/ka-random-22.webp', '2025-05-28 06:35:00'),
(95, 95, 'assets/uploads/products/ka-random-23.webp', '2025-05-28 06:35:00'),
(96, 96, 'assets/uploads/products/ka-random-24.webp', '2025-05-28 06:35:00'),
(97, 97, 'assets/uploads/products/ka-random-25.webp', '2025-05-28 06:35:00'),
(98, 98, 'assets/uploads/products/ka-random-26.webp', '2025-05-28 06:35:00'),
(99, 99, 'assets/uploads/products/ka-random-27.webp', '2025-05-28 06:35:00'),
(100, 100, 'assets/uploads/products/ka-random-28.webp', '2025-05-28 06:35:00'),
(101, 101, 'assets/uploads/products/ka-object-01.webp', '2025-05-28 06:35:00'),
(102, 102, 'assets/uploads/products/ka-object-02.webp', '2025-05-28 06:35:00'),
(103, 103, 'assets/uploads/products/ka-object-03.webp', '2025-05-28 06:35:00'),
(104, 104, 'assets/uploads/products/ka-object-04.webp', '2025-05-28 06:35:00'),
(105, 105, 'assets/uploads/products/ka-object-05.webp', '2025-05-28 06:35:00'),
(106, 106, 'assets/uploads/products/ka-object-06.webp', '2025-05-28 06:35:00'),
(107, 107, 'assets/uploads/products/ka-object-07.webp', '2025-05-28 06:35:00'),
(108, 108, 'assets/uploads/products/ka-object-08.webp', '2025-05-28 06:35:00'),
(109, 109, 'assets/uploads/products/ka-object-09.webp', '2025-05-28 06:35:00'),
(110, 110, 'assets/uploads/products/ka-object-10.webp', '2025-05-28 06:35:00'),
(111, 111, 'assets/uploads/products/ka-object-11.webp', '2025-05-28 06:35:00'),
(112, 112, 'assets/uploads/products/ka-object-12.webp', '2025-05-28 06:35:00'),
(113, 113, 'assets/uploads/products/ka-random-29.webp', '2025-05-28 06:35:00'),
(114, 114, 'assets/uploads/products/ka-random-30.webp', '2025-05-28 06:35:00'),
(115, 115, 'assets/uploads/products/ka-random-31.webp', '2025-05-28 06:35:00'),
(116, 116, 'assets/uploads/products/ka-child-15.webp', '2025-05-28 06:35:00'),
(117, 117, 'assets/uploads/products/ka-child-16.webp', '2025-05-28 06:35:00'),
(118, 118, 'assets/uploads/products/ka-child-17.webp', '2025-05-28 06:35:00'),
(119, 119, 'assets/uploads/products/ka-child-18.webp', '2025-05-28 06:35:00'),
(120, 120, 'assets/uploads/products/ka-child-19.webp', '2025-05-28 06:35:00'),
(121, 121, 'assets/uploads/products/ka-child-20.webp', '2025-05-28 06:35:00'),
(122, 122, 'assets/uploads/products/ka-child-21.webp', '2025-05-28 06:35:00'),
(123, 123, 'assets/uploads/products/ka-child-22.webp', '2025-05-28 06:35:00'),
(124, 124, 'assets/uploads/products/ka-random-32.webp', '2025-05-28 06:35:00'),
(125, 125, 'assets/uploads/products/ka-random-33.webp', '2025-05-28 06:35:00'),
(126, 126, 'assets/uploads/products/ka-random-34.webp', '2025-05-28 06:35:00'),
(127, 127, 'assets/uploads/products/ka-random-35.webp', '2025-05-28 06:35:00'),
(128, 128, 'assets/uploads/products/ka-random-36.webp', '2025-05-28 06:35:00'),
(129, 129, 'assets/uploads/products/ka-random-37.webp', '2025-05-28 06:35:00'),
(130, 130, 'assets/uploads/products/ka-random-38.webp', '2025-05-28 06:35:00'),
(131, 131, 'assets/uploads/products/ka-random-39.webp', '2025-05-28 06:35:00'),
(132, 132, 'assets/uploads/products/ka-random-40.webp', '2025-05-28 06:35:00'),
(133, 133, 'assets/uploads/products/ka-random-41.webp', '2025-05-28 06:35:00'),
(134, 134, 'assets/uploads/products/ka-random-42.webp', '2025-05-28 06:35:00'),
(135, 135, 'assets/uploads/products/ka-random-43.webp', '2025-05-28 06:35:00'),
(136, 136, 'assets/uploads/products/ka-random-44.webp', '2025-05-28 06:35:00'),
(137, 137, 'assets/uploads/products/ka-random-45.webp', '2025-05-28 06:35:00'),
(138, 138, 'assets/uploads/products/ka-random-46.webp', '2025-05-28 06:35:00'),
(139, 139, 'assets/uploads/products/ka-random-47.webp', '2025-05-28 06:35:00'),
(140, 140, 'assets/uploads/products/ka-random-48.webp', '2025-05-28 06:35:00'),
(141, 141, 'assets/uploads/products/ka-spel-1.webp', '2025-05-28 06:35:00'),
(142, 142, 'assets/uploads/products/ka-spel-2.webp', '2025-05-28 06:35:00'),
(143, 143, 'assets/uploads/products/ka-spel-3.webp', '2025-05-28 06:35:00'),
(144, 144, 'assets/uploads/products/ka-spel-4.webp', '2025-05-28 06:35:00'),
(145, 145, 'assets/uploads/products/ka-random-49.webp', '2025-05-28 06:35:00'),
(146, 146, 'assets/uploads/products/ka-random-50.webp', '2025-05-28 06:35:00'),
(147, 147, 'assets/uploads/products/ka-random-51.webp', '2025-05-28 06:35:00'),
(148, 148, 'assets/uploads/products/ka-random-52.webp', '2025-05-28 06:35:00'),
(149, 149, 'assets/uploads/products/ka-random-53.webp', '2025-05-28 06:35:00'),
(150, 150, 'assets/uploads/products/ka-random-54.webp', '2025-05-28 06:35:00'),
(151, 1, 'assets/uploads/products/ka-random-55.webp', '2025-05-28 06:35:00'),
(152, 1, 'assets/uploads/products/ka-random-56.webp', '2025-05-28 06:35:00'),
(153, 1, 'assets/uploads/products/ka-random-57.webp', '2025-05-28 06:35:00'),
(154, 3, 'assets/uploads/products/ka-random-58.webp', '2025-05-28 06:35:00'),
(155, 3, 'assets/uploads/products/ka-random-59.webp', '2025-05-28 06:35:00'),
(156, 5, 'assets/uploads/products/ka-random-60.webp', '2025-05-28 06:35:00'),
(157, 5, 'assets/uploads/products/ka-random-61.webp', '2025-05-28 06:35:00'),
(158, 5, 'assets/uploads/products/ka-random-62.webp', '2025-05-28 06:35:00'),
(159, 5, 'assets/uploads/products/ka-random-63.webp', '2025-05-28 06:35:00'),
(160, 8, 'assets/uploads/products/ka-random-64.webp', '2025-05-28 06:35:00'),
(161, 11, 'assets/uploads/products/ka-random-65.webp', '2025-05-28 06:35:00'),
(162, 11, 'assets/uploads/products/ka-random-66.webp', '2025-05-28 06:35:00'),
(163, 11, 'assets/uploads/products/ka-random-67.webp', '2025-05-28 06:35:00'),
(164, 15, 'assets/uploads/products/ka-random-68.webp', '2025-05-28 06:35:00'),
(165, 15, 'assets/uploads/products/ka-random-69.webp', '2025-05-28 06:35:00'),
(166, 17, 'assets/uploads/products/ka-random-70.webp', '2025-05-28 06:35:00'),
(167, 17, 'assets/uploads/products/ka-random-71.webp', '2025-05-28 06:35:00'),
(168, 17, 'assets/uploads/products/ka-random-72.webp', '2025-05-28 06:35:00'),
(169, 17, 'assets/uploads/products/ka-random-73.webp', '2025-05-28 06:35:00'),
(170, 20, 'assets/uploads/products/ka-random-74.webp', '2025-05-28 06:35:00'),
(171, 20, 'assets/uploads/products/ka-random-75.webp', '2025-05-28 06:35:00'),
(172, 22, 'assets/uploads/products/ka-random-76.webp', '2025-05-28 06:35:00'),
(173, 22, 'assets/uploads/products/ka-random-77.webp', '2025-05-28 06:35:00'),
(174, 22, 'assets/uploads/products/ka-random-78.webp', '2025-05-28 06:35:00'),
(175, 25, 'assets/uploads/products/ka-random-79.webp', '2025-05-28 06:35:00'),
(176, 27, 'assets/uploads/products/ka-random-80.webp', '2025-05-28 06:35:00'),
(177, 27, 'assets/uploads/products/ka-random-81.webp', '2025-05-28 06:35:00'),
(178, 27, 'assets/uploads/products/ka-random-82.webp', '2025-05-28 06:35:00'),
(179, 27, 'assets/uploads/products/ka-random-83.webp', '2025-05-28 06:35:00'),
(180, 30, 'assets/uploads/products/ka-random-84.webp', '2025-05-28 06:35:00'),
(181, 30, 'assets/uploads/products/ka-random-85.webp', '2025-05-28 06:35:00'),
(182, 33, 'assets/uploads/products/ka-random-86.webp', '2025-05-28 06:35:00'),
(183, 33, 'assets/uploads/products/ka-random-87.webp', '2025-05-28 06:35:00'),
(184, 33, 'assets/uploads/products/ka-random-88.webp', '2025-05-28 06:35:00'),
(185, 36, 'assets/uploads/products/ka-random-89.webp', '2025-05-28 06:35:00'),
(186, 37, 'assets/uploads/products/ka-random-90.webp', '2025-05-28 06:35:00'),
(187, 37, 'assets/uploads/products/ka-random-91.webp', '2025-05-28 06:35:00'),
(188, 37, 'assets/uploads/products/ka-random-92.webp', '2025-05-28 06:35:00'),
(189, 37, 'assets/uploads/products/ka-random-93.webp', '2025-05-28 06:35:00'),
(190, 40, 'assets/uploads/products/ka-random-94.webp', '2025-05-28 06:35:00'),
(191, 40, 'assets/uploads/products/ka-random-95.webp', '2025-05-28 06:35:00'),
(192, 42, 'assets/uploads/products/ka-random-96.webp', '2025-05-28 06:35:00'),
(193, 42, 'assets/uploads/products/ka-random-97.webp', '2025-05-28 06:35:00'),
(194, 42, 'assets/uploads/products/ka-random-98.webp', '2025-05-28 06:35:00'),
(195, 46, 'assets/uploads/products/ka-random-99.webp', '2025-05-28 06:35:00'),
(196, 48, 'assets/uploads/products/ka-random-100.webp', '2025-05-28 06:35:00'),
(197, 48, 'assets/uploads/products/ka-random-101.webp', '2025-05-28 06:35:00'),
(198, 48, 'assets/uploads/products/ka-random-102.webp', '2025-05-28 06:35:00'),
(199, 48, 'assets/uploads/products/ka-random-103.webp', '2025-05-28 06:35:00'),
(200, 51, 'assets/uploads/products/ka-random-104.webp', '2025-05-28 06:35:00'),
(201, 51, 'assets/uploads/products/ka-random-105.webp', '2025-05-28 06:35:00'),
(202, 55, 'assets/uploads/products/ka-random-106.webp', '2025-05-28 06:35:00'),
(203, 55, 'assets/uploads/products/ka-random-107.webp', '2025-05-28 06:35:00'),
(204, 55, 'assets/uploads/products/ka-random-108.webp', '2025-05-28 06:35:00'),
(205, 59, 'assets/uploads/products/ka-random-109.webp', '2025-05-28 06:35:00'),
(206, 62, 'assets/uploads/products/ka-random-110.webp', '2025-05-28 06:35:00'),
(207, 62, 'assets/uploads/products/ka-random-111.webp', '2025-05-28 06:35:00'),
(208, 62, 'assets/uploads/products/ka-random-112.webp', '2025-05-28 06:35:00'),
(209, 62, 'assets/uploads/products/ka-random-113.webp', '2025-05-28 06:35:00'),
(210, 67, 'assets/uploads/products/ka-random-114.webp', '2025-05-28 06:35:00'),
(211, 67, 'assets/uploads/products/ka-random-115.webp', '2025-05-28 06:35:00'),
(212, 70, 'assets/uploads/products/ka-random-116.webp', '2025-05-28 06:35:00'),
(213, 70, 'assets/uploads/products/ka-random-117.webp', '2025-05-28 06:35:00'),
(214, 70, 'assets/uploads/products/ka-random-118.webp', '2025-05-28 06:35:00'),
(215, 74, 'assets/uploads/products/ka-random-119.webp', '2025-05-28 06:35:00'),
(216, 77, 'assets/uploads/products/ka-random-120.webp', '2025-05-28 06:35:00'),
(217, 77, 'assets/uploads/products/ka-random-121.webp', '2025-05-28 06:35:00'),
(218, 77, 'assets/uploads/products/ka-random-122.webp', '2025-05-28 06:35:00'),
(219, 77, 'assets/uploads/products/ka-random-123.webp', '2025-05-28 06:35:00'),
(220, 79, 'assets/uploads/products/ka-random-124.webp', '2025-05-28 06:35:00'),
(221, 79, 'assets/uploads/products/ka-random-125.webp', '2025-05-28 06:35:00'),
(222, 80, 'assets/uploads/products/ka-random-126.webp', '2025-05-28 06:35:00'),
(223, 80, 'assets/uploads/products/ka-random-127.webp', '2025-05-28 06:35:00'),
(224, 80, 'assets/uploads/products/ka-random-128.webp', '2025-05-28 06:35:00'),
(225, 85, 'assets/uploads/products/ka-random-129.webp', '2025-05-28 06:35:00'),
(226, 92, 'assets/uploads/products/ka-random-130.webp', '2025-05-28 06:35:00'),
(227, 92, 'assets/uploads/products/ka-random-131.webp', '2025-05-28 06:35:00'),
(228, 92, 'assets/uploads/products/ka-random-132.webp', '2025-05-28 06:35:00'),
(229, 92, 'assets/uploads/products/ka-random-133.webp', '2025-05-28 06:35:00'),
(230, 95, 'assets/uploads/products/ka-random-134.webp', '2025-05-28 06:35:00'),
(231, 95, 'assets/uploads/products/ka-random-135.webp', '2025-05-28 06:35:00'),
(232, 99, 'assets/uploads/products/ka-random-136.webp', '2025-05-28 06:35:00'),
(233, 99, 'assets/uploads/products/ka-random-137.webp', '2025-05-28 06:35:00'),
(234, 99, 'assets/uploads/products/ka-random-138.webp', '2025-05-28 06:35:00'),
(235, 101, 'assets/uploads/products/ka-random-139.webp', '2025-05-28 06:35:00'),
(236, 104, 'assets/uploads/products/ka-random-140.webp', '2025-05-28 06:35:00'),
(237, 104, 'assets/uploads/products/ka-random-141.webp', '2025-05-28 06:35:00'),
(238, 104, 'assets/uploads/products/ka-random-142.webp', '2025-05-28 06:35:00'),
(239, 104, 'assets/uploads/products/ka-random-143.webp', '2025-05-28 06:35:00'),
(240, 107, 'assets/uploads/products/ka-random-144.webp', '2025-05-28 06:35:00'),
(241, 107, 'assets/uploads/products/ka-random-145.webp', '2025-05-28 06:35:00'),
(242, 110, 'assets/uploads/products/ka-random-146.webp', '2025-05-28 06:35:00'),
(243, 110, 'assets/uploads/products/ka-random-147.webp', '2025-05-28 06:35:00'),
(244, 110, 'assets/uploads/products/ka-random-148.webp', '2025-05-28 06:35:00'),
(245, 115, 'assets/uploads/products/ka-random-149.webp', '2025-05-28 06:35:00'),
(246, 118, 'assets/uploads/products/ka-random-150.webp', '2025-05-28 06:35:00'),
(247, 118, 'assets/uploads/products/ka-random-151.webp', '2025-05-28 06:35:00'),
(248, 118, 'assets/uploads/products/ka-random-152.webp', '2025-05-28 06:35:00'),
(249, 118, 'assets/uploads/products/ka-random-153.webp', '2025-05-28 06:35:00'),
(250, 121, 'assets/uploads/products/ka-random-154.webp', '2025-05-28 06:35:00'),
(251, 121, 'assets/uploads/products/ka-random-155.webp', '2025-05-28 06:35:00'),
(252, 125, 'assets/uploads/products/ka-random-156.webp', '2025-05-28 06:35:00'),
(253, 125, 'assets/uploads/products/ka-random-157.webp', '2025-05-28 06:35:00'),
(254, 125, 'assets/uploads/products/ka-random-158.webp', '2025-05-28 06:35:00'),
(255, 128, 'assets/uploads/products/ka-random-159.webp', '2025-05-28 06:35:00'),
(256, 134, 'assets/uploads/products/ka-random-160.webp', '2025-05-28 06:35:00'),
(257, 134, 'assets/uploads/products/ka-random-161.webp', '2025-05-28 06:35:00'),
(258, 134, 'assets/uploads/products/ka-random-162.webp', '2025-05-28 06:35:00'),
(259, 134, 'assets/uploads/products/ka-random-163.webp', '2025-05-28 06:35:00'),
(260, 137, 'assets/uploads/products/ka-random-164.webp', '2025-05-28 06:35:00'),
(261, 137, 'assets/uploads/products/ka-random-165.webp', '2025-05-28 06:35:00'),
(262, 142, 'assets/uploads/products/ka-random-166.webp', '2025-05-28 06:35:00'),
(263, 142, 'assets/uploads/products/ka-random-167.webp', '2025-05-28 06:35:00'),
(264, 142, 'assets/uploads/products/ka-random-168.webp', '2025-05-28 06:35:00'),
(265, 145, 'assets/uploads/products/ka-random-169.webp', '2025-05-28 06:35:00'),
(266, 147, 'assets/uploads/products/ka-random-170.webp', '2025-05-28 06:35:00'),
(267, 147, 'assets/uploads/products/ka-random-171.webp', '2025-05-28 06:35:00'),
(268, 147, 'assets/uploads/products/ka-random-172.webp', '2025-05-28 06:35:00'),
(269, 147, 'assets/uploads/products/ka-random-173.webp', '2025-05-28 06:35:00'),
(270, 149, 'assets/uploads/products/ka-random-174.webp', '2025-05-28 06:35:00'),
(271, 149, 'assets/uploads/products/ka-random-175.webp', '2025-05-28 06:35:00'),
(272, 150, 'assets/uploads/products/ka-random-176.webp', '2025-05-28 06:35:00'),
(273, 150, 'assets/uploads/products/ka-random-177.webp', '2025-05-28 06:35:00'),
(274, 150, 'assets/uploads/products/ka-random-178.webp', '2025-05-28 06:35:00'),
(275, 29, 'assets/uploads/products/29_683697c9c6c5b_1748408265.webp', '2025-05-28 07:57:47'),
(276, 29, 'assets/uploads/products/29_683697cb0c9e5_1748408267.webp', '2025-05-28 07:57:48'),
(277, 28, 'assets/uploads/products/28_683697f8279a3_1748408312.webp', '2025-05-28 07:58:33'),
(278, 28, 'assets/uploads/products/28_683697f946858_1748408313.webp', '2025-05-28 07:58:34');

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `language_id` int NOT NULL,
  `language_sv_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `language_fi_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`language_id`, `language_sv_name`, `language_fi_name`) VALUES
(1, 'Svenska', 'Ruotsi'),
(2, 'Finska', 'Suomi'),
(3, 'Engelska', 'Englanti'),
(4, 'Norska', 'Norja'),
(5, 'Tyska', 'Saksa'),
(6, 'Ryska', 'Venäjä'),
(7, 'Franska', 'Ranska'),
(8, 'Spanska', 'Espanja');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscriber`
--

CREATE TABLE `newsletter_subscriber` (
  `subscriber_id` int NOT NULL,
  `subscriber_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `subscriber_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subscribed_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `subscriber_is_active` tinyint(1) DEFAULT '1',
  `subscriber_language_pref` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'sv'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newsletter_subscriber`
--

INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES
(1, 'johanna.karlsson@example.com', 'Johanna Karlsson', '2025-05-13 12:37:38', 0, 'sv'),
(2, 'mikko.nieminen@example.fi', 'Mikko Nieminen', '2025-05-13 12:37:38', 1, 'fi'),
(3, 'anna.lindholm@example.com', 'Anna Lindholm', '2025-05-13 12:37:38', 1, 'sv'),
(4, 'erik.johansson@example.se', 'Erik Johansson', '2025-05-13 12:37:38', 1, 'sv'),
(5, 'liisa.makinen@example.fi', 'Liisa Mäkinen', '2025-05-13 12:37:38', 1, 'fi'),
(6, 'bengt.gustafsson@example.com', 'Bengt Gustafsson', '2025-05-13 12:37:38', 1, 'sv'),
(7, 'aino.virtanen@gmail.com', 'Aino Virtanen', '2025-01-15 10:30:00', 1, 'fi'),
(8, 'lars.andersson@hotmail.com', 'Lars Andersson', '2025-01-18 14:22:00', 1, 'sv'),
(9, 'maria.korhonen@yahoo.fi', 'Maria Korhonen', '2025-01-20 09:15:00', 1, 'fi'),
(10, 'erik.johansson@gmail.com', 'Erik Johansson', '2025-01-22 16:45:00', 1, 'sv'),
(11, 'helena.nieminen@outlook.com', 'Helena Nieminen', '2025-01-25 11:30:00', 1, 'fi'),
(12, 'sven.karlsson@telia.com', 'Sven Karlsson', '2025-01-28 13:20:00', 1, 'sv'),
(13, 'liisa.hakkarainen@gmail.com', 'Liisa Hakkarainen', '2025-02-01 10:00:00', 1, 'fi'),
(14, 'anna.lindberg@hotmail.se', 'Anna Lindberg', '2025-02-03 15:30:00', 1, 'sv'),
(15, 'mikko.salminen@elisa.fi', 'Mikko Salminen', '2025-02-05 12:45:00', 1, 'fi'),
(16, 'ingrid.gustafsson@gmail.com', 'Ingrid Gustafsson', '2025-02-08 09:20:00', 1, 'sv'),
(17, 'kari.laine@luukku.com', 'Kari Laine', '2025-02-10 14:15:00', 1, 'fi'),
(18, 'gunnar.pettersson@spray.se', 'Gunnar Pettersson', '2025-02-12 16:00:00', 1, 'sv'),
(19, 'tuula.heikkinen@gmail.com', 'Tuula Heikkinen', '2025-02-15 11:45:00', 1, 'fi'),
(20, 'astrid.nilsson@yahoo.se', 'Astrid Nilsson', '2025-02-17 13:30:00', 1, 'sv'),
(21, 'pekka.virtanen@saunalahti.fi', 'Pekka Virtanen', '2025-02-20 10:20:00', 1, 'fi'),
(22, 'margareta.holm@telia.se', 'Margareta Holm', '2025-02-22 15:10:00', 1, 'sv'),
(23, 'ritva.korhonen@kolumbus.fi', 'Ritva Korhonen', '2025-02-25 12:00:00', 1, 'fi'),
(24, 'nils.berg@gmail.com', 'Nils Berg', '2025-02-27 14:30:00', 1, 'sv'),
(25, 'riitta.makinen@hotmail.com', 'Riitta Mäkinen', '2025-03-01 09:45:00', 1, 'fi'),
(26, 'bengt.larsson@bredband.net', 'Bengt Larsson', '2025-03-03 16:20:00', 1, 'sv'),
(27, 'maija.koskinen@gmail.com', 'Maija Koskinen', '2025-03-05 11:15:00', 1, 'fi'),
(28, 'olof.stromberg@yahoo.se', 'Olof Strömberg', '2025-03-08 13:40:00', 1, 'sv'),
(29, 'elina.saarinen@elisa.fi', 'Elina Saarinen', '2025-03-10 10:30:00', 1, 'fi'),
(30, 'birgitta.hansson@gmail.com', 'Birgitta Hansson', '2025-03-12 15:50:00', 1, 'sv'),
(31, 'jukka.rantala@luukku.com', 'Jukka Rantala', '2025-03-15 12:25:00', 1, 'fi'),
(32, 'rolf.danielsson@telia.com', 'Rolf Danielsson', '2025-03-17 14:10:00', 1, 'sv'),
(33, 'sirpa.lahtinen@gmail.com', 'Sirpa Lahtinen', '2025-03-20 09:35:00', 1, 'fi'),
(34, 'ulla.hedberg@hotmail.se', 'Ulla Hedberg', '2025-03-22 16:15:00', 1, 'sv'),
(35, 'paavo.kallio@saunalahti.fi', 'Paavo Kallio', '2025-03-25 11:50:00', 1, 'fi'),
(36, 'gustav.lindqvist@gmail.com', 'Gustav Lindqvist', '2025-03-27 13:25:00', 1, 'sv'),
(37, 'anneli.harju@kolumbus.fi', 'Anneli Harju', '2025-04-01 10:40:00', 1, 'fi'),
(38, 'torsten.wickman@yahoo.se', 'Torsten Wickman', '2025-04-03 15:05:00', 1, 'sv'),
(39, 'kirsti.aalto@elisa.fi', 'Kirsti Aalto', '2025-04-05 12:20:00', 1, 'fi'),
(40, 'ragnar.sundberg@telia.se', 'Ragnar Sundberg', '2025-04-08 14:45:00', 1, 'sv'),
(41, 'terttu.virtanen@gmail.com', 'Terttu Virtanen', '2025-04-10 09:55:00', 1, 'fi');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `prod_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int DEFAULT NULL,
  `shelf_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `condition_id` int DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `internal_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `year` int DEFAULT NULL,
  `publisher` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `special_price` tinyint(1) DEFAULT NULL COMMENT 'Om produkten är på rea: 1 = Ja, 0 = Nej',
  `recommended` tinyint(1) DEFAULT NULL COMMENT 'Om produkten är rekommenderad: 1 = Ja, 0 = Nej	',
  `rare` tinyint(1) DEFAULT '0',
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  `language_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES
(1, 'Tuntematon sotilas', 1, 1, 1, 24.50, 2, 'Klassisk krigsroman', 'Mycket populär', 1954, 'WSOY', 0, 1, 0, '2025-05-28 00:26:26', 2),
(2, 'Täällä Pohjantähden alla', 1, 1, 1, 28.90, 1, 'Trilogi del 1', NULL, 1959, 'WSOY', 0, 1, 0, '2025-05-28 00:26:26', 2),
(3, 'Sinuhe egyptiläinen', 1, 1, 1, 26.50, 2, 'Historisk roman', 'Fint skick', 1945, 'WSOY', 0, 1, 0, '2025-05-28 00:26:26', 2),
(4, 'Seitsemän veljestä', 1, 1, 1, 22.00, 3, 'Finlands nationalroman', NULL, 1870, 'SKS', 0, 0, 1, '2025-05-28 00:26:26', 2),
(5, 'Purge', 1, 1, 1, 23.50, 1, 'Prisbelönt', 'Internationell succé', 2008, 'Otava', 0, 1, 0, '2025-05-28 00:26:26', 2),
(6, 'Koirankynnen leikkaaja', 1, 1, 1, 19.90, 2, 'Modern finsk prosa', NULL, 1980, 'Otava', 0, 0, 0, '2025-05-28 00:26:26', 2),
(7, 'Elämä lyhyt, Rytkönen pitkä', 1, 1, 1, 18.50, 2, 'Komisk roman', NULL, 1994, 'WSOY', 0, 0, 0, '2025-05-28 00:26:26', 2),
(8, 'Hurmaava joukkoitsemurha', 1, 1, 1, 17.90, 1, 'Satiirinen komedia', NULL, 1990, 'WSOY', 0, 1, 0, '2025-05-28 00:26:26', 2),
(9, 'Suloinen myrkynkeittäjä', 1, 1, 1, 16.50, 2, 'Huumori', NULL, 1988, 'WSOY', 0, 0, 0, '2025-05-28 00:26:26', 2),
(10, 'Paholaisen haarukka', 1, 1, 1, 18.90, 1, 'Komedia', NULL, 1991, 'WSOY', 0, 0, 0, '2025-05-28 00:26:26', 2),
(11, 'Hytti nro 6', 1, 1, 1, 21.50, 1, 'Finlandia-pristagare', NULL, 2011, 'Otava', 0, 1, 0, '2025-05-28 00:26:26', 2),
(12, 'Norma', 1, 1, 1, 20.50, 2, 'Samtida finsk litteratur', NULL, 2015, 'Otava', 0, 0, 0, '2025-05-28 00:26:26', 2),
(13, 'Kun kyyhkyset katosivat', 1, 1, 1, 19.50, 1, 'Finlandia-pristagare', NULL, 2012, 'Otava', 0, 1, 0, '2025-05-28 00:26:26', 2),
(14, 'Baby Jane', 1, 1, 1, 22.50, 2, 'Modern finsk roman', NULL, 2005, 'Tammi', 0, 0, 0, '2025-05-28 00:26:26', 2),
(15, 'Käsky', 1, 1, 1, 18.50, 1, 'Psykologisk thriller', NULL, 2003, 'Tammi', 0, 0, 0, '2025-05-28 00:26:26', 2),
(16, 'Pippi Långstrump', 1, 5, 1, 16.90, 1, 'Barnklassiker', 'Populär bland barn', 1945, 'Rabén & Sjögren', 0, 1, 0, '2025-05-28 00:26:26', 1),
(17, 'Ronja rövardotter', 1, 5, 1, 18.50, 2, 'Fantasy för barn', NULL, 1981, 'Rabén & Sjögren', 1, 0, 0, '2025-05-28 00:26:26', 1),
(18, 'Emil i Lönneberga', 1, 5, 1, 15.90, 1, 'Barnbok', NULL, 1963, 'Rabén & Sjögren', 0, 0, 0, '2025-05-28 00:26:26', 1),
(19, 'Karlsson på taket', 1, 5, 1, 17.50, 2, 'Klassisk barnbok', NULL, 1955, 'Rabén & Sjögren', 1, 0, 0, '2025-05-28 00:26:26', 1),
(20, 'Män som hatar kvinnor', 1, 1, 1, 24.50, 2, 'Millennium trilogi del 1', 'Mycket populär', 2005, 'Norstedts', 0, 1, 0, '2025-05-28 00:26:26', 1),
(21, 'Flickan som lekte med elden', 1, 1, 1, 23.50, 1, 'Millennium trilogi del 2', NULL, 2006, 'Norstedts', 0, 1, 0, '2025-05-28 00:26:26', 1),
(22, 'Luftslottet som sprängdes', 1, 1, 1, 22.90, 2, 'Millennium trilogi del 3', NULL, 2007, 'Norstedts', 0, 1, 0, '2025-05-28 00:26:26', 1),
(23, 'Faceless Killers', 1, 1, 1, 21.50, 2, 'Första Wallander', NULL, 1991, 'Harvill', 0, 0, 0, '2025-05-28 00:26:26', 3),
(24, 'The White Lioness', 1, 1, 1, 20.50, 1, 'Wallander serie', NULL, 1993, 'Harvill', 0, 0, 0, '2025-05-28 00:26:26', 3),
(25, 'One Step Behind', 1, 1, 1, 19.90, 2, 'Wallander deckare', NULL, 1997, 'Harvill', 0, 0, 0, '2025-05-28 00:26:26', 3),
(26, 'Trollkarlens hatt', 1, 5, 1, 17.50, 1, 'Mumin klassiker', 'Illustrerad', 1948, 'Schildts', 0, 1, 0, '2025-05-28 00:26:26', 1),
(27, 'Farlig midsommar', 1, 5, 1, 16.90, 2, 'Mumin äventyr', '', 1954, 'Schildts', 1, 0, 0, '2025-05-28 00:26:26', 1),
(28, 'Trollvinter', 1, 5, 1, 18.50, 1, 'Mumin roman', '', 1957, 'Schildts', 0, 1, 0, '2025-05-28 00:26:26', 1),
(29, 'Pappan och havet', 1, 5, 1, 17.90, 2, 'Mumin serie', '', 1965, 'Schildts', 1, 0, 0, '2025-05-28 00:26:26', 1),
(30, 'Sent i november', 1, 5, 1, 19.50, 1, 'Sista Mumin boken', NULL, 1970, 'Schildts', 0, 0, 0, '2025-05-28 00:26:26', 1),
(31, 'Murder on the Orient Express', 1, 1, 1, 16.50, 2, 'Poirot klassiker', NULL, 1934, 'Collins', 0, 0, 0, '2025-05-28 00:26:26', 3),
(32, 'And Then There Were None', 1, 1, 1, 17.90, 1, 'Christie mästerverk', NULL, 1939, 'Collins', 0, 1, 0, '2025-05-28 00:26:26', 3),
(33, 'The Murder of Roger Ackroyd', 1, 1, 1, 15.50, 2, 'Poirot mysterium', NULL, 1926, 'Collins', 0, 0, 0, '2025-05-28 00:26:26', 3),
(34, 'The Adventures of Sherlock Holmes', 1, 1, 1, 20.00, 3, 'Detektiv klassiker', 'Äldre upplaga', 1892, 'Wordsworth', 0, 1, 0, '2025-05-28 00:26:26', 3),
(35, 'The Hound of the Baskervilles', 1, 1, 1, 18.50, 2, 'Holmes klassiker', NULL, 1902, 'Penguin', 0, 0, 0, '2025-05-28 00:26:26', 3),
(36, 'The Hobbit', 1, 1, 1, 22.00, 1, 'Fantasy klassiker', 'Bra skick', 1937, 'HarperCollins', 0, 1, 0, '2025-05-28 00:26:26', 3),
(37, 'The Lord of the Rings', 1, 1, 1, 35.50, 2, 'Fantasy epos', 'Komplett trilogi', 1954, 'HarperCollins', 0, 1, 1, '2025-05-28 00:26:26', 3),
(38, '1984', 1, 1, 1, 17.90, 1, 'Dystopisk klassiker', 'Mycket aktuell', 1949, 'Penguin', 0, 1, 0, '2025-05-28 00:26:26', 3),
(39, 'Animal Farm', 1, 1, 1, 14.50, 2, 'Politisk allegori', NULL, 1945, 'Penguin', 0, 0, 0, '2025-05-28 00:26:26', 3),
(40, 'Pride and Prejudice', 1, 1, 1, 19.50, 2, 'Romantisk klassiker', NULL, 1813, 'Penguin Classics', 0, 1, 0, '2025-05-28 00:26:26', 3),
(41, 'Emma', 1, 1, 1, 18.90, 1, 'Jane Austen', NULL, 1815, 'Penguin Classics', 0, 0, 0, '2025-05-28 00:26:26', 3),
(42, 'Great Expectations', 1, 1, 1, 21.50, 2, 'Viktoriansk roman', NULL, 1861, 'Penguin Classics', 0, 0, 0, '2025-05-28 00:26:26', 3),
(43, 'A Tale of Two Cities', 1, 1, 1, 20.50, 1, 'Historisk roman', NULL, 1859, 'Penguin Classics', 0, 0, 0, '2025-05-28 00:26:26', 3),
(44, 'Hamlet', 1, 1, 1, 16.50, 3, 'Shakespeare tragedi', 'Äldre upplaga', 1603, 'Wordsworth', 0, 1, 0, '2025-05-28 00:26:26', 3),
(45, 'Romeo and Juliet', 1, 1, 1, 15.90, 2, 'Kärlekstragedi', NULL, 1597, 'Wordsworth', 0, 1, 0, '2025-05-28 00:26:26', 3),
(46, 'The Old Man and the Sea', 1, 1, 1, 18.50, 1, 'Nobelprisbärare', NULL, 1952, 'Scribner', 0, 1, 0, '2025-05-28 00:26:26', 3),
(47, 'For Whom the Bell Tolls', 1, 1, 1, 22.50, 2, 'Krigsskildring', NULL, 1940, 'Scribner', 0, 0, 0, '2025-05-28 00:26:26', 3),
(48, 'The Great Gatsby', 1, 1, 1, 19.90, 1, 'Amerikansk klassiker', NULL, 1925, 'Scribner', 0, 1, 0, '2025-05-28 00:26:26', 3),
(49, 'To Kill a Mockingbird', 1, 1, 1, 18.50, 2, 'Amerikanskl klassiker', NULL, 1960, 'Arrow Books', 0, 1, 0, '2025-05-28 00:26:26', 3),
(50, 'The Adventures of Tom Sawyer', 1, 5, 1, 16.50, 1, 'Ungdomsbok', NULL, 1876, 'Penguin', 0, 0, 0, '2025-05-28 00:26:26', 3),
(51, 'War and Peace', 1, 1, 1, 32.95, 3, 'Rysk epos', 'Tjock bok', 1869, 'Penguin Classics', 0, 1, 0, '2025-05-28 00:26:26', 3),
(52, 'Anna Karenina', 1, 1, 1, 28.50, 2, 'Rysk klassiker', NULL, 1877, 'Penguin Classics', 0, 1, 0, '2025-05-28 00:26:26', 3),
(53, 'Crime and Punishment', 1, 1, 1, 24.50, 2, 'Psykologisk roman', NULL, 1866, 'Penguin Classics', 0, 1, 0, '2025-05-28 00:26:26', 3),
(54, 'The Metamorphosis', 1, 1, 1, 16.90, 1, 'Surrealistisk novell', NULL, 1915, 'Penguin Classics', 0, 0, 0, '2025-05-28 00:26:26', 3),
(55, 'Mrs Dalloway', 1, 1, 1, 17.90, 2, 'Modernistisk roman', NULL, 1925, 'Penguin Classics', 0, 0, 0, '2025-05-28 00:26:26', 3),
(56, 'Ulysses', 1, 1, 1, 28.90, 3, 'Modernistisk mästerverk', 'Svårläst', 1922, 'Penguin Classics', 0, 1, 1, '2025-05-28 00:26:26', 3),
(57, 'Charlie and the Chocolate Factory', 1, 5, 1, 13.50, 1, 'Barnklassiker', NULL, 1964, 'Puffin', 0, 1, 0, '2025-05-28 00:26:26', 3),
(58, 'Matilda', 1, 5, 1, 14.90, 2, 'Roald Dahl', NULL, 1988, 'Puffin', 1, 0, 0, '2025-05-28 00:26:26', 3),
(59, 'Alice in Wonderland', 1, 5, 1, 15.50, 1, 'Fantasy klassiker', NULL, 1865, 'Penguin Classics', 0, 1, 0, '2025-05-28 00:26:26', 3),
(60, 'The Lion, the Witch and the Wardrobe', 1, 5, 1, 16.50, 2, 'Narnia serie', NULL, 1950, 'HarperCollins', 1, 0, 0, '2025-05-28 00:26:26', 3),
(61, 'Fahrenheit 451', 1, 1, 1, 18.90, 1, 'Dystopisk sci-fi', NULL, 1953, 'Ballantine', 0, 1, 0, '2025-05-28 00:26:26', 3),
(62, 'The Martian Chronicles', 1, 1, 1, 20.50, 2, 'Mars berättelser', NULL, 1950, 'Bantam', 0, 0, 0, '2025-05-28 00:26:26', 3),
(63, 'Foundation', 1, 1, 1, 19.50, 1, 'Sci-fi klassiker', NULL, 1951, 'Bantam', 0, 1, 0, '2025-05-28 00:26:26', 3),
(64, 'I, Robot', 1, 1, 1, 17.90, 2, 'Robot berättelser', '', 1950, 'Bantam', 0, 0, 0, '2025-05-28 00:26:26', 3),
(65, 'The Caves of Steel', 1, 1, 1, 18.50, 1, 'Robot detektiv', NULL, 1954, 'Bantam', 0, 0, 0, '2025-05-28 00:26:26', 3),
(66, 'Finlandia - Sibelius Greatest', 1, 6, 5, 18.90, 1, 'Klassisk samling', NULL, 1995, 'Ondine', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(67, 'Sibelius Symphonies 1-7', 1, 6, 5, 24.50, 2, 'Komplett', NULL, 2001, 'BIS', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(68, 'The Beatles - Abbey Road', 1, 6, 5, 22.50, 1, 'Klassiskt album', 'Remastrad', 1969, 'Apple', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(69, 'The Beatles - Sgt Pepper', 1, 6, 5, 21.90, 2, 'Psykedelisk klassiker', NULL, 1967, 'Parlophone', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(70, 'Elvis Presley - The Essential', 1, 6, 5, 21.00, 2, 'Rock n roll kung', NULL, 2007, 'RCA', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(71, 'Elvis - 30 #1 Hits', 1, 6, 5, 19.50, 1, 'Bästa hits', NULL, 2002, 'RCA', 0, 0, 0, '2025-05-28 00:26:26', NULL),
(72, 'Bob Dylan - Greatest Hits', 1, 6, 5, 20.50, 1, 'Folk rock legend', NULL, 1967, 'Columbia', 0, 0, 0, '2025-05-28 00:26:26', NULL),
(73, 'Blood on the Tracks', 1, 6, 5, 18.90, 2, 'Dylan klassiker', NULL, 1975, 'Columbia', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(74, 'ABBA Gold', 1, 6, 5, 19.90, 2, 'Bästa hits', 'Internationell succé', 1992, 'Polar', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(75, 'ABBA - Arrival', 1, 6, 5, 17.50, 1, 'Svenskt original', NULL, 1976, 'Polar', 0, 0, 0, '2025-05-28 00:26:26', NULL),
(76, 'Pink Floyd - Dark Side of the Moon', 1, 6, 5, 23.50, 1, 'Prog rock klassiker', NULL, 1973, 'Harvest', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(77, 'The Wall', 1, 6, 5, 21.90, 2, 'Konceptalbum', NULL, 1979, 'Harvest', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(78, 'Queen - Greatest Hits', 1, 6, 5, 20.50, 1, 'Rock klassiker', NULL, 1981, 'EMI', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(79, 'A Night at the Opera', 1, 6, 5, 19.90, 2, 'Innehåller Bohemian Rhapsody', NULL, 1975, 'EMI', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(80, 'Led Zeppelin IV', 1, 6, 5, 22.50, 1, 'Hard rock klassiker', NULL, 1971, 'Atlantic', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(81, 'Physical Graffiti', 1, 6, 5, 24.50, 2, 'Dubbel-CD', NULL, 1975, 'Swan Song', 0, 0, 0, '2025-05-28 00:26:26', NULL),
(82, 'David Bowie - The Rise and Fall', 1, 6, 5, 21.50, 1, 'Glam rock', NULL, 1972, 'RCA', 0, 0, 0, '2025-05-28 00:26:26', NULL),
(83, 'Heroes', 1, 6, 5, 19.50, 2, 'Berlin trilogi', NULL, 1977, 'RCA', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(84, 'The Rolling Stones - Sticky Fingers', 1, 6, 5, 20.90, 1, 'Rock klassiker', NULL, 1971, 'Rolling Stones', 0, 0, 0, '2025-05-28 00:26:26', NULL),
(85, 'Exile on Main St.', 1, 6, 5, 23.50, 2, 'Dubbel-CD', NULL, 1972, 'Rolling Stones', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(86, 'Juice Leskinen - Kootut levyt', 1, 6, 5, 24.50, 2, 'Finsk rocklegend', 'Komplett samling', 1989, 'Poko', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(87, 'Eppu Normaali - Akun tehdas', 1, 6, 5, 18.50, 1, 'Finsk punk rock', NULL, 1980, 'Poko', 0, 0, 0, '2025-05-28 00:26:26', NULL),
(88, 'Dingo - Kerjäläisten valtakunta', 1, 6, 5, 16.50, 1, 'Kultalbum', NULL, 1986, 'Fazer', 0, 0, 0, '2025-05-28 00:26:26', NULL),
(89, 'Hassisen Kone - Rumat sävelet', 1, 6, 5, 22.50, 2, 'Punk klassiker', NULL, 1982, 'Poko', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(90, 'Leevi and the Leavings - Menestyksen salaisuus', 1, 6, 5, 19.90, 1, 'Indie pop', NULL, 1991, 'Pyramid', 0, 0, 0, '2025-05-28 00:26:26', NULL),
(91, 'Aki Kaurismäki Box Set', 1, 7, 7, 45.50, 1, 'Finsk filmsamling', 'Komplett box', 2005, 'Criterion', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(92, 'Ariel', 1, 7, 7, 16.50, 2, 'Kaurismäki klassiker', NULL, 1988, 'Villealfa', 0, 0, 0, '2025-05-28 00:26:26', NULL),
(93, 'Leningrad Cowboys Go America', 1, 7, 7, 18.50, 1, 'Kultfilm', NULL, 1989, 'Villealfa', 0, 0, 0, '2025-05-28 00:26:26', NULL),
(94, 'The Seventh Seal', 1, 7, 7, 22.50, 2, 'Bergman mästerverk', 'Criterion edition', 1957, 'Criterion', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(95, 'Persona', 1, 7, 7, 20.50, 1, 'Psykologiskt drama', NULL, 1966, 'Criterion', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(96, 'Jaws', 1, 7, 7, 15.50, 2, 'Thriller klassiker', NULL, 1975, 'Universal', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(97, 'E.T. the Extra-Terrestrial', 1, 7, 7, 14.90, 1, 'Sci-fi familj', NULL, 1982, 'Universal', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(98, 'Inception', 1, 7, 7, 18.50, 1, 'Sci-fi thriller', NULL, 2010, 'Warner Bros', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(99, 'The Dark Knight', 1, 7, 7, 17.90, 2, 'Superhero epos', NULL, 2008, 'Warner Bros', 0, 1, 0, '2025-05-28 00:26:26', NULL),
(100, 'Psycho', 1, 7, 7, 16.50, 3, 'Thriller klassiker', 'Äldre utgåva', 1960, 'Universal', 0, 1, 1, '2025-05-28 00:26:26', NULL),
(101, 'Vintage Helsinki postcard collection', 1, 3, 9, 45.00, 2, 'Gamla vykort 1920-1950', 'Historiskt värde', 1935, '', 0, 0, 1, '2025-05-28 00:32:42', NULL),
(102, 'Antique Finnish stamps collection', 1, 1, 9, 125.50, 1, 'Sällsynta frimärken', 'Komplett serie 1860-1917', 1900, 'Suomen Posti', 0, 1, 1, '2025-05-28 00:32:42', NULL),
(103, 'Old map of Turku/Åbo', 1, 3, 9, 78.50, 2, 'Historisk stadskarta', '1800-talet original', 1889, 'Kartografiska', 0, 0, 1, '2025-05-28 00:32:42', NULL),
(104, 'Vintage Marimekko fabric pieces', 1, 1, 9, 65.00, 3, 'Design klassiker tyg', '1960-tal Unikko mönster', 1968, 'Marimekko', 0, 1, 0, '2025-05-28 00:32:42', NULL),
(105, 'Arabia ceramic bowl set', 1, 1, 9, 95.00, 1, 'Finsk designkeramik', 'Vintage Ruska serie', 1960, 'Arabia', 0, 1, 1, '2025-05-28 00:32:42', NULL),
(106, 'Old Nokia 3310 phone', 1, 1, 9, 35.50, 2, 'Retro mobiltelefon', 'Nostalgi från 90-talet', 1999, 'Nokia', 0, 0, 0, '2025-05-28 00:32:42', NULL),
(107, 'Vintage Fazer chocolate tins', 1, 1, 9, 28.90, 3, 'Gamla förpackningar', 'Samlarobjekt 1950-60-tal', 1958, 'Fazer', 0, 0, 0, '2025-05-28 00:32:42', NULL),
(108, 'Alvar Aalto vase original', 1, 1, 9, 185.00, 2, 'Savoy vase 1937', 'Äkta Iittala original', 1937, 'Iittala', 0, 1, 1, '2025-05-28 00:32:42', NULL),
(109, 'Pentik pottery collection', 1, 1, 9, 58.50, 2, 'Lapplands keramik', '1970-tal kollektion', 1975, 'Pentik', 0, 0, 0, '2025-05-28 00:32:42', NULL),
(110, 'Vintage Finnish banknotes', 1, 1, 9, 125.00, 1, 'Gamla sedlar', 'Markka sedlar 1860-1963', 1920, 'Suomen Pankki', 0, 0, 1, '2025-05-28 00:32:42', NULL),
(111, 'Old compass and navigation tools', 1, 4, 9, 145.50, 2, 'Sjöfarts instrument', 'Mässing kompass 1920-tal', 1925, 'Maritime Instruments', 0, 0, 1, '2025-05-28 00:32:42', NULL),
(112, 'Vintage fishing equipment', 1, 4, 9, 78.90, 3, 'Gamla fiskeverktyg', 'Träspön och rullar', 1950, '', 0, 0, 0, '2025-05-28 00:32:42', NULL),
(113, 'Antique book binding tools', 1, 1, 9, 89.50, 2, 'Bokbinderi verktyg', 'Komplett set från 1930-tal', 1935, '', 0, 0, 1, '2025-05-28 00:32:42', NULL),
(114, 'Vintage Finnish coins collection', 1, 1, 9, 165.00, 1, 'Mynt samling', 'Penni och markka 1860-2001', 1950, 'Suomen Rahapaja', 0, 1, 1, '2025-05-28 00:32:42', NULL),
(115, 'Old ship model', 1, 4, 9, 225.50, 2, 'Handgjord skeppsmodell', 'Finsk ångbåt modell', 1960, '', 0, 1, 0, '2025-05-28 00:32:42', NULL),
(116, 'Tintin: Det svarta guldet', 1, 1, 8, 18.50, 1, 'Hergé klassiker', 'Svensk översättning', 1950, 'Casterman', 0, 0, 0, '2025-05-28 00:32:42', 1),
(117, 'Tintin: Månen tur och retur', 1, 1, 8, 19.90, 2, 'Rymdäventyr', 'Del 1 och 2', 1954, 'Casterman', 1, 1, 0, '2025-05-28 00:32:42', 1),
(118, 'Asterix: Gallernas häuptling', 1, 1, 8, 16.50, 1, 'Asterix klassiker', NULL, 1961, 'Dargaud', 0, 0, 0, '2025-05-28 00:32:42', 1),
(119, 'Asterix och Kleopatra', 1, 1, 8, 17.90, 2, 'Populär album', NULL, 1965, 'Dargaud', 1, 0, 0, '2025-05-28 00:32:42', 1),
(120, 'Kalle Anka pocket samling', 1, 5, 8, 45.50, 3, 'Svenska pockets 1980-tal', '20 exemplar', 1985, 'Egmont', 0, 0, 0, '2025-05-28 00:32:42', 1),
(121, 'Uncle Scrooge: Life and Times', 1, 1, 8, 28.50, 1, 'Don Rosa mästerverk', 'Engelska', 1994, 'Gladstone', 0, 1, 0, '2025-05-28 00:32:42', 3),
(122, 'Spider-Man: Amazing Fantasy #15', 2, 1, 8, 450.00, 4, 'Första Spider-Man', 'Mycket sällsynt reprint', 1962, 'Marvel', 0, 1, 1, '2025-05-28 00:32:42', 3),
(123, 'X-Men #1', 2, 1, 8, 125.50, 3, 'Första X-Men', 'Silverålder klassiker', 1963, 'Marvel', 0, 1, 1, '2025-05-28 00:32:42', 3),
(124, 'Watchmen', 1, 1, 8, 32.50, 1, 'Grafisk roman mästerverk', 'Komplett serie', 1987, 'DC Comics', 0, 1, 0, '2025-05-28 00:32:42', 3),
(125, 'Batman: The Dark Knight Returns', 1, 1, 8, 26.50, 1, 'Frank Miller klassiker', NULL, 1986, 'DC Comics', 0, 1, 0, '2025-05-28 00:32:42', 3),
(126, 'Sandman Vol 1: Preludes', 1, 1, 8, 22.90, 2, 'Neil Gaiman', 'Trade paperback', 1991, 'Vertigo', 1, 0, 0, '2025-05-28 00:32:42', 3),
(127, 'Maus: A Survivor\'s Tale', 1, 1, 8, 24.50, 1, 'Pulitzer Prize vinnare', 'Komplett', 1991, 'Pantheon', 0, 1, 1, '2025-05-28 00:32:42', 3),
(128, 'Persepolis', 1, 1, 8, 21.50, 1, 'Autobiografisk grafisk roman', NULL, 2000, 'Pantheon', 0, 1, 0, '2025-05-28 00:32:42', 3),
(129, 'Koirien Kalevala', 1, 1, 8, 19.90, 1, 'Mauri Kunnas klassiker', 'Finsk barnseriebok', 1992, 'Otava', 0, 1, 0, '2025-05-28 00:32:42', 2),
(130, 'Smurfarna: Det blå folket', 1, 5, 8, 16.50, 2, 'Peyo klassiker', 'Svenska', 1963, 'Dupuis', 1, 0, 0, '2025-05-28 00:32:42', 1),
(131, 'National Geographic 1987 årssats', 1, 1, 13, 45.50, 2, 'Komplett årssamling', '12 nummer i box', 1987, 'National Geographic', 0, 0, 0, '2025-05-28 00:32:42', 3),
(132, 'Tekniikan Maailma 1975-1985', 1, 1, 13, 78.50, 3, 'Teknisk tidskrift arkiv', '10 års samling', 1980, 'Tekniikan Maailma', 0, 0, 0, '2025-05-28 00:32:42', 2),
(133, 'Suosikki-lehti 1980-talet', 1, 1, 13, 32.90, 2, 'Musiktidning samling', 'Pop och rock 80-tal', 1985, 'Suosikki', 0, 0, 0, '2025-05-28 00:32:42', 2),
(134, 'Apu-lehti vintage collection', 1, 1, 13, 28.50, 3, 'Veckotidning 1970-tal', '50 exemplar', 1975, 'Yhtyneet Kuvalehdet', 0, 0, 0, '2025-05-28 00:32:42', 2),
(135, 'Life Magazine 1960s', 1, 1, 13, 65.00, 2, 'Amerikanska Life 60-tal', 'Historiska nummer', 1965, 'Time Inc', 0, 1, 0, '2025-05-28 00:32:42', 3),
(136, 'Playboy vintage 1970s', 2, 1, 13, 85.50, 3, 'Klassiska nummer', 'Samlarobjekt', 1975, 'Playboy', 0, 0, 1, '2025-05-28 00:32:42', 3),
(137, 'Mad Magazine collection', 1, 1, 13, 42.50, 2, 'Humor tidning 1970-80', 'Klassisk satir', 1978, 'EC Comics', 0, 0, 0, '2025-05-28 00:32:42', 3),
(138, 'Historiallinen Aikakauskirja', 1, 3, 13, 38.90, 2, 'Historisk tidskrift', '1990-talet komplett', 1995, 'Suomen Historiallinen Seura', 0, 0, 0, '2025-05-28 00:32:42', 2),
(139, 'Kotiliesi 1960-luku', 1, 1, 13, 35.50, 3, 'Hem och kök tidning', 'Vintage lifestyle', 1965, 'Otava', 0, 0, 0, '2025-05-28 00:32:42', 2),
(140, 'Time Magazine 1980s', 1, 1, 13, 48.50, 2, 'Amerikansk nyhetstidning', 'Viktiga 80-tals nummer', 1985, 'Time Inc', 0, 0, 0, '2025-05-28 00:32:42', 3),
(141, 'Monopol Helsingfors edition', 1, 1, 12, 28.50, 1, 'Lokalversion Helsinki', 'Komplett spel', 1995, 'Hasbro', 0, 0, 0, '2025-05-28 00:32:42', 2),
(142, 'Afrikan tähti', 1, 1, 12, 22.50, 1, 'Klassiskt finskt spel', 'Original från 1951', 1951, 'Kari Mannerla', 0, 1, 1, '2025-05-28 00:32:42', 2),
(143, 'Trivial Pursuit Svenska', 1, 1, 12, 24.90, 2, 'Kunskapsspel svenska', '', 1984, 'Parker Brothers', 0, 0, 0, '2025-05-28 00:32:42', 1),
(144, 'Risk världsherravälde', 1, 1, 12, 32.50, 2, 'Strategispel klassiker', NULL, 1985, 'Parker Brothers', 0, 0, 0, '2025-05-28 00:32:42', 1),
(145, 'Dungeons & Dragons Basic Set', 1, 1, 12, 125.50, 3, 'Vintage rollspel', 'Röd box från 1983', 1983, 'TSR', 0, 1, 1, '2025-05-28 00:32:42', 3),
(146, 'Dark Side of the Moon vinyl', 1, 6, 6, 85.50, 2, 'Pink Floyd original pressning', 'UK original 1973', 1973, 'Harvest', 0, 1, 1, '2025-05-28 00:32:42', NULL),
(147, 'Abbey Road original vinyl', 1, 6, 6, 125.00, 1, 'Beatles original pressning', 'UK Parlophone 1969', 1969, 'Apple', 0, 1, 1, '2025-05-28 00:32:42', NULL),
(148, 'ABBA - Waterloo vinyl', 1, 6, 6, 45.50, 2, 'Eurovision vinnare', 'Svenskt original 1974', 1974, 'Polar', 0, 0, 0, '2025-05-28 00:32:42', NULL),
(149, 'Led Zeppelin IV vinyl', 1, 6, 6, 78.90, 1, 'Zoso album original', 'Atlantic original 1971', 1971, 'Atlantic', 0, 1, 0, '2025-05-28 00:32:42', NULL),
(150, 'Juice Leskinen - Kala, kala, kala', 1, 6, 6, 65.00, 2, 'Finsk rock vinyl', 'Love Records original', 1978, 'Love Records', 0, 1, 1, '2025-05-28 00:32:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_author`
--

CREATE TABLE `product_author` (
  `product_author_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `author_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_author`
--

INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 2),
(4, 4, 3),
(5, 5, 4),
(6, 6, 4),
(7, 7, 5),
(8, 8, 5),
(9, 9, 5),
(10, 10, 5),
(11, 11, 4),
(12, 12, 4),
(13, 13, 4),
(14, 14, 2),
(15, 15, 2),
(16, 16, 6),
(17, 17, 6),
(18, 18, 6),
(19, 19, 6),
(20, 20, 7),
(21, 21, 7),
(22, 22, 7),
(23, 23, 8),
(24, 24, 8),
(25, 25, 8),
(26, 26, 9),
(127, 27, 9),
(129, 28, 9),
(128, 29, 9),
(30, 30, 9),
(31, 31, 10),
(32, 32, 10),
(33, 33, 10),
(34, 34, 11),
(35, 35, 11),
(36, 36, 12),
(37, 37, 12),
(125, 38, 13),
(39, 39, 13),
(40, 40, 14),
(41, 41, 14),
(42, 42, 15),
(43, 43, 15),
(44, 44, 16),
(45, 45, 16),
(46, 46, 17),
(47, 47, 17),
(48, 48, 18),
(49, 49, 19),
(50, 50, 20),
(51, 51, 21),
(52, 52, 21),
(53, 53, 22),
(54, 54, 23),
(55, 55, 24),
(56, 56, 25),
(57, 57, 26),
(58, 58, 26),
(59, 59, 27),
(60, 60, 28),
(61, 61, 29),
(62, 62, 29),
(63, 63, 30),
(126, 64, 30),
(65, 65, 30),
(66, 66, 31),
(67, 67, 31),
(68, 68, 32),
(69, 69, 32),
(70, 70, 33),
(71, 71, 33),
(72, 72, 34),
(73, 73, 34),
(74, 74, 35),
(75, 75, 35),
(76, 76, 36),
(77, 77, 36),
(78, 78, 37),
(79, 79, 37),
(80, 80, 38),
(81, 81, 38),
(82, 82, 39),
(83, 83, 39),
(84, 84, 40),
(85, 85, 40),
(86, 86, 41),
(87, 87, 42),
(88, 88, 43),
(89, 89, 44),
(90, 90, 45),
(91, 91, 46),
(92, 92, 46),
(93, 93, 46),
(94, 94, 47),
(95, 95, 47),
(96, 96, 48),
(97, 97, 48),
(98, 98, 49),
(99, 99, 49),
(100, 100, 50),
(101, 116, 51),
(102, 117, 51),
(103, 118, 52),
(104, 118, 53),
(105, 119, 52),
(106, 119, 53),
(107, 120, 54),
(108, 121, 55),
(109, 122, 56),
(110, 122, 57),
(111, 123, 56),
(112, 123, 57),
(113, 124, 58),
(114, 125, 59),
(115, 126, 60),
(116, 127, 61),
(117, 128, 62),
(118, 129, 63),
(119, 130, 65),
(120, 146, 36),
(121, 147, 32),
(122, 148, 35),
(123, 149, 38),
(124, 150, 41);

-- --------------------------------------------------------

--
-- Table structure for table `product_genre`
--

CREATE TABLE `product_genre` (
  `product_genre_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `genre_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_genre`
--

INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 1),
(10, 10, 1),
(11, 11, 1),
(12, 12, 1),
(13, 13, 1),
(14, 14, 1),
(15, 15, 1),
(16, 16, 6),
(17, 17, 6),
(18, 18, 6),
(19, 19, 6),
(20, 20, 1),
(21, 21, 1),
(22, 22, 1),
(23, 23, 1),
(24, 24, 1),
(25, 25, 1),
(26, 26, 6),
(157, 27, 6),
(159, 28, 6),
(158, 29, 6),
(30, 30, 6),
(31, 31, 1),
(32, 32, 1),
(33, 33, 1),
(34, 34, 1),
(35, 35, 1),
(36, 36, 1),
(37, 37, 1),
(151, 38, 1),
(39, 39, 1),
(40, 40, 1),
(41, 41, 1),
(42, 42, 1),
(43, 43, 1),
(44, 44, 1),
(45, 45, 1),
(46, 46, 1),
(47, 47, 1),
(48, 48, 1),
(49, 49, 1),
(50, 50, 1),
(51, 51, 1),
(52, 52, 1),
(53, 53, 1),
(54, 54, 1),
(55, 55, 1),
(56, 56, 1),
(57, 57, 6),
(58, 58, 6),
(59, 59, 6),
(60, 60, 6),
(61, 61, 1),
(62, 62, 1),
(63, 63, 1),
(152, 64, 1),
(65, 65, 1),
(66, 66, 9),
(67, 67, 9),
(68, 68, 7),
(69, 69, 7),
(70, 70, 7),
(71, 71, 7),
(72, 72, 7),
(73, 73, 7),
(74, 74, 7),
(75, 75, 7),
(76, 76, 7),
(77, 77, 7),
(78, 78, 7),
(79, 79, 7),
(80, 80, 7),
(81, 81, 7),
(82, 82, 7),
(83, 83, 7),
(84, 84, 7),
(85, 85, 7),
(86, 86, 7),
(87, 87, 7),
(88, 88, 7),
(89, 89, 7),
(90, 90, 7),
(91, 91, 10),
(92, 92, 10),
(93, 93, 10),
(94, 94, 10),
(95, 95, 10),
(96, 96, 10),
(97, 97, 10),
(98, 98, 10),
(99, 99, 10),
(100, 100, 10),
(101, 101, 11),
(102, 102, 11),
(103, 103, 11),
(104, 104, 11),
(105, 105, 11),
(106, 106, 11),
(107, 107, 11),
(108, 108, 11),
(109, 109, 11),
(110, 110, 11),
(111, 111, 11),
(112, 112, 11),
(113, 113, 11),
(114, 114, 11),
(115, 115, 11),
(116, 116, 10),
(117, 117, 10),
(118, 118, 10),
(119, 119, 10),
(120, 120, 6),
(121, 121, 10),
(122, 122, 10),
(123, 123, 10),
(124, 124, 1),
(125, 125, 10),
(126, 126, 1),
(127, 127, 5),
(128, 128, 5),
(129, 129, 6),
(130, 130, 6),
(131, 131, 11),
(132, 132, 11),
(133, 133, 11),
(134, 134, 11),
(135, 135, 11),
(136, 136, 11),
(137, 137, 11),
(138, 138, 3),
(139, 139, 11),
(140, 140, 11),
(155, 141, 11),
(153, 142, 11),
(156, 143, 11),
(144, 144, 11),
(154, 145, 11),
(146, 146, 7),
(147, 147, 7),
(148, 148, 7),
(149, 149, 7),
(150, 150, 7);

-- --------------------------------------------------------

--
-- Table structure for table `shelf`
--

CREATE TABLE `shelf` (
  `shelf_id` int NOT NULL,
  `shelf_sv_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `shelf_fi_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shelf`
--

INSERT INTO `shelf` (`shelf_id`, `shelf_sv_name`, `shelf_fi_name`) VALUES
(1, 'Finlandssvenska', 'Suomenruotsalainen'),
(3, 'Lokalhistoria', 'Paikallishistoria'),
(4, 'Sjöfart', 'Merenkulku'),
(5, 'Barn/Ungdom', 'Lapset/Nuoret'),
(6, 'Musik', 'Musiikki'),
(7, 'Film', 'Elokuva');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `status_id` int NOT NULL,
  `status_sv_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_fi_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`status_id`, `status_sv_name`, `status_fi_name`) VALUES
(1, 'Tillgänglig', 'Saatavilla'),
(2, 'Såld', 'Myyty');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int NOT NULL,
  `user_username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_role` int NOT NULL DEFAULT '3',
  `user_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_last_login` datetime DEFAULT NULL,
  `user_created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `user_is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_username`, `user_password_hash`, `user_role`, `user_email`, `user_last_login`, `user_created_at`, `user_is_active`) VALUES
(1, 'Admin', '$2y$10$J0jSNdu1QUebZT4KRq6yTOkwFQ4DyyIqO8Lj/o5KZuSTXUQ1MgCgu', 1, 'admin@karisantikvariat.fi', '2025-05-28 07:56:15', '2025-04-10 10:41:05', 1),
(3, 'Redaktor', '$2y$10$Qx1YgizfEOSuzTAp3r5bd.qfGJbMcXjdneHL9Ge9icsPbIsm5uicO', 2, 'redaktor@karisantikvariat.fi', '2025-05-27 13:41:40', '2025-04-30 13:57:26', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `author`
--
ALTER TABLE `author`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_sv_name`);

--
-- Indexes for table `condition`
--
ALTER TABLE `condition`
  ADD PRIMARY KEY (`condition_id`);

--
-- Indexes for table `event_log`
--
ALTER TABLE `event_log`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`genre_id`),
  ADD UNIQUE KEY `genre_name` (`genre_sv_name`);

--
-- Indexes for table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`image_id`),
  ADD UNIQUE KEY `image_path` (`image_path`),
  ADD KEY `fk_image_product` (`prod_id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`language_id`),
  ADD UNIQUE KEY `language_sv_name` (`language_sv_name`),
  ADD UNIQUE KEY `language_fi_name` (`language_fi_name`);

--
-- Indexes for table `newsletter_subscriber`
--
ALTER TABLE `newsletter_subscriber`
  ADD PRIMARY KEY (`subscriber_id`),
  ADD UNIQUE KEY `subscriber_email` (`subscriber_email`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`prod_id`),
  ADD KEY `shelf_id` (`shelf_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `condition_id` (`condition_id`),
  ADD KEY `idx_product_status` (`status`),
  ADD KEY `fk_product_language` (`language_id`);

--
-- Indexes for table `product_author`
--
ALTER TABLE `product_author`
  ADD PRIMARY KEY (`product_author_id`),
  ADD UNIQUE KEY `unique_product_author` (`product_id`,`author_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `product_genre`
--
ALTER TABLE `product_genre`
  ADD PRIMARY KEY (`product_genre_id`),
  ADD UNIQUE KEY `unique_product_genre` (`product_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `shelf`
--
ALTER TABLE `shelf`
  ADD PRIMARY KEY (`shelf_id`),
  ADD UNIQUE KEY `shelf_name` (`shelf_sv_name`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`),
  ADD UNIQUE KEY `status_name` (`status_sv_name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_username` (`user_username`),
  ADD UNIQUE KEY `user_email` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `author`
--
ALTER TABLE `author`
  MODIFY `author_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `condition`
--
ALTER TABLE `condition`
  MODIFY `condition_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event_log`
--
ALTER TABLE `event_log`
  MODIFY `event_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=286;

--
-- AUTO_INCREMENT for table `genre`
--
ALTER TABLE `genre`
  MODIFY `genre_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `image_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=279;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `language_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `newsletter_subscriber`
--
ALTER TABLE `newsletter_subscriber`
  MODIFY `subscriber_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `prod_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `product_author`
--
ALTER TABLE `product_author`
  MODIFY `product_author_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `product_genre`
--
ALTER TABLE `product_genre`
  MODIFY `product_genre_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `shelf`
--
ALTER TABLE `shelf`
  MODIFY `shelf_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_log`
--
ALTER TABLE `event_log`
  ADD CONSTRAINT `event_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `event_log_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`prod_id`);

--
-- Constraints for table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `fk_image_product` FOREIGN KEY (`prod_id`) REFERENCES `product` (`prod_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_language` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`),
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`shelf_id`) REFERENCES `shelf` (`shelf_id`),
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`),
  ADD CONSTRAINT `product_ibfk_3` FOREIGN KEY (`condition_id`) REFERENCES `condition` (`condition_id`),
  ADD CONSTRAINT `product_ibfk_4` FOREIGN KEY (`status`) REFERENCES `status` (`status_id`);

--
-- Constraints for table `product_author`
--
ALTER TABLE `product_author`
  ADD CONSTRAINT `product_author_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`prod_id`),
  ADD CONSTRAINT `product_author_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `author` (`author_id`);

--
-- Constraints for table `product_genre`
--
ALTER TABLE `product_genre`
  ADD CONSTRAINT `product_genre_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`prod_id`),
  ADD CONSTRAINT `product_genre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`genre_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
