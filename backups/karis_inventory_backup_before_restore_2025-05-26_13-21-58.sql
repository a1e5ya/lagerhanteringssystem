-- Database backup created by PHP on 2025-05-26 13:21:58
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
) ENGINE=InnoDB AUTO_INCREMENT=205 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('203', '1', 'batch_delete', 'Batch operation: 285 produkter har tagits bort.', '2025-05-26 13:21:31', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('204', '1', 'batch_delete', 'Batch operation: 285 produkter har tagits bort.', '2025-05-26 13:21:32', NULL);

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

INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('43', 'Första utgåvan Ulysses', '2', '1', '9', '2500.00', '4', 'Sällsynt första utgåva', 'Extremt sällsynt, har verifierats äkta', '1922', 'Sylvia Beach', '0', '0', '1', '2025-05-13 12:37:38', '1');
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
