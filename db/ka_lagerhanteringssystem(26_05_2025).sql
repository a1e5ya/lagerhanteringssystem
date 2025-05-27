-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2025 at 12:05 PM
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
(37, 'Alfred Hitchcock'),
(38, '-');

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
  `condition_code` varchar(10) NOT NULL
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
(199, 1, 'login', 'Backdoor login used for admin', '2025-05-15 13:30:52', NULL),
(200, 1, 'logout', 'User logged out: admin', '2025-05-19 12:22:24', NULL),
(201, 1, 'login', 'Backdoor login used for admin', '2025-05-19 12:22:33', NULL),
(202, 1, 'logout', 'User logged out: admin', '2025-05-19 12:23:19', NULL);

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
(45, 'Antika bokstöd', 1, 1, 9, NULL, 2, 'Dekorativa bokstöd', NULL, 1930, '', 0, 0, 0, '2025-05-13 12:37:38', 1),
(46, 'Trollkarlens skogen', 1, 3, 1, 290.49, 1, NULL, NULL, 1968, 'Music Records Ltd.', 1, 0, 1, '2025-05-21 12:45:49', 6),
(47, 'Trollkarlens glömda hemligheter', 1, 5, 1, 107.03, 2, NULL, NULL, 1961, 'Independent Books', 1, 0, 1, '2025-05-21 12:45:49', 8),
(48, 'Den hemliga tidens gång', 1, 3, 1, 237.58, 2, NULL, NULL, 1993, 'Big Publishing House', 1, 0, 1, '2025-05-21 12:45:49', 5),
(49, 'Vargens stjärnorna', 1, 5, 1, 107.60, 4, NULL, NULL, 2021, 'Independent Books', 1, 0, 1, '2025-05-21 12:45:49', 5),
(50, 'Äventyr med drömmarnas värld', 1, 1, 8, 298.54, 3, NULL, NULL, 1969, 'Comic Arts Inc.', 1, 0, 1, '2025-05-21 12:45:49', 5),
(51, 'Vargens stenar', 1, 3, 1, 269.96, 2, NULL, NULL, 2005, 'Big Publishing House', 1, 0, 1, '2025-05-21 12:45:49', 6),
(52, 'Vargens glömda hemligheter', 1, 5, 1, 246.36, 4, NULL, NULL, 1978, 'Small Press', 1, 0, 1, '2025-05-21 12:45:49', 5),
(53, 'Trollkarlens stjärnorna', 1, 4, 1, 185.07, 3, NULL, NULL, 2005, 'Independent Books', 1, 0, 1, '2025-05-21 12:45:49', 7),
(54, 'The Album: Jazz Classics', 1, 6, 5, 109.91, 3, NULL, NULL, 1971, 'Music Records Ltd.', 1, 0, 1, '2025-05-21 12:45:49', 5),
(55, 'Sounds of Tomorrow', 1, 6, 6, 172.93, 2, NULL, NULL, 2011, 'Music Records Ltd.', 1, 0, 1, '2025-05-21 12:45:49', 7),
(56, 'Sagan om det förlorade landet', 1, 5, 1, 266.38, 4, NULL, NULL, 2002, 'Big Publishing House', 1, 1, 0, '2025-05-21 12:45:49', 8),
(57, 'Collection: Rock Anthems', 1, 6, 5, 155.06, 4, NULL, NULL, 1980, 'Music Records Ltd.', 1, 1, 0, '2025-05-21 12:45:49', 8),
(58, 'Mysteriet på tidens gång', 1, 5, 1, 185.73, 1, NULL, NULL, 1968, 'Small Press', 1, 1, 0, '2025-05-21 12:45:49', 5),
(59, 'Filmen: rymdens djup', 1, 7, 7, 240.23, 1, NULL, NULL, 2017, 'Film Studio Ent.', 1, 1, 0, '2025-05-21 12:45:49', 6),
(60, 'Collection: Dreams', 1, 6, 6, 268.21, 2, NULL, NULL, 1985, 'Music Records Ltd.', 1, 1, 0, '2025-05-21 12:45:49', 2),
(61, 'The Album: Jazz Classics', 1, 6, 6, 26.65, 3, NULL, NULL, 1981, 'Music Records Ltd.', 1, 1, 0, '2025-05-21 12:45:49', 5),
(62, 'Filmen: rymdens djup', 1, 7, 7, 219.03, 2, NULL, NULL, 2003, 'Film Studio Ent.', 1, 1, 0, '2025-05-21 12:45:49', 8),
(63, 'Filmen: rymdens djup', 1, 7, 7, 230.13, 1, NULL, NULL, 2021, 'Film Studio Ent.', 1, 1, 0, '2025-05-21 12:45:49', 3),
(64, 'Collection: Rock Anthems', 1, 6, 5, 195.40, 2, NULL, NULL, 1993, 'Music Records Ltd.', 1, 1, 0, '2025-05-21 12:45:49', 3),
(65, 'Äventyr i tid och rum', 1, 7, 7, 102.39, 1, NULL, NULL, 1967, 'Film Studio Ent.', 1, 1, 0, '2025-05-21 12:45:49', 5),
(66, 'Mysteriet på tidens gång', 1, 1, 1, 290.99, 1, NULL, NULL, 2006, 'Independent Books', 0, 0, 0, '2025-05-21 12:45:49', 7),
(67, 'Filmen: den gamla staden', 1, 7, 7, 147.23, 1, NULL, NULL, 1982, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 12:45:49', 5),
(68, 'Filmen: rymdens djup', 1, 7, 7, 256.77, 1, NULL, NULL, 1985, 'Film Studio Ent.', 0, 1, 0, '2025-05-21 12:45:49', 8),
(69, 'Äventyr i tid och rum', 1, 7, 7, 172.93, 3, NULL, NULL, 2024, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 4),
(70, 'Äventyr i tid och rum', 1, 7, 7, 258.85, 3, NULL, NULL, 1964, 'Film Studio Ent.', 0, 1, 0, '2025-05-21 12:45:49', 8),
(71, 'Den hemliga det förlorade landet', 1, 5, 1, 100.86, 1, NULL, NULL, 1978, 'Small Press', 1, 0, 0, '2025-05-21 12:45:49', 4),
(72, 'Äventyr i den gamla staden', 1, 7, 7, 175.76, 4, NULL, NULL, 1962, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 8),
(73, 'En resa till tidens gång', 1, 5, 1, 173.34, 1, NULL, NULL, 1993, 'Big Publishing House', 0, 0, 0, '2025-05-21 12:45:49', 3),
(74, 'Mysteriet i rymdens djup', 1, 7, 7, 103.11, 4, NULL, NULL, 1988, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 6),
(75, 'Mysteriet på skogen', 1, 5, 1, 237.95, 3, NULL, NULL, 2001, 'Independent Books', 0, 1, 0, '2025-05-21 12:45:49', 4),
(76, 'Vintage leksak', 1, 6, 9, 219.04, 3, NULL, NULL, 2017, 'Small Press', 0, 0, 0, '2025-05-21 12:45:49', 6),
(77, 'Filmen: tid och rum', 1, 7, 7, 150.96, 2, NULL, NULL, 2011, 'Film Studio Ent.', 0, 1, 0, '2025-05-21 12:45:49', 4),
(78, 'Den hemliga glömda hemligheter', 1, 3, 1, 179.91, 3, NULL, NULL, 1987, 'Big Publishing House', 0, 0, 0, '2025-05-21 12:45:49', 8),
(79, 'Sagan om det förlorade landet', 1, 5, 1, 137.90, 1, NULL, NULL, 1998, 'Big Publishing House', 0, 0, 0, '2025-05-21 12:45:49', 1),
(80, 'Filmen: den gamla staden', 1, 7, 7, 26.54, 4, NULL, NULL, 1987, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 6),
(81, 'Greatest Hits: Nature', 1, 6, 5, 114.77, 4, NULL, NULL, 1968, 'Music Records Ltd.', 0, 0, 0, '2025-05-21 12:45:49', 7),
(82, 'Vargens glömda hemligheter', 1, 4, 1, 271.74, 1, NULL, NULL, 1997, 'Big Publishing House', 0, 0, 0, '2025-05-21 12:45:49', 8),
(83, 'Filmen: den förbjudna skogen', 1, 7, 7, 153.25, 3, NULL, NULL, 1974, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 7),
(84, 'Vargens stjärnorna', 1, 1, 1, 102.39, 1, NULL, NULL, 1965, 'Small Press', 0, 0, 0, '2025-05-21 12:45:49', 7),
(85, 'Vargens glömda hemligheter', 1, 1, 1, 273.74, 4, NULL, NULL, 2012, 'Big Publishing House', 0, 0, 0, '2025-05-21 12:45:49', 2),
(86, 'Mysteriet på det förlorade landet', 1, 1, 1, 185.04, 2, NULL, NULL, 1982, 'Big Publishing House', 0, 0, 0, '2025-05-21 12:45:49', 1),
(87, 'Filmen: tid och rum', 1, 7, 7, 278.41, 1, NULL, NULL, 1982, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 5),
(88, 'Greatest Hits: Dreams', 1, 6, 6, 172.96, 2, NULL, NULL, 1960, 'Music Records Ltd.', 0, 0, 0, '2025-05-21 12:45:49', 6),
(89, 'Trollkarlens glömda hemligheter', 1, 1, 1, 126.96, 4, NULL, NULL, 1980, 'Big Publishing House', 0, 0, 0, '2025-05-21 12:45:49', 8),
(90, 'En resa till glömda hemligheter', 1, 5, 1, 142.14, 2, NULL, NULL, 1999, 'Independent Books', 0, 0, 0, '2025-05-21 12:45:49', 1),
(91, 'Collection: Rock Anthems', 1, 6, 6, 137.91, 1, NULL, NULL, 1978, 'Music Records Ltd.', 0, 0, 0, '2025-05-21 12:45:49', 8),
(92, 'Sagan om det förlorade landet', 1, 3, 1, 237.52, 3, NULL, NULL, 2015, 'Big Publishing House', 0, 0, 0, '2025-05-21 12:45:49', 8),
(93, 'Vintage Magasin: Historia Nr.38', 1, 1, 13, 277.62, 3, NULL, NULL, 2007, 'Small Press', 0, 0, 0, '2025-05-21 12:45:49', 6),
(94, 'Trollkarlens stjärnorna', 1, 3, 1, 217.48, 4, NULL, NULL, 1977, 'Independent Books', 0, 0, 0, '2025-05-21 12:45:49', 2),
(95, 'Den hemliga skogen', 1, 4, 1, 26.23, 1, NULL, NULL, 1951, 'Big Publishing House', 0, 0, 0, '2025-05-21 12:45:49', 6),
(96, 'Den hemliga glömda hemligheter', 1, 3, 1, 197.80, 4, NULL, NULL, 2021, 'Independent Books', 0, 0, 0, '2025-05-21 12:45:49', 2),
(97, 'Mysteriet på det förlorade landet', 1, 1, 1, 279.79, 1, NULL, NULL, 2009, 'Big Publishing House', 0, 0, 0, '2025-05-21 12:45:49', 1),
(98, 'Best of Nature', 1, 6, 5, 234.33, 4, NULL, NULL, 1986, 'Music Records Ltd.', 0, 0, 0, '2025-05-21 12:45:49', 1),
(99, 'Filmen: den gamla staden', 1, 7, 7, 197.77, 3, NULL, NULL, 1993, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 5),
(100, 'Trollkarlens stenar', 1, 5, 1, 187.97, 2, NULL, NULL, 1989, 'Independent Books', 0, 0, 0, '2025-05-21 12:45:49', 8),
(101, 'Den mystiska från framtiden', 1, 1, 8, 126.31, 3, NULL, NULL, 1974, 'Comic Arts Inc.', 0, 0, 0, '2025-05-21 12:45:49', 6),
(102, 'Sagan om tidens gång', 1, 5, 1, 237.52, 4, NULL, NULL, 2009, 'Small Press', 0, 0, 0, '2025-05-21 12:45:49', 4),
(103, 'Äventyr i rymdens djup', 1, 7, 7, 246.36, 1, NULL, NULL, 1951, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 4),
(104, 'Mysteriet i rymdens djup', 1, 7, 7, 207.76, 3, NULL, NULL, 2008, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 7),
(105, 'Vargens tidens gång', 1, 1, 1, 199.16, 2, NULL, NULL, 1986, 'Independent Books', 0, 0, 0, '2025-05-21 12:45:49', 3),
(106, 'Livet med stenar', 1, 3, 1, 172.93, 4, NULL, NULL, 1986, 'Small Press', 0, 0, 0, '2025-05-21 12:45:49', 4),
(107, 'Superhjältarna från framtiden', 1, 1, 8, 257.65, 3, NULL, NULL, 1968, 'Comic Arts Inc.', 0, 0, 0, '2025-05-21 12:45:49', 3),
(108, 'Den hemliga det förlorade landet', 1, 1, 1, 176.77, 2, NULL, NULL, 1973, 'Independent Books', 0, 0, 0, '2025-05-21 12:45:49', 2),
(109, 'Best of the 80s', 1, 6, 5, 298.54, 3, NULL, NULL, 1962, 'Music Records Ltd.', 0, 0, 0, '2025-05-21 12:45:49', 4),
(110, 'Sagan om tidens gång', 1, 4, 1, 267.45, 1, NULL, NULL, 2006, 'Big Publishing House', 0, 0, 0, '2025-05-21 12:45:49', 5),
(111, 'Vargens stjärnorna', 1, 5, 1, 290.49, 1, NULL, NULL, 2018, 'Independent Books', 0, 0, 0, '2025-05-21 12:45:49', 3),
(112, 'Filmen: den gamla staden', 1, 7, 7, 258.85, 4, NULL, NULL, 1999, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 7),
(113, 'Äventyr i tid och rum', 1, 7, 7, 185.04, 3, NULL, NULL, 2005, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 5),
(114, 'Mysteriet på det förlorade landet', 1, 1, 1, 256.77, 2, NULL, NULL, 1993, 'Small Press', 0, 0, 0, '2025-05-21 12:45:49', 1),
(115, 'Äventyr med kapitel 1', 1, 1, 8, 281.33, 4, NULL, NULL, 1968, 'Comic Arts Inc.', 0, 0, 0, '2025-05-21 12:45:49', 2),
(116, 'En resa till glömda hemligheter', 1, 1, 1, 230.13, 4, NULL, NULL, 2004, 'Small Press', 0, 0, 0, '2025-05-21 12:45:49', 6),
(117, 'Greatest Hits: Dreams', 1, 6, 6, 277.62, 1, NULL, NULL, 1989, 'Music Records Ltd.', 0, 0, 0, '2025-05-21 12:45:49', 1),
(118, 'Mysteriet i rymdens djup', 1, 7, 7, 107.03, 1, NULL, NULL, 1969, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 4),
(119, 'Vintage Magasin: Historia Nr.52', 1, 1, 13, 273.74, 1, NULL, NULL, 1974, 'Small Press', 0, 0, 0, '2025-05-21 12:45:49', 6),
(120, 'Trollkarlens glömda hemligheter', 1, 3, 1, 142.14, 2, NULL, NULL, 1968, 'Big Publishing House', 0, 0, 0, '2025-05-21 12:45:49', 2),
(121, 'Superhjältarna från framtiden', 1, 1, 8, 234.33, 2, NULL, NULL, 1984, 'Comic Arts Inc.', 0, 0, 0, '2025-05-21 12:45:49', 2),
(122, 'Filmen: rymdens djup', 1, 7, 7, 100.86, 1, NULL, NULL, 1965, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 7),
(123, 'Äventyr i den gamla staden', 1, 7, 7, 179.91, 1, NULL, NULL, 2014, 'Film Studio Ent.', 0, 0, 0, '2025-05-21 12:45:49', 3),
(124, 'Livet med stenar', 1, 4, 1, 269.96, 4, NULL, NULL, 1966, 'Small Press', 0, 0, 0, '2025-05-21 12:45:49', 5),
(125, 'Sagan om det förlorade landet', 1, 1, 1, 267.45, 2, NULL, NULL, 1993, 'Big Publishing House', 0, 0, 0, '2025-05-21 12:45:49', 6),
(126, 'Mysteriet på stjärnorna', 1, 1, 1, 217.48, 2, NULL, NULL, 1970, 'Independent Books', 0, 0, 0, '2025-05-21 12:45:49', 4),
(127, 'Den hemliga tiden', 2, 5, 1, 16.32, 2, NULL, NULL, 1993, 'Big Publishing House', 1, 0, 1, '2025-05-21 12:57:12', 3),
(128, 'Mysteriet på det förlorade landet', 2, 4, 1, 119.53, 3, NULL, NULL, 1991, 'Film Studio Ent.', 1, 0, 1, '2025-05-21 12:57:12', 4),
(129, 'Äventyr med kapitel 1', 2, 1, 8, 22.18, 4, NULL, NULL, 1957, 'Small Press', 1, 0, 1, '2025-05-21 12:57:12', 2),
(130, 'Vintage karta', 2, 1, 9, 137.95, 3, NULL, NULL, 1957, 'Independent Books', 1, 0, 1, '2025-05-21 12:57:12', 6),
(131, 'The Album: Jazz Classics', 2, 6, 5, 193.39, 3, NULL, NULL, 2020, 'Music Records Ltd.', 1, 0, 1, '2025-05-21 12:57:12', 8),
(132, 'Fantasy Spel: De glömda världarna', 2, 1, 12, 51.22, 3, NULL, NULL, 2019, 'Independent Books', 1, 0, 1, '2025-05-21 12:57:12', 8),
(133, 'Klassikern: tid och rum', 2, 7, 7, 82.66, 1, NULL, NULL, 1991, 'Small Press', 1, 0, 1, '2025-05-21 12:57:12', 6),
(134, 'Vintage Magasin: Vetenskap Nr.26', 2, 1, 13, 194.27, 2, NULL, NULL, 1970, 'Music Records Ltd.', 1, 0, 1, '2025-05-21 12:57:12', 1),
(135, 'Den hemliga tiden', 2, 3, 1, 126.96, 2, NULL, NULL, 1967, 'Big Publishing House', 1, 0, 1, '2025-05-21 12:57:12', 2),
(136, 'Äventyr i rymdens djup', 2, 7, 7, 276.13, 3, NULL, NULL, 1978, 'Independent Books', 1, 0, 1, '2025-05-21 12:57:12', 5),
(137, 'Filmen: den förbjudna skogen', 2, 7, 7, 123.63, 1, NULL, NULL, 1956, 'Film Studio Ent.', 1, 0, 1, '2025-05-21 12:57:12', 6),
(138, 'Mysteriet i den gamla staden', 2, 7, 7, 271.74, 1, NULL, NULL, 1963, 'Independent Books', 1, 0, 1, '2025-05-21 12:57:12', 5),
(139, 'Fantasy Spel: Kungarikets öde', 2, 1, 12, 264.38, 2, NULL, NULL, 1992, 'Independent Books', 1, 0, 0, '2025-05-21 12:57:12', 5),
(140, 'Collection: Tomorrow', 2, 1, 12, 196.97, 3, NULL, NULL, 1964, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 12:57:12', 4),
(141, 'Sällsynt Magasin: Mode Nr.95', 2, 1, 8, 24.28, 3, NULL, NULL, 2012, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 12:57:12', 7),
(142, 'Superhjältarna från framtiden', 2, 1, 12, 241.30, 2, NULL, NULL, 1991, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 12:57:12', 7),
(143, 'Fantasy Spel: Kungarikets öde', 2, 1, 13, 245.74, 4, NULL, NULL, 1998, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 12:57:12', 5),
(144, 'The Album: Tomorrow', 2, 1, 1, 83.41, 1, NULL, NULL, 2000, 'Small Press', 1, 0, 0, '2025-05-21 12:57:12', 1),
(145, 'Vintage mynt', 2, 7, 7, 84.64, 4, NULL, NULL, 1996, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 12:57:12', 5),
(146, 'Sällsynt Magasin: Historia Nr.77', 2, 6, 5, 233.43, 4, NULL, NULL, 2010, 'Big Publishing House', 1, 0, 0, '2025-05-21 12:57:12', 1),
(147, 'Vintage frimärke', 1, 6, 6, 190.99, 1, NULL, NULL, 1993, 'Music Records Ltd.', 1, 1, 0, '2025-05-21 12:57:12', 4),
(148, 'Sagan om tidens gång', 1, 3, 1, 187.67, 3, NULL, NULL, 1974, 'Small Press', 1, 0, 0, '2025-05-21 12:57:12', 5),
(149, 'Vargens glömda hemligheter', 1, 5, 1, 21.05, 3, NULL, NULL, 1993, 'Big Publishing House', 1, 1, 1, '2025-05-21 12:57:12', 5),
(150, 'Best of Rock Anthems', 1, 6, 5, 237.07, 3, NULL, NULL, 2011, 'Big Publishing House', 1, 0, 0, '2025-05-21 12:57:12', 8),
(151, 'Klassikern: den gamla staden', 1, 7, 7, 240.23, 2, NULL, NULL, 1969, 'Film Studio Ent.', 1, 1, 0, '2025-05-21 12:57:12', 7),
(152, 'Vargens tidens gång', 1, 3, 1, 220.35, 3, NULL, NULL, 2005, 'Film Studio Ent.', 1, 1, 1, '2025-05-21 12:57:12', 1),
(153, 'Mysteriet på det förlorade landet', 1, 3, 1, 298.11, 4, NULL, NULL, 1976, 'Big Publishing House', 1, 0, 0, '2025-05-21 12:57:12', 1),
(154, 'En resa till det förlorade landet', 1, 5, 1, 291.68, 4, NULL, NULL, 1968, 'Big Publishing House', 1, 0, 1, '2025-05-21 12:57:12', 7),
(155, 'Best of the 80s', 1, 6, 5, 187.35, 1, NULL, NULL, 1978, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 12:57:12', 3),
(156, 'Sagan om det förlorade landet', 1, 3, 1, 169.69, 1, NULL, NULL, 1957, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 12:57:12', 6),
(157, 'Sagan om det förlorade landet', 1, 5, 1, 256.76, 4, NULL, NULL, 2024, 'Music Records Ltd.', 1, 1, 0, '2025-05-21 12:57:12', 4),
(158, 'Mysteriet i rymdens djup', 1, 7, 7, 204.64, 2, NULL, NULL, 1989, 'Music Records Ltd.', 1, 1, 0, '2025-05-21 12:57:12', 2),
(159, 'Filmen: den förbjudna skogen', 1, 7, 7, 123.63, 1, NULL, NULL, 1956, 'Film Studio Ent.', 1, 1, 0, '2025-05-21 12:57:12', 6),
(160, 'Mysteriet i den gamla staden', 1, 7, 7, 271.74, 1, NULL, NULL, 1963, 'Independent Books', 1, 1, 1, '2025-05-21 12:57:12', 5),
(161, 'Trollkarlens glömda hemligheter', 1, 1, 12, 267.27, 4, NULL, NULL, 1994, 'Music Records Ltd.', 1, 1, 1, '2025-05-21 12:57:12', 4),
(162, 'Best of Nature', 1, 1, 13, 215.43, 3, NULL, NULL, 1996, 'Big Publishing House', 1, 1, 0, '2025-05-21 12:57:12', 5),
(163, 'Äventyr med drömmarnas värld', 1, 1, 12, 171.06, 4, NULL, NULL, 1985, 'Independent Books', 1, 1, 0, '2025-05-21 12:57:12', 3),
(164, 'Äventyr Spel: De glömda världarna', 1, 1, 13, 174.44, 3, NULL, NULL, 1996, 'Big Publishing House', 1, 1, 0, '2025-05-21 12:57:12', 6),
(165, 'Greatest Hits: Jazz Classics', 1, 1, 12, 284.75, 4, NULL, NULL, 1991, 'Independent Books', 1, 1, 0, '2025-05-21 12:57:12', 2),
(166, 'The Album: the 80s', 1, 7, 7, 205.74, 4, NULL, NULL, 1994, 'Independent Books', 1, 1, 0, '2025-05-21 12:57:12', 3),
(167, 'Best of Dreams', 1, 1, 8, 167.67, 1, NULL, NULL, 2022, 'Comic Arts Inc.', 1, 1, 0, '2025-05-21 12:57:12', 1),
(168, 'Vintage frimärke', 1, 6, 6, 127.29, 1, NULL, NULL, 1980, 'Independent Books', 1, 0, 1, '2025-05-21 12:57:12', 2),
(169, 'Vintage Magasin: Mode Nr.19', 1, 6, 5, 261.22, 1, NULL, NULL, 1987, 'Big Publishing House', 1, 1, 1, '2025-05-21 12:57:12', 1),
(170, 'Sagan om det förlorade landet', 1, 1, 8, 29.12, 4, NULL, NULL, 1951, 'Small Press', 1, 0, 0, '2025-05-21 12:57:12', 8),
(171, 'Filmen: den förbjudna skogen', 1, 6, 5, 292.74, 2, NULL, NULL, 1954, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 12:57:12', 8),
(172, 'Vintage karta', 1, 7, 7, 266.24, 4, NULL, NULL, 1993, 'Big Publishing House', 1, 0, 0, '2025-05-21 12:57:12', 5),
(173, 'Filmen: den förbjudna skogen', 1, 1, 12, 24.33, 4, NULL, NULL, 2014, 'Comic Arts Inc.', 1, 1, 1, '2025-05-21 12:57:12', 8),
(174, 'Vintage docka', 1, 6, 5, 128.96, 4, NULL, NULL, 1961, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 12:57:12', 2),
(175, 'En resa till stjärnorna', 1, 6, 6, 114.18, 2, NULL, NULL, 1961, 'Small Press', 1, 0, 0, '2025-05-21 12:57:12', 6),
(176, 'Sounds of Rock Anthems', 1, 1, 12, 229.68, 2, NULL, NULL, 2003, 'Music Records Ltd.', 1, 0, 1, '2025-05-21 12:57:12', 1),
(177, 'Den hemliga tiden', 2, 4, 1, 16.32, 2, NULL, NULL, 1993, 'Big Publishing House', 1, 0, 1, '2025-05-21 12:59:01', 3),
(178, 'Mysteriet på det förlorade landet', 2, 4, 1, 119.53, 3, NULL, NULL, 1991, 'Film Studio Ent.', 1, 0, 1, '2025-05-21 12:59:01', 4),
(179, 'Äventyr med kapitel 1', 2, 1, 8, 22.18, 4, NULL, NULL, 1957, 'Small Press', 1, 0, 1, '2025-05-21 12:59:01', 2),
(180, 'Vintage karta', 2, 1, 9, 137.95, 3, NULL, NULL, 1957, 'Independent Books', 1, 0, 1, '2025-05-21 12:59:01', 6),
(181, 'The Album: Jazz Classics', 2, 6, 5, 193.39, 3, NULL, NULL, 2020, 'Music Records Ltd.', 1, 0, 1, '2025-05-21 12:59:01', 8),
(182, 'Fantasy Spel: De glömda världarna', 2, 1, 12, 51.22, 3, NULL, NULL, 2019, 'Independent Books', 1, 0, 1, '2025-05-21 12:59:01', 8),
(183, 'Klassikern: tid och rum', 2, 7, 7, 82.66, 1, NULL, NULL, 1991, 'Small Press', 1, 0, 1, '2025-05-21 12:59:01', 6),
(184, 'Vintage Magasin: Vetenskap Nr.26', 2, 1, 13, 194.27, 2, NULL, NULL, 1970, 'Music Records Ltd.', 1, 0, 1, '2025-05-21 12:59:01', 1),
(185, 'Den hemliga tiden', 2, 3, 1, 126.96, 2, NULL, NULL, 1967, 'Big Publishing House', 1, 0, 1, '2025-05-21 12:59:01', 2),
(186, 'Äventyr i rymdens djup', 2, 7, 7, 276.13, 3, NULL, NULL, 1978, 'Independent Books', 1, 0, 1, '2025-05-21 12:59:01', 5),
(187, 'Filmen: den förbjudna skogen', 2, 7, 7, 123.63, 1, NULL, NULL, 1956, 'Film Studio Ent.', 1, 0, 1, '2025-05-21 12:59:01', 6),
(188, 'Mysteriet i den gamla staden', 2, 7, 7, 271.74, 1, NULL, NULL, 1963, 'Independent Books', 1, 0, 1, '2025-05-21 12:59:01', 5),
(189, 'Fantasy Spel: Kungarikets öde', 2, 1, 12, 264.38, 2, NULL, NULL, 1992, 'Independent Books', 1, 0, 0, '2025-05-21 12:59:01', 5),
(190, 'Collection: Tomorrow', 2, 1, 12, 196.97, 3, NULL, NULL, 1964, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 12:59:01', 4),
(191, 'Sällsynt Magasin: Mode Nr.95', 2, 1, 8, 24.28, 3, NULL, NULL, 2012, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 12:59:01', 7),
(192, 'Superhjältarna från framtiden', 2, 1, 12, 241.30, 2, NULL, NULL, 1991, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 12:59:01', 7),
(193, 'Fantasy Spel: Kungarikets öde', 2, 1, 13, 245.74, 4, NULL, NULL, 1998, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 12:59:01', 5),
(194, 'The Album: Tomorrow', 2, 1, 1, 83.41, 1, NULL, NULL, 2000, 'Small Press', 1, 0, 0, '2025-05-21 12:59:01', 1),
(195, 'Vintage mynt', 2, 7, 7, 84.64, 4, NULL, NULL, 1996, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 12:59:01', 5),
(196, 'Sällsynt Magasin: Historia Nr.77', 2, 6, 5, 233.43, 4, NULL, NULL, 2010, 'Big Publishing House', 1, 0, 0, '2025-05-21 12:59:01', 1),
(197, 'Vintage frimärke', 1, 6, 6, 190.99, 1, NULL, NULL, 1993, 'Music Records Ltd.', 1, 1, 0, '2025-05-21 12:59:01', 4),
(198, 'Sagan om tidens gång', 1, 3, 1, 187.67, 3, NULL, NULL, 1974, 'Small Press', 1, 0, 0, '2025-05-21 12:59:01', 5),
(199, 'Vargens glömda hemligheter', 1, 5, 1, 21.05, 3, NULL, NULL, 1993, 'Big Publishing House', 1, 1, 1, '2025-05-21 12:59:01', 5),
(200, 'Best of Rock Anthems', 1, 6, 5, 237.07, 3, NULL, NULL, 2011, 'Big Publishing House', 1, 0, 0, '2025-05-21 12:59:01', 8),
(201, 'Klassikern: den gamla staden', 1, 7, 7, 240.23, 2, NULL, NULL, 1969, 'Film Studio Ent.', 1, 1, 0, '2025-05-21 12:59:01', 7),
(202, 'Vargens tidens gång', 1, 3, 1, 220.35, 3, NULL, NULL, 2005, 'Film Studio Ent.', 1, 1, 1, '2025-05-21 12:59:01', 1),
(203, 'Mysteriet på det förlorade landet', 1, 3, 1, 298.11, 4, NULL, NULL, 1976, 'Big Publishing House', 1, 0, 0, '2025-05-21 12:59:01', 1),
(204, 'En resa till det förlorade landet', 1, 5, 1, 291.68, 4, NULL, NULL, 1968, 'Big Publishing House', 1, 0, 1, '2025-05-21 12:59:01', 7),
(205, 'Best of the 80s', 1, 6, 5, 187.35, 1, NULL, NULL, 1978, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 12:59:01', 3),
(206, 'Sagan om det förlorade landet', 1, 3, 1, 169.69, 1, NULL, NULL, 1957, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 12:59:01', 6),
(207, 'Sagan om det förlorade landet', 1, 5, 1, 256.76, 4, NULL, NULL, 2024, 'Music Records Ltd.', 1, 1, 0, '2025-05-21 12:59:01', 4),
(208, 'Mysteriet i rymdens djup', 1, 7, 7, 204.64, 2, NULL, NULL, 1989, 'Music Records Ltd.', 1, 1, 0, '2025-05-21 12:59:01', 2),
(209, 'Filmen: den förbjudna skogen', 1, 7, 7, 123.63, 1, NULL, NULL, 1956, 'Film Studio Ent.', 1, 1, 0, '2025-05-21 12:59:01', 6),
(210, 'Mysteriet i den gamla staden', 1, 7, 7, 271.74, 1, NULL, NULL, 1963, 'Independent Books', 1, 1, 1, '2025-05-21 12:59:01', 5),
(211, 'Trollkarlens glömda hemligheter', 1, 1, 12, 267.27, 4, NULL, NULL, 1994, 'Music Records Ltd.', 1, 1, 1, '2025-05-21 12:59:01', 4),
(212, 'Best of Nature', 1, 1, 13, 215.43, 3, NULL, NULL, 1996, 'Big Publishing House', 1, 1, 0, '2025-05-21 12:59:01', 5),
(213, 'Äventyr med drömmarnas värld', 1, 1, 12, 171.06, 4, NULL, NULL, 1985, 'Independent Books', 1, 1, 0, '2025-05-21 12:59:01', 3),
(214, 'Äventyr Spel: De glömda världarna', 1, 1, 13, 174.44, 3, NULL, NULL, 1996, 'Big Publishing House', 1, 1, 0, '2025-05-21 12:59:01', 6),
(215, 'Greatest Hits: Jazz Classics', 1, 1, 12, 284.75, 4, NULL, NULL, 1991, 'Independent Books', 1, 1, 0, '2025-05-21 12:59:01', 2),
(216, 'The Album: the 80s', 1, 7, 7, 205.74, 4, NULL, NULL, 1994, 'Independent Books', 1, 1, 0, '2025-05-21 12:59:01', 3),
(217, 'Best of Dreams', 1, 1, 8, 167.67, 1, NULL, NULL, 2022, 'Comic Arts Inc.', 1, 1, 0, '2025-05-21 12:59:01', 1),
(218, 'Vintage frimärke', 1, 6, 6, 127.29, 1, NULL, NULL, 1980, 'Independent Books', 1, 0, 1, '2025-05-21 12:59:01', 2),
(219, 'Vintage Magasin: Mode Nr.19', 1, 6, 5, 261.22, 1, NULL, NULL, 1987, 'Big Publishing House', 1, 1, 1, '2025-05-21 12:59:01', 1),
(220, 'Sagan om det förlorade landet', 1, 1, 8, 29.12, 4, NULL, NULL, 1951, 'Small Press', 1, 0, 0, '2025-05-21 12:59:01', 8),
(221, 'Filmen: den förbjudna skogen', 1, 6, 5, 292.74, 2, NULL, NULL, 1954, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 12:59:01', 8),
(222, 'Vintage karta', 1, 7, 7, 266.24, 4, NULL, NULL, 1993, 'Big Publishing House', 1, 0, 0, '2025-05-21 12:59:01', 5),
(223, 'Filmen: den förbjudna skogen', 1, 1, 12, 24.33, 4, NULL, NULL, 2014, 'Comic Arts Inc.', 1, 1, 1, '2025-05-21 12:59:01', 8),
(224, 'Vintage docka', 1, 6, 5, 128.96, 4, NULL, NULL, 1961, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 12:59:01', 2),
(225, 'En resa till stjärnorna', 1, 6, 6, 114.18, 2, NULL, NULL, 1961, 'Small Press', 1, 0, 0, '2025-05-21 12:59:01', 6),
(226, 'Sounds of Rock Anthems', 1, 1, 12, 229.68, 2, NULL, NULL, 2003, 'Music Records Ltd.', 1, 0, 1, '2025-05-21 12:59:01', 1),
(227, 'Vintage Magasin: Historia Nr.51', 1, 1, 13, 45.13, 2, NULL, NULL, 1990, 'Small Press', 1, 0, 0, '2025-05-21 10:05:35', 6),
(228, 'Vintage frimärke', 1, 7, 7, 161.21, 3, NULL, NULL, 1987, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 10:05:35', 8),
(229, 'Äventyr i rymdens djup', 1, 1, 8, 134.56, 2, NULL, NULL, 1960, 'Small Press', 1, 0, 0, '2025-05-21 10:05:35', 8),
(230, 'Äventyr i rymdens djup', 1, 6, 9, 126.17, 4, NULL, NULL, 1991, 'Independent Books', 1, 0, 0, '2025-05-21 10:05:35', 2),
(231, 'Fantasy Spel: Kungarikets öde', 1, 4, 1, 128.07, 3, NULL, NULL, 1981, 'Independent Books', 1, 0, 0, '2025-05-21 10:05:35', 1),
(232, 'En resa till stjärnorna', 1, 1, 9, 12.07, 1, NULL, NULL, 1954, 'Small Press', 1, 0, 0, '2025-05-21 10:05:35', 6),
(233, 'Live at Dreams', 1, 1, 8, 128.11, 1, NULL, NULL, 1993, 'Big Publishing House', 1, 0, 0, '2025-05-21 10:05:35', 4),
(234, 'Vintage frimärke', 1, 1, 13, 133.44, 3, NULL, NULL, 1964, 'Small Press', 1, 0, 0, '2025-05-21 10:05:35', 3),
(235, 'Live at the 80s', 1, 1, 12, 247.48, 4, NULL, NULL, 2014, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 10:05:35', 1),
(236, 'The Album: Dreams', 1, 1, 13, 171.66, 3, NULL, NULL, 1982, 'Big Publishing House', 1, 0, 0, '2025-05-21 10:05:35', 6),
(237, 'Mysteriet i den gamla staden', 1, 1, 8, 150.29, 3, NULL, NULL, 2014, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 10:05:35', 4),
(238, 'Sällsynt Magasin: Mode Nr.76', 1, 1, 12, 76.86, 3, NULL, NULL, 1959, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 10:05:35', 5),
(239, 'Vintage docka', 1, 7, 7, 132.43, 3, NULL, NULL, 1959, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 10:05:35', 5),
(240, 'Live at Nature', 1, 7, 7, 59.30, 2, NULL, NULL, 1973, 'Big Publishing House', 1, 0, 0, '2025-05-21 10:05:35', 5),
(241, 'Klassikern: rymdens djup', 1, 6, 6, 76.99, 3, NULL, NULL, 1996, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 10:05:35', 1),
(242, 'Trollkarlens glömda hemligheter', 1, 6, 5, 289.76, 3, NULL, NULL, 1967, 'Big Publishing House', 1, 0, 0, '2025-05-21 10:05:35', 1),
(243, 'Den hemliga skogen', 1, 7, 7, 69.82, 1, NULL, NULL, 1952, 'Independent Books', 1, 0, 0, '2025-05-21 10:05:35', 4),
(244, 'Äventyr i den förbjudna skogen', 1, 1, 12, 60.90, 3, NULL, NULL, 1983, 'Big Publishing House', 1, 0, 0, '2025-05-21 10:05:35', 7),
(245, 'Sällsynt Magasin: Historia Nr.35', 1, 6, 9, 177.48, 4, NULL, NULL, 1964, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 10:05:35', 3),
(246, 'Äventyr Spel: De glömda världarna', 1, 6, 5, 76.93, 3, NULL, NULL, 2006, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 10:05:35', 1),
(247, 'Klassikern: rymdens djup', 1, 1, 9, 233.28, 1, NULL, NULL, 2012, 'Independent Books', 1, 0, 0, '2025-05-21 10:05:35', 5),
(248, 'Sällsynt Magasin: Vetenskap Nr.32', 1, 6, 6, 154.05, 4, NULL, NULL, 1990, 'Small Press', 1, 0, 0, '2025-05-21 10:05:35', 8),
(249, 'Vintage Magasin: Vetenskap Nr.64', 1, 7, 7, 243.98, 2, NULL, NULL, 1967, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 10:05:35', 8),
(250, 'Live at Nature', 1, 7, 7, 76.78, 3, NULL, NULL, 1973, 'Small Press', 1, 0, 0, '2025-05-21 10:05:35', 2),
(251, 'Collection: Rock Anthems', 1, 7, 7, 145.66, 3, NULL, NULL, 1960, 'Big Publishing House', 1, 0, 0, '2025-05-21 10:05:35', 2),
(252, 'Sällsynt Magasin: Mode Nr.8', 1, 1, 9, 78.07, 4, NULL, NULL, 1956, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 10:05:35', 2),
(253, 'Klassikern: den förbjudna skogen', 1, 1, 12, 243.14, 3, NULL, NULL, 2014, 'Big Publishing House', 1, 0, 0, '2025-05-21 10:05:35', 3),
(254, 'The Album: Nature', 1, 6, 5, 82.33, 4, NULL, NULL, 1963, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 10:05:35', 3),
(255, 'Best of Nature', 1, 1, 9, 169.15, 2, NULL, NULL, 1965, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 10:05:35', 8),
(256, 'Collection: the 80s', 1, 1, 1, 290.87, 2, NULL, NULL, 1981, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 10:05:35', 4),
(257, 'Äventyr i den förbjudna skogen', 1, 1, 9, 279.07, 4, NULL, NULL, 1953, 'Small Press', 1, 0, 0, '2025-05-21 10:05:35', 4),
(258, 'The Album: Nature', 1, 6, 9, 155.15, 3, NULL, NULL, 1979, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 10:05:35', 8),
(259, 'Äventyr Spel: Kungarikets öde', 1, 6, 6, 110.89, 2, NULL, NULL, 1986, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 10:05:35', 2),
(260, 'Live at Rock Anthems', 1, 1, 12, 94.50, 4, NULL, NULL, 1951, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 10:05:35', 4),
(261, 'Fantasy Spel: De glömda världarna', 1, 6, 5, 38.57, 2, NULL, NULL, 1958, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 10:05:35', 4),
(262, 'Fantasy Spel: Kungarikets öde', 1, 7, 7, 299.20, 3, NULL, NULL, 1992, 'Small Press', 1, 0, 0, '2025-05-21 10:05:35', 8),
(263, 'Sagan om tidens gång', 1, 1, 1, 231.27, 3, NULL, NULL, 1992, 'Big Publishing House', 1, 0, 0, '2025-05-21 10:05:35', 2),
(264, 'Sounds of Rock Anthems', 1, 1, 12, 282.26, 4, NULL, NULL, 1972, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 10:05:35', 1),
(265, 'Fantasy Spel: Kungarikets öde', 1, 7, 7, 69.81, 1, NULL, NULL, 1987, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 10:05:35', 8),
(266, 'Collection: Nature', 1, 1, 12, 58.94, 1, NULL, NULL, 2020, 'Independent Books', 1, 0, 0, '2025-05-21 10:05:35', 3),
(267, 'Collection: Rock Anthems', 1, 6, 6, 67.57, 2, NULL, NULL, 1991, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 10:05:35', 4),
(268, 'Vargens stjärnorna', 1, 6, 6, 195.17, 2, NULL, NULL, 1967, 'Big Publishing House', 1, 0, 0, '2025-05-21 10:05:35', 6),
(269, 'Vargens det förlorade landet', 1, 7, 7, 227.31, 1, NULL, NULL, 1975, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 10:05:35', 3),
(270, 'Greatest Hits: Tomorrow', 1, 7, 7, 93.72, 1, NULL, NULL, 1957, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 10:05:35', 5),
(271, 'Best of Jazz Classics', 1, 7, 7, 192.74, 2, NULL, NULL, 2000, 'Big Publishing House', 1, 0, 0, '2025-05-21 10:05:35', 1),
(272, 'Äventyr i den förbjudna skogen', 1, 1, 8, 35.48, 1, NULL, NULL, 1968, 'Independent Books', 1, 0, 0, '2025-05-21 10:05:35', 1),
(273, 'Trollkarlens glömda hemligheter', 1, 1, 1, 161.76, 1, NULL, NULL, 1968, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 10:05:35', 1),
(274, 'Greatest Hits: Nature', 1, 1, 8, 289.87, 4, NULL, NULL, 1987, 'Independent Books', 1, 0, 0, '2025-05-21 10:05:35', 6),
(275, 'Vintage mynt', 1, 1, 13, 46.08, 3, NULL, NULL, 1995, 'Small Press', 1, 0, 0, '2025-05-21 10:05:35', 1),
(276, 'Äventyr Spel: De glömda världarna', 1, 6, 6, 285.35, 1, NULL, NULL, 1992, 'Small Press', 1, 0, 0, '2025-05-21 10:05:35', 1),
(277, 'Fantasy Spel: Kungarikets öde', 1, 1, 12, 128.07, 3, NULL, NULL, 1981, 'Independent Books', 1, 0, 0, '2025-05-21 13:07:14', 1),
(278, 'En resa till stjärnorna', 1, 1, 9, 12.07, 1, NULL, NULL, 1954, 'Small Press', 1, 0, 0, '2025-05-21 13:07:14', 6),
(279, 'Live at Dreams', 1, 1, 8, 128.11, 1, NULL, NULL, 1993, 'Big Publishing House', 1, 0, 0, '2025-05-21 13:07:14', 4),
(280, 'Vintage frimärke', 1, 1, 13, 133.44, 3, NULL, NULL, 1964, 'Small Press', 1, 0, 0, '2025-05-21 13:07:14', 3),
(281, 'Live at the 80s', 1, 1, 12, 247.48, 4, NULL, NULL, 2014, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 13:07:14', 1),
(282, 'The Album: Dreams', 1, 1, 13, 171.66, 3, NULL, NULL, 1982, 'Big Publishing House', 1, 0, 0, '2025-05-21 13:07:14', 6),
(283, 'Mysteriet i den gamla staden', 1, 1, 8, 150.29, 3, NULL, NULL, 2014, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 13:07:14', 4),
(284, 'Sällsynt Magasin: Mode Nr.76', 1, 1, 12, 76.86, 3, NULL, NULL, 1959, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 13:07:14', 5),
(285, 'Vintage docka', 1, 7, 7, 132.43, 3, NULL, NULL, 1959, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 13:07:14', 5),
(286, 'Live at Nature', 1, 7, 7, 59.30, 2, NULL, NULL, 1973, 'Big Publishing House', 1, 0, 0, '2025-05-21 13:07:14', 5),
(287, 'Klassikern: rymdens djup', 1, 6, 6, 76.99, 3, NULL, NULL, 1996, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 13:07:14', 1),
(288, 'Trollkarlens glömda hemligheter', 1, 6, 5, 289.76, 3, NULL, NULL, 1967, 'Big Publishing House', 1, 0, 0, '2025-05-21 13:07:14', 1),
(289, 'Den hemliga skogen', 1, 7, 7, 69.82, 1, NULL, NULL, 1952, 'Independent Books', 1, 0, 0, '2025-05-21 13:07:14', 4),
(290, 'Äventyr i den förbjudna skogen', 1, 1, 12, 60.90, 3, NULL, NULL, 1983, 'Big Publishing House', 1, 0, 0, '2025-05-21 13:07:14', 7),
(291, 'Sällsynt Magasin: Historia Nr.35', 1, 6, 9, 177.48, 4, NULL, NULL, 1964, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 13:07:14', 3),
(292, 'Äventyr Spel: De glömda världarna', 1, 6, 5, 76.93, 3, NULL, NULL, 2006, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 13:07:14', 1),
(293, 'Klassikern: rymdens djup', 1, 1, 9, 233.28, 1, NULL, NULL, 2012, 'Independent Books', 1, 0, 0, '2025-05-21 13:07:14', 5),
(294, 'Sällsynt Magasin: Vetenskap Nr.32', 1, 6, 6, 154.05, 4, NULL, NULL, 1990, 'Small Press', 1, 0, 0, '2025-05-21 13:07:14', 8),
(295, 'Vintage Magasin: Vetenskap Nr.64', 1, 7, 7, 243.98, 2, NULL, NULL, 1967, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 13:07:14', 8),
(296, 'Live at Nature', 1, 7, 7, 76.78, 3, NULL, NULL, 1973, 'Small Press', 1, 0, 0, '2025-05-21 13:07:14', 2),
(297, 'Collection: Rock Anthems', 1, 7, 7, 145.66, 3, NULL, NULL, 1960, 'Big Publishing House', 1, 0, 0, '2025-05-21 13:07:14', 2),
(298, 'Sällsynt Magasin: Mode Nr.8', 1, 1, 9, 78.07, 4, NULL, NULL, 1956, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 13:07:14', 2),
(299, 'Klassikern: den förbjudna skogen', 1, 1, 12, 243.14, 3, NULL, NULL, 2014, 'Big Publishing House', 1, 0, 0, '2025-05-21 13:07:14', 3),
(300, 'The Album: Nature', 1, 6, 5, 82.33, 4, NULL, NULL, 1963, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 13:07:14', 3),
(301, 'Best of Nature', 1, 1, 9, 169.15, 2, NULL, NULL, 1965, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 13:07:14', 8),
(302, 'Collection: the 80s', 1, 1, 1, 290.87, 2, NULL, NULL, 1981, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 13:07:14', 4),
(303, 'Äventyr i den förbjudna skogen', 1, 1, 9, 279.07, 4, NULL, NULL, 1953, 'Small Press', 1, 0, 0, '2025-05-21 13:07:14', 4),
(304, 'The Album: Nature', 1, 6, 9, 155.15, 3, NULL, NULL, 1979, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 13:07:14', 8),
(305, 'Äventyr Spel: Kungarikets öde', 1, 6, 6, 110.89, 2, NULL, NULL, 1986, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 13:07:14', 2),
(306, 'Live at Rock Anthems', 1, 1, 12, 94.50, 4, NULL, NULL, 1951, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 13:07:14', 4),
(307, 'Fantasy Spel: De glömda världarna', 1, 6, 5, 38.57, 2, NULL, NULL, 1958, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 13:07:14', 4),
(308, 'Fantasy Spel: Kungarikets öde', 1, 7, 7, 299.20, 3, NULL, NULL, 1992, 'Small Press', 1, 0, 0, '2025-05-21 13:07:14', 8),
(309, 'Sagan om tidens gång', 1, 1, 1, 231.27, 3, NULL, NULL, 1992, 'Big Publishing House', 1, 0, 0, '2025-05-21 13:07:14', 2),
(310, 'Sounds of Rock Anthems', 1, 1, 12, 282.26, 4, NULL, NULL, 1972, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 13:07:14', 1),
(311, 'Fantasy Spel: Kungarikets öde', 1, 7, 7, 69.81, 1, NULL, NULL, 1987, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 13:07:14', 8),
(312, 'Collection: Nature', 1, 1, 12, 58.94, 1, NULL, NULL, 2020, 'Independent Books', 1, 0, 0, '2025-05-21 13:07:14', 3),
(313, 'Collection: Rock Anthems', 1, 6, 6, 67.57, 2, NULL, NULL, 1991, 'Music Records Ltd.', 1, 0, 0, '2025-05-21 13:07:14', 4),
(314, 'Vargens stjärnorna', 1, 6, 6, 195.17, 2, NULL, NULL, 1967, 'Big Publishing House', 1, 0, 0, '2025-05-21 13:07:14', 6),
(315, 'Vargens det förlorade landet', 1, 7, 7, 227.31, 1, NULL, NULL, 1975, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 13:07:14', 3),
(316, 'Greatest Hits: Tomorrow', 1, 7, 7, 93.72, 1, NULL, NULL, 1957, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 13:07:14', 5),
(317, 'Best of Jazz Classics', 1, 7, 7, 192.74, 2, NULL, NULL, 2000, 'Big Publishing House', 1, 0, 0, '2025-05-21 13:07:14', 1),
(318, 'Äventyr i den förbjudna skogen', 1, 1, 8, 35.48, 1, NULL, NULL, 1968, 'Independent Books', 1, 0, 0, '2025-05-21 13:07:14', 1),
(319, 'Trollkarlens glömda hemligheter', 1, 1, 1, 161.76, 1, NULL, NULL, 1968, 'Comic Arts Inc.', 1, 0, 0, '2025-05-21 13:07:14', 1),
(320, 'Greatest Hits: Nature', 1, 1, 8, 289.87, 4, NULL, NULL, 1987, 'Independent Books', 1, 0, 0, '2025-05-21 13:07:14', 6),
(321, 'Vintage mynt', 1, 1, 13, 46.08, 3, NULL, NULL, 1995, 'Small Press', 1, 0, 0, '2025-05-21 13:07:14', 1),
(322, 'Äventyr Spel: De glömda världarna', 1, 6, 6, 285.35, 1, NULL, NULL, 1992, 'Small Press', 1, 0, 0, '2025-05-21 13:07:14', 1),
(323, 'Den hemliga tiden', 1, 5, 1, 16.32, 2, NULL, NULL, 1993, 'Big Publishing House', 1, 0, 0, '2025-05-21 13:07:14', 3),
(324, 'Mysteriet på det förlorade landet', 1, 4, 1, 119.53, 3, NULL, NULL, 1991, 'Film Studio Ent.', 1, 0, 0, '2025-05-21 13:07:14', 4),
(325, 'Äventyr med kapitel 1', 1, 1, 8, 22.18, 4, NULL, NULL, 1957, 'Small Press', 1, 0, 0, '2025-05-21 13:07:14', 2),
(326, 'Vintage karta', 1, 1, 9, 137.95, 3, NULL, NULL, 1957, 'Independent Books', 1, 0, 0, '2025-05-21 13:07:14', 6);

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
(33, 39, 36),
(188, 46, 17),
(187, 46, 21),
(189, 47, 1),
(190, 47, 13),
(191, 48, 16),
(192, 48, 20),
(193, 49, 13),
(194, 49, 23),
(195, 50, 36),
(196, 51, 14),
(197, 51, 15),
(199, 52, 24),
(198, 52, 25),
(200, 53, 3),
(201, 53, 20),
(202, 54, 26),
(203, 54, 30),
(205, 55, 27),
(204, 55, 30),
(207, 56, 10),
(206, 56, 17),
(209, 57, 28),
(208, 57, 30),
(211, 58, 1),
(210, 58, 20),
(213, 59, 35),
(212, 59, 36),
(215, 60, 31),
(214, 60, 33),
(217, 61, 26),
(216, 61, 28),
(218, 62, 34),
(219, 62, 36),
(220, 63, 34),
(221, 63, 36),
(222, 64, 27),
(223, 64, 30),
(224, 65, 34),
(225, 65, 35),
(227, 66, 1),
(226, 66, 21),
(228, 67, 35),
(230, 68, 35),
(229, 68, 36),
(231, 69, 35),
(233, 70, 34),
(232, 70, 36),
(234, 71, 1),
(235, 71, 14),
(236, 72, 35),
(237, 72, 37),
(238, 73, 10),
(239, 73, 20),
(240, 74, 37),
(241, 75, 1),
(242, 75, 14),
(244, 76, 12),
(243, 76, 24),
(246, 77, 35),
(245, 77, 36),
(247, 78, 11),
(248, 78, 25),
(249, 79, 16),
(250, 79, 17),
(251, 80, 34),
(252, 80, 37),
(253, 81, 29),
(255, 82, 9),
(254, 82, 16),
(256, 83, 35),
(257, 83, 37),
(259, 84, 17),
(258, 84, 18),
(260, 85, 17),
(261, 85, 25),
(262, 86, 11),
(263, 86, 24),
(265, 87, 34),
(264, 87, 35),
(266, 88, 32),
(267, 89, 18),
(268, 89, 21),
(269, 90, 11),
(270, 90, 12),
(272, 91, 31),
(271, 91, 33),
(273, 92, 11),
(274, 92, 23),
(275, 93, 21),
(276, 94, 19),
(278, 95, 15),
(277, 95, 16),
(279, 96, 18),
(280, 96, 25),
(282, 97, 19),
(281, 97, 20),
(283, 98, 27),
(284, 98, 30),
(285, 99, 34),
(286, 99, 37),
(287, 100, 10),
(288, 100, 15),
(289, 101, 35),
(290, 101, 37),
(291, 102, 12),
(292, 102, 23),
(294, 103, 34),
(293, 103, 36),
(295, 104, 34),
(296, 104, 35),
(297, 105, 14),
(298, 105, 16),
(300, 106, 24),
(299, 106, 25),
(302, 107, 34),
(301, 107, 37),
(303, 108, 10),
(304, 108, 12),
(306, 109, 26),
(305, 109, 32),
(307, 110, 16),
(308, 110, 19),
(310, 111, 20),
(309, 111, 23),
(311, 112, 35),
(312, 112, 36),
(313, 113, 35),
(314, 113, 37),
(316, 114, 12),
(315, 114, 25),
(317, 115, 34),
(318, 115, 37),
(320, 116, 9),
(319, 116, 10),
(321, 117, 26),
(322, 117, 33),
(324, 118, 36),
(323, 118, 37),
(325, 119, 22),
(327, 120, 17),
(326, 120, 24),
(329, 121, 34),
(328, 121, 36),
(331, 122, 35),
(330, 122, 36),
(332, 123, 34),
(333, 123, 35),
(334, 124, 18),
(335, 124, 24),
(337, 125, 16),
(336, 125, 23),
(338, 126, 10),
(339, 126, 19),
(341, 127, 14),
(340, 127, 16),
(342, 128, 12),
(343, 128, 13),
(345, 129, 21),
(344, 129, 30),
(347, 130, 3),
(346, 130, 31),
(349, 131, 28),
(348, 131, 29),
(350, 132, 4),
(351, 132, 10),
(352, 133, 7),
(353, 133, 18),
(354, 134, 20),
(356, 135, 14),
(355, 135, 16),
(357, 136, 36),
(358, 137, 34),
(359, 138, 34),
(361, 139, 12),
(360, 139, 13),
(362, 140, 28),
(364, 141, 14),
(363, 141, 17),
(365, 142, 3),
(366, 142, 14),
(367, 143, 15),
(369, 144, 11),
(368, 144, 19),
(370, 145, 3),
(371, 145, 31),
(373, 146, 18),
(372, 146, 25),
(374, 147, 35),
(375, 148, 2),
(376, 149, 10),
(377, 150, 26),
(379, 151, 35),
(378, 151, 36),
(380, 152, 10),
(381, 153, 9),
(382, 154, 1),
(383, 154, 18),
(384, 155, 33),
(385, 156, 11),
(386, 156, 13),
(387, 157, 12),
(388, 158, 35),
(389, 158, 37),
(390, 159, 34),
(391, 160, 34),
(392, 161, 22),
(393, 162, 7),
(394, 163, 13),
(395, 164, 22),
(396, 165, 7),
(397, 166, 4),
(398, 167, 26),
(399, 168, 30),
(400, 169, 21),
(401, 170, 33),
(402, 171, 14),
(403, 171, 25),
(405, 172, 17),
(404, 172, 27),
(406, 173, 31),
(408, 174, 30),
(407, 174, 37),
(409, 175, 13),
(410, 175, 14),
(411, 176, 12),
(412, 176, 32),
(414, 177, 14),
(413, 177, 16),
(415, 178, 12),
(416, 178, 13),
(418, 179, 21),
(417, 179, 30),
(420, 180, 3),
(419, 180, 31),
(422, 181, 28),
(421, 181, 29),
(423, 182, 4),
(424, 182, 10),
(425, 183, 7),
(426, 183, 18),
(427, 184, 20),
(429, 185, 14),
(428, 185, 16),
(430, 186, 36),
(431, 187, 34),
(432, 188, 34),
(434, 189, 12),
(433, 189, 13),
(435, 190, 28),
(437, 191, 14),
(436, 191, 17),
(438, 192, 3),
(439, 192, 14),
(440, 193, 15),
(442, 194, 11),
(441, 194, 19),
(443, 195, 3),
(444, 195, 31),
(446, 196, 18),
(445, 196, 25),
(447, 197, 35),
(448, 198, 2),
(449, 199, 10),
(450, 200, 26),
(452, 201, 35),
(451, 201, 36),
(453, 202, 10),
(454, 203, 9),
(455, 204, 1),
(456, 204, 18),
(457, 205, 33),
(458, 206, 11),
(459, 206, 13),
(460, 207, 12),
(461, 208, 35),
(462, 208, 37),
(463, 209, 34),
(464, 210, 34),
(465, 211, 22),
(466, 212, 7),
(467, 213, 13),
(468, 214, 22),
(469, 215, 7),
(470, 216, 4),
(471, 217, 26),
(472, 218, 30),
(473, 219, 21),
(474, 220, 33),
(475, 221, 14),
(476, 221, 25),
(478, 222, 17),
(477, 222, 27),
(479, 223, 31),
(481, 224, 30),
(480, 224, 37),
(482, 225, 13),
(483, 225, 14),
(484, 226, 12),
(485, 226, 32),
(486, 227, 18),
(487, 228, 15),
(488, 229, 6),
(489, 229, 23),
(490, 230, 30),
(491, 231, 34),
(493, 232, 27),
(492, 232, 35),
(494, 233, 22),
(495, 233, 23),
(496, 234, 9),
(497, 235, 17),
(499, 236, 17),
(498, 236, 34),
(500, 237, 16),
(501, 237, 29),
(502, 238, 27),
(503, 239, 23),
(504, 240, 7),
(505, 240, 35),
(507, 241, 12),
(506, 241, 33),
(509, 242, 8),
(508, 242, 23),
(511, 243, 13),
(510, 243, 18),
(512, 244, 8),
(513, 245, 9),
(515, 246, 11),
(514, 246, 22),
(516, 247, 19),
(517, 247, 22),
(519, 248, 17),
(518, 248, 37),
(520, 249, 22),
(521, 249, 32),
(522, 250, 17),
(523, 250, 29),
(524, 251, 34),
(526, 252, 5),
(525, 252, 33),
(527, 253, 29),
(528, 254, 4),
(529, 254, 30),
(530, 255, 8),
(531, 256, 25),
(533, 257, 1),
(532, 257, 32),
(534, 258, 11),
(535, 259, 21),
(536, 260, 20),
(537, 261, 10),
(538, 262, 37),
(539, 263, 4),
(540, 264, 25),
(541, 265, 1),
(542, 265, 31),
(543, 266, 19),
(544, 267, 16),
(546, 268, 31),
(545, 268, 37),
(547, 269, 22),
(548, 269, 31),
(549, 270, 32),
(550, 270, 36),
(551, 271, 15),
(552, 271, 33),
(553, 272, 11),
(554, 273, 32),
(556, 274, 16),
(555, 274, 35),
(557, 275, 23),
(558, 276, 9),
(559, 276, 13),
(560, 277, 34),
(562, 278, 27),
(561, 278, 35),
(563, 279, 22),
(564, 279, 23),
(565, 280, 9),
(566, 281, 17),
(568, 282, 17),
(567, 282, 34),
(569, 283, 16),
(570, 283, 29),
(571, 284, 27),
(572, 285, 23),
(573, 286, 7),
(574, 286, 35),
(576, 287, 12),
(575, 287, 33),
(578, 288, 8),
(577, 288, 23),
(580, 289, 13),
(579, 289, 18),
(581, 290, 8),
(582, 291, 9),
(584, 292, 11),
(583, 292, 22),
(585, 293, 19),
(586, 293, 22),
(588, 294, 17),
(587, 294, 37),
(589, 295, 22),
(590, 295, 32),
(591, 296, 17),
(592, 296, 29),
(593, 297, 34),
(595, 298, 5),
(594, 298, 33),
(596, 299, 29),
(597, 300, 4),
(598, 300, 30),
(599, 301, 8),
(600, 302, 25),
(602, 303, 1),
(601, 303, 32),
(603, 304, 11),
(604, 305, 21),
(605, 306, 20),
(606, 307, 10),
(607, 308, 37),
(608, 309, 4),
(609, 310, 25),
(610, 311, 1),
(611, 311, 31),
(612, 312, 19),
(613, 313, 16),
(615, 314, 31),
(614, 314, 37),
(616, 315, 22),
(617, 315, 31),
(618, 316, 32),
(619, 316, 36),
(620, 317, 15),
(621, 317, 33),
(622, 318, 11),
(623, 319, 32),
(625, 320, 16),
(624, 320, 35),
(626, 321, 23),
(627, 322, 9),
(628, 322, 13),
(630, 323, 14),
(629, 323, 16),
(631, 324, 12),
(632, 324, 13),
(634, 325, 21),
(633, 325, 30),
(636, 326, 3),
(635, 326, 31);

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
(47, 42, 10),
(211, 46, 5),
(210, 46, 11),
(212, 47, 1),
(213, 47, 10),
(214, 48, 1),
(215, 48, 10),
(216, 49, 1),
(217, 49, 10),
(218, 50, 1),
(219, 50, 10),
(220, 51, 1),
(221, 51, 10),
(222, 52, 1),
(223, 52, 10),
(224, 53, 1),
(225, 53, 10),
(227, 54, 7),
(226, 54, 8),
(228, 55, 7),
(229, 55, 8),
(230, 56, 1),
(231, 56, 10),
(232, 57, 7),
(233, 57, 8),
(234, 58, 1),
(235, 58, 10),
(236, 59, 1),
(237, 59, 10),
(239, 60, 7),
(238, 60, 8),
(241, 61, 8),
(240, 61, 9),
(243, 62, 1),
(242, 62, 10),
(244, 63, 1),
(245, 63, 10),
(246, 64, 7),
(247, 64, 8),
(249, 65, 1),
(248, 65, 10),
(251, 66, 1),
(250, 66, 10),
(252, 67, 1),
(253, 67, 10),
(255, 68, 1),
(254, 68, 10),
(256, 69, 1),
(257, 69, 10),
(259, 70, 1),
(258, 70, 10),
(260, 71, 1),
(261, 71, 10),
(263, 72, 1),
(262, 72, 10),
(265, 73, 1),
(264, 73, 10),
(267, 74, 1),
(266, 74, 10),
(268, 75, 1),
(269, 75, 10),
(271, 76, 10),
(270, 76, 11),
(272, 77, 1),
(273, 77, 10),
(274, 78, 1),
(275, 78, 10),
(277, 79, 1),
(276, 79, 10),
(279, 80, 1),
(278, 80, 10),
(280, 81, 7),
(281, 81, 8),
(283, 82, 1),
(282, 82, 10),
(284, 83, 1),
(285, 83, 10),
(286, 84, 1),
(287, 84, 10),
(289, 85, 1),
(288, 85, 10),
(291, 86, 1),
(290, 86, 10),
(293, 87, 1),
(292, 87, 10),
(294, 88, 7),
(295, 88, 8),
(296, 89, 1),
(297, 89, 10),
(299, 90, 1),
(298, 90, 10),
(300, 91, 7),
(301, 91, 8),
(303, 92, 1),
(302, 92, 10),
(305, 93, 3),
(304, 93, 11),
(306, 94, 1),
(307, 94, 10),
(309, 95, 1),
(308, 95, 10),
(311, 96, 1),
(310, 96, 10),
(312, 97, 1),
(313, 97, 10),
(314, 98, 7),
(315, 98, 8),
(317, 99, 1),
(316, 99, 10),
(318, 100, 1),
(319, 100, 10),
(320, 101, 1),
(321, 101, 10),
(323, 102, 1),
(322, 102, 10),
(325, 103, 1),
(324, 103, 10),
(327, 104, 1),
(326, 104, 10),
(328, 105, 1),
(329, 105, 10),
(330, 106, 1),
(331, 106, 10),
(333, 107, 1),
(332, 107, 10),
(334, 108, 1),
(335, 108, 10),
(336, 109, 7),
(337, 109, 8),
(339, 110, 1),
(338, 110, 10),
(340, 111, 1),
(341, 111, 10),
(342, 112, 1),
(343, 112, 10),
(345, 113, 1),
(344, 113, 10),
(347, 114, 1),
(346, 114, 10),
(348, 115, 1),
(349, 115, 10),
(351, 116, 1),
(350, 116, 10),
(352, 117, 7),
(353, 117, 8),
(355, 118, 1),
(354, 118, 10),
(357, 119, 3),
(356, 119, 11),
(359, 120, 1),
(358, 120, 10),
(361, 121, 1),
(360, 121, 10),
(363, 122, 1),
(362, 122, 10),
(364, 123, 1),
(365, 123, 10),
(366, 124, 1),
(367, 124, 10),
(368, 125, 1),
(369, 125, 10),
(371, 126, 1),
(370, 126, 10),
(372, 127, 1),
(373, 127, 5),
(374, 128, 1),
(375, 128, 10),
(377, 129, 1),
(376, 129, 10),
(379, 130, 1),
(378, 130, 11),
(380, 131, 8),
(381, 131, 9),
(383, 132, 7),
(382, 132, 11),
(385, 133, 1),
(384, 133, 10),
(386, 134, 11),
(387, 135, 1),
(388, 135, 5),
(389, 136, 1),
(390, 136, 10),
(391, 137, 1),
(392, 137, 10),
(393, 138, 1),
(394, 138, 10),
(396, 139, 7),
(395, 139, 11),
(397, 140, 8),
(398, 141, 11),
(400, 142, 3),
(399, 142, 5),
(401, 143, 1),
(403, 144, 3),
(402, 144, 6),
(405, 145, 1),
(404, 145, 11),
(406, 146, 8),
(407, 147, 7),
(408, 148, 10),
(409, 149, 10),
(410, 150, 7),
(411, 151, 1),
(412, 151, 10),
(413, 152, 10),
(414, 153, 1),
(415, 153, 3),
(416, 154, 1),
(417, 154, 10),
(418, 155, 7),
(419, 156, 1),
(420, 156, 3),
(421, 157, 1),
(422, 157, 10),
(423, 158, 1),
(424, 158, 10),
(425, 159, 1),
(426, 159, 10),
(427, 160, 1),
(428, 160, 10),
(429, 161, 6),
(430, 161, 7),
(431, 162, 8),
(432, 163, 9),
(433, 164, 4),
(434, 165, 10),
(435, 166, 1),
(436, 166, 10),
(438, 167, 1),
(437, 167, 11),
(439, 168, 7),
(440, 168, 9),
(441, 169, 9),
(442, 170, 11),
(443, 171, 9),
(444, 172, 1),
(445, 173, 7),
(446, 173, 8),
(447, 174, 9),
(448, 174, 11),
(449, 175, 7),
(450, 176, 4),
(451, 177, 1),
(452, 177, 5),
(453, 178, 1),
(454, 178, 10),
(456, 179, 1),
(455, 179, 10),
(458, 180, 1),
(457, 180, 11),
(459, 181, 8),
(460, 181, 9),
(462, 182, 7),
(461, 182, 11),
(464, 183, 1),
(463, 183, 10),
(465, 184, 11),
(466, 185, 1),
(467, 185, 5),
(468, 186, 1),
(469, 186, 10),
(470, 187, 1),
(471, 187, 10),
(472, 188, 1),
(473, 188, 10),
(475, 189, 7),
(474, 189, 11),
(476, 190, 8),
(477, 191, 11),
(479, 192, 3),
(478, 192, 5),
(480, 193, 1),
(482, 194, 3),
(481, 194, 6),
(484, 195, 1),
(483, 195, 11),
(485, 196, 8),
(486, 197, 7),
(487, 198, 10),
(488, 199, 10),
(489, 200, 7),
(490, 201, 1),
(491, 201, 10),
(492, 202, 10),
(493, 203, 1),
(494, 203, 3),
(495, 204, 1),
(496, 204, 10),
(497, 205, 7),
(498, 206, 1),
(499, 206, 3),
(500, 207, 1),
(501, 207, 10),
(502, 208, 1),
(503, 208, 10),
(504, 209, 1),
(505, 209, 10),
(506, 210, 1),
(507, 210, 10),
(508, 211, 6),
(509, 211, 7),
(510, 212, 8),
(511, 213, 9),
(512, 214, 4),
(513, 215, 10),
(514, 216, 1),
(515, 216, 10),
(517, 217, 1),
(516, 217, 11),
(518, 218, 7),
(519, 218, 9),
(520, 219, 9),
(521, 220, 11),
(522, 221, 9),
(523, 222, 1),
(524, 223, 7),
(525, 223, 8),
(526, 224, 9),
(527, 224, 11),
(528, 225, 7),
(529, 226, 4),
(530, 227, 7),
(531, 228, 1),
(532, 228, 11),
(534, 229, 1),
(533, 229, 10),
(535, 230, 3),
(536, 230, 11),
(537, 231, 5),
(538, 232, 4),
(539, 233, 11),
(540, 234, 4),
(541, 234, 7),
(542, 235, 5),
(544, 236, 7),
(543, 236, 8),
(545, 237, 11),
(546, 238, 11),
(547, 239, 1),
(548, 239, 10),
(549, 240, 1),
(550, 240, 11),
(552, 241, 7),
(551, 241, 9),
(554, 242, 8),
(553, 242, 9),
(555, 243, 10),
(556, 243, 11),
(557, 244, 6),
(558, 244, 8),
(560, 245, 3),
(559, 245, 6),
(561, 246, 8),
(563, 247, 3),
(562, 247, 10),
(564, 248, 9),
(565, 249, 1),
(566, 249, 11),
(567, 250, 11),
(569, 251, 1),
(568, 251, 10),
(570, 252, 5),
(571, 252, 10),
(573, 253, 5),
(572, 253, 9),
(574, 254, 11),
(575, 255, 1),
(576, 256, 4),
(577, 257, 8),
(578, 258, 6),
(580, 259, 9),
(579, 259, 11),
(582, 260, 1),
(581, 260, 11),
(583, 261, 7),
(584, 261, 9),
(585, 262, 11),
(586, 263, 4),
(587, 264, 9),
(588, 265, 10),
(589, 266, 4),
(590, 266, 8),
(591, 267, 11),
(592, 268, 7),
(593, 268, 8),
(594, 269, 1),
(595, 269, 10),
(597, 270, 1),
(596, 270, 11),
(598, 271, 10),
(599, 271, 11),
(600, 272, 10),
(602, 273, 3),
(601, 273, 10),
(603, 274, 11),
(605, 275, 4),
(604, 275, 7),
(606, 276, 9),
(608, 277, 1),
(607, 277, 11),
(609, 278, 4),
(610, 279, 11),
(611, 280, 4),
(612, 280, 7),
(613, 281, 5),
(615, 282, 7),
(614, 282, 8),
(616, 283, 11),
(617, 284, 11),
(618, 285, 1),
(619, 285, 10),
(620, 286, 1),
(621, 286, 11),
(623, 287, 7),
(622, 287, 9),
(625, 288, 8),
(624, 288, 9),
(626, 289, 10),
(627, 289, 11),
(628, 290, 6),
(629, 290, 8),
(631, 291, 3),
(630, 291, 6),
(632, 292, 8),
(634, 293, 3),
(633, 293, 10),
(635, 294, 9),
(636, 295, 1),
(637, 295, 11),
(638, 296, 11),
(640, 297, 1),
(639, 297, 10),
(641, 298, 5),
(642, 298, 10),
(644, 299, 5),
(643, 299, 9),
(645, 300, 11),
(646, 301, 1),
(647, 302, 4),
(648, 303, 8),
(649, 304, 6),
(651, 305, 9),
(650, 305, 11),
(653, 306, 1),
(652, 306, 11),
(654, 307, 7),
(655, 307, 9),
(656, 308, 11),
(657, 309, 4),
(658, 310, 9),
(659, 311, 10),
(660, 312, 4),
(661, 312, 8),
(662, 313, 11),
(663, 314, 7),
(664, 314, 8),
(665, 315, 1),
(666, 315, 10),
(668, 316, 1),
(667, 316, 11),
(669, 317, 10),
(670, 317, 11),
(671, 318, 10),
(673, 319, 3),
(672, 319, 10),
(674, 320, 11),
(676, 321, 4),
(675, 321, 7),
(677, 322, 9),
(678, 323, 1),
(679, 323, 5),
(680, 324, 1),
(681, 324, 10),
(683, 325, 1),
(682, 325, 10),
(685, 326, 1),
(684, 326, 11);

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
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

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
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=203;

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
  MODIFY `prod_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=327;

--
-- AUTO_INCREMENT for table `product_author`
--
ALTER TABLE `product_author`
  MODIFY `product_author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=637;

--
-- AUTO_INCREMENT for table `product_genre`
--
ALTER TABLE `product_genre`
  MODIFY `product_genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=686;

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
