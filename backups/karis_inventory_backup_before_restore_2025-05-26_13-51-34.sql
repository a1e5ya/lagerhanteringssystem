-- Database backup created by PHP on 2025-05-26 13:51:34
-- Database: 

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 
-- Table structure for table `author`
-- 

DROP TABLE IF EXISTS `author`;
CREATE TABLE `author` (
  `author_id` int NOT NULL AUTO_INCREMENT,
  `author_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `author`
-- 

INSERT INTO `author` (`author_id`, `author_name`) VALUES ('1', 'J.K. Rowling');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('2', 'Stephen King');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('3', 'Margaret Atwood');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('4', 'Neil Gaiman');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('5', 'Toni Morrison');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('6', 'Tove Jansson');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('7', 'Astrid Lindgren');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('8', 'Stieg Larsson');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('9', 'Karl Ove Knausgård');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('10', 'Jonas Jonasson');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('11', 'Jane Austen');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('12', 'Fyodor Dostojevskij');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('13', 'Leo Tolstoj');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('14', 'Virginia Woolf');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('15', 'Franz Kafka');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('16', 'Hilary Mantel');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('17', 'Ken Follett');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('18', 'Philippa Gregory');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('19', 'Yuval Noah Harari');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('20', 'Michelle Obama');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('21', 'Malcolm Gladwell');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('22', 'Rupi Kaur');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('23', 'Edith Södergran');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('24', 'Bo Carpelan');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('25', 'Tomas Tranströmer');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('26', 'Ludwig van Beethoven');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('27', 'Wolfgang Amadeus Mozart');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('28', 'Jean Sibelius');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('29', 'Johann Sebastian Bach');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('30', 'ABBA ');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('31', 'Adele ');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('32', 'Ed Sheeran');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('33', 'Björk ');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('34', 'Christopher Nolan');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('35', 'Ingmar Bergman');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('36', 'Steven Spielberg');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('37', 'Alfred Hitchcock');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('38', '-');

-- 
-- Table structure for table `category`
-- 

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_sv_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category_fi_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_name` (`category_sv_name`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `category`
-- 

INSERT INTO `category` (`category_id`, `category_sv_name`, `category_fi_name`) VALUES ('1', 'Bok', 'Kirja');
INSERT INTO `category` (`category_id`, `category_sv_name`, `category_fi_name`) VALUES ('5', 'CD', 'CD');
INSERT INTO `category` (`category_id`, `category_sv_name`, `category_fi_name`) VALUES ('6', 'Vinyl', 'Vinyyli');
INSERT INTO `category` (`category_id`, `category_sv_name`, `category_fi_name`) VALUES ('7', 'DVD', 'DVD');
INSERT INTO `category` (`category_id`, `category_sv_name`, `category_fi_name`) VALUES ('8', 'Serier', 'Sarjakuva');
INSERT INTO `category` (`category_id`, `category_sv_name`, `category_fi_name`) VALUES ('9', 'Samlarobjekt', 'Keräilyesine');
INSERT INTO `category` (`category_id`, `category_sv_name`, `category_fi_name`) VALUES ('12', 'Spel', 'Pelit');
INSERT INTO `category` (`category_id`, `category_sv_name`, `category_fi_name`) VALUES ('13', 'Tidningar', 'Lehdet');

-- 
-- Table structure for table `condition`
-- 

DROP TABLE IF EXISTS `condition`;
CREATE TABLE `condition` (
  `condition_id` int NOT NULL AUTO_INCREMENT,
  `condition_sv_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `condition_fi_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `condition_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`condition_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `condition`
-- 

INSERT INTO `condition` (`condition_id`, `condition_sv_name`, `condition_fi_name`, `condition_code`) VALUES ('1', 'Nyskick', 'Erinomainen', 'K-4');
INSERT INTO `condition` (`condition_id`, `condition_sv_name`, `condition_fi_name`, `condition_code`) VALUES ('2', 'Mycket bra', 'Erittäin hyvä', 'K-3');
INSERT INTO `condition` (`condition_id`, `condition_sv_name`, `condition_fi_name`, `condition_code`) VALUES ('3', 'Bra', 'Hyvä', 'K-2');
INSERT INTO `condition` (`condition_id`, `condition_sv_name`, `condition_fi_name`, `condition_code`) VALUES ('4', 'Acceptabelt', 'Hyväksyttävä', 'K-1');

-- 
-- Table structure for table `event_log`
-- 

DROP TABLE IF EXISTS `event_log`;
CREATE TABLE `event_log` (
  `event_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `event_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `event_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `event_timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `product_id` int DEFAULT NULL,
  PRIMARY KEY (`event_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `event_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `event_log_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`prod_id`)
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `event_log`
-- 

INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('1', NULL, 'create', 'Skapade produkt: Trollvinter', '2025-04-22 11:21:34', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('2', NULL, 'create', 'Skapade produkt: Muumipeikko ja pyrstötähti', '2025-04-22 11:21:34', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('3', NULL, 'update', 'Uppdaterade pris på: Harry Potter och De Vises Sten', '2025-04-22 11:21:34', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('4', NULL, 'create', 'Skapade produkt: Sibelius Symphony No. 2', '2025-04-22 11:21:34', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('5', NULL, 'login', 'Backdoor login used for admin', '2025-05-12 10:53:38', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('198', '1', 'logout', 'User logged out: admin', '2025-05-15 13:30:36', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('199', '1', 'login', 'Backdoor login used for admin', '2025-05-15 13:30:52', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('200', '1', 'logout', 'User logged out: admin', '2025-05-19 12:22:24', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('201', '1', 'login', 'Backdoor login used for admin', '2025-05-19 12:22:33', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('202', '1', 'logout', 'User logged out: admin', '2025-05-19 12:23:19', NULL);

-- 
-- Table structure for table `genre`
-- 

DROP TABLE IF EXISTS `genre`;
CREATE TABLE `genre` (
  `genre_id` int NOT NULL AUTO_INCREMENT,
  `genre_sv_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `genre_fi_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`genre_id`),
  UNIQUE KEY `genre_name` (`genre_sv_name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `genre`
-- 

INSERT INTO `genre` (`genre_id`, `genre_sv_name`, `genre_fi_name`) VALUES ('1', 'Romaner', 'Romaanit');
INSERT INTO `genre` (`genre_id`, `genre_sv_name`, `genre_fi_name`) VALUES ('3', 'Historia', 'Historia');
INSERT INTO `genre` (`genre_id`, `genre_sv_name`, `genre_fi_name`) VALUES ('4', 'Dikter', 'Runot');
INSERT INTO `genre` (`genre_id`, `genre_sv_name`, `genre_fi_name`) VALUES ('5', 'Biografi', 'Elämäkerta');
INSERT INTO `genre` (`genre_id`, `genre_sv_name`, `genre_fi_name`) VALUES ('6', 'Barnböcker', 'Lastenkirjat');
INSERT INTO `genre` (`genre_id`, `genre_sv_name`, `genre_fi_name`) VALUES ('7', 'Rock', 'Rock');
INSERT INTO `genre` (`genre_id`, `genre_sv_name`, `genre_fi_name`) VALUES ('8', 'Jazz', 'Jazz');
INSERT INTO `genre` (`genre_id`, `genre_sv_name`, `genre_fi_name`) VALUES ('9', 'Klassisk', 'Klassinen');
INSERT INTO `genre` (`genre_id`, `genre_sv_name`, `genre_fi_name`) VALUES ('10', 'Äventyr', 'Seikkailu');
INSERT INTO `genre` (`genre_id`, `genre_sv_name`, `genre_fi_name`) VALUES ('11', '-', '-');

-- 
-- Table structure for table `image`
-- 

DROP TABLE IF EXISTS `image`;
CREATE TABLE `image` (
  `image_id` int NOT NULL AUTO_INCREMENT,
  `prod_id` int DEFAULT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `image_uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`image_id`),
  UNIQUE KEY `image_path` (`image_path`),
  KEY `fk_image_product` (`prod_id`),
  CONSTRAINT `fk_image_product` FOREIGN KEY (`prod_id`) REFERENCES `product` (`prod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `image`
-- 


-- 
-- Table structure for table `language`
-- 

DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `language_id` int NOT NULL AUTO_INCREMENT,
  `language_sv_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `language_fi_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`language_id`),
  UNIQUE KEY `language_sv_name` (`language_sv_name`),
  UNIQUE KEY `language_fi_name` (`language_fi_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `language`
-- 

INSERT INTO `language` (`language_id`, `language_sv_name`, `language_fi_name`) VALUES ('1', 'Svenska', 'Ruotsi');
INSERT INTO `language` (`language_id`, `language_sv_name`, `language_fi_name`) VALUES ('2', 'Finska', 'Suomi');
INSERT INTO `language` (`language_id`, `language_sv_name`, `language_fi_name`) VALUES ('3', 'Engelska', 'Englanti');
INSERT INTO `language` (`language_id`, `language_sv_name`, `language_fi_name`) VALUES ('4', 'Norska', 'Norja');
INSERT INTO `language` (`language_id`, `language_sv_name`, `language_fi_name`) VALUES ('5', 'Tyska', 'Saksa');
INSERT INTO `language` (`language_id`, `language_sv_name`, `language_fi_name`) VALUES ('6', 'Ryska', 'Venäjä');
INSERT INTO `language` (`language_id`, `language_sv_name`, `language_fi_name`) VALUES ('7', 'Franska', 'Ranska');
INSERT INTO `language` (`language_id`, `language_sv_name`, `language_fi_name`) VALUES ('8', 'Spanska', 'Espanja');

-- 
-- Table structure for table `newsletter_subscriber`
-- 

DROP TABLE IF EXISTS `newsletter_subscriber`;
CREATE TABLE `newsletter_subscriber` (
  `subscriber_id` int NOT NULL AUTO_INCREMENT,
  `subscriber_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `subscriber_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subscribed_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `subscriber_is_active` tinyint(1) DEFAULT '1',
  `subscriber_language_pref` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'sv',
  PRIMARY KEY (`subscriber_id`),
  UNIQUE KEY `subscriber_email` (`subscriber_email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `newsletter_subscriber`
-- 

INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('1', 'johanna.karlsson@example.com', 'Johanna Karlsson', '2025-05-13 12:37:38', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('2', 'mikko.nieminen@example.fi', 'Mikko Nieminen', '2025-05-13 12:37:38', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('3', 'anna.lindholm@example.com', 'Anna Lindholm', '2025-05-13 12:37:38', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('4', 'erik.johansson@example.se', 'Erik Johansson', '2025-05-13 12:37:38', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('5', 'liisa.makinen@example.fi', 'Liisa Mäkinen', '2025-05-13 12:37:38', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('6', 'bengt.gustafsson@example.com', 'Bengt Gustafsson', '2025-05-13 12:37:38', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('7', 'maria.henriksson@example.se', NULL, '2025-05-13 12:37:38', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('8', 'juhani.korhonen@example.fi', NULL, '2025-05-13 12:37:38', '1', 'fi');

-- 
-- Table structure for table `product`
-- 

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `prod_id` int NOT NULL AUTO_INCREMENT,
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
  `language_id` int DEFAULT NULL,
  PRIMARY KEY (`prod_id`),
  KEY `shelf_id` (`shelf_id`),
  KEY `category_id` (`category_id`),
  KEY `condition_id` (`condition_id`),
  KEY `idx_product_status` (`status`),
  KEY `fk_product_language` (`language_id`),
  CONSTRAINT `fk_product_language` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`shelf_id`) REFERENCES `shelf` (`shelf_id`),
  CONSTRAINT `product_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`),
  CONSTRAINT `product_ibfk_3` FOREIGN KEY (`condition_id`) REFERENCES `condition` (`condition_id`),
  CONSTRAINT `product_ibfk_4` FOREIGN KEY (`status`) REFERENCES `status` (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=327 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `product`
-- 

INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('1', 'Harry Potter och De Vises Sten', '1', '5', '1', '24.95', '2', 'Första boken i Harry Potter-serien', 'Bra skick, populär bland yngre läsare', '1997', 'Tiden', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('2', 'Lysningen', '1', '1', '1', '19.95', '3', NULL, NULL, '1977', 'Bra Böcker', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('3', 'Tjänarinnans berättelse', '1', '1', '1', '22.50', '1', 'Dystopisk roman', NULL, '1985', 'Norstedts', '1', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('4', 'Amerikanska gudar', '1', '1', '1', NULL, '2', 'Fantasyroman', 'Köpt på bokauktion i Helsingfors', '2001', 'Bonnier Carlsen', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('5', 'Älskade', '1', '1', '1', NULL, '2', NULL, NULL, '1987', 'Trevi', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('6', 'Trollvinter', '1', '1', '1', '26.50', '1', 'Mumin-roman', 'En av våra bästsäljare', '1957', 'Schildts', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('7', 'Pippi Långstrump', '1', '5', '1', '18.95', '1', 'Barnklassiker', NULL, '1945', 'Rabén & Sjögren', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('8', 'Män som hatar kvinnor', '1', '1', '1', '23.75', '2', NULL, NULL, '2005', NULL, '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('9', 'Min kamp 1', '1', '1', '1', '29.95', '2', 'Självbiografisk roman', 'Kontroversiell titel men efterfrågad', '2009', 'Oktober', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('10', 'Hundraåringen som klev ut genom fönstret och försvann', '1', '1', '1', '22.95', '1', 'Komisk roman', NULL, '2009', 'Piratförlaget', '1', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('11', 'Sinuhe egyptiläinen', '1', '3', '1', '28.50', '2', NULL, NULL, '1945', 'WSOY', '0', '0', '0', '2025-05-13 12:37:38', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('12', 'Tuntematon sotilas', '1', '3', '1', '24.95', '3', 'Krigsskildring', 'Viktigt historiskt verk, flera på väntelista', '1954', 'WSOY', '0', '0', '0', '2025-05-13 12:37:38', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('13', 'Kalevala', '1', '3', '1', '32.00', '1', 'Finsk nationalepos', NULL, '1835', 'SKS', '0', '0', '1', '2025-05-13 12:37:38', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('14', 'Muumipappa ja meri', '1', '5', '1', NULL, '2', NULL, NULL, '1965', 'WSOY', '0', '0', '0', '2025-05-13 12:37:38', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('15', 'Stolthet och fördom', '1', '1', '1', '15.50', '2', 'Klassisk kärleksroman', 'Originalbindning, värdefull', '1813', NULL, '0', '0', '1', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('16', 'Brott och straff', '1', '1', '1', '20.25', '3', NULL, NULL, '1866', 'Norstedts', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('17', 'Krig och fred', '1', '1', '1', '32.95', '4', 'Episk roman', NULL, '1869', 'Norstedts', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('18', 'Fru Dalloway', '1', '1', '1', '17.95', '2', 'Modernistisk roman', 'Fina understrykningar med blyerts', NULL, 'Bonnier', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('19', 'Wolf Hall', '1', '1', '1', NULL, '1', NULL, NULL, '2009', 'Fourth Estate', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('20', 'Svärdet och spiran', '1', '1', '1', '28.50', '2', 'Medeltida historisk roman', 'Personligt ex från författaren', '1989', 'Bonnier', '1', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('21', 'Dikter', '1', '1', '1', '29.50', '2', 'Diktsamling', 'Sällsynt utgåva, rödkantad', '1916', 'Holger Schildts förlag', '0', '0', '1', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('22', 'Min själ var en stilla sjö', '1', '1', '1', '24.50', '1', NULL, NULL, '1954', 'Schildts', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('23', 'Sapiens: En kort historik över mänskligheten', '1', '3', '1', '28.95', '1', 'Mänsklighetens historia', NULL, '2011', 'Natur & Kultur', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('24', 'Min historia', '1', '3', '1', NULL, '1', NULL, NULL, '2018', 'Bokförlaget Forum', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('25', 'Skärgårdens båtar', '1', '4', '1', '45.00', '2', 'Maritim historia', 'Intressant för lokala båtentusiaster', '2005', 'Wahlström & Widstrand', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('26', 'Östersjöns fyrar', '1', '4', '1', '39.95', '1', NULL, NULL, '2012', 'Nautiska Förlaget', '1', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('27', 'Beethoven: De kompletta symfonierna', '1', '6', '5', '35.99', '1', NULL, NULL, '2003', 'Deutsche Grammophon', '0', '0', '0', '2025-05-13 12:37:38', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('28', 'Mozart: Pianokonserter', '1', '6', '5', NULL, '2', 'Urval av pianokonserter', 'Speciell inspelning, efterfrågad', '1999', 'Philips', '0', '0', '0', '2025-05-13 12:37:38', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('29', 'Sibelius: Symfonier nr 1-7', '1', '6', '5', '29.95', '1', 'Kompletta symfonier', NULL, '2001', 'BIS Records', '1', '0', '0', '2025-05-13 12:37:38', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('30', 'ABBA Gold: Greatest Hits', '1', '6', '5', '18.50', '2', 'ABBA-samling', 'Nära nyskick, original', '1992', 'Polar', '0', '0', '0', '2025-05-13 12:37:38', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('31', '25', '1', '6', '5', '15.95', '1', NULL, NULL, '2015', 'XL Recordings', '0', '0', '0', '2025-05-13 12:37:38', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('32', '÷ (Divide)', '1', '6', '5', '17.99', '1', NULL, NULL, '2017', 'Asylum Records', '0', '0', '0', '2025-05-13 12:37:38', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('33', 'Vespertine', '1', '6', '5', NULL, '2', 'Björk-album', 'Limiterad upplaga', '2001', 'One Little Indian', '0', '0', '0', '2025-05-13 12:37:38', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('34', 'Abbey Road', '1', '6', '6', '45.99', '2', NULL, NULL, '1969', 'Apple Records', '0', '0', '1', '2025-05-13 12:37:38', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('35', 'Thriller', '1', '6', '6', '39.95', '3', 'Michael Jackson-album', 'Originalpressning, samlarobjekt', '1982', 'Epic Records', '0', '0', '0', '2025-05-13 12:37:38', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('36', 'Waterloo', '1', '6', '6', '42.50', '2', NULL, NULL, '1974', 'Polar', '1', '0', '0', '2025-05-13 12:37:38', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('37', 'Inception', '1', '7', '7', '14.99', '1', NULL, NULL, '2010', 'Warner Bros.', '0', '0', '0', '2025-05-13 12:37:38', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('38', 'Det sjunde inseglet', '1', '7', '7', '22.50', '2', 'Klassisk svensk film', 'Restaurerad utgåva', '1957', 'Criterion Collection', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('39', 'Schindlers lista', '1', '7', '7', NULL, '2', 'Historiskt drama', NULL, '1993', 'Universal Pictures', '0', '0', '0', '2025-05-13 12:37:38', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('40', 'Watchmen', '1', '1', '8', '29.99', '1', NULL, NULL, '1986', 'DC Comics', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('41', 'Maus', '1', '1', '8', '24.95', '2', 'Grafisk roman', 'Prisbelönt och eftersökt', '1991', 'Bonnier Carlsen', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('42', 'Tintin: Den blå lotus', '1', '1', '8', '18.50', '3', NULL, NULL, '1936', NULL, '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('43', 'Första utgåvan Ulysses', '2', '1', '9', '2500.00', '4', 'Sällsynt första utgåva', 'Extremt sällsynt, har verifierats äkta', '1922', 'Sylvia Beach', '0', '0', '1', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('44', 'Limiterad vinyl-boxupplaga', '1', '6', '9', '199.95', '1', NULL, NULL, '2022', 'Rhino Records', '0', '0', '1', '2025-05-13 12:37:38', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('45', 'Antika bokstöd', '1', '1', '9', NULL, '2', 'Dekorativa bokstöd', NULL, '1930', '', '0', '0', '0', '2025-05-13 12:37:38', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('46', 'Trollkarlens skogen', '1', '3', '1', '290.49', '1', NULL, NULL, '1968', 'Music Records Ltd.', '1', '0', '1', '2025-05-21 12:45:49', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('47', 'Trollkarlens glömda hemligheter', '1', '5', '1', '107.03', '2', NULL, NULL, '1961', 'Independent Books', '1', '0', '1', '2025-05-21 12:45:49', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('48', 'Den hemliga tidens gång', '1', '3', '1', '237.58', '2', NULL, NULL, '1993', 'Big Publishing House', '1', '0', '1', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('49', 'Vargens stjärnorna', '1', '5', '1', '107.60', '4', NULL, NULL, '2021', 'Independent Books', '1', '0', '1', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('50', 'Äventyr med drömmarnas värld', '1', '1', '8', '298.54', '3', NULL, NULL, '1969', 'Comic Arts Inc.', '1', '0', '1', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('51', 'Vargens stenar', '1', '3', '1', '269.96', '2', NULL, NULL, '2005', 'Big Publishing House', '1', '0', '1', '2025-05-21 12:45:49', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('52', 'Vargens glömda hemligheter', '1', '5', '1', '246.36', '4', NULL, NULL, '1978', 'Small Press', '1', '0', '1', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('53', 'Trollkarlens stjärnorna', '1', '4', '1', '185.07', '3', NULL, NULL, '2005', 'Independent Books', '1', '0', '1', '2025-05-21 12:45:49', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('54', 'The Album: Jazz Classics', '1', '6', '5', '109.91', '3', NULL, NULL, '1971', 'Music Records Ltd.', '1', '0', '1', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('55', 'Sounds of Tomorrow', '1', '6', '6', '172.93', '2', NULL, NULL, '2011', 'Music Records Ltd.', '1', '0', '1', '2025-05-21 12:45:49', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('56', 'Sagan om det förlorade landet', '1', '5', '1', '266.38', '4', NULL, NULL, '2002', 'Big Publishing House', '1', '1', '0', '2025-05-21 12:45:49', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('57', 'Collection: Rock Anthems', '1', '6', '5', '155.06', '4', NULL, NULL, '1980', 'Music Records Ltd.', '1', '1', '0', '2025-05-21 12:45:49', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('58', 'Mysteriet på tidens gång', '1', '5', '1', '185.73', '1', NULL, NULL, '1968', 'Small Press', '1', '1', '0', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('59', 'Filmen: rymdens djup', '1', '7', '7', '240.23', '1', NULL, NULL, '2017', 'Film Studio Ent.', '1', '1', '0', '2025-05-21 12:45:49', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('60', 'Collection: Dreams', '1', '6', '6', '268.21', '2', NULL, NULL, '1985', 'Music Records Ltd.', '1', '1', '0', '2025-05-21 12:45:49', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('61', 'The Album: Jazz Classics', '1', '6', '6', '26.65', '3', NULL, NULL, '1981', 'Music Records Ltd.', '1', '1', '0', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('62', 'Filmen: rymdens djup', '1', '7', '7', '219.03', '2', NULL, NULL, '2003', 'Film Studio Ent.', '1', '1', '0', '2025-05-21 12:45:49', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('63', 'Filmen: rymdens djup', '1', '7', '7', '230.13', '1', NULL, NULL, '2021', 'Film Studio Ent.', '1', '1', '0', '2025-05-21 12:45:49', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('64', 'Collection: Rock Anthems', '1', '6', '5', '195.40', '2', NULL, NULL, '1993', 'Music Records Ltd.', '1', '1', '0', '2025-05-21 12:45:49', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('65', 'Äventyr i tid och rum', '1', '7', '7', '102.39', '1', NULL, NULL, '1967', 'Film Studio Ent.', '1', '1', '0', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('66', 'Mysteriet på tidens gång', '1', '1', '1', '290.99', '1', NULL, NULL, '2006', 'Independent Books', '0', '0', '0', '2025-05-21 12:45:49', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('67', 'Filmen: den gamla staden', '1', '7', '7', '147.23', '1', NULL, NULL, '1982', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('68', 'Filmen: rymdens djup', '1', '7', '7', '256.77', '1', NULL, NULL, '1985', 'Film Studio Ent.', '0', '1', '0', '2025-05-21 12:45:49', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('69', 'Äventyr i tid och rum', '1', '7', '7', '172.93', '3', NULL, NULL, '2024', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('70', 'Äventyr i tid och rum', '1', '7', '7', '258.85', '3', NULL, NULL, '1964', 'Film Studio Ent.', '0', '1', '0', '2025-05-21 12:45:49', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('71', 'Den hemliga det förlorade landet', '1', '5', '1', '100.86', '1', NULL, NULL, '1978', 'Small Press', '1', '0', '0', '2025-05-21 12:45:49', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('72', 'Äventyr i den gamla staden', '1', '7', '7', '175.76', '4', NULL, NULL, '1962', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('73', 'En resa till tidens gång', '1', '5', '1', '173.34', '1', NULL, NULL, '1993', 'Big Publishing House', '0', '0', '0', '2025-05-21 12:45:49', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('74', 'Mysteriet i rymdens djup', '1', '7', '7', '103.11', '4', NULL, NULL, '1988', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('75', 'Mysteriet på skogen', '1', '5', '1', '237.95', '3', NULL, NULL, '2001', 'Independent Books', '0', '1', '0', '2025-05-21 12:45:49', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('76', 'Vintage leksak', '1', '6', '9', '219.04', '3', NULL, NULL, '2017', 'Small Press', '0', '0', '0', '2025-05-21 12:45:49', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('77', 'Filmen: tid och rum', '1', '7', '7', '150.96', '2', NULL, NULL, '2011', 'Film Studio Ent.', '0', '1', '0', '2025-05-21 12:45:49', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('78', 'Den hemliga glömda hemligheter', '1', '3', '1', '179.91', '3', NULL, NULL, '1987', 'Big Publishing House', '0', '0', '0', '2025-05-21 12:45:49', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('79', 'Sagan om det förlorade landet', '1', '5', '1', '137.90', '1', NULL, NULL, '1998', 'Big Publishing House', '0', '0', '0', '2025-05-21 12:45:49', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('80', 'Filmen: den gamla staden', '1', '7', '7', '26.54', '4', NULL, NULL, '1987', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('81', 'Greatest Hits: Nature', '1', '6', '5', '114.77', '4', NULL, NULL, '1968', 'Music Records Ltd.', '0', '0', '0', '2025-05-21 12:45:49', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('82', 'Vargens glömda hemligheter', '1', '4', '1', '271.74', '1', NULL, NULL, '1997', 'Big Publishing House', '0', '0', '0', '2025-05-21 12:45:49', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('83', 'Filmen: den förbjudna skogen', '1', '7', '7', '153.25', '3', NULL, NULL, '1974', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('84', 'Vargens stjärnorna', '1', '1', '1', '102.39', '1', NULL, NULL, '1965', 'Small Press', '0', '0', '0', '2025-05-21 12:45:49', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('85', 'Vargens glömda hemligheter', '1', '1', '1', '273.74', '4', NULL, NULL, '2012', 'Big Publishing House', '0', '0', '0', '2025-05-21 12:45:49', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('86', 'Mysteriet på det förlorade landet', '1', '1', '1', '185.04', '2', NULL, NULL, '1982', 'Big Publishing House', '0', '0', '0', '2025-05-21 12:45:49', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('87', 'Filmen: tid och rum', '1', '7', '7', '278.41', '1', NULL, NULL, '1982', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('88', 'Greatest Hits: Dreams', '1', '6', '6', '172.96', '2', NULL, NULL, '1960', 'Music Records Ltd.', '0', '0', '0', '2025-05-21 12:45:49', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('89', 'Trollkarlens glömda hemligheter', '1', '1', '1', '126.96', '4', NULL, NULL, '1980', 'Big Publishing House', '0', '0', '0', '2025-05-21 12:45:49', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('90', 'En resa till glömda hemligheter', '1', '5', '1', '142.14', '2', NULL, NULL, '1999', 'Independent Books', '0', '0', '0', '2025-05-21 12:45:49', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('91', 'Collection: Rock Anthems', '1', '6', '6', '137.91', '1', NULL, NULL, '1978', 'Music Records Ltd.', '0', '0', '0', '2025-05-21 12:45:49', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('92', 'Sagan om det förlorade landet', '1', '3', '1', '237.52', '3', NULL, NULL, '2015', 'Big Publishing House', '0', '0', '0', '2025-05-21 12:45:49', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('93', 'Vintage Magasin: Historia Nr.38', '1', '1', '13', '277.62', '3', NULL, NULL, '2007', 'Small Press', '0', '0', '0', '2025-05-21 12:45:49', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('94', 'Trollkarlens stjärnorna', '1', '3', '1', '217.48', '4', NULL, NULL, '1977', 'Independent Books', '0', '0', '0', '2025-05-21 12:45:49', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('95', 'Den hemliga skogen', '1', '4', '1', '26.23', '1', NULL, NULL, '1951', 'Big Publishing House', '0', '0', '0', '2025-05-21 12:45:49', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('96', 'Den hemliga glömda hemligheter', '1', '3', '1', '197.80', '4', NULL, NULL, '2021', 'Independent Books', '0', '0', '0', '2025-05-21 12:45:49', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('97', 'Mysteriet på det förlorade landet', '1', '1', '1', '279.79', '1', NULL, NULL, '2009', 'Big Publishing House', '0', '0', '0', '2025-05-21 12:45:49', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('98', 'Best of Nature', '1', '6', '5', '234.33', '4', NULL, NULL, '1986', 'Music Records Ltd.', '0', '0', '0', '2025-05-21 12:45:49', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('99', 'Filmen: den gamla staden', '1', '7', '7', '197.77', '3', NULL, NULL, '1993', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('100', 'Trollkarlens stenar', '1', '5', '1', '187.97', '2', NULL, NULL, '1989', 'Independent Books', '0', '0', '0', '2025-05-21 12:45:49', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('101', 'Den mystiska från framtiden', '1', '1', '8', '126.31', '3', NULL, NULL, '1974', 'Comic Arts Inc.', '0', '0', '0', '2025-05-21 12:45:49', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('102', 'Sagan om tidens gång', '1', '5', '1', '237.52', '4', NULL, NULL, '2009', 'Small Press', '0', '0', '0', '2025-05-21 12:45:49', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('103', 'Äventyr i rymdens djup', '1', '7', '7', '246.36', '1', NULL, NULL, '1951', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('104', 'Mysteriet i rymdens djup', '1', '7', '7', '207.76', '3', NULL, NULL, '2008', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('105', 'Vargens tidens gång', '1', '1', '1', '199.16', '2', NULL, NULL, '1986', 'Independent Books', '0', '0', '0', '2025-05-21 12:45:49', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('106', 'Livet med stenar', '1', '3', '1', '172.93', '4', NULL, NULL, '1986', 'Small Press', '0', '0', '0', '2025-05-21 12:45:49', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('107', 'Superhjältarna från framtiden', '1', '1', '8', '257.65', '3', NULL, NULL, '1968', 'Comic Arts Inc.', '0', '0', '0', '2025-05-21 12:45:49', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('108', 'Den hemliga det förlorade landet', '1', '1', '1', '176.77', '2', NULL, NULL, '1973', 'Independent Books', '0', '0', '0', '2025-05-21 12:45:49', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('109', 'Best of the 80s', '1', '6', '5', '298.54', '3', NULL, NULL, '1962', 'Music Records Ltd.', '0', '0', '0', '2025-05-21 12:45:49', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('110', 'Sagan om tidens gång', '1', '4', '1', '267.45', '1', NULL, NULL, '2006', 'Big Publishing House', '0', '0', '0', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('111', 'Vargens stjärnorna', '1', '5', '1', '290.49', '1', NULL, NULL, '2018', 'Independent Books', '0', '0', '0', '2025-05-21 12:45:49', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('112', 'Filmen: den gamla staden', '1', '7', '7', '258.85', '4', NULL, NULL, '1999', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('113', 'Äventyr i tid och rum', '1', '7', '7', '185.04', '3', NULL, NULL, '2005', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('114', 'Mysteriet på det förlorade landet', '1', '1', '1', '256.77', '2', NULL, NULL, '1993', 'Small Press', '0', '0', '0', '2025-05-21 12:45:49', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('115', 'Äventyr med kapitel 1', '1', '1', '8', '281.33', '4', NULL, NULL, '1968', 'Comic Arts Inc.', '0', '0', '0', '2025-05-21 12:45:49', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('116', 'En resa till glömda hemligheter', '1', '1', '1', '230.13', '4', NULL, NULL, '2004', 'Small Press', '0', '0', '0', '2025-05-21 12:45:49', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('117', 'Greatest Hits: Dreams', '1', '6', '6', '277.62', '1', NULL, NULL, '1989', 'Music Records Ltd.', '0', '0', '0', '2025-05-21 12:45:49', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('118', 'Mysteriet i rymdens djup', '1', '7', '7', '107.03', '1', NULL, NULL, '1969', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('119', 'Vintage Magasin: Historia Nr.52', '1', '1', '13', '273.74', '1', NULL, NULL, '1974', 'Small Press', '0', '0', '0', '2025-05-21 12:45:49', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('120', 'Trollkarlens glömda hemligheter', '1', '3', '1', '142.14', '2', NULL, NULL, '1968', 'Big Publishing House', '0', '0', '0', '2025-05-21 12:45:49', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('121', 'Superhjältarna från framtiden', '1', '1', '8', '234.33', '2', NULL, NULL, '1984', 'Comic Arts Inc.', '0', '0', '0', '2025-05-21 12:45:49', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('122', 'Filmen: rymdens djup', '1', '7', '7', '100.86', '1', NULL, NULL, '1965', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('123', 'Äventyr i den gamla staden', '1', '7', '7', '179.91', '1', NULL, NULL, '2014', 'Film Studio Ent.', '0', '0', '0', '2025-05-21 12:45:49', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('124', 'Livet med stenar', '1', '4', '1', '269.96', '4', NULL, NULL, '1966', 'Small Press', '0', '0', '0', '2025-05-21 12:45:49', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('125', 'Sagan om det förlorade landet', '1', '1', '1', '267.45', '2', NULL, NULL, '1993', 'Big Publishing House', '0', '0', '0', '2025-05-21 12:45:49', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('126', 'Mysteriet på stjärnorna', '1', '1', '1', '217.48', '2', NULL, NULL, '1970', 'Independent Books', '0', '0', '0', '2025-05-21 12:45:49', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('127', 'Den hemliga tiden', '2', '5', '1', '16.32', '2', NULL, NULL, '1993', 'Big Publishing House', '1', '0', '1', '2025-05-21 12:57:12', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('128', 'Mysteriet på det förlorade landet', '2', '4', '1', '119.53', '3', NULL, NULL, '1991', 'Film Studio Ent.', '1', '0', '1', '2025-05-21 12:57:12', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('129', 'Äventyr med kapitel 1', '2', '1', '8', '22.18', '4', NULL, NULL, '1957', 'Small Press', '1', '0', '1', '2025-05-21 12:57:12', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('130', 'Vintage karta', '2', '1', '9', '137.95', '3', NULL, NULL, '1957', 'Independent Books', '1', '0', '1', '2025-05-21 12:57:12', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('131', 'The Album: Jazz Classics', '2', '6', '5', '193.39', '3', NULL, NULL, '2020', 'Music Records Ltd.', '1', '0', '1', '2025-05-21 12:57:12', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('132', 'Fantasy Spel: De glömda världarna', '2', '1', '12', '51.22', '3', NULL, NULL, '2019', 'Independent Books', '1', '0', '1', '2025-05-21 12:57:12', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('133', 'Klassikern: tid och rum', '2', '7', '7', '82.66', '1', NULL, NULL, '1991', 'Small Press', '1', '0', '1', '2025-05-21 12:57:12', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('134', 'Vintage Magasin: Vetenskap Nr.26', '2', '1', '13', '194.27', '2', NULL, NULL, '1970', 'Music Records Ltd.', '1', '0', '1', '2025-05-21 12:57:12', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('135', 'Den hemliga tiden', '2', '3', '1', '126.96', '2', NULL, NULL, '1967', 'Big Publishing House', '1', '0', '1', '2025-05-21 12:57:12', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('136', 'Äventyr i rymdens djup', '2', '7', '7', '276.13', '3', NULL, NULL, '1978', 'Independent Books', '1', '0', '1', '2025-05-21 12:57:12', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('137', 'Filmen: den förbjudna skogen', '2', '7', '7', '123.63', '1', NULL, NULL, '1956', 'Film Studio Ent.', '1', '0', '1', '2025-05-21 12:57:12', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('138', 'Mysteriet i den gamla staden', '2', '7', '7', '271.74', '1', NULL, NULL, '1963', 'Independent Books', '1', '0', '1', '2025-05-21 12:57:12', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('139', 'Fantasy Spel: Kungarikets öde', '2', '1', '12', '264.38', '2', NULL, NULL, '1992', 'Independent Books', '1', '0', '0', '2025-05-21 12:57:12', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('140', 'Collection: Tomorrow', '2', '1', '12', '196.97', '3', NULL, NULL, '1964', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 12:57:12', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('141', 'Sällsynt Magasin: Mode Nr.95', '2', '1', '8', '24.28', '3', NULL, NULL, '2012', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 12:57:12', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('142', 'Superhjältarna från framtiden', '2', '1', '12', '241.30', '2', NULL, NULL, '1991', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 12:57:12', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('143', 'Fantasy Spel: Kungarikets öde', '2', '1', '13', '245.74', '4', NULL, NULL, '1998', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 12:57:12', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('144', 'The Album: Tomorrow', '2', '1', '1', '83.41', '1', NULL, NULL, '2000', 'Small Press', '1', '0', '0', '2025-05-21 12:57:12', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('145', 'Vintage mynt', '2', '7', '7', '84.64', '4', NULL, NULL, '1996', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 12:57:12', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('146', 'Sällsynt Magasin: Historia Nr.77', '2', '6', '5', '233.43', '4', NULL, NULL, '2010', 'Big Publishing House', '1', '0', '0', '2025-05-21 12:57:12', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('147', 'Vintage frimärke', '1', '6', '6', '190.99', '1', NULL, NULL, '1993', 'Music Records Ltd.', '1', '1', '0', '2025-05-21 12:57:12', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('148', 'Sagan om tidens gång', '1', '3', '1', '187.67', '3', NULL, NULL, '1974', 'Small Press', '1', '0', '0', '2025-05-21 12:57:12', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('149', 'Vargens glömda hemligheter', '1', '5', '1', '21.05', '3', NULL, NULL, '1993', 'Big Publishing House', '1', '1', '1', '2025-05-21 12:57:12', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('150', 'Best of Rock Anthems', '1', '6', '5', '237.07', '3', NULL, NULL, '2011', 'Big Publishing House', '1', '0', '0', '2025-05-21 12:57:12', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('151', 'Klassikern: den gamla staden', '1', '7', '7', '240.23', '2', NULL, NULL, '1969', 'Film Studio Ent.', '1', '1', '0', '2025-05-21 12:57:12', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('152', 'Vargens tidens gång', '1', '3', '1', '220.35', '3', NULL, NULL, '2005', 'Film Studio Ent.', '1', '1', '1', '2025-05-21 12:57:12', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('153', 'Mysteriet på det förlorade landet', '1', '3', '1', '298.11', '4', NULL, NULL, '1976', 'Big Publishing House', '1', '0', '0', '2025-05-21 12:57:12', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('154', 'En resa till det förlorade landet', '1', '5', '1', '291.68', '4', NULL, NULL, '1968', 'Big Publishing House', '1', '0', '1', '2025-05-21 12:57:12', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('155', 'Best of the 80s', '1', '6', '5', '187.35', '1', NULL, NULL, '1978', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 12:57:12', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('156', 'Sagan om det förlorade landet', '1', '3', '1', '169.69', '1', NULL, NULL, '1957', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 12:57:12', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('157', 'Sagan om det förlorade landet', '1', '5', '1', '256.76', '4', NULL, NULL, '2024', 'Music Records Ltd.', '1', '1', '0', '2025-05-21 12:57:12', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('158', 'Mysteriet i rymdens djup', '1', '7', '7', '204.64', '2', NULL, NULL, '1989', 'Music Records Ltd.', '1', '1', '0', '2025-05-21 12:57:12', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('159', 'Filmen: den förbjudna skogen', '1', '7', '7', '123.63', '1', NULL, NULL, '1956', 'Film Studio Ent.', '1', '1', '0', '2025-05-21 12:57:12', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('160', 'Mysteriet i den gamla staden', '1', '7', '7', '271.74', '1', NULL, NULL, '1963', 'Independent Books', '1', '1', '1', '2025-05-21 12:57:12', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('161', 'Trollkarlens glömda hemligheter', '1', '1', '12', '267.27', '4', NULL, NULL, '1994', 'Music Records Ltd.', '1', '1', '1', '2025-05-21 12:57:12', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('162', 'Best of Nature', '1', '1', '13', '215.43', '3', NULL, NULL, '1996', 'Big Publishing House', '1', '1', '0', '2025-05-21 12:57:12', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('163', 'Äventyr med drömmarnas värld', '1', '1', '12', '171.06', '4', NULL, NULL, '1985', 'Independent Books', '1', '1', '0', '2025-05-21 12:57:12', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('164', 'Äventyr Spel: De glömda världarna', '1', '1', '13', '174.44', '3', NULL, NULL, '1996', 'Big Publishing House', '1', '1', '0', '2025-05-21 12:57:12', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('165', 'Greatest Hits: Jazz Classics', '1', '1', '12', '284.75', '4', NULL, NULL, '1991', 'Independent Books', '1', '1', '0', '2025-05-21 12:57:12', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('166', 'The Album: the 80s', '1', '7', '7', '205.74', '4', NULL, NULL, '1994', 'Independent Books', '1', '1', '0', '2025-05-21 12:57:12', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('167', 'Best of Dreams', '1', '1', '8', '167.67', '1', NULL, NULL, '2022', 'Comic Arts Inc.', '1', '1', '0', '2025-05-21 12:57:12', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('168', 'Vintage frimärke', '1', '6', '6', '127.29', '1', NULL, NULL, '1980', 'Independent Books', '1', '0', '1', '2025-05-21 12:57:12', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('169', 'Vintage Magasin: Mode Nr.19', '1', '6', '5', '261.22', '1', NULL, NULL, '1987', 'Big Publishing House', '1', '1', '1', '2025-05-21 12:57:12', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('170', 'Sagan om det förlorade landet', '1', '1', '8', '29.12', '4', NULL, NULL, '1951', 'Small Press', '1', '0', '0', '2025-05-21 12:57:12', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('171', 'Filmen: den förbjudna skogen', '1', '6', '5', '292.74', '2', NULL, NULL, '1954', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 12:57:12', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('172', 'Vintage karta', '1', '7', '7', '266.24', '4', NULL, NULL, '1993', 'Big Publishing House', '1', '0', '0', '2025-05-21 12:57:12', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('173', 'Filmen: den förbjudna skogen', '1', '1', '12', '24.33', '4', NULL, NULL, '2014', 'Comic Arts Inc.', '1', '1', '1', '2025-05-21 12:57:12', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('174', 'Vintage docka', '1', '6', '5', '128.96', '4', NULL, NULL, '1961', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 12:57:12', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('175', 'En resa till stjärnorna', '1', '6', '6', '114.18', '2', NULL, NULL, '1961', 'Small Press', '1', '0', '0', '2025-05-21 12:57:12', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('176', 'Sounds of Rock Anthems', '1', '1', '12', '229.68', '2', NULL, NULL, '2003', 'Music Records Ltd.', '1', '0', '1', '2025-05-21 12:57:12', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('177', 'Den hemliga tiden', '2', '4', '1', '16.32', '2', NULL, NULL, '1993', 'Big Publishing House', '1', '0', '1', '2025-05-21 12:59:01', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('178', 'Mysteriet på det förlorade landet', '2', '4', '1', '119.53', '3', NULL, NULL, '1991', 'Film Studio Ent.', '1', '0', '1', '2025-05-21 12:59:01', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('179', 'Äventyr med kapitel 1', '2', '1', '8', '22.18', '4', NULL, NULL, '1957', 'Small Press', '1', '0', '1', '2025-05-21 12:59:01', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('180', 'Vintage karta', '2', '1', '9', '137.95', '3', NULL, NULL, '1957', 'Independent Books', '1', '0', '1', '2025-05-21 12:59:01', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('181', 'The Album: Jazz Classics', '2', '6', '5', '193.39', '3', NULL, NULL, '2020', 'Music Records Ltd.', '1', '0', '1', '2025-05-21 12:59:01', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('182', 'Fantasy Spel: De glömda världarna', '2', '1', '12', '51.22', '3', NULL, NULL, '2019', 'Independent Books', '1', '0', '1', '2025-05-21 12:59:01', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('183', 'Klassikern: tid och rum', '2', '7', '7', '82.66', '1', NULL, NULL, '1991', 'Small Press', '1', '0', '1', '2025-05-21 12:59:01', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('184', 'Vintage Magasin: Vetenskap Nr.26', '2', '1', '13', '194.27', '2', NULL, NULL, '1970', 'Music Records Ltd.', '1', '0', '1', '2025-05-21 12:59:01', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('185', 'Den hemliga tiden', '2', '3', '1', '126.96', '2', NULL, NULL, '1967', 'Big Publishing House', '1', '0', '1', '2025-05-21 12:59:01', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('186', 'Äventyr i rymdens djup', '2', '7', '7', '276.13', '3', NULL, NULL, '1978', 'Independent Books', '1', '0', '1', '2025-05-21 12:59:01', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('187', 'Filmen: den förbjudna skogen', '2', '7', '7', '123.63', '1', NULL, NULL, '1956', 'Film Studio Ent.', '1', '0', '1', '2025-05-21 12:59:01', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('188', 'Mysteriet i den gamla staden', '2', '7', '7', '271.74', '1', NULL, NULL, '1963', 'Independent Books', '1', '0', '1', '2025-05-21 12:59:01', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('189', 'Fantasy Spel: Kungarikets öde', '2', '1', '12', '264.38', '2', NULL, NULL, '1992', 'Independent Books', '1', '0', '0', '2025-05-21 12:59:01', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('190', 'Collection: Tomorrow', '2', '1', '12', '196.97', '3', NULL, NULL, '1964', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 12:59:01', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('191', 'Sällsynt Magasin: Mode Nr.95', '2', '1', '8', '24.28', '3', NULL, NULL, '2012', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 12:59:01', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('192', 'Superhjältarna från framtiden', '2', '1', '12', '241.30', '2', NULL, NULL, '1991', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 12:59:01', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('193', 'Fantasy Spel: Kungarikets öde', '2', '1', '13', '245.74', '4', NULL, NULL, '1998', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 12:59:01', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('194', 'The Album: Tomorrow', '2', '1', '1', '83.41', '1', NULL, NULL, '2000', 'Small Press', '1', '0', '0', '2025-05-21 12:59:01', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('195', 'Vintage mynt', '2', '7', '7', '84.64', '4', NULL, NULL, '1996', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 12:59:01', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('196', 'Sällsynt Magasin: Historia Nr.77', '2', '6', '5', '233.43', '4', NULL, NULL, '2010', 'Big Publishing House', '1', '0', '0', '2025-05-21 12:59:01', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('197', 'Vintage frimärke', '1', '6', '6', '190.99', '1', NULL, NULL, '1993', 'Music Records Ltd.', '1', '1', '0', '2025-05-21 12:59:01', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('198', 'Sagan om tidens gång', '1', '3', '1', '187.67', '3', NULL, NULL, '1974', 'Small Press', '1', '0', '0', '2025-05-21 12:59:01', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('199', 'Vargens glömda hemligheter', '1', '5', '1', '21.05', '3', NULL, NULL, '1993', 'Big Publishing House', '1', '1', '1', '2025-05-21 12:59:01', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('200', 'Best of Rock Anthems', '1', '6', '5', '237.07', '3', NULL, NULL, '2011', 'Big Publishing House', '1', '0', '0', '2025-05-21 12:59:01', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('201', 'Klassikern: den gamla staden', '1', '7', '7', '240.23', '2', NULL, NULL, '1969', 'Film Studio Ent.', '1', '1', '0', '2025-05-21 12:59:01', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('202', 'Vargens tidens gång', '1', '3', '1', '220.35', '3', NULL, NULL, '2005', 'Film Studio Ent.', '1', '1', '1', '2025-05-21 12:59:01', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('203', 'Mysteriet på det förlorade landet', '1', '3', '1', '298.11', '4', NULL, NULL, '1976', 'Big Publishing House', '1', '0', '0', '2025-05-21 12:59:01', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('204', 'En resa till det förlorade landet', '1', '5', '1', '291.68', '4', NULL, NULL, '1968', 'Big Publishing House', '1', '0', '1', '2025-05-21 12:59:01', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('205', 'Best of the 80s', '1', '6', '5', '187.35', '1', NULL, NULL, '1978', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 12:59:01', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('206', 'Sagan om det förlorade landet', '1', '3', '1', '169.69', '1', NULL, NULL, '1957', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 12:59:01', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('207', 'Sagan om det förlorade landet', '1', '5', '1', '256.76', '4', NULL, NULL, '2024', 'Music Records Ltd.', '1', '1', '0', '2025-05-21 12:59:01', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('208', 'Mysteriet i rymdens djup', '1', '7', '7', '204.64', '2', NULL, NULL, '1989', 'Music Records Ltd.', '1', '1', '0', '2025-05-21 12:59:01', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('209', 'Filmen: den förbjudna skogen', '1', '7', '7', '123.63', '1', NULL, NULL, '1956', 'Film Studio Ent.', '1', '1', '0', '2025-05-21 12:59:01', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('210', 'Mysteriet i den gamla staden', '1', '7', '7', '271.74', '1', NULL, NULL, '1963', 'Independent Books', '1', '1', '1', '2025-05-21 12:59:01', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('211', 'Trollkarlens glömda hemligheter', '1', '1', '12', '267.27', '4', NULL, NULL, '1994', 'Music Records Ltd.', '1', '1', '1', '2025-05-21 12:59:01', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('212', 'Best of Nature', '1', '1', '13', '215.43', '3', NULL, NULL, '1996', 'Big Publishing House', '1', '1', '0', '2025-05-21 12:59:01', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('213', 'Äventyr med drömmarnas värld', '1', '1', '12', '171.06', '4', NULL, NULL, '1985', 'Independent Books', '1', '1', '0', '2025-05-21 12:59:01', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('214', 'Äventyr Spel: De glömda världarna', '1', '1', '13', '174.44', '3', NULL, NULL, '1996', 'Big Publishing House', '1', '1', '0', '2025-05-21 12:59:01', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('215', 'Greatest Hits: Jazz Classics', '1', '1', '12', '284.75', '4', NULL, NULL, '1991', 'Independent Books', '1', '1', '0', '2025-05-21 12:59:01', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('216', 'The Album: the 80s', '1', '7', '7', '205.74', '4', NULL, NULL, '1994', 'Independent Books', '1', '1', '0', '2025-05-21 12:59:01', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('217', 'Best of Dreams', '1', '1', '8', '167.67', '1', NULL, NULL, '2022', 'Comic Arts Inc.', '1', '1', '0', '2025-05-21 12:59:01', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('218', 'Vintage frimärke', '1', '6', '6', '127.29', '1', NULL, NULL, '1980', 'Independent Books', '1', '0', '1', '2025-05-21 12:59:01', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('219', 'Vintage Magasin: Mode Nr.19', '1', '6', '5', '261.22', '1', NULL, NULL, '1987', 'Big Publishing House', '1', '1', '1', '2025-05-21 12:59:01', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('220', 'Sagan om det förlorade landet', '1', '1', '8', '29.12', '4', NULL, NULL, '1951', 'Small Press', '1', '0', '0', '2025-05-21 12:59:01', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('221', 'Filmen: den förbjudna skogen', '1', '6', '5', '292.74', '2', NULL, NULL, '1954', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 12:59:01', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('222', 'Vintage karta', '1', '7', '7', '266.24', '4', NULL, NULL, '1993', 'Big Publishing House', '1', '0', '0', '2025-05-21 12:59:01', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('223', 'Filmen: den förbjudna skogen', '1', '1', '12', '24.33', '4', NULL, NULL, '2014', 'Comic Arts Inc.', '1', '1', '1', '2025-05-21 12:59:01', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('224', 'Vintage docka', '1', '6', '5', '128.96', '4', NULL, NULL, '1961', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 12:59:01', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('225', 'En resa till stjärnorna', '1', '6', '6', '114.18', '2', NULL, NULL, '1961', 'Small Press', '1', '0', '0', '2025-05-21 12:59:01', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('226', 'Sounds of Rock Anthems', '1', '1', '12', '229.68', '2', NULL, NULL, '2003', 'Music Records Ltd.', '1', '0', '1', '2025-05-21 12:59:01', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('227', 'Vintage Magasin: Historia Nr.51', '1', '1', '13', '45.13', '2', NULL, NULL, '1990', 'Small Press', '1', '0', '0', '2025-05-21 10:05:35', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('228', 'Vintage frimärke', '1', '7', '7', '161.21', '3', NULL, NULL, '1987', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 10:05:35', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('229', 'Äventyr i rymdens djup', '1', '1', '8', '134.56', '2', NULL, NULL, '1960', 'Small Press', '1', '0', '0', '2025-05-21 10:05:35', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('230', 'Äventyr i rymdens djup', '1', '6', '9', '126.17', '4', NULL, NULL, '1991', 'Independent Books', '1', '0', '0', '2025-05-21 10:05:35', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('231', 'Fantasy Spel: Kungarikets öde', '1', '4', '1', '128.07', '3', NULL, NULL, '1981', 'Independent Books', '1', '0', '0', '2025-05-21 10:05:35', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('232', 'En resa till stjärnorna', '1', '1', '9', '12.07', '1', NULL, NULL, '1954', 'Small Press', '1', '0', '0', '2025-05-21 10:05:35', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('233', 'Live at Dreams', '1', '1', '8', '128.11', '1', NULL, NULL, '1993', 'Big Publishing House', '1', '0', '0', '2025-05-21 10:05:35', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('234', 'Vintage frimärke', '1', '1', '13', '133.44', '3', NULL, NULL, '1964', 'Small Press', '1', '0', '0', '2025-05-21 10:05:35', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('235', 'Live at the 80s', '1', '1', '12', '247.48', '4', NULL, NULL, '2014', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 10:05:35', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('236', 'The Album: Dreams', '1', '1', '13', '171.66', '3', NULL, NULL, '1982', 'Big Publishing House', '1', '0', '0', '2025-05-21 10:05:35', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('237', 'Mysteriet i den gamla staden', '1', '1', '8', '150.29', '3', NULL, NULL, '2014', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 10:05:35', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('238', 'Sällsynt Magasin: Mode Nr.76', '1', '1', '12', '76.86', '3', NULL, NULL, '1959', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 10:05:35', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('239', 'Vintage docka', '1', '7', '7', '132.43', '3', NULL, NULL, '1959', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 10:05:35', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('240', 'Live at Nature', '1', '7', '7', '59.30', '2', NULL, NULL, '1973', 'Big Publishing House', '1', '0', '0', '2025-05-21 10:05:35', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('241', 'Klassikern: rymdens djup', '1', '6', '6', '76.99', '3', NULL, NULL, '1996', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 10:05:35', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('242', 'Trollkarlens glömda hemligheter', '1', '6', '5', '289.76', '3', NULL, NULL, '1967', 'Big Publishing House', '1', '0', '0', '2025-05-21 10:05:35', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('243', 'Den hemliga skogen', '1', '7', '7', '69.82', '1', NULL, NULL, '1952', 'Independent Books', '1', '0', '0', '2025-05-21 10:05:35', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('244', 'Äventyr i den förbjudna skogen', '1', '1', '12', '60.90', '3', NULL, NULL, '1983', 'Big Publishing House', '1', '0', '0', '2025-05-21 10:05:35', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('245', 'Sällsynt Magasin: Historia Nr.35', '1', '6', '9', '177.48', '4', NULL, NULL, '1964', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 10:05:35', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('246', 'Äventyr Spel: De glömda världarna', '1', '6', '5', '76.93', '3', NULL, NULL, '2006', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 10:05:35', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('247', 'Klassikern: rymdens djup', '1', '1', '9', '233.28', '1', NULL, NULL, '2012', 'Independent Books', '1', '0', '0', '2025-05-21 10:05:35', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('248', 'Sällsynt Magasin: Vetenskap Nr.32', '1', '6', '6', '154.05', '4', NULL, NULL, '1990', 'Small Press', '1', '0', '0', '2025-05-21 10:05:35', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('249', 'Vintage Magasin: Vetenskap Nr.64', '1', '7', '7', '243.98', '2', NULL, NULL, '1967', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 10:05:35', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('250', 'Live at Nature', '1', '7', '7', '76.78', '3', NULL, NULL, '1973', 'Small Press', '1', '0', '0', '2025-05-21 10:05:35', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('251', 'Collection: Rock Anthems', '1', '7', '7', '145.66', '3', NULL, NULL, '1960', 'Big Publishing House', '1', '0', '0', '2025-05-21 10:05:35', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('252', 'Sällsynt Magasin: Mode Nr.8', '1', '1', '9', '78.07', '4', NULL, NULL, '1956', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 10:05:35', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('253', 'Klassikern: den förbjudna skogen', '1', '1', '12', '243.14', '3', NULL, NULL, '2014', 'Big Publishing House', '1', '0', '0', '2025-05-21 10:05:35', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('254', 'The Album: Nature', '1', '6', '5', '82.33', '4', NULL, NULL, '1963', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 10:05:35', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('255', 'Best of Nature', '1', '1', '9', '169.15', '2', NULL, NULL, '1965', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 10:05:35', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('256', 'Collection: the 80s', '1', '1', '1', '290.87', '2', NULL, NULL, '1981', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 10:05:35', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('257', 'Äventyr i den förbjudna skogen', '1', '1', '9', '279.07', '4', NULL, NULL, '1953', 'Small Press', '1', '0', '0', '2025-05-21 10:05:35', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('258', 'The Album: Nature', '1', '6', '9', '155.15', '3', NULL, NULL, '1979', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 10:05:35', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('259', 'Äventyr Spel: Kungarikets öde', '1', '6', '6', '110.89', '2', NULL, NULL, '1986', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 10:05:35', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('260', 'Live at Rock Anthems', '1', '1', '12', '94.50', '4', NULL, NULL, '1951', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 10:05:35', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('261', 'Fantasy Spel: De glömda världarna', '1', '6', '5', '38.57', '2', NULL, NULL, '1958', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 10:05:35', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('262', 'Fantasy Spel: Kungarikets öde', '1', '7', '7', '299.20', '3', NULL, NULL, '1992', 'Small Press', '1', '0', '0', '2025-05-21 10:05:35', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('263', 'Sagan om tidens gång', '1', '1', '1', '231.27', '3', NULL, NULL, '1992', 'Big Publishing House', '1', '0', '0', '2025-05-21 10:05:35', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('264', 'Sounds of Rock Anthems', '1', '1', '12', '282.26', '4', NULL, NULL, '1972', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 10:05:35', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('265', 'Fantasy Spel: Kungarikets öde', '1', '7', '7', '69.81', '1', NULL, NULL, '1987', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 10:05:35', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('266', 'Collection: Nature', '1', '1', '12', '58.94', '1', NULL, NULL, '2020', 'Independent Books', '1', '0', '0', '2025-05-21 10:05:35', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('267', 'Collection: Rock Anthems', '1', '6', '6', '67.57', '2', NULL, NULL, '1991', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 10:05:35', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('268', 'Vargens stjärnorna', '1', '6', '6', '195.17', '2', NULL, NULL, '1967', 'Big Publishing House', '1', '0', '0', '2025-05-21 10:05:35', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('269', 'Vargens det förlorade landet', '1', '7', '7', '227.31', '1', NULL, NULL, '1975', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 10:05:35', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('270', 'Greatest Hits: Tomorrow', '1', '7', '7', '93.72', '1', NULL, NULL, '1957', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 10:05:35', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('271', 'Best of Jazz Classics', '1', '7', '7', '192.74', '2', NULL, NULL, '2000', 'Big Publishing House', '1', '0', '0', '2025-05-21 10:05:35', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('272', 'Äventyr i den förbjudna skogen', '1', '1', '8', '35.48', '1', NULL, NULL, '1968', 'Independent Books', '1', '0', '0', '2025-05-21 10:05:35', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('273', 'Trollkarlens glömda hemligheter', '1', '1', '1', '161.76', '1', NULL, NULL, '1968', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 10:05:35', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('274', 'Greatest Hits: Nature', '1', '1', '8', '289.87', '4', NULL, NULL, '1987', 'Independent Books', '1', '0', '0', '2025-05-21 10:05:35', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('275', 'Vintage mynt', '1', '1', '13', '46.08', '3', NULL, NULL, '1995', 'Small Press', '1', '0', '0', '2025-05-21 10:05:35', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('276', 'Äventyr Spel: De glömda världarna', '1', '6', '6', '285.35', '1', NULL, NULL, '1992', 'Small Press', '1', '0', '0', '2025-05-21 10:05:35', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('277', 'Fantasy Spel: Kungarikets öde', '1', '1', '12', '128.07', '3', NULL, NULL, '1981', 'Independent Books', '1', '0', '0', '2025-05-21 13:07:14', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('278', 'En resa till stjärnorna', '1', '1', '9', '12.07', '1', NULL, NULL, '1954', 'Small Press', '1', '0', '0', '2025-05-21 13:07:14', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('279', 'Live at Dreams', '1', '1', '8', '128.11', '1', NULL, NULL, '1993', 'Big Publishing House', '1', '0', '0', '2025-05-21 13:07:14', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('280', 'Vintage frimärke', '1', '1', '13', '133.44', '3', NULL, NULL, '1964', 'Small Press', '1', '0', '0', '2025-05-21 13:07:14', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('281', 'Live at the 80s', '1', '1', '12', '247.48', '4', NULL, NULL, '2014', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 13:07:14', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('282', 'The Album: Dreams', '1', '1', '13', '171.66', '3', NULL, NULL, '1982', 'Big Publishing House', '1', '0', '0', '2025-05-21 13:07:14', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('283', 'Mysteriet i den gamla staden', '1', '1', '8', '150.29', '3', NULL, NULL, '2014', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 13:07:14', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('284', 'Sällsynt Magasin: Mode Nr.76', '1', '1', '12', '76.86', '3', NULL, NULL, '1959', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 13:07:14', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('285', 'Vintage docka', '1', '7', '7', '132.43', '3', NULL, NULL, '1959', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 13:07:14', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('286', 'Live at Nature', '1', '7', '7', '59.30', '2', NULL, NULL, '1973', 'Big Publishing House', '1', '0', '0', '2025-05-21 13:07:14', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('287', 'Klassikern: rymdens djup', '1', '6', '6', '76.99', '3', NULL, NULL, '1996', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 13:07:14', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('288', 'Trollkarlens glömda hemligheter', '1', '6', '5', '289.76', '3', NULL, NULL, '1967', 'Big Publishing House', '1', '0', '0', '2025-05-21 13:07:14', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('289', 'Den hemliga skogen', '1', '7', '7', '69.82', '1', NULL, NULL, '1952', 'Independent Books', '1', '0', '0', '2025-05-21 13:07:14', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('290', 'Äventyr i den förbjudna skogen', '1', '1', '12', '60.90', '3', NULL, NULL, '1983', 'Big Publishing House', '1', '0', '0', '2025-05-21 13:07:14', '7');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('291', 'Sällsynt Magasin: Historia Nr.35', '1', '6', '9', '177.48', '4', NULL, NULL, '1964', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 13:07:14', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('292', 'Äventyr Spel: De glömda världarna', '1', '6', '5', '76.93', '3', NULL, NULL, '2006', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 13:07:14', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('293', 'Klassikern: rymdens djup', '1', '1', '9', '233.28', '1', NULL, NULL, '2012', 'Independent Books', '1', '0', '0', '2025-05-21 13:07:14', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('294', 'Sällsynt Magasin: Vetenskap Nr.32', '1', '6', '6', '154.05', '4', NULL, NULL, '1990', 'Small Press', '1', '0', '0', '2025-05-21 13:07:14', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('295', 'Vintage Magasin: Vetenskap Nr.64', '1', '7', '7', '243.98', '2', NULL, NULL, '1967', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 13:07:14', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('296', 'Live at Nature', '1', '7', '7', '76.78', '3', NULL, NULL, '1973', 'Small Press', '1', '0', '0', '2025-05-21 13:07:14', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('297', 'Collection: Rock Anthems', '1', '7', '7', '145.66', '3', NULL, NULL, '1960', 'Big Publishing House', '1', '0', '0', '2025-05-21 13:07:14', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('298', 'Sällsynt Magasin: Mode Nr.8', '1', '1', '9', '78.07', '4', NULL, NULL, '1956', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 13:07:14', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('299', 'Klassikern: den förbjudna skogen', '1', '1', '12', '243.14', '3', NULL, NULL, '2014', 'Big Publishing House', '1', '0', '0', '2025-05-21 13:07:14', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('300', 'The Album: Nature', '1', '6', '5', '82.33', '4', NULL, NULL, '1963', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 13:07:14', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('301', 'Best of Nature', '1', '1', '9', '169.15', '2', NULL, NULL, '1965', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 13:07:14', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('302', 'Collection: the 80s', '1', '1', '1', '290.87', '2', NULL, NULL, '1981', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 13:07:14', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('303', 'Äventyr i den förbjudna skogen', '1', '1', '9', '279.07', '4', NULL, NULL, '1953', 'Small Press', '1', '0', '0', '2025-05-21 13:07:14', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('304', 'The Album: Nature', '1', '6', '9', '155.15', '3', NULL, NULL, '1979', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 13:07:14', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('305', 'Äventyr Spel: Kungarikets öde', '1', '6', '6', '110.89', '2', NULL, NULL, '1986', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 13:07:14', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('306', 'Live at Rock Anthems', '1', '1', '12', '94.50', '4', NULL, NULL, '1951', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 13:07:14', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('307', 'Fantasy Spel: De glömda världarna', '1', '6', '5', '38.57', '2', NULL, NULL, '1958', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 13:07:14', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('308', 'Fantasy Spel: Kungarikets öde', '1', '7', '7', '299.20', '3', NULL, NULL, '1992', 'Small Press', '1', '0', '0', '2025-05-21 13:07:14', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('309', 'Sagan om tidens gång', '1', '1', '1', '231.27', '3', NULL, NULL, '1992', 'Big Publishing House', '1', '0', '0', '2025-05-21 13:07:14', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('310', 'Sounds of Rock Anthems', '1', '1', '12', '282.26', '4', NULL, NULL, '1972', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 13:07:14', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('311', 'Fantasy Spel: Kungarikets öde', '1', '7', '7', '69.81', '1', NULL, NULL, '1987', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 13:07:14', '8');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('312', 'Collection: Nature', '1', '1', '12', '58.94', '1', NULL, NULL, '2020', 'Independent Books', '1', '0', '0', '2025-05-21 13:07:14', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('313', 'Collection: Rock Anthems', '1', '6', '6', '67.57', '2', NULL, NULL, '1991', 'Music Records Ltd.', '1', '0', '0', '2025-05-21 13:07:14', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('314', 'Vargens stjärnorna', '1', '6', '6', '195.17', '2', NULL, NULL, '1967', 'Big Publishing House', '1', '0', '0', '2025-05-21 13:07:14', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('315', 'Vargens det förlorade landet', '1', '7', '7', '227.31', '1', NULL, NULL, '1975', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 13:07:14', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('316', 'Greatest Hits: Tomorrow', '1', '7', '7', '93.72', '1', NULL, NULL, '1957', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 13:07:14', '5');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('317', 'Best of Jazz Classics', '1', '7', '7', '192.74', '2', NULL, NULL, '2000', 'Big Publishing House', '1', '0', '0', '2025-05-21 13:07:14', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('318', 'Äventyr i den förbjudna skogen', '1', '1', '8', '35.48', '1', NULL, NULL, '1968', 'Independent Books', '1', '0', '0', '2025-05-21 13:07:14', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('319', 'Trollkarlens glömda hemligheter', '1', '1', '1', '161.76', '1', NULL, NULL, '1968', 'Comic Arts Inc.', '1', '0', '0', '2025-05-21 13:07:14', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('320', 'Greatest Hits: Nature', '1', '1', '8', '289.87', '4', NULL, NULL, '1987', 'Independent Books', '1', '0', '0', '2025-05-21 13:07:14', '6');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('321', 'Vintage mynt', '1', '1', '13', '46.08', '3', NULL, NULL, '1995', 'Small Press', '1', '0', '0', '2025-05-21 13:07:14', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('322', 'Äventyr Spel: De glömda världarna', '1', '6', '6', '285.35', '1', NULL, NULL, '1992', 'Small Press', '1', '0', '0', '2025-05-21 13:07:14', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('323', 'Den hemliga tiden', '1', '5', '1', '16.32', '2', NULL, NULL, '1993', 'Big Publishing House', '1', '0', '0', '2025-05-21 13:07:14', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('324', 'Mysteriet på det förlorade landet', '1', '4', '1', '119.53', '3', NULL, NULL, '1991', 'Film Studio Ent.', '1', '0', '0', '2025-05-21 13:07:14', '4');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('325', 'Äventyr med kapitel 1', '1', '1', '8', '22.18', '4', NULL, NULL, '1957', 'Small Press', '1', '0', '0', '2025-05-21 13:07:14', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('326', 'Vintage karta', '1', '1', '9', '137.95', '3', NULL, NULL, '1957', 'Independent Books', '1', '0', '0', '2025-05-21 13:07:14', '6');

-- 
-- Table structure for table `product_author`
-- 

DROP TABLE IF EXISTS `product_author`;
CREATE TABLE `product_author` (
  `product_author_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `author_id` int DEFAULT NULL,
  PRIMARY KEY (`product_author_id`),
  UNIQUE KEY `unique_product_author` (`product_id`,`author_id`),
  KEY `author_id` (`author_id`),
  CONSTRAINT `product_author_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`prod_id`),
  CONSTRAINT `product_author_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `author` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=637 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `product_author`
-- 

INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('1', '1', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('2', '2', '2');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('3', '3', '3');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('4', '4', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('5', '5', '5');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('6', '6', '6');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('7', '7', '7');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('8', '8', '8');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('9', '9', '9');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('10', '10', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('11', '11', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('12', '12', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('13', '14', '6');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('14', '15', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('15', '16', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('16', '17', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('17', '18', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('18', '19', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('19', '20', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('20', '21', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('21', '22', '24');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('22', '23', '19');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('23', '24', '20');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('24', '27', '26');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('25', '28', '27');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('26', '29', '28');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('27', '30', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('28', '31', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('29', '32', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('30', '33', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('31', '37', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('32', '38', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('33', '39', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('188', '46', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('187', '46', '21');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('189', '47', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('190', '47', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('191', '48', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('192', '48', '20');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('193', '49', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('194', '49', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('195', '50', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('196', '51', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('197', '51', '15');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('199', '52', '24');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('198', '52', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('200', '53', '3');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('201', '53', '20');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('202', '54', '26');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('203', '54', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('205', '55', '27');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('204', '55', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('207', '56', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('206', '56', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('209', '57', '28');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('208', '57', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('211', '58', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('210', '58', '20');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('213', '59', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('212', '59', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('215', '60', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('214', '60', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('217', '61', '26');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('216', '61', '28');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('218', '62', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('219', '62', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('220', '63', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('221', '63', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('222', '64', '27');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('223', '64', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('224', '65', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('225', '65', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('227', '66', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('226', '66', '21');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('228', '67', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('230', '68', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('229', '68', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('231', '69', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('233', '70', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('232', '70', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('234', '71', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('235', '71', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('236', '72', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('237', '72', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('238', '73', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('239', '73', '20');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('240', '74', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('241', '75', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('242', '75', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('244', '76', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('243', '76', '24');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('246', '77', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('245', '77', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('247', '78', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('248', '78', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('249', '79', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('250', '79', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('251', '80', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('252', '80', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('253', '81', '29');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('255', '82', '9');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('254', '82', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('256', '83', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('257', '83', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('259', '84', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('258', '84', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('260', '85', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('261', '85', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('262', '86', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('263', '86', '24');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('265', '87', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('264', '87', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('266', '88', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('267', '89', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('268', '89', '21');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('269', '90', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('270', '90', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('272', '91', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('271', '91', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('273', '92', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('274', '92', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('275', '93', '21');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('276', '94', '19');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('278', '95', '15');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('277', '95', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('279', '96', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('280', '96', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('282', '97', '19');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('281', '97', '20');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('283', '98', '27');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('284', '98', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('285', '99', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('286', '99', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('287', '100', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('288', '100', '15');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('289', '101', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('290', '101', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('291', '102', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('292', '102', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('294', '103', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('293', '103', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('295', '104', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('296', '104', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('297', '105', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('298', '105', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('300', '106', '24');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('299', '106', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('302', '107', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('301', '107', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('303', '108', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('304', '108', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('306', '109', '26');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('305', '109', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('307', '110', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('308', '110', '19');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('310', '111', '20');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('309', '111', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('311', '112', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('312', '112', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('313', '113', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('314', '113', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('316', '114', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('315', '114', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('317', '115', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('318', '115', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('320', '116', '9');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('319', '116', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('321', '117', '26');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('322', '117', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('324', '118', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('323', '118', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('325', '119', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('327', '120', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('326', '120', '24');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('329', '121', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('328', '121', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('331', '122', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('330', '122', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('332', '123', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('333', '123', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('334', '124', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('335', '124', '24');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('337', '125', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('336', '125', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('338', '126', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('339', '126', '19');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('341', '127', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('340', '127', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('342', '128', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('343', '128', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('345', '129', '21');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('344', '129', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('347', '130', '3');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('346', '130', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('349', '131', '28');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('348', '131', '29');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('350', '132', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('351', '132', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('352', '133', '7');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('353', '133', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('354', '134', '20');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('356', '135', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('355', '135', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('357', '136', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('358', '137', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('359', '138', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('361', '139', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('360', '139', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('362', '140', '28');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('364', '141', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('363', '141', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('365', '142', '3');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('366', '142', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('367', '143', '15');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('369', '144', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('368', '144', '19');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('370', '145', '3');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('371', '145', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('373', '146', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('372', '146', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('374', '147', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('375', '148', '2');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('376', '149', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('377', '150', '26');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('379', '151', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('378', '151', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('380', '152', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('381', '153', '9');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('382', '154', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('383', '154', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('384', '155', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('385', '156', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('386', '156', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('387', '157', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('388', '158', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('389', '158', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('390', '159', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('391', '160', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('392', '161', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('393', '162', '7');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('394', '163', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('395', '164', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('396', '165', '7');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('397', '166', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('398', '167', '26');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('399', '168', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('400', '169', '21');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('401', '170', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('402', '171', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('403', '171', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('405', '172', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('404', '172', '27');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('406', '173', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('408', '174', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('407', '174', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('409', '175', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('410', '175', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('411', '176', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('412', '176', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('414', '177', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('413', '177', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('415', '178', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('416', '178', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('418', '179', '21');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('417', '179', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('420', '180', '3');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('419', '180', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('422', '181', '28');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('421', '181', '29');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('423', '182', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('424', '182', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('425', '183', '7');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('426', '183', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('427', '184', '20');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('429', '185', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('428', '185', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('430', '186', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('431', '187', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('432', '188', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('434', '189', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('433', '189', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('435', '190', '28');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('437', '191', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('436', '191', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('438', '192', '3');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('439', '192', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('440', '193', '15');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('442', '194', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('441', '194', '19');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('443', '195', '3');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('444', '195', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('446', '196', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('445', '196', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('447', '197', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('448', '198', '2');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('449', '199', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('450', '200', '26');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('452', '201', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('451', '201', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('453', '202', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('454', '203', '9');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('455', '204', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('456', '204', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('457', '205', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('458', '206', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('459', '206', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('460', '207', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('461', '208', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('462', '208', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('463', '209', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('464', '210', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('465', '211', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('466', '212', '7');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('467', '213', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('468', '214', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('469', '215', '7');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('470', '216', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('471', '217', '26');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('472', '218', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('473', '219', '21');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('474', '220', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('475', '221', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('476', '221', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('478', '222', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('477', '222', '27');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('479', '223', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('481', '224', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('480', '224', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('482', '225', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('483', '225', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('484', '226', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('485', '226', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('486', '227', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('487', '228', '15');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('488', '229', '6');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('489', '229', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('490', '230', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('491', '231', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('493', '232', '27');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('492', '232', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('494', '233', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('495', '233', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('496', '234', '9');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('497', '235', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('499', '236', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('498', '236', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('500', '237', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('501', '237', '29');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('502', '238', '27');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('503', '239', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('504', '240', '7');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('505', '240', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('507', '241', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('506', '241', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('509', '242', '8');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('508', '242', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('511', '243', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('510', '243', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('512', '244', '8');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('513', '245', '9');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('515', '246', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('514', '246', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('516', '247', '19');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('517', '247', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('519', '248', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('518', '248', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('520', '249', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('521', '249', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('522', '250', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('523', '250', '29');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('524', '251', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('526', '252', '5');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('525', '252', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('527', '253', '29');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('528', '254', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('529', '254', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('530', '255', '8');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('531', '256', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('533', '257', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('532', '257', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('534', '258', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('535', '259', '21');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('536', '260', '20');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('537', '261', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('538', '262', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('539', '263', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('540', '264', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('541', '265', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('542', '265', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('543', '266', '19');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('544', '267', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('546', '268', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('545', '268', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('547', '269', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('548', '269', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('549', '270', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('550', '270', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('551', '271', '15');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('552', '271', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('553', '272', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('554', '273', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('556', '274', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('555', '274', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('557', '275', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('558', '276', '9');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('559', '276', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('560', '277', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('562', '278', '27');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('561', '278', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('563', '279', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('564', '279', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('565', '280', '9');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('566', '281', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('568', '282', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('567', '282', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('569', '283', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('570', '283', '29');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('571', '284', '27');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('572', '285', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('573', '286', '7');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('574', '286', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('576', '287', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('575', '287', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('578', '288', '8');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('577', '288', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('580', '289', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('579', '289', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('581', '290', '8');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('582', '291', '9');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('584', '292', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('583', '292', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('585', '293', '19');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('586', '293', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('588', '294', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('587', '294', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('589', '295', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('590', '295', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('591', '296', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('592', '296', '29');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('593', '297', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('595', '298', '5');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('594', '298', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('596', '299', '29');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('597', '300', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('598', '300', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('599', '301', '8');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('600', '302', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('602', '303', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('601', '303', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('603', '304', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('604', '305', '21');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('605', '306', '20');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('606', '307', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('607', '308', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('608', '309', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('609', '310', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('610', '311', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('611', '311', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('612', '312', '19');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('613', '313', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('615', '314', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('614', '314', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('616', '315', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('617', '315', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('618', '316', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('619', '316', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('620', '317', '15');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('621', '317', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('622', '318', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('623', '319', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('625', '320', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('624', '320', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('626', '321', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('627', '322', '9');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('628', '322', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('630', '323', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('629', '323', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('631', '324', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('632', '324', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('634', '325', '21');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('633', '325', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('636', '326', '3');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('635', '326', '31');

-- 
-- Table structure for table `product_genre`
-- 

DROP TABLE IF EXISTS `product_genre`;
CREATE TABLE `product_genre` (
  `product_genre_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `genre_id` int DEFAULT NULL,
  PRIMARY KEY (`product_genre_id`),
  UNIQUE KEY `unique_product_genre` (`product_id`,`genre_id`),
  KEY `genre_id` (`genre_id`),
  CONSTRAINT `product_genre_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`prod_id`),
  CONSTRAINT `product_genre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`genre_id`)
) ENGINE=InnoDB AUTO_INCREMENT=686 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `product_genre`
-- 

INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('2', '1', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('1', '1', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('3', '2', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('4', '3', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('5', '4', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('6', '4', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('7', '5', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('8', '6', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('9', '7', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('10', '7', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('11', '8', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('12', '9', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('13', '10', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('14', '10', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('15', '11', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('16', '11', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('17', '12', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('18', '13', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('19', '14', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('20', '15', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('21', '16', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('22', '17', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('23', '17', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('24', '18', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('25', '19', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('26', '19', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('27', '20', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('28', '20', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('29', '21', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('30', '22', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('31', '23', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('32', '24', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('33', '25', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('34', '26', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('35', '27', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('36', '28', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('37', '29', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('38', '30', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('39', '31', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('40', '32', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('41', '33', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('42', '34', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('43', '35', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('44', '36', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('45', '40', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('46', '41', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('47', '42', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('211', '46', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('210', '46', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('212', '47', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('213', '47', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('214', '48', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('215', '48', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('216', '49', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('217', '49', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('218', '50', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('219', '50', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('220', '51', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('221', '51', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('222', '52', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('223', '52', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('224', '53', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('225', '53', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('227', '54', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('226', '54', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('228', '55', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('229', '55', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('230', '56', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('231', '56', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('232', '57', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('233', '57', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('234', '58', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('235', '58', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('236', '59', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('237', '59', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('239', '60', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('238', '60', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('241', '61', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('240', '61', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('243', '62', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('242', '62', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('244', '63', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('245', '63', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('246', '64', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('247', '64', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('249', '65', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('248', '65', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('251', '66', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('250', '66', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('252', '67', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('253', '67', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('255', '68', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('254', '68', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('256', '69', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('257', '69', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('259', '70', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('258', '70', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('260', '71', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('261', '71', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('263', '72', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('262', '72', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('265', '73', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('264', '73', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('267', '74', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('266', '74', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('268', '75', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('269', '75', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('271', '76', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('270', '76', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('272', '77', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('273', '77', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('274', '78', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('275', '78', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('277', '79', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('276', '79', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('279', '80', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('278', '80', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('280', '81', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('281', '81', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('283', '82', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('282', '82', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('284', '83', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('285', '83', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('286', '84', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('287', '84', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('289', '85', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('288', '85', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('291', '86', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('290', '86', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('293', '87', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('292', '87', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('294', '88', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('295', '88', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('296', '89', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('297', '89', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('299', '90', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('298', '90', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('300', '91', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('301', '91', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('303', '92', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('302', '92', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('305', '93', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('304', '93', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('306', '94', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('307', '94', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('309', '95', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('308', '95', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('311', '96', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('310', '96', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('312', '97', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('313', '97', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('314', '98', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('315', '98', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('317', '99', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('316', '99', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('318', '100', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('319', '100', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('320', '101', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('321', '101', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('323', '102', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('322', '102', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('325', '103', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('324', '103', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('327', '104', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('326', '104', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('328', '105', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('329', '105', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('330', '106', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('331', '106', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('333', '107', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('332', '107', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('334', '108', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('335', '108', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('336', '109', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('337', '109', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('339', '110', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('338', '110', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('340', '111', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('341', '111', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('342', '112', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('343', '112', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('345', '113', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('344', '113', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('347', '114', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('346', '114', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('348', '115', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('349', '115', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('351', '116', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('350', '116', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('352', '117', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('353', '117', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('355', '118', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('354', '118', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('357', '119', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('356', '119', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('359', '120', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('358', '120', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('361', '121', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('360', '121', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('363', '122', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('362', '122', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('364', '123', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('365', '123', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('366', '124', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('367', '124', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('368', '125', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('369', '125', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('371', '126', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('370', '126', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('372', '127', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('373', '127', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('374', '128', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('375', '128', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('377', '129', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('376', '129', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('379', '130', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('378', '130', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('380', '131', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('381', '131', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('383', '132', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('382', '132', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('385', '133', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('384', '133', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('386', '134', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('387', '135', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('388', '135', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('389', '136', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('390', '136', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('391', '137', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('392', '137', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('393', '138', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('394', '138', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('396', '139', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('395', '139', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('397', '140', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('398', '141', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('400', '142', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('399', '142', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('401', '143', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('403', '144', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('402', '144', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('405', '145', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('404', '145', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('406', '146', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('407', '147', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('408', '148', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('409', '149', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('410', '150', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('411', '151', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('412', '151', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('413', '152', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('414', '153', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('415', '153', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('416', '154', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('417', '154', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('418', '155', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('419', '156', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('420', '156', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('421', '157', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('422', '157', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('423', '158', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('424', '158', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('425', '159', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('426', '159', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('427', '160', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('428', '160', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('429', '161', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('430', '161', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('431', '162', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('432', '163', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('433', '164', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('434', '165', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('435', '166', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('436', '166', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('438', '167', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('437', '167', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('439', '168', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('440', '168', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('441', '169', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('442', '170', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('443', '171', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('444', '172', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('445', '173', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('446', '173', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('447', '174', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('448', '174', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('449', '175', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('450', '176', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('451', '177', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('452', '177', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('453', '178', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('454', '178', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('456', '179', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('455', '179', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('458', '180', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('457', '180', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('459', '181', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('460', '181', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('462', '182', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('461', '182', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('464', '183', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('463', '183', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('465', '184', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('466', '185', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('467', '185', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('468', '186', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('469', '186', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('470', '187', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('471', '187', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('472', '188', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('473', '188', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('475', '189', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('474', '189', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('476', '190', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('477', '191', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('479', '192', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('478', '192', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('480', '193', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('482', '194', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('481', '194', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('484', '195', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('483', '195', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('485', '196', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('486', '197', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('487', '198', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('488', '199', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('489', '200', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('490', '201', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('491', '201', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('492', '202', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('493', '203', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('494', '203', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('495', '204', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('496', '204', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('497', '205', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('498', '206', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('499', '206', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('500', '207', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('501', '207', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('502', '208', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('503', '208', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('504', '209', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('505', '209', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('506', '210', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('507', '210', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('508', '211', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('509', '211', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('510', '212', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('511', '213', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('512', '214', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('513', '215', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('514', '216', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('515', '216', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('517', '217', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('516', '217', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('518', '218', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('519', '218', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('520', '219', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('521', '220', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('522', '221', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('523', '222', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('524', '223', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('525', '223', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('526', '224', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('527', '224', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('528', '225', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('529', '226', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('530', '227', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('531', '228', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('532', '228', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('534', '229', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('533', '229', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('535', '230', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('536', '230', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('537', '231', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('538', '232', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('539', '233', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('540', '234', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('541', '234', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('542', '235', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('544', '236', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('543', '236', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('545', '237', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('546', '238', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('547', '239', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('548', '239', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('549', '240', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('550', '240', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('552', '241', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('551', '241', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('554', '242', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('553', '242', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('555', '243', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('556', '243', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('557', '244', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('558', '244', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('560', '245', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('559', '245', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('561', '246', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('563', '247', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('562', '247', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('564', '248', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('565', '249', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('566', '249', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('567', '250', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('569', '251', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('568', '251', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('570', '252', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('571', '252', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('573', '253', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('572', '253', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('574', '254', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('575', '255', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('576', '256', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('577', '257', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('578', '258', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('580', '259', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('579', '259', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('582', '260', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('581', '260', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('583', '261', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('584', '261', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('585', '262', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('586', '263', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('587', '264', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('588', '265', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('589', '266', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('590', '266', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('591', '267', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('592', '268', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('593', '268', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('594', '269', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('595', '269', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('597', '270', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('596', '270', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('598', '271', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('599', '271', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('600', '272', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('602', '273', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('601', '273', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('603', '274', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('605', '275', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('604', '275', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('606', '276', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('608', '277', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('607', '277', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('609', '278', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('610', '279', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('611', '280', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('612', '280', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('613', '281', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('615', '282', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('614', '282', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('616', '283', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('617', '284', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('618', '285', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('619', '285', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('620', '286', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('621', '286', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('623', '287', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('622', '287', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('625', '288', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('624', '288', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('626', '289', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('627', '289', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('628', '290', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('629', '290', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('631', '291', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('630', '291', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('632', '292', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('634', '293', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('633', '293', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('635', '294', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('636', '295', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('637', '295', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('638', '296', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('640', '297', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('639', '297', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('641', '298', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('642', '298', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('644', '299', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('643', '299', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('645', '300', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('646', '301', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('647', '302', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('648', '303', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('649', '304', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('651', '305', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('650', '305', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('653', '306', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('652', '306', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('654', '307', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('655', '307', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('656', '308', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('657', '309', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('658', '310', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('659', '311', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('660', '312', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('661', '312', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('662', '313', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('663', '314', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('664', '314', '8');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('665', '315', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('666', '315', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('668', '316', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('667', '316', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('669', '317', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('670', '317', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('671', '318', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('673', '319', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('672', '319', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('674', '320', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('676', '321', '4');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('675', '321', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('677', '322', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('678', '323', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('679', '323', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('680', '324', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('681', '324', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('683', '325', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('682', '325', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('685', '326', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('684', '326', '11');

-- 
-- Table structure for table `shelf`
-- 

DROP TABLE IF EXISTS `shelf`;
CREATE TABLE `shelf` (
  `shelf_id` int NOT NULL AUTO_INCREMENT,
  `shelf_sv_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `shelf_fi_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`shelf_id`),
  UNIQUE KEY `shelf_name` (`shelf_sv_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `shelf`
-- 

INSERT INTO `shelf` (`shelf_id`, `shelf_sv_name`, `shelf_fi_name`) VALUES ('1', 'Finlandssvenska', 'Suomenruotsalainen');
INSERT INTO `shelf` (`shelf_id`, `shelf_sv_name`, `shelf_fi_name`) VALUES ('3', 'Lokalhistoria', 'Paikallishistoria');
INSERT INTO `shelf` (`shelf_id`, `shelf_sv_name`, `shelf_fi_name`) VALUES ('4', 'Sjöfart', 'Merenkulku');
INSERT INTO `shelf` (`shelf_id`, `shelf_sv_name`, `shelf_fi_name`) VALUES ('5', 'Barn/Ungdom', 'Lapset/Nuoret');
INSERT INTO `shelf` (`shelf_id`, `shelf_sv_name`, `shelf_fi_name`) VALUES ('6', 'Musik', 'Musiikki');
INSERT INTO `shelf` (`shelf_id`, `shelf_sv_name`, `shelf_fi_name`) VALUES ('7', 'Film', 'Elokuva');

-- 
-- Table structure for table `status`
-- 

DROP TABLE IF EXISTS `status`;
CREATE TABLE `status` (
  `status_id` int NOT NULL AUTO_INCREMENT,
  `status_sv_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_fi_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `status_name` (`status_sv_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `status`
-- 

INSERT INTO `status` (`status_id`, `status_sv_name`, `status_fi_name`) VALUES ('1', 'Tillgänglig', 'Saatavilla');
INSERT INTO `status` (`status_id`, `status_sv_name`, `status_fi_name`) VALUES ('2', 'Såld', 'Myyty');

-- 
-- Table structure for table `user`
-- 

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `user_username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_role` int NOT NULL DEFAULT '3',
  `user_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_last_login` datetime DEFAULT NULL,
  `user_created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `user_is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_username` (`user_username`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `user`
-- 

INSERT INTO `user` (`user_id`, `user_username`, `user_password_hash`, `user_role`, `user_email`, `user_last_login`, `user_created_at`, `user_is_active`) VALUES ('1', 'Admin', '$2y$10$J0jSNdu1QUebZT4KRq6yTOkwFQ4DyyIqO8Lj/o5KZuSTXUQ1MgCgu', '1', 'admin@karisantikvariat.fi', '2025-05-06 10:04:21', '2025-04-10 10:41:05', '1');
INSERT INTO `user` (`user_id`, `user_username`, `user_password_hash`, `user_role`, `user_email`, `user_last_login`, `user_created_at`, `user_is_active`) VALUES ('3', 'Redaktor', '$2y$10$Qx1YgizfEOSuzTAp3r5bd.qfGJbMcXjdneHL9Ge9icsPbIsm5uicO', '2', 'redaktor@karisantikvariat.fi', '2025-05-06 10:30:18', '2025-04-30 13:57:26', '1');

COMMIT;
