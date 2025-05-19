-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2025 at 10:22 AM
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
-- Database: `ka_lagerhanteringssystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

CREATE TABLE `author` (
  `author_id` int(11) NOT NULL,
  `author_name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `author`
--

INSERT INTO `author` (`author_id`, `author_name`) VALUES
(1, 'J.K. Rowling'),
(2, 'Stephen King'),
(3, 'Margaret Atwood'),
(4, 'Neil Gaiman'),
(5, 'Toni Morrison'),
(6, 'Tove Jansson'),
(7, 'Astrid Lindgren'),
(8, 'Stieg Larsson'),
(9, 'Karl Ove Knausgård'),
(10, 'Jonas Jonasson'),
(11, 'Jane Austen'),
(12, 'Fyodor Dostojevskij'),
(13, 'Leo Tolstoj'),
(14, 'Virginia Woolf'),
(15, 'Franz Kafka'),
(16, 'Hilary Mantel'),
(17, 'Ken Follett'),
(18, 'Philippa Gregory'),
(19, 'Yuval Noah Harari'),
(20, 'Michelle Obama'),
(21, 'Malcolm Gladwell'),
(22, 'Rupi Kaur'),
(23, 'Edith Södergran'),
(24, 'Bo Carpelan'),
(25, 'Tomas Tranströmer'),
(26, 'Ludwig van Beethoven'),
(27, 'Wolfgang Amadeus Mozart'),
(28, 'Jean Sibelius'),
(29, 'Johann Sebastian Bach'),
(30, 'ABBA '),
(31, 'Adele '),
(32, 'Ed Sheeran'),
(33, 'Björk '),
(34, 'Christopher Nolan'),
(35, 'Ingmar Bergman'),
(36, 'Steven Spielberg'),
(37, 'Alfred Hitchcock');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_sv_name` varchar(100) NOT NULL,
  `category_fi_name` varchar(100) NOT NULL
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
  `condition_id` int(11) NOT NULL,
  `condition_sv_name` varchar(50) NOT NULL,
  `condition_fi_name` varchar(50) NOT NULL,
  `condition_code` varchar(10) NOT NULL,
  `condition_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `condition`
--

INSERT INTO `condition` (`condition_id`, `condition_sv_name`, `condition_fi_name`, `condition_code`, `condition_description`) VALUES
(1, 'Nyskick', '', 'K-1', 'Like new, no visible wear'),
(2, 'Mycket bra', '', 'K-2', 'Very good, minimal signs of use'),
(3, 'Bra', '', 'K-3', 'Good condition, some signs of wear'),
(4, 'Acceptabelt', '', 'K-4', 'Acceptable, significant wear but functional');

-- --------------------------------------------------------

--
-- Table structure for table `event_log`
--

CREATE TABLE `event_log` (
  `event_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_type` varchar(50) NOT NULL,
  `event_description` text DEFAULT NULL,
  `event_timestamp` datetime DEFAULT current_timestamp(),
  `product_id` int(11) DEFAULT NULL
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
(199, 1, 'login', 'Backdoor login used for admin', '2025-05-15 13:30:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE `genre` (
  `genre_id` int(11) NOT NULL,
  `genre_sv_name` varchar(100) NOT NULL,
  `genre_fi_name` varchar(100) NOT NULL
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
  `image_id` int(11) NOT NULL,
  `prod_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `image_uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `language_id` int(11) NOT NULL,
  `language_sv_name` varchar(50) NOT NULL,
  `language_fi_name` varchar(50) NOT NULL
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
  `subscriber_id` int(11) NOT NULL,
  `subscriber_email` varchar(100) NOT NULL,
  `subscriber_name` varchar(100) DEFAULT NULL,
  `subscribed_date` datetime DEFAULT current_timestamp(),
  `subscriber_is_active` tinyint(1) DEFAULT 1,
  `subscriber_language_pref` varchar(10) DEFAULT 'sv'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newsletter_subscriber`
--

INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES
(1, 'johanna.karlsson@example.com', 'Johanna Karlsson', '2025-05-13 12:37:38', 1, 'sv'),
(2, 'mikko.nieminen@example.fi', 'Mikko Nieminen', '2025-05-13 12:37:38', 1, 'fi'),
(3, 'anna.lindholm@example.com', 'Anna Lindholm', '2025-05-13 12:37:38', 1, 'sv'),
(4, 'erik.johansson@example.se', 'Erik Johansson', '2025-05-13 12:37:38', 1, 'sv'),
(5, 'liisa.makinen@example.fi', 'Liisa Mäkinen', '2025-05-13 12:37:38', 1, 'fi'),
(6, 'bengt.gustafsson@example.com', 'Bengt Gustafsson', '2025-05-13 12:37:38', 1, 'sv'),
(7, 'maria.henriksson@example.se', NULL, '2025-05-13 12:37:38', 1, 'sv'),
(8, 'juhani.korhonen@example.fi', NULL, '2025-05-13 12:37:38', 1, 'fi');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `prod_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` int(11) DEFAULT NULL,
  `shelf_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `condition_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `internal_notes` text DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `publisher` varchar(100) DEFAULT NULL,
  `special_price` tinyint(1) DEFAULT NULL COMMENT 'Om produkten är på rea: 1 = Ja, 0 = Nej',
  `recommended` tinyint(1) DEFAULT NULL COMMENT 'Om produkten är rekommenderad: 1 = Ja, 0 = Nej	',
  `rare` tinyint(1) DEFAULT 0,
  `date_added` datetime DEFAULT current_timestamp(),
  `language_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES
(1, 'Harry Potter och De Vises Sten', 1, 5, 1, 24.95, 2, 'Första boken i Harry Potter-serien', 'Bra skick, populär bland yngre läsare', 1997, 'Tiden', 0, 0, 0, '2025-05-13 12:37:38', 1),
(2, 'Lysningen', 1, 1, 1, 19.95, 3, NULL, NULL, 1977, 'Bra Böcker', 0, 0, 0, '2025-05-13 12:37:38', 1),
(3, 'Tjänarinnans berättelse', 1, 1, 1, 22.50, 1, 'Dystopisk roman', NULL, 1985, 'Norstedts', 1, 0, 0, '2025-05-13 12:37:38', 1),
(4, 'Amerikanska gudar', 1, 1, 1, NULL, 2, 'Fantasyroman', 'Köpt på bokauktion i Helsingfors', 2001, 'Bonnier Carlsen', 0, 0, 0, '2025-05-13 12:37:38', 1),
(5, 'Älskade', 1, 1, 1, NULL, 2, NULL, NULL, 1987, 'Trevi', 0, 0, 0, '2025-05-13 12:37:38', 1),
(6, 'Trollvinter', 1, 1, 1, 26.50, 1, 'Mumin-roman', 'En av våra bästsäljare', 1957, 'Schildts', 0, 0, 0, '2025-05-13 12:37:38', 1),
(7, 'Pippi Långstrump', 1, 5, 1, 18.95, 1, 'Barnklassiker', NULL, 1945, 'Rabén & Sjögren', 0, 0, 0, '2025-05-13 12:37:38', 1),
(8, 'Män som hatar kvinnor', 1, 1, 1, 23.75, 2, NULL, NULL, 2005, NULL, 0, 0, 0, '2025-05-13 12:37:38', 1),
(9, 'Min kamp 1', 1, 1, 1, 29.95, 2, 'Självbiografisk roman', 'Kontroversiell titel men efterfrågad', 2009, 'Oktober', 0, 0, 0, '2025-05-13 12:37:38', 1),
(10, 'Hundraåringen som klev ut genom fönstret och försvann', 1, 1, 1, 22.95, 1, 'Komisk roman', NULL, 2009, 'Piratförlaget', 1, 0, 0, '2025-05-13 12:37:38', 1),
(11, 'Sinuhe egyptiläinen', 1, 3, 1, 28.50, 2, NULL, NULL, 1945, 'WSOY', 0, 0, 0, '2025-05-13 12:37:38', 2),
(12, 'Tuntematon sotilas', 1, 3, 1, 24.95, 3, 'Krigsskildring', 'Viktigt historiskt verk, flera på väntelista', 1954, 'WSOY', 0, 0, 0, '2025-05-13 12:37:38', 2),
(13, 'Kalevala', 1, 3, 1, 32.00, 1, 'Finsk nationalepos', NULL, 1835, 'SKS', 0, 0, 1, '2025-05-13 12:37:38', 2),
(14, 'Muumipappa ja meri', 1, 5, 1, NULL, 2, NULL, NULL, 1965, 'WSOY', 0, 0, 0, '2025-05-13 12:37:38', 2),
(15, 'Stolthet och fördom', 1, 1, 1, 15.50, 2, 'Klassisk kärleksroman', 'Originalbindning, värdefull', 1813, NULL, 0, 0, 1, '2025-05-13 12:37:38', 1),
(16, 'Brott och straff', 1, 1, 1, 20.25, 3, NULL, NULL, 1866, 'Norstedts', 0, 0, 0, '2025-05-13 12:37:38', 1),
(17, 'Krig och fred', 1, 1, 1, 32.95, 4, 'Episk roman', NULL, 1869, 'Norstedts', 0, 0, 0, '2025-05-13 12:37:38', 1),
(18, 'Fru Dalloway', 1, 1, 1, 17.95, 2, 'Modernistisk roman', 'Fina understrykningar med blyerts', NULL, 'Bonnier', 0, 0, 0, '2025-05-13 12:37:38', 1),
(19, 'Wolf Hall', 1, 1, 1, NULL, 1, NULL, NULL, 2009, 'Fourth Estate', 0, 0, 0, '2025-05-13 12:37:38', 1),
(20, 'Svärdet och spiran', 1, 1, 1, 28.50, 2, 'Medeltida historisk roman', 'Personligt ex från författaren', 1989, 'Bonnier', 1, 0, 0, '2025-05-13 12:37:38', 1),
(21, 'Dikter', 1, 1, 1, 29.50, 2, 'Diktsamling', 'Sällsynt utgåva, rödkantad', 1916, 'Holger Schildts förlag', 0, 0, 1, '2025-05-13 12:37:38', 1),
(22, 'Min själ var en stilla sjö', 1, 1, 1, 24.50, 1, NULL, NULL, 1954, 'Schildts', 0, 0, 0, '2025-05-13 12:37:38', 1),
(23, 'Sapiens: En kort historik över mänskligheten', 1, 3, 1, 28.95, 1, 'Mänsklighetens historia', NULL, 2011, 'Natur & Kultur', 0, 0, 0, '2025-05-13 12:37:38', 1),
(24, 'Min historia', 1, 3, 1, NULL, 1, NULL, NULL, 2018, 'Bokförlaget Forum', 0, 0, 0, '2025-05-13 12:37:38', 1),
(25, 'Skärgårdens båtar', 1, 4, 1, 45.00, 2, 'Maritim historia', 'Intressant för lokala båtentusiaster', 2005, 'Wahlström & Widstrand', 0, 0, 0, '2025-05-13 12:37:38', 1),
(26, 'Östersjöns fyrar', 1, 4, 1, 39.95, 1, NULL, NULL, 2012, 'Nautiska Förlaget', 1, 0, 0, '2025-05-13 12:37:38', 1),
(27, 'Beethoven: De kompletta symfonierna', 1, 6, 5, 35.99, 1, NULL, NULL, 2003, 'Deutsche Grammophon', 0, 0, 0, '2025-05-13 12:37:38', 3),
(28, 'Mozart: Pianokonserter', 1, 6, 5, NULL, 2, 'Urval av pianokonserter', 'Speciell inspelning, efterfrågad', 1999, 'Philips', 0, 0, 0, '2025-05-13 12:37:38', 3),
(29, 'Sibelius: Symfonier nr 1-7', 1, 6, 5, 29.95, 1, 'Kompletta symfonier', NULL, 2001, 'BIS Records', 1, 0, 0, '2025-05-13 12:37:38', 3),
(30, 'ABBA Gold: Greatest Hits', 1, 6, 5, 18.50, 2, 'ABBA-samling', 'Nära nyskick, original', 1992, 'Polar', 0, 0, 0, '2025-05-13 12:37:38', 3),
(31, '25', 1, 6, 5, 15.95, 1, NULL, NULL, 2015, 'XL Recordings', 0, 0, 0, '2025-05-13 12:37:38', 3),
(32, '÷ (Divide)', 1, 6, 5, 17.99, 1, NULL, NULL, 2017, 'Asylum Records', 0, 0, 0, '2025-05-13 12:37:38', 3),
(33, 'Vespertine', 1, 6, 5, NULL, 2, 'Björk-album', 'Limiterad upplaga', 2001, 'One Little Indian', 0, 0, 0, '2025-05-13 12:37:38', 3),
(34, 'Abbey Road', 1, 6, 6, 45.99, 2, NULL, NULL, 1969, 'Apple Records', 0, 0, 1, '2025-05-13 12:37:38', 3),
(35, 'Thriller', 1, 6, 6, 39.95, 3, 'Michael Jackson-album', 'Originalpressning, samlarobjekt', 1982, 'Epic Records', 0, 0, 0, '2025-05-13 12:37:38', 3),
(36, 'Waterloo', 1, 6, 6, 42.50, 2, NULL, NULL, 1974, 'Polar', 1, 0, 0, '2025-05-13 12:37:38', 3),
(37, 'Inception', 1, 7, 7, 14.99, 1, NULL, NULL, 2010, 'Warner Bros.', 0, 0, 0, '2025-05-13 12:37:38', 3),
(38, 'Det sjunde inseglet', 1, 7, 7, 22.50, 2, 'Klassisk svensk film', 'Restaurerad utgåva', 1957, 'Criterion Collection', 0, 0, 0, '2025-05-13 12:37:38', 1),
(39, 'Schindlers lista', 1, 7, 7, NULL, 2, 'Historiskt drama', NULL, 1993, 'Universal Pictures', 0, 0, 0, '2025-05-13 12:37:38', 3),
(40, 'Watchmen', 1, 1, 8, 29.99, 1, NULL, NULL, 1986, 'DC Comics', 0, 0, 0, '2025-05-13 12:37:38', 1),
(41, 'Maus', 1, 1, 8, 24.95, 2, 'Grafisk roman', 'Prisbelönt och eftersökt', 1991, 'Bonnier Carlsen', 0, 0, 0, '2025-05-13 12:37:38', 1),
(42, 'Tintin: Den blå lotus', 1, 1, 8, 18.50, 3, NULL, NULL, 1936, NULL, 0, 0, 0, '2025-05-13 12:37:38', 1),
(43, 'Första utgåvan Ulysses', 2, 1, 9, 2500.00, 4, 'Sällsynt första utgåva', 'Extremt sällsynt, har verifierats äkta', 1922, 'Sylvia Beach', 0, 0, 1, '2025-05-13 12:37:38', 1),
(44, 'Limiterad vinyl-boxupplaga', 1, 6, 9, 199.95, 1, NULL, NULL, 2022, 'Rhino Records', 0, 0, 1, '2025-05-13 12:37:38', 3),
(45, 'Antika bokstöd', 1, 1, 9, NULL, 2, 'Dekorativa bokstöd', NULL, 1930, '', 0, 0, 0, '2025-05-13 12:37:38', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_author`
--

CREATE TABLE `product_author` (
  `product_author_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_author`
--

INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4),
(5, 5, 5),
(6, 6, 6),
(7, 7, 7),
(8, 8, 8),
(9, 9, 9),
(10, 10, 10),
(11, 11, 12),
(12, 12, 13),
(13, 14, 6),
(14, 15, 11),
(15, 16, 12),
(16, 17, 13),
(17, 18, 14),
(18, 19, 16),
(19, 20, 17),
(20, 21, 23),
(21, 22, 24),
(22, 23, 19),
(23, 24, 20),
(24, 27, 26),
(25, 28, 27),
(26, 29, 28),
(27, 30, 30),
(28, 31, 31),
(29, 32, 32),
(30, 33, 33),
(31, 37, 34),
(32, 38, 35),
(33, 39, 36);

-- --------------------------------------------------------

--
-- Table structure for table `product_genre`
--

CREATE TABLE `product_genre` (
  `product_genre_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `genre_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_genre`
--

INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES
(2, 1, 6),
(1, 1, 10),
(3, 2, 1),
(4, 3, 1),
(5, 4, 1),
(6, 4, 10),
(7, 5, 1),
(8, 6, 6),
(9, 7, 6),
(10, 7, 10),
(11, 8, 1),
(12, 9, 1),
(13, 10, 1),
(14, 10, 10),
(15, 11, 1),
(16, 11, 3),
(17, 12, 1),
(18, 13, 4),
(19, 14, 6),
(20, 15, 1),
(21, 16, 1),
(22, 17, 1),
(23, 17, 3),
(24, 18, 1),
(25, 19, 1),
(26, 19, 3),
(27, 20, 1),
(28, 20, 3),
(29, 21, 4),
(30, 22, 4),
(31, 23, 3),
(32, 24, 5),
(33, 25, 3),
(34, 26, 3),
(35, 27, 9),
(36, 28, 9),
(37, 29, 9),
(38, 30, 7),
(39, 31, 7),
(40, 32, 7),
(41, 33, 7),
(42, 34, 7),
(43, 35, 7),
(44, 36, 7),
(45, 40, 1),
(46, 41, 5),
(47, 42, 10);

-- --------------------------------------------------------

--
-- Table structure for table `shelf`
--

CREATE TABLE `shelf` (
  `shelf_id` int(11) NOT NULL,
  `shelf_sv_name` varchar(100) NOT NULL,
  `shelf_fi_name` varchar(100) NOT NULL
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
  `status_id` int(11) NOT NULL,
  `status_sv_name` varchar(50) NOT NULL,
  `status_fi_name` varchar(50) NOT NULL
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
  `user_id` int(11) NOT NULL,
  `user_username` varchar(50) NOT NULL,
  `user_password_hash` varchar(255) NOT NULL,
  `user_role` int(11) NOT NULL DEFAULT 3,
  `user_email` varchar(100) DEFAULT NULL,
  `user_last_login` datetime DEFAULT NULL,
  `user_created_at` datetime DEFAULT current_timestamp(),
  `user_is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_username`, `user_password_hash`, `user_role`, `user_email`, `user_last_login`, `user_created_at`, `user_is_active`) VALUES
(1, 'Admin', '$2y$10$J0jSNdu1QUebZT4KRq6yTOkwFQ4DyyIqO8Lj/o5KZuSTXUQ1MgCgu', 1, 'admin@karisantikvariat.fi', '2025-05-06 10:04:21', '2025-04-10 10:41:05', 1),
(3, 'Redaktor', '$2y$10$Qx1YgizfEOSuzTAp3r5bd.qfGJbMcXjdneHL9Ge9icsPbIsm5uicO', 2, 'redaktor@karisantikvariat.fi', '2025-05-06 10:30:18', '2025-04-30 13:57:26', 1);

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
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `condition`
--
ALTER TABLE `condition`
  MODIFY `condition_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event_log`
--
ALTER TABLE `event_log`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `genre`
--
ALTER TABLE `genre`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `language_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `newsletter_subscriber`
--
ALTER TABLE `newsletter_subscriber`
  MODIFY `subscriber_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `prod_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `product_author`
--
ALTER TABLE `product_author`
  MODIFY `product_author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `product_genre`
--
ALTER TABLE `product_genre`
  MODIFY `product_genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `shelf`
--
ALTER TABLE `shelf`
  MODIFY `shelf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
