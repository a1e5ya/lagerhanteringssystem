-- Database backup created by PHP on 2025-05-28 10:04:01
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
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `author`
-- 

INSERT INTO `author` (`author_id`, `author_name`) VALUES ('1', 'Väinö Linna');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('2', 'Mika Waltari');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('3', 'Aleksis Kivi');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('4', 'Sofi Oksanen');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('5', 'Arto Paasilinna');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('6', 'Astrid Lindgren');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('7', 'Stieg Larsson');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('8', 'Henning Mankell');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('9', 'Tove Jansson');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('10', 'Agatha Christie');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('11', 'Arthur Conan Doyle');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('12', 'J.R.R. Tolkien');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('13', 'George Orwell');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('14', 'Jane Austen');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('15', 'Charles Dickens');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('16', 'William Shakespeare');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('17', 'Ernest Hemingway');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('18', 'F. Scott Fitzgerald');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('19', 'Harper Lee');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('20', 'Mark Twain');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('21', 'Leo Tolstoy');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('22', 'Fyodor Dostoevsky');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('23', 'Franz Kafka');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('24', 'Virginia Woolf');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('25', 'James Joyce');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('26', 'Roald Dahl');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('27', 'Lewis Carroll');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('28', 'C.S. Lewis');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('29', 'Ray Bradbury');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('30', 'Isaac Asimov');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('31', 'Jean Sibelius');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('32', 'The Beatles');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('33', 'Elvis Presley');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('34', 'Bob Dylan');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('35', 'ABBA');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('36', 'Pink Floyd');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('37', 'Queen');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('38', 'Led Zeppelin');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('39', 'David Bowie');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('40', 'The Rolling Stones');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('41', 'Juice Leskinen');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('42', 'Eppu Normaali');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('43', 'Dingo');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('44', 'Hassisen Kone');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('45', 'Leevi and the Leavings');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('46', 'Aki Kaurismäki');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('47', 'Ingmar Bergman');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('48', 'Steven Spielberg');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('49', 'Christopher Nolan');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('50', 'Alfred Hitchcock');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('51', 'Hergé');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('52', 'René Goscinny');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('53', 'Albert Uderzo');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('54', 'Carl Barks');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('55', 'Don Rosa');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('56', 'Stan Lee');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('57', 'Jack Kirby');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('58', 'Alan Moore');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('59', 'Frank Miller');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('60', 'Neil Gaiman');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('61', 'Art Spiegelman');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('62', 'Marjane Satrapi');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('63', 'Mauri Kunnas');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('64', 'Turk');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('65', 'Peyo');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('66', 'Aleksandra M');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('67', 'ggg');
INSERT INTO `author` (`author_id`, `author_name`) VALUES ('68', 'Aleksandra Maurina');

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
) ENGINE=InnoDB AUTO_INCREMENT=304 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('203', '1', 'logout', 'User logged out: Admin', '2025-05-27 08:58:34', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('204', '1', 'login', 'Successful login for user: Admin', '2025-05-27 10:41:47', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('205', '1', 'logout', 'User logged out: Admin', '2025-05-27 11:01:50', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('206', '1', 'login', 'Successful login for user: Admin', '2025-05-27 11:01:55', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('211', '1', 'batch_update_status', 'Batch operation: 30 produkter har fått ny status.', '2025-05-27 12:38:20', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('212', '1', 'batch_update_status', 'Batch operation: 30 produkter har fått ny status.', '2025-05-27 12:38:20', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('213', '1', 'batch_delete', 'Batch operation: 3 produkter har tagits bort.', '2025-05-27 12:38:33', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('214', '1', 'batch_delete', 'Batch operation: 3 produkter har tagits bort.', '2025-05-27 12:38:33', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('215', '1', 'batch_set_rare', 'Batch operation: 2 produkter markerade som sällsynta.', '2025-05-27 12:38:37', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('216', '1', 'batch_set_rare', 'Batch operation: 2 produkter markerade som sällsynta.', '2025-05-27 12:38:37', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('217', '1', 'batch_update_price', 'Batch operation: 6 produkter uppdaterade med nytt pris.', '2025-05-27 12:38:47', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('218', '1', 'batch_update_price', 'Batch operation: 6 produkter uppdaterade med nytt pris.', '2025-05-27 12:38:47', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('221', '1', 'logout', 'User logged out: Admin', '2025-05-27 12:52:55', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('222', '1', 'login', 'Successful login for user: Admin', '2025-05-27 12:53:00', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('224', '1', 'batch_set_rare', 'Batch operation: 21 produkter markerade som sällsynta.', '2025-05-27 12:54:23', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('225', '1', 'batch_set_rare', 'Batch operation: 21 produkter markerade som sällsynta.', '2025-05-27 12:54:23', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('226', '1', 'batch_set_rare', 'Batch operation: 21 produkter tog bort sällsynt-markeringen från.', '2025-05-27 12:54:28', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('227', '1', 'batch_set_rare', 'Batch operation: 21 produkter tog bort sällsynt-markeringen från.', '2025-05-27 12:54:28', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('228', '1', 'logout', 'User logged out: Admin', '2025-05-27 12:56:42', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('229', '1', 'login', 'Successful login for user: Admin', '2025-05-27 12:56:47', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('230', '1', 'update_subscriber', 'Newsletter subscriber deactivated (ID: 19)', '2025-05-27 12:59:46', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('231', '1', 'delete_subscriber', 'Newsletter subscriber deleted: lesya.maurin@gmail.com', '2025-05-27 13:00:19', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('232', '1', 'update_subscriber', 'Newsletter subscriber deactivated (ID: 1)', '2025-05-27 13:01:02', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('233', '1', 'create_author', 'Author created: Aleksandra Maurina', '2025-05-27 13:16:45', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('234', '1', 'update_author', 'Author updated: \'Aleksandra Maurina\' to \'Aleksandra Maurinaa\'', '2025-05-27 13:16:56', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('235', '1', 'update_author', 'Author updated: \'Aleksandra Maurinaa\' to \'Aleksandra Maurinaa\'', '2025-05-27 13:16:56', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('236', '1', 'create_author', 'Author created: Aleksandra Maurina', '2025-05-27 13:17:05', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('237', '1', 'delete_author', 'Author deleted: Aleksandra Maurina', '2025-05-27 13:17:19', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('238', '1', 'delete_author', 'Author deleted: Aleksandra Maurinaa', '2025-05-27 13:17:21', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('239', '1', 'create_author', 'Author created: Aleksandra Maurina', '2025-05-27 13:17:23', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('240', '1', 'update_author', 'Author updated: \'Aleksandra Maurina\' to \'Aleksandra Maurina\'', '2025-05-27 13:17:46', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('241', '1', 'update_author', 'Author updated: \'Aleksandra Maurina\' to \'Aleksandra Maurinaa\'', '2025-05-27 13:17:54', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('242', '1', 'create_author', 'Author created: Aleksandra Maurina', '2025-05-27 13:17:57', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('243', '1', 'delete_author', 'Author deleted: Aleksandra Maurinaa', '2025-05-27 13:18:01', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('244', '1', 'logout', 'User logged out: Admin', '2025-05-27 13:32:08', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('245', '3', 'login', 'Successful login for user: Redaktor', '2025-05-27 13:32:13', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('247', '3', 'logout', 'User logged out: Redaktor', '2025-05-27 13:32:28', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('248', '1', 'login', 'Successful login for user: Admin', '2025-05-27 13:32:34', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('249', '1', 'logout', 'User logged out: Admin', '2025-05-27 13:41:34', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('250', '3', 'login', 'Successful login for user: Redaktor', '2025-05-27 13:41:40', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('251', '3', 'logout', 'User logged out: Redaktor', '2025-05-27 13:42:14', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('252', '1', 'login', 'Successful login for user: Admin', '2025-05-27 13:42:19', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('256', '1', 'logout', 'User logged out: Admin', '2025-05-27 19:28:53', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('257', '1', 'login', 'Successful login for user: Admin', '2025-05-27 19:28:57', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('258', '1', 'create_author', 'Author created: Aleksandra Maurina 2', '2025-05-27 20:21:18', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('259', '1', 'delete_image', 'Raderade produktbild med ID: 4', '2025-05-27 20:41:05', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('267', '1', 'update', 'Uppdaterade produkt: 1984', '2025-05-28 00:37:59', '38');
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('268', '1', 'update', 'Uppdaterade produkt: I, Robot', '2025-05-28 00:39:47', '64');
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('269', '1', 'update', 'Uppdaterade produkt: Afrikan tähti', '2025-05-28 00:42:16', '142');
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('270', '1', 'update', 'Uppdaterade produkt: Dungeons & Dragons Basic Set', '2025-05-28 00:42:52', '145');
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('271', '1', 'update', 'Uppdaterade produkt: Monopol Helsingfors edition', '2025-05-28 00:43:47', '141');
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('272', '1', 'update', 'Uppdaterade produkt: Trivial Pursuit Svenska', '2025-05-28 00:44:10', '143');
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('273', '1', 'logout', 'User logged out: Admin', '2025-05-28 05:39:20', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('274', '1', 'login', 'Successful login for user: Admin', '2025-05-28 06:11:54', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('275', '1', 'create', 'Skapade produkt: AAAA', '2025-05-28 06:12:21', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('276', '1', 'batch_delete', 'Batch operation: 1 produkter har tagits bort.', '2025-05-28 06:13:33', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('277', '1', 'batch_set_special_price', 'Batch operation: 7 produkter markerade som rea.', '2025-05-28 06:24:11', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('278', '1', 'batch_set_special_price', 'Batch operation: 3 produkter markerade som rea.', '2025-05-28 06:24:31', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('279', '1', 'logout', 'User logged out: Admin', '2025-05-28 07:53:40', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('280', '1', 'login', 'Successful login for user: Admin', '2025-05-28 07:56:15', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('281', '1', 'update', 'Uppdaterade produkt: Farlig midsommar', '2025-05-28 07:57:05', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('282', '1', 'update', 'Uppdaterade produkt: Pappan och havet', '2025-05-28 07:57:48', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('283', '1', 'update', 'Uppdaterade produkt: Trollvinter', '2025-05-28 07:58:34', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('284', '1', 'create', 'Skapade produkt: Product', '2025-05-28 08:06:45', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('285', '1', 'batch_delete', 'Batch operation: 1 produkter har tagits bort.', '2025-05-28 08:10:34', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('286', '1', 'logout', 'User logged out: Admin', '2025-05-28 08:20:52', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('287', '1', 'login', 'Successful login for user: Admin', '2025-05-28 08:29:44', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('288', '1', 'update', 'Uppdaterade produkt: 1984', '2025-05-28 08:32:10', '38');
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('289', '1', 'logout', 'User logged out: Admin', '2025-05-28 08:37:55', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('290', '3', 'login', 'Successful login for user: Redaktor', '2025-05-28 09:26:19', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('291', '3', 'sell', 'Produkt markerad som såld: 1984', '2025-05-28 09:26:42', '38');
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('292', '3', 'return', 'Produkt återställd till tillgänglig: 1984', '2025-05-28 09:26:45', '38');
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('293', '3', 'update', 'Uppdaterade produkt: Antique book binding tools', '2025-05-28 09:28:45', '113');
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('294', '3', 'create', 'Skapade produkt: Produkt', '2025-05-28 09:35:30', '153');
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('295', '3', 'update', 'Uppdaterade produkt: A Night at the Opera', '2025-05-28 09:37:47', '79');
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('296', '3', 'create_author', 'Author created: Aleksandra Maurina', '2025-05-28 09:38:25', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('297', '3', 'update_author', 'Author updated: \'Aleksandra\' to \'Aleksandra M\'', '2025-05-28 09:46:19', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('298', '3', 'batch_set_recommended', 'Batch operation: 77 produkter tog bort rekommendation från.', '2025-05-28 09:48:42', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('299', '3', 'logout', 'User logged out: Redaktor', '2025-05-28 10:00:54', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('300', '1', 'login', 'Successful login for user: Admin', '2025-05-28 10:01:18', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('301', '1', 'sell', 'Produkt markerad som såld: Emil i Lönneberga', '2025-05-28 10:01:36', '18');
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('302', '1', 'batch_update_status', 'Batch operation: 3 produkter har fått ny status.', '2025-05-28 10:01:58', NULL);
INSERT INTO `event_log` (`event_id`, `user_id`, `event_type`, `event_description`, `event_timestamp`, `product_id`) VALUES ('303', '1', 'batch_delete', 'Batch operation: 12 produkter har tagits bort.', '2025-05-28 10:03:29', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=284 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `image`
-- 

INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('1', '1', 'assets/uploads/products/ka-book-01.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('2', '2', 'assets/uploads/products/ka-book-02.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('3', '3', 'assets/uploads/products/ka-book-03.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('4', '4', 'assets/uploads/products/ka-book-04.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('5', '5', 'assets/uploads/products/ka-book-05.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('6', '6', 'assets/uploads/products/ka-book-06.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('7', '7', 'assets/uploads/products/ka-book-07.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('8', '8', 'assets/uploads/products/ka-book-08.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('9', '9', 'assets/uploads/products/ka-book-09.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('10', '10', 'assets/uploads/products/ka-book-10.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('11', '11', 'assets/uploads/products/ka-book-11.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('12', '12', 'assets/uploads/products/ka-book-12.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('13', '13', 'assets/uploads/products/ka-book-13.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('14', '14', 'assets/uploads/products/ka-book-14.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('15', '15', 'assets/uploads/products/ka-book-15.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('16', '16', 'assets/uploads/products/ka-child-01.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('17', '17', 'assets/uploads/products/ka-child-02.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('18', '18', 'assets/uploads/products/ka-child-03.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('19', '19', 'assets/uploads/products/ka-child-04.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('20', '20', 'assets/uploads/products/ka-book-16.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('21', '21', 'assets/uploads/products/ka-book-17.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('22', '22', 'assets/uploads/products/ka-book-18.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('23', '23', 'assets/uploads/products/ka-book-19.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('24', '24', 'assets/uploads/products/ka-book-20.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('25', '25', 'assets/uploads/products/ka-book-21.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('31', '31', 'assets/uploads/products/ka-book-22.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('32', '32', 'assets/uploads/products/ka-book-23.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('33', '33', 'assets/uploads/products/ka-book-24.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('34', '34', 'assets/uploads/products/ka-book-25.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('35', '35', 'assets/uploads/products/ka-book-26.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('36', '36', 'assets/uploads/products/ka-book-27.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('37', '37', 'assets/uploads/products/ka-book-28.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('38', '38', 'assets/uploads/products/ka-book-29.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('39', '39', 'assets/uploads/products/ka-book-30.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('40', '40', 'assets/uploads/products/ka-book-31.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('41', '41', 'assets/uploads/products/ka-book-32.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('42', '42', 'assets/uploads/products/ka-book-33.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('43', '43', 'assets/uploads/products/ka-book-34.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('44', '44', 'assets/uploads/products/ka-book-35.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('45', '45', 'assets/uploads/products/ka-book-36.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('46', '46', 'assets/uploads/products/ka-book-37.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('47', '47', 'assets/uploads/products/ka-book-38.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('48', '48', 'assets/uploads/products/ka-book-39.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('49', '49', 'assets/uploads/products/ka-random-01.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('51', '51', 'assets/uploads/products/ka-random-02.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('52', '52', 'assets/uploads/products/ka-random-03.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('53', '53', 'assets/uploads/products/ka-random-04.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('54', '54', 'assets/uploads/products/ka-random-05.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('55', '55', 'assets/uploads/products/ka-random-06.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('56', '56', 'assets/uploads/products/ka-random-07.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('61', '61', 'assets/uploads/products/ka-random-08.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('62', '62', 'assets/uploads/products/ka-random-09.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('63', '63', 'assets/uploads/products/ka-random-10.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('64', '64', 'assets/uploads/products/ka-random-11.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('65', '65', 'assets/uploads/products/ka-random-12.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('66', '66', 'assets/uploads/products/ka-cd-01.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('67', '67', 'assets/uploads/products/ka-cd-02.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('68', '68', 'assets/uploads/products/ka-cd-03.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('69', '69', 'assets/uploads/products/ka-cd-04.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('70', '70', 'assets/uploads/products/ka-cd-05.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('71', '71', 'assets/uploads/products/ka-cd-06.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('72', '72', 'assets/uploads/products/ka-cd-07.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('73', '73', 'assets/uploads/products/ka-cd-08.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('74', '74', 'assets/uploads/products/ka-cd-09.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('75', '75', 'assets/uploads/products/ka-cd-10.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('76', '76', 'assets/uploads/products/ka-cd-11.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('77', '77', 'assets/uploads/products/ka-cd-12.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('78', '78', 'assets/uploads/products/ka-cd-13.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('79', '79', 'assets/uploads/products/ka-cd-14.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('80', '80', 'assets/uploads/products/ka-cd-15.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('81', '81', 'assets/uploads/products/ka-cd-16.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('82', '82', 'assets/uploads/products/ka-cd-17.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('83', '83', 'assets/uploads/products/ka-cd-18.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('84', '84', 'assets/uploads/products/ka-cd-19.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('85', '85', 'assets/uploads/products/ka-random-13.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('86', '86', 'assets/uploads/products/ka-random-14.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('87', '87', 'assets/uploads/products/ka-random-15.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('88', '88', 'assets/uploads/products/ka-random-16.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('89', '89', 'assets/uploads/products/ka-random-17.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('90', '90', 'assets/uploads/products/ka-random-18.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('91', '91', 'assets/uploads/products/ka-random-19.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('92', '92', 'assets/uploads/products/ka-random-20.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('93', '93', 'assets/uploads/products/ka-random-21.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('94', '94', 'assets/uploads/products/ka-random-22.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('95', '95', 'assets/uploads/products/ka-random-23.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('96', '96', 'assets/uploads/products/ka-random-24.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('97', '97', 'assets/uploads/products/ka-random-25.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('98', '98', 'assets/uploads/products/ka-random-26.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('99', '99', 'assets/uploads/products/ka-random-27.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('100', '100', 'assets/uploads/products/ka-random-28.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('101', '101', 'assets/uploads/products/ka-object-01.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('102', '102', 'assets/uploads/products/ka-object-02.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('103', '103', 'assets/uploads/products/ka-object-03.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('104', '104', 'assets/uploads/products/ka-object-04.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('105', '105', 'assets/uploads/products/ka-object-05.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('106', '106', 'assets/uploads/products/ka-object-06.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('107', '107', 'assets/uploads/products/ka-object-07.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('108', '108', 'assets/uploads/products/ka-object-08.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('109', '109', 'assets/uploads/products/ka-object-09.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('110', '110', 'assets/uploads/products/ka-object-10.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('111', '111', 'assets/uploads/products/ka-object-11.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('112', '112', 'assets/uploads/products/ka-object-12.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('113', '113', 'assets/uploads/products/ka-random-29.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('114', '114', 'assets/uploads/products/ka-random-30.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('115', '115', 'assets/uploads/products/ka-random-31.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('116', '116', 'assets/uploads/products/ka-child-15.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('117', '117', 'assets/uploads/products/ka-child-16.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('118', '118', 'assets/uploads/products/ka-child-17.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('119', '119', 'assets/uploads/products/ka-child-18.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('121', '121', 'assets/uploads/products/ka-child-20.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('122', '122', 'assets/uploads/products/ka-child-21.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('123', '123', 'assets/uploads/products/ka-child-22.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('124', '124', 'assets/uploads/products/ka-random-32.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('125', '125', 'assets/uploads/products/ka-random-33.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('126', '126', 'assets/uploads/products/ka-random-34.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('127', '127', 'assets/uploads/products/ka-random-35.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('128', '128', 'assets/uploads/products/ka-random-36.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('129', '129', 'assets/uploads/products/ka-random-37.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('131', '131', 'assets/uploads/products/ka-random-39.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('132', '132', 'assets/uploads/products/ka-random-40.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('133', '133', 'assets/uploads/products/ka-random-41.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('134', '134', 'assets/uploads/products/ka-random-42.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('135', '135', 'assets/uploads/products/ka-random-43.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('136', '136', 'assets/uploads/products/ka-random-44.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('137', '137', 'assets/uploads/products/ka-random-45.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('138', '138', 'assets/uploads/products/ka-random-46.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('139', '139', 'assets/uploads/products/ka-random-47.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('140', '140', 'assets/uploads/products/ka-random-48.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('141', '141', 'assets/uploads/products/ka-spel-1.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('142', '142', 'assets/uploads/products/ka-spel-2.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('143', '143', 'assets/uploads/products/ka-spel-3.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('144', '144', 'assets/uploads/products/ka-spel-4.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('145', '145', 'assets/uploads/products/ka-random-49.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('146', '146', 'assets/uploads/products/ka-random-50.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('147', '147', 'assets/uploads/products/ka-random-51.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('148', '148', 'assets/uploads/products/ka-random-52.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('149', '149', 'assets/uploads/products/ka-random-53.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('150', '150', 'assets/uploads/products/ka-random-54.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('151', '1', 'assets/uploads/products/ka-random-55.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('152', '1', 'assets/uploads/products/ka-random-56.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('153', '1', 'assets/uploads/products/ka-random-57.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('154', '3', 'assets/uploads/products/ka-random-58.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('155', '3', 'assets/uploads/products/ka-random-59.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('156', '5', 'assets/uploads/products/ka-random-60.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('157', '5', 'assets/uploads/products/ka-random-61.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('158', '5', 'assets/uploads/products/ka-random-62.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('159', '5', 'assets/uploads/products/ka-random-63.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('160', '8', 'assets/uploads/products/ka-random-64.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('161', '11', 'assets/uploads/products/ka-random-65.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('162', '11', 'assets/uploads/products/ka-random-66.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('163', '11', 'assets/uploads/products/ka-random-67.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('164', '15', 'assets/uploads/products/ka-random-68.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('165', '15', 'assets/uploads/products/ka-random-69.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('166', '17', 'assets/uploads/products/ka-random-70.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('167', '17', 'assets/uploads/products/ka-random-71.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('168', '17', 'assets/uploads/products/ka-random-72.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('169', '17', 'assets/uploads/products/ka-random-73.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('170', '20', 'assets/uploads/products/ka-random-74.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('171', '20', 'assets/uploads/products/ka-random-75.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('172', '22', 'assets/uploads/products/ka-random-76.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('173', '22', 'assets/uploads/products/ka-random-77.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('174', '22', 'assets/uploads/products/ka-random-78.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('175', '25', 'assets/uploads/products/ka-random-79.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('182', '33', 'assets/uploads/products/ka-random-86.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('183', '33', 'assets/uploads/products/ka-random-87.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('184', '33', 'assets/uploads/products/ka-random-88.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('185', '36', 'assets/uploads/products/ka-random-89.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('186', '37', 'assets/uploads/products/ka-random-90.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('187', '37', 'assets/uploads/products/ka-random-91.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('188', '37', 'assets/uploads/products/ka-random-92.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('189', '37', 'assets/uploads/products/ka-random-93.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('190', '40', 'assets/uploads/products/ka-random-94.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('191', '40', 'assets/uploads/products/ka-random-95.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('192', '42', 'assets/uploads/products/ka-random-96.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('193', '42', 'assets/uploads/products/ka-random-97.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('194', '42', 'assets/uploads/products/ka-random-98.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('195', '46', 'assets/uploads/products/ka-random-99.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('196', '48', 'assets/uploads/products/ka-random-100.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('197', '48', 'assets/uploads/products/ka-random-101.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('198', '48', 'assets/uploads/products/ka-random-102.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('199', '48', 'assets/uploads/products/ka-random-103.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('200', '51', 'assets/uploads/products/ka-random-104.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('201', '51', 'assets/uploads/products/ka-random-105.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('202', '55', 'assets/uploads/products/ka-random-106.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('203', '55', 'assets/uploads/products/ka-random-107.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('204', '55', 'assets/uploads/products/ka-random-108.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('206', '62', 'assets/uploads/products/ka-random-110.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('207', '62', 'assets/uploads/products/ka-random-111.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('208', '62', 'assets/uploads/products/ka-random-112.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('209', '62', 'assets/uploads/products/ka-random-113.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('210', '67', 'assets/uploads/products/ka-random-114.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('211', '67', 'assets/uploads/products/ka-random-115.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('212', '70', 'assets/uploads/products/ka-random-116.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('213', '70', 'assets/uploads/products/ka-random-117.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('214', '70', 'assets/uploads/products/ka-random-118.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('215', '74', 'assets/uploads/products/ka-random-119.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('216', '77', 'assets/uploads/products/ka-random-120.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('217', '77', 'assets/uploads/products/ka-random-121.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('218', '77', 'assets/uploads/products/ka-random-122.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('219', '77', 'assets/uploads/products/ka-random-123.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('220', '79', 'assets/uploads/products/ka-random-124.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('221', '79', 'assets/uploads/products/ka-random-125.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('222', '80', 'assets/uploads/products/ka-random-126.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('223', '80', 'assets/uploads/products/ka-random-127.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('224', '80', 'assets/uploads/products/ka-random-128.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('225', '85', 'assets/uploads/products/ka-random-129.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('226', '92', 'assets/uploads/products/ka-random-130.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('227', '92', 'assets/uploads/products/ka-random-131.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('228', '92', 'assets/uploads/products/ka-random-132.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('229', '92', 'assets/uploads/products/ka-random-133.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('230', '95', 'assets/uploads/products/ka-random-134.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('231', '95', 'assets/uploads/products/ka-random-135.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('232', '99', 'assets/uploads/products/ka-random-136.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('233', '99', 'assets/uploads/products/ka-random-137.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('234', '99', 'assets/uploads/products/ka-random-138.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('235', '101', 'assets/uploads/products/ka-random-139.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('236', '104', 'assets/uploads/products/ka-random-140.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('237', '104', 'assets/uploads/products/ka-random-141.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('238', '104', 'assets/uploads/products/ka-random-142.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('239', '104', 'assets/uploads/products/ka-random-143.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('240', '107', 'assets/uploads/products/ka-random-144.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('241', '107', 'assets/uploads/products/ka-random-145.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('242', '110', 'assets/uploads/products/ka-random-146.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('243', '110', 'assets/uploads/products/ka-random-147.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('244', '110', 'assets/uploads/products/ka-random-148.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('245', '115', 'assets/uploads/products/ka-random-149.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('246', '118', 'assets/uploads/products/ka-random-150.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('247', '118', 'assets/uploads/products/ka-random-151.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('248', '118', 'assets/uploads/products/ka-random-152.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('249', '118', 'assets/uploads/products/ka-random-153.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('250', '121', 'assets/uploads/products/ka-random-154.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('251', '121', 'assets/uploads/products/ka-random-155.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('252', '125', 'assets/uploads/products/ka-random-156.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('253', '125', 'assets/uploads/products/ka-random-157.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('254', '125', 'assets/uploads/products/ka-random-158.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('255', '128', 'assets/uploads/products/ka-random-159.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('256', '134', 'assets/uploads/products/ka-random-160.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('257', '134', 'assets/uploads/products/ka-random-161.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('258', '134', 'assets/uploads/products/ka-random-162.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('259', '134', 'assets/uploads/products/ka-random-163.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('260', '137', 'assets/uploads/products/ka-random-164.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('261', '137', 'assets/uploads/products/ka-random-165.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('262', '142', 'assets/uploads/products/ka-random-166.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('263', '142', 'assets/uploads/products/ka-random-167.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('264', '142', 'assets/uploads/products/ka-random-168.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('265', '145', 'assets/uploads/products/ka-random-169.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('266', '147', 'assets/uploads/products/ka-random-170.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('267', '147', 'assets/uploads/products/ka-random-171.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('268', '147', 'assets/uploads/products/ka-random-172.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('269', '147', 'assets/uploads/products/ka-random-173.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('270', '149', 'assets/uploads/products/ka-random-174.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('271', '149', 'assets/uploads/products/ka-random-175.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('272', '150', 'assets/uploads/products/ka-random-176.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('273', '150', 'assets/uploads/products/ka-random-177.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('274', '150', 'assets/uploads/products/ka-random-178.webp', '2025-05-28 06:35:00');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('279', '38', 'assets/uploads/products/38_68369fd9d2f1b_1748410329.webp', '2025-05-28 08:32:10');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('280', '153', 'assets/uploads/products/153_6836aeb20bf68_1748414130.webp', '2025-05-28 09:35:31');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('281', '153', 'assets/uploads/products/153_6836aeb32605b_1748414131.webp', '2025-05-28 09:35:31');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('282', '153', 'assets/uploads/products/153_6836aeb3e7e61_1748414131.webp', '2025-05-28 09:35:32');
INSERT INTO `image` (`image_id`, `prod_id`, `image_path`, `image_uploaded_at`) VALUES ('283', '153', 'assets/uploads/products/153_6836aeb4d4d82_1748414132.webp', '2025-05-28 09:35:33');

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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `newsletter_subscriber`
-- 

INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('1', 'johanna.karlsson@example.com', 'Johanna Karlsson', '2025-05-13 12:37:38', '0', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('2', 'mikko.nieminen@example.fi', 'Mikko Nieminen', '2025-05-13 12:37:38', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('3', 'anna.lindholm@example.com', 'Anna Lindholm', '2025-05-13 12:37:38', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('4', 'erik.johansson@example.se', 'Erik Johansson', '2025-05-13 12:37:38', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('5', 'liisa.makinen@example.fi', 'Liisa Mäkinen', '2025-05-13 12:37:38', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('6', 'bengt.gustafsson@example.com', 'Bengt Gustafsson', '2025-05-13 12:37:38', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('7', 'aino.virtanen@gmail.com', 'Aino Virtanen', '2025-01-15 10:30:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('8', 'lars.andersson@hotmail.com', 'Lars Andersson', '2025-01-18 14:22:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('9', 'maria.korhonen@yahoo.fi', 'Maria Korhonen', '2025-01-20 09:15:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('10', 'erik.johansson@gmail.com', 'Erik Johansson', '2025-01-22 16:45:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('11', 'helena.nieminen@outlook.com', 'Helena Nieminen', '2025-01-25 11:30:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('12', 'sven.karlsson@telia.com', 'Sven Karlsson', '2025-01-28 13:20:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('13', 'liisa.hakkarainen@gmail.com', 'Liisa Hakkarainen', '2025-02-01 10:00:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('14', 'anna.lindberg@hotmail.se', 'Anna Lindberg', '2025-02-03 15:30:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('15', 'mikko.salminen@elisa.fi', 'Mikko Salminen', '2025-02-05 12:45:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('16', 'ingrid.gustafsson@gmail.com', 'Ingrid Gustafsson', '2025-02-08 09:20:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('17', 'kari.laine@luukku.com', 'Kari Laine', '2025-02-10 14:15:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('18', 'gunnar.pettersson@spray.se', 'Gunnar Pettersson', '2025-02-12 16:00:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('19', 'tuula.heikkinen@gmail.com', 'Tuula Heikkinen', '2025-02-15 11:45:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('20', 'astrid.nilsson@yahoo.se', 'Astrid Nilsson', '2025-02-17 13:30:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('21', 'pekka.virtanen@saunalahti.fi', 'Pekka Virtanen', '2025-02-20 10:20:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('22', 'margareta.holm@telia.se', 'Margareta Holm', '2025-02-22 15:10:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('23', 'ritva.korhonen@kolumbus.fi', 'Ritva Korhonen', '2025-02-25 12:00:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('24', 'nils.berg@gmail.com', 'Nils Berg', '2025-02-27 14:30:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('25', 'riitta.makinen@hotmail.com', 'Riitta Mäkinen', '2025-03-01 09:45:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('26', 'bengt.larsson@bredband.net', 'Bengt Larsson', '2025-03-03 16:20:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('27', 'maija.koskinen@gmail.com', 'Maija Koskinen', '2025-03-05 11:15:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('28', 'olof.stromberg@yahoo.se', 'Olof Strömberg', '2025-03-08 13:40:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('29', 'elina.saarinen@elisa.fi', 'Elina Saarinen', '2025-03-10 10:30:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('30', 'birgitta.hansson@gmail.com', 'Birgitta Hansson', '2025-03-12 15:50:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('31', 'jukka.rantala@luukku.com', 'Jukka Rantala', '2025-03-15 12:25:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('32', 'rolf.danielsson@telia.com', 'Rolf Danielsson', '2025-03-17 14:10:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('33', 'sirpa.lahtinen@gmail.com', 'Sirpa Lahtinen', '2025-03-20 09:35:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('34', 'ulla.hedberg@hotmail.se', 'Ulla Hedberg', '2025-03-22 16:15:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('35', 'paavo.kallio@saunalahti.fi', 'Paavo Kallio', '2025-03-25 11:50:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('36', 'gustav.lindqvist@gmail.com', 'Gustav Lindqvist', '2025-03-27 13:25:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('37', 'anneli.harju@kolumbus.fi', 'Anneli Harju', '2025-04-01 10:40:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('38', 'torsten.wickman@yahoo.se', 'Torsten Wickman', '2025-04-03 15:05:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('39', 'kirsti.aalto@elisa.fi', 'Kirsti Aalto', '2025-04-05 12:20:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('40', 'ragnar.sundberg@telia.se', 'Ragnar Sundberg', '2025-04-08 14:45:00', '1', 'sv');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('41', 'terttu.virtanen@gmail.com', 'Terttu Virtanen', '2025-04-10 09:55:00', '1', 'fi');
INSERT INTO `newsletter_subscriber` (`subscriber_id`, `subscriber_email`, `subscriber_name`, `subscribed_date`, `subscriber_is_active`, `subscriber_language_pref`) VALUES ('43', 'lesya.maurin@gmail.com', 'Alexandra', '2025-05-28 09:23:02', '1', 'sv');

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
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `product`
-- 

INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('1', 'Tuntematon sotilas', '1', '1', '1', '24.50', '2', 'Klassisk krigsroman', 'Mycket populär', '1954', 'WSOY', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('2', 'Täällä Pohjantähden alla', '1', '1', '1', '28.90', '1', 'Trilogi del 1', NULL, '1959', 'WSOY', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('3', 'Sinuhe egyptiläinen', '1', '1', '1', '26.50', '2', 'Historisk roman', 'Fint skick', '1945', 'WSOY', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('4', 'Seitsemän veljestä', '1', '1', '1', '22.00', '3', 'Finlands nationalroman', NULL, '1870', 'SKS', '0', '0', '1', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('5', 'Purge', '1', '1', '1', '23.50', '1', 'Prisbelönt', 'Internationell succé', '2008', 'Otava', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('6', 'Koirankynnen leikkaaja', '1', '1', '1', '19.90', '2', 'Modern finsk prosa', NULL, '1980', 'Otava', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('7', 'Elämä lyhyt, Rytkönen pitkä', '1', '1', '1', '18.50', '2', 'Komisk roman', NULL, '1994', 'WSOY', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('8', 'Hurmaava joukkoitsemurha', '1', '1', '1', '17.90', '1', 'Satiirinen komedia', NULL, '1990', 'WSOY', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('9', 'Suloinen myrkynkeittäjä', '1', '1', '1', '16.50', '2', 'Huumori', NULL, '1988', 'WSOY', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('10', 'Paholaisen haarukka', '1', '1', '1', '18.90', '1', 'Komedia', NULL, '1991', 'WSOY', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('11', 'Hytti nro 6', '1', '1', '1', '21.50', '1', 'Finlandia-pristagare', NULL, '2011', 'Otava', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('12', 'Norma', '1', '1', '1', '20.50', '2', 'Samtida finsk litteratur', NULL, '2015', 'Otava', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('13', 'Kun kyyhkyset katosivat', '1', '1', '1', '19.50', '1', 'Finlandia-pristagare', NULL, '2012', 'Otava', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('14', 'Baby Jane', '1', '1', '1', '22.50', '2', 'Modern finsk roman', NULL, '2005', 'Tammi', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('15', 'Käsky', '1', '1', '1', '18.50', '1', 'Psykologisk thriller', NULL, '2003', 'Tammi', '0', '0', '0', '2025-05-28 00:26:26', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('16', 'Pippi Långstrump', '2', '5', '1', '16.90', '1', 'Barnklassiker', 'Populär bland barn', '1945', 'Rabén & Sjögren', '0', '0', '0', '2025-05-28 00:26:26', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('17', 'Ronja rövardotter', '2', '5', '1', '18.50', '2', 'Fantasy för barn', NULL, '1981', 'Rabén & Sjögren', '1', '0', '0', '2025-05-28 00:26:26', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('18', 'Emil i Lönneberga', '2', '5', '1', '15.90', '1', 'Barnbok', NULL, '1963', 'Rabén & Sjögren', '0', '0', '0', '2025-05-28 00:26:26', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('19', 'Karlsson på taket', '2', '5', '1', '17.50', '2', 'Klassisk barnbok', NULL, '1955', 'Rabén & Sjögren', '1', '0', '0', '2025-05-28 00:26:26', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('20', 'Män som hatar kvinnor', '1', '1', '1', '24.50', '2', 'Millennium trilogi del 1', 'Mycket populär', '2005', 'Norstedts', '0', '0', '0', '2025-05-28 00:26:26', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('21', 'Flickan som lekte med elden', '1', '1', '1', '23.50', '1', 'Millennium trilogi del 2', NULL, '2006', 'Norstedts', '0', '0', '0', '2025-05-28 00:26:26', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('22', 'Luftslottet som sprängdes', '1', '1', '1', '22.90', '2', 'Millennium trilogi del 3', NULL, '2007', 'Norstedts', '0', '0', '0', '2025-05-28 00:26:26', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('23', 'Faceless Killers', '1', '1', '1', '21.50', '2', 'Första Wallander', NULL, '1991', 'Harvill', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('24', 'The White Lioness', '1', '1', '1', '20.50', '1', 'Wallander serie', NULL, '1993', 'Harvill', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('25', 'One Step Behind', '1', '1', '1', '19.90', '2', 'Wallander deckare', NULL, '1997', 'Harvill', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('31', 'Murder on the Orient Express', '1', '1', '1', '16.50', '2', 'Poirot klassiker', NULL, '1934', 'Collins', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('32', 'And Then There Were None', '1', '1', '1', '17.90', '1', 'Christie mästerverk', NULL, '1939', 'Collins', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('33', 'The Murder of Roger Ackroyd', '1', '1', '1', '15.50', '2', 'Poirot mysterium', NULL, '1926', 'Collins', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('34', 'The Adventures of Sherlock Holmes', '1', '1', '1', '20.00', '3', 'Detektiv klassiker', 'Äldre upplaga', '1892', 'Wordsworth', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('35', 'The Hound of the Baskervilles', '1', '1', '1', '18.50', '2', 'Holmes klassiker', NULL, '1902', 'Penguin', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('36', 'The Hobbit', '1', '1', '1', '22.00', '1', 'Fantasy klassiker', 'Bra skick', '1937', 'HarperCollins', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('37', 'The Lord of the Rings', '1', '1', '1', '35.50', '2', 'Fantasy epos', 'Komplett trilogi', '1954', 'HarperCollins', '0', '0', '1', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('38', '1984', '1', '1', '1', '17.90', '1', 'Dystopisk klassiker', 'Mycket aktuell', '1949', 'Penguin', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('39', 'Animal Farm', '1', '1', '1', '14.50', '2', 'Politisk allegori', NULL, '1945', 'Penguin', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('40', 'Pride and Prejudice', '1', '1', '1', '19.50', '2', 'Romantisk klassiker', NULL, '1813', 'Penguin Classics', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('41', 'Emma', '1', '1', '1', '18.90', '1', 'Jane Austen', NULL, '1815', 'Penguin Classics', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('42', 'Great Expectations', '1', '1', '1', '21.50', '2', 'Viktoriansk roman', NULL, '1861', 'Penguin Classics', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('43', 'A Tale of Two Cities', '1', '1', '1', '20.50', '1', 'Historisk roman', NULL, '1859', 'Penguin Classics', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('44', 'Hamlet', '1', '1', '1', '16.50', '3', 'Shakespeare tragedi', 'Äldre upplaga', '1603', 'Wordsworth', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('45', 'Romeo and Juliet', '1', '1', '1', '15.90', '2', 'Kärlekstragedi', NULL, '1597', 'Wordsworth', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('46', 'The Old Man and the Sea', '1', '1', '1', '18.50', '1', 'Nobelprisbärare', NULL, '1952', 'Scribner', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('47', 'For Whom the Bell Tolls', '1', '1', '1', '22.50', '2', 'Krigsskildring', NULL, '1940', 'Scribner', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('48', 'The Great Gatsby', '1', '1', '1', '19.90', '1', 'Amerikansk klassiker', NULL, '1925', 'Scribner', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('49', 'To Kill a Mockingbird', '1', '1', '1', '18.50', '2', 'Amerikanskl klassiker', NULL, '1960', 'Arrow Books', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('51', 'War and Peace', '1', '1', '1', '32.95', '3', 'Rysk epos', 'Tjock bok', '1869', 'Penguin Classics', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('52', 'Anna Karenina', '1', '1', '1', '28.50', '2', 'Rysk klassiker', NULL, '1877', 'Penguin Classics', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('53', 'Crime and Punishment', '1', '1', '1', '24.50', '2', 'Psykologisk roman', NULL, '1866', 'Penguin Classics', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('54', 'The Metamorphosis', '1', '1', '1', '16.90', '1', 'Surrealistisk novell', NULL, '1915', 'Penguin Classics', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('55', 'Mrs Dalloway', '1', '1', '1', '17.90', '2', 'Modernistisk roman', NULL, '1925', 'Penguin Classics', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('56', 'Ulysses', '1', '1', '1', '28.90', '3', 'Modernistisk mästerverk', 'Svårläst', '1922', 'Penguin Classics', '0', '0', '1', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('61', 'Fahrenheit 451', '1', '1', '1', '18.90', '1', 'Dystopisk sci-fi', NULL, '1953', 'Ballantine', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('62', 'The Martian Chronicles', '1', '1', '1', '20.50', '2', 'Mars berättelser', NULL, '1950', 'Bantam', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('63', 'Foundation', '1', '1', '1', '19.50', '1', 'Sci-fi klassiker', NULL, '1951', 'Bantam', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('64', 'I, Robot', '1', '1', '1', '17.90', '2', 'Robot berättelser', '', '1950', 'Bantam', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('65', 'The Caves of Steel', '1', '1', '1', '18.50', '1', 'Robot detektiv', NULL, '1954', 'Bantam', '0', '0', '0', '2025-05-28 00:26:26', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('66', 'Finlandia - Sibelius Greatest', '1', '6', '5', '18.90', '1', 'Klassisk samling', NULL, '1995', 'Ondine', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('67', 'Sibelius Symphonies 1-7', '1', '6', '5', '24.50', '2', 'Komplett', NULL, '2001', 'BIS', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('68', 'The Beatles - Abbey Road', '1', '6', '5', '22.50', '1', 'Klassiskt album', 'Remastrad', '1969', 'Apple', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('69', 'The Beatles - Sgt Pepper', '1', '6', '5', '21.90', '2', 'Psykedelisk klassiker', NULL, '1967', 'Parlophone', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('70', 'Elvis Presley - The Essential', '1', '6', '5', '21.00', '2', 'Rock n roll kung', NULL, '2007', 'RCA', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('71', 'Elvis - 30 #1 Hits', '1', '6', '5', '19.50', '1', 'Bästa hits', NULL, '2002', 'RCA', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('72', 'Bob Dylan - Greatest Hits', '1', '6', '5', '20.50', '1', 'Folk rock legend', NULL, '1967', 'Columbia', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('73', 'Blood on the Tracks', '1', '6', '5', '18.90', '2', 'Dylan klassiker', NULL, '1975', 'Columbia', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('74', 'ABBA Gold', '1', '6', '5', '19.90', '2', 'Bästa hits', 'Internationell succé', '1992', 'Polar', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('75', 'ABBA - Arrival', '1', '6', '5', '17.50', '1', 'Svenskt original', NULL, '1976', 'Polar', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('76', 'Pink Floyd - Dark Side of the Moon', '1', '6', '5', '23.50', '1', 'Prog rock klassiker', NULL, '1973', 'Harvest', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('77', 'The Wall', '1', '6', '5', '21.90', '2', 'Konceptalbum', NULL, '1979', 'Harvest', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('78', 'Queen - Greatest Hits', '1', '6', '5', '20.50', '1', 'Rock klassiker', NULL, '1981', 'EMI', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('79', 'A Night at the Opera', '1', '6', '5', '19.90', '2', 'Innehåller Bohemian Rhapsody', '', '1975', 'EMI', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('80', 'Led Zeppelin IV', '1', '6', '5', '22.50', '1', 'Hard rock klassiker', NULL, '1971', 'Atlantic', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('81', 'Physical Graffiti', '1', '6', '5', '24.50', '2', 'Dubbel-CD', NULL, '1975', 'Swan Song', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('82', 'David Bowie - The Rise and Fall', '1', '6', '5', '21.50', '1', 'Glam rock', NULL, '1972', 'RCA', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('83', 'Heroes', '1', '6', '5', '19.50', '2', 'Berlin trilogi', NULL, '1977', 'RCA', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('84', 'The Rolling Stones - Sticky Fingers', '1', '6', '5', '20.90', '1', 'Rock klassiker', NULL, '1971', 'Rolling Stones', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('85', 'Exile on Main St.', '1', '6', '5', '23.50', '2', 'Dubbel-CD', NULL, '1972', 'Rolling Stones', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('86', 'Juice Leskinen - Kootut levyt', '1', '6', '5', '24.50', '2', 'Finsk rocklegend', 'Komplett samling', '1989', 'Poko', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('87', 'Eppu Normaali - Akun tehdas', '1', '6', '5', '18.50', '1', 'Finsk punk rock', NULL, '1980', 'Poko', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('88', 'Dingo - Kerjäläisten valtakunta', '1', '6', '5', '16.50', '1', 'Kultalbum', NULL, '1986', 'Fazer', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('89', 'Hassisen Kone - Rumat sävelet', '1', '6', '5', '22.50', '2', 'Punk klassiker', NULL, '1982', 'Poko', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('90', 'Leevi and the Leavings - Menestyksen salaisuus', '1', '6', '5', '19.90', '1', 'Indie pop', NULL, '1991', 'Pyramid', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('91', 'Aki Kaurismäki Box Set', '1', '7', '7', '45.50', '1', 'Finsk filmsamling', 'Komplett box', '2005', 'Criterion', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('92', 'Ariel', '1', '7', '7', '16.50', '2', 'Kaurismäki klassiker', NULL, '1988', 'Villealfa', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('93', 'Leningrad Cowboys Go America', '1', '7', '7', '18.50', '1', 'Kultfilm', NULL, '1989', 'Villealfa', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('94', 'The Seventh Seal', '1', '7', '7', '22.50', '2', 'Bergman mästerverk', 'Criterion edition', '1957', 'Criterion', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('95', 'Persona', '1', '7', '7', '20.50', '1', 'Psykologiskt drama', NULL, '1966', 'Criterion', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('96', 'Jaws', '1', '7', '7', '15.50', '2', 'Thriller klassiker', NULL, '1975', 'Universal', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('97', 'E.T. the Extra-Terrestrial', '1', '7', '7', '14.90', '1', 'Sci-fi familj', NULL, '1982', 'Universal', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('98', 'Inception', '1', '7', '7', '18.50', '1', 'Sci-fi thriller', NULL, '2010', 'Warner Bros', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('99', 'The Dark Knight', '1', '7', '7', '17.90', '2', 'Superhero epos', NULL, '2008', 'Warner Bros', '0', '0', '0', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('100', 'Psycho', '1', '7', '7', '16.50', '3', 'Thriller klassiker', 'Äldre utgåva', '1960', 'Universal', '0', '0', '1', '2025-05-28 00:26:26', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('101', 'Vintage Helsinki postcard collection', '1', '3', '9', '45.00', '2', 'Gamla vykort 1920-1950', 'Historiskt värde', '1935', '', '0', '0', '1', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('102', 'Antique Finnish stamps collection', '1', '1', '9', '125.50', '1', 'Sällsynta frimärken', 'Komplett serie 1860-1917', '1900', 'Suomen Posti', '0', '0', '1', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('103', 'Old map of Turku/Åbo', '1', '3', '9', '78.50', '2', 'Historisk stadskarta', '1800-talet original', '1889', 'Kartografiska', '0', '0', '1', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('104', 'Vintage Marimekko fabric pieces', '1', '1', '9', '65.00', '3', 'Design klassiker tyg', '1960-tal Unikko mönster', '1968', 'Marimekko', '0', '0', '0', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('105', 'Arabia ceramic bowl set', '1', '1', '9', '95.00', '1', 'Finsk designkeramik', 'Vintage Ruska serie', '1960', 'Arabia', '0', '0', '1', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('106', 'Old Nokia 3310 phone', '1', '1', '9', '35.50', '2', 'Retro mobiltelefon', 'Nostalgi från 90-talet', '1999', 'Nokia', '0', '0', '0', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('107', 'Vintage Fazer chocolate tins', '1', '1', '9', '28.90', '3', 'Gamla förpackningar', 'Samlarobjekt 1950-60-tal', '1958', 'Fazer', '0', '0', '0', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('108', 'Alvar Aalto vase original', '1', '1', '9', '185.00', '2', 'Savoy vase 1937', 'Äkta Iittala original', '1937', 'Iittala', '0', '0', '1', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('109', 'Pentik pottery collection', '1', '1', '9', '58.50', '2', 'Lapplands keramik', '1970-tal kollektion', '1975', 'Pentik', '0', '0', '0', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('110', 'Vintage Finnish banknotes', '1', '1', '9', '125.00', '1', 'Gamla sedlar', 'Markka sedlar 1860-1963', '1920', 'Suomen Pankki', '0', '0', '1', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('111', 'Old compass and navigation tools', '1', '4', '9', '145.50', '2', 'Sjöfarts instrument', 'Mässing kompass 1920-tal', '1925', 'Maritime Instruments', '0', '0', '1', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('112', 'Vintage fishing equipment', '1', '4', '9', '78.90', '3', 'Gamla fiskeverktyg', 'Träspön och rullar', '1950', '', '0', '0', '0', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('113', 'Antique book binding tools', '1', '1', '9', '89.50', '2', 'Bokbinderi verktyg', 'Komplett set från 1930-tal', '1935', '', '1', '0', '1', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('114', 'Vintage Finnish coins collection', '1', '1', '9', '165.00', '1', 'Mynt samling', 'Penni och markka 1860-2001', '1950', 'Suomen Rahapaja', '0', '0', '1', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('115', 'Old ship model', '1', '4', '9', '225.50', '2', 'Handgjord skeppsmodell', 'Finsk ångbåt modell', '1960', '', '0', '0', '0', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('116', 'Tintin: Det svarta guldet', '1', '1', '8', '18.50', '1', 'Hergé klassiker', 'Svensk översättning', '1950', 'Casterman', '0', '0', '0', '2025-05-28 00:32:42', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('117', 'Tintin: Månen tur och retur', '1', '1', '8', '19.90', '2', 'Rymdäventyr', 'Del 1 och 2', '1954', 'Casterman', '1', '0', '0', '2025-05-28 00:32:42', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('118', 'Asterix: Gallernas häuptling', '1', '1', '8', '16.50', '1', 'Asterix klassiker', NULL, '1961', 'Dargaud', '0', '0', '0', '2025-05-28 00:32:42', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('119', 'Asterix och Kleopatra', '1', '1', '8', '17.90', '2', 'Populär album', NULL, '1965', 'Dargaud', '1', '0', '0', '2025-05-28 00:32:42', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('121', 'Uncle Scrooge: Life and Times', '1', '1', '8', '28.50', '1', 'Don Rosa mästerverk', 'Engelska', '1994', 'Gladstone', '0', '0', '0', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('122', 'Spider-Man: Amazing Fantasy #15', '2', '1', '8', '450.00', '4', 'Första Spider-Man', 'Mycket sällsynt reprint', '1962', 'Marvel', '0', '1', '1', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('123', 'X-Men #1', '2', '1', '8', '125.50', '3', 'Första X-Men', 'Silverålder klassiker', '1963', 'Marvel', '0', '1', '1', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('124', 'Watchmen', '1', '1', '8', '32.50', '1', 'Grafisk roman mästerverk', 'Komplett serie', '1987', 'DC Comics', '0', '0', '0', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('125', 'Batman: The Dark Knight Returns', '1', '1', '8', '26.50', '1', 'Frank Miller klassiker', NULL, '1986', 'DC Comics', '0', '0', '0', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('126', 'Sandman Vol 1: Preludes', '1', '1', '8', '22.90', '2', 'Neil Gaiman', 'Trade paperback', '1991', 'Vertigo', '1', '0', '0', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('127', 'Maus: A Survivor\'s Tale', '1', '1', '8', '24.50', '1', 'Pulitzer Prize vinnare', 'Komplett', '1991', 'Pantheon', '0', '0', '1', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('128', 'Persepolis', '1', '1', '8', '21.50', '1', 'Autobiografisk grafisk roman', NULL, '2000', 'Pantheon', '0', '0', '0', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('129', 'Koirien Kalevala', '1', '1', '8', '19.90', '1', 'Mauri Kunnas klassiker', 'Finsk barnseriebok', '1992', 'Otava', '0', '0', '0', '2025-05-28 00:32:42', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('131', 'National Geographic 1987 årssats', '1', '1', '13', '45.50', '2', 'Komplett årssamling', '12 nummer i box', '1987', 'National Geographic', '0', '0', '0', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('132', 'Tekniikan Maailma 1975-1985', '1', '1', '13', '78.50', '3', 'Teknisk tidskrift arkiv', '10 års samling', '1980', 'Tekniikan Maailma', '0', '0', '0', '2025-05-28 00:32:42', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('133', 'Suosikki-lehti 1980-talet', '1', '1', '13', '32.90', '2', 'Musiktidning samling', 'Pop och rock 80-tal', '1985', 'Suosikki', '0', '0', '0', '2025-05-28 00:32:42', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('134', 'Apu-lehti vintage collection', '1', '1', '13', '28.50', '3', 'Veckotidning 1970-tal', '50 exemplar', '1975', 'Yhtyneet Kuvalehdet', '0', '0', '0', '2025-05-28 00:32:42', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('135', 'Life Magazine 1960s', '1', '1', '13', '65.00', '2', 'Amerikanska Life 60-tal', 'Historiska nummer', '1965', 'Time Inc', '0', '0', '0', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('136', 'Playboy vintage 1970s', '2', '1', '13', '85.50', '3', 'Klassiska nummer', 'Samlarobjekt', '1975', 'Playboy', '0', '0', '1', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('137', 'Mad Magazine collection', '1', '1', '13', '42.50', '2', 'Humor tidning 1970-80', 'Klassisk satir', '1978', 'EC Comics', '0', '0', '0', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('138', 'Historiallinen Aikakauskirja', '1', '3', '13', '38.90', '2', 'Historisk tidskrift', '1990-talet komplett', '1995', 'Suomen Historiallinen Seura', '0', '0', '0', '2025-05-28 00:32:42', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('139', 'Kotiliesi 1960-luku', '1', '1', '13', '35.50', '3', 'Hem och kök tidning', 'Vintage lifestyle', '1965', 'Otava', '0', '0', '0', '2025-05-28 00:32:42', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('140', 'Time Magazine 1980s', '1', '1', '13', '48.50', '2', 'Amerikansk nyhetstidning', 'Viktiga 80-tals nummer', '1985', 'Time Inc', '0', '0', '0', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('141', 'Monopol Helsingfors edition', '1', '1', '12', '28.50', '1', 'Lokalversion Helsinki', 'Komplett spel', '1995', 'Hasbro', '0', '0', '0', '2025-05-28 00:32:42', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('142', 'Afrikan tähti', '1', '1', '12', '22.50', '1', 'Klassiskt finskt spel', 'Original från 1951', '1951', 'Kari Mannerla', '0', '0', '1', '2025-05-28 00:32:42', '2');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('143', 'Trivial Pursuit Svenska', '1', '1', '12', '24.90', '2', 'Kunskapsspel svenska', '', '1984', 'Parker Brothers', '0', '0', '0', '2025-05-28 00:32:42', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('144', 'Risk världsherravälde', '1', '1', '12', '32.50', '2', 'Strategispel klassiker', NULL, '1985', 'Parker Brothers', '0', '0', '0', '2025-05-28 00:32:42', '1');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('145', 'Dungeons & Dragons Basic Set', '1', '1', '12', '125.50', '3', 'Vintage rollspel', 'Röd box från 1983', '1983', 'TSR', '0', '0', '1', '2025-05-28 00:32:42', '3');
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('146', 'Dark Side of the Moon vinyl', '1', '6', '6', '85.50', '2', 'Pink Floyd original pressning', 'UK original 1973', '1973', 'Harvest', '0', '0', '1', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('147', 'Abbey Road original vinyl', '1', '6', '6', '125.00', '1', 'Beatles original pressning', 'UK Parlophone 1969', '1969', 'Apple', '0', '0', '1', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('148', 'ABBA - Waterloo vinyl', '1', '6', '6', '45.50', '2', 'Eurovision vinnare', 'Svenskt original 1974', '1974', 'Polar', '0', '0', '0', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('149', 'Led Zeppelin IV vinyl', '1', '6', '6', '78.90', '1', 'Zoso album original', 'Atlantic original 1971', '1971', 'Atlantic', '0', '0', '0', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('150', 'Juice Leskinen - Kala, kala, kala', '1', '6', '6', '65.00', '2', 'Finsk rock vinyl', 'Love Records original', '1978', 'Love Records', '0', '0', '1', '2025-05-28 00:32:42', NULL);
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `language_id`) VALUES ('153', 'Produkt', '1', NULL, '1', NULL, NULL, '', '', NULL, '', '0', '0', '0', '2025-05-28 09:35:29', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `product_author`
-- 

INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('1', '1', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('2', '2', '1');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('3', '3', '2');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('4', '4', '3');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('5', '5', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('6', '6', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('7', '7', '5');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('8', '8', '5');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('9', '9', '5');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('10', '10', '5');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('11', '11', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('12', '12', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('13', '13', '4');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('14', '14', '2');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('15', '15', '2');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('16', '16', '6');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('17', '17', '6');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('18', '18', '6');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('19', '19', '6');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('20', '20', '7');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('21', '21', '7');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('22', '22', '7');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('23', '23', '8');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('24', '24', '8');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('25', '25', '8');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('31', '31', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('32', '32', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('33', '33', '10');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('34', '34', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('35', '35', '11');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('36', '36', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('37', '37', '12');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('131', '38', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('39', '39', '13');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('40', '40', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('41', '41', '14');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('42', '42', '15');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('43', '43', '15');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('44', '44', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('45', '45', '16');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('46', '46', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('47', '47', '17');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('48', '48', '18');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('49', '49', '19');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('51', '51', '21');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('52', '52', '21');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('53', '53', '22');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('54', '54', '23');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('55', '55', '24');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('56', '56', '25');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('61', '61', '29');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('62', '62', '29');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('63', '63', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('126', '64', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('65', '65', '30');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('66', '66', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('67', '67', '31');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('68', '68', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('69', '69', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('70', '70', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('71', '71', '33');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('72', '72', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('73', '73', '34');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('74', '74', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('75', '75', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('76', '76', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('77', '77', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('78', '78', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('134', '79', '37');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('135', '79', '67');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('80', '80', '38');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('81', '81', '38');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('82', '82', '39');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('83', '83', '39');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('84', '84', '40');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('85', '85', '40');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('86', '86', '41');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('87', '87', '42');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('88', '88', '43');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('89', '89', '44');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('90', '90', '45');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('91', '91', '46');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('92', '92', '46');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('93', '93', '46');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('94', '94', '47');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('95', '95', '47');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('96', '96', '48');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('97', '97', '48');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('98', '98', '49');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('99', '99', '49');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('100', '100', '50');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('132', '113', '9');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('133', '113', '66');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('101', '116', '51');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('102', '117', '51');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('103', '118', '52');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('104', '118', '53');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('105', '119', '52');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('106', '119', '53');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('108', '121', '55');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('109', '122', '56');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('110', '122', '57');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('111', '123', '56');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('112', '123', '57');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('113', '124', '58');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('114', '125', '59');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('115', '126', '60');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('116', '127', '61');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('117', '128', '62');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('118', '129', '63');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('120', '146', '36');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('121', '147', '32');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('122', '148', '35');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('123', '149', '38');
INSERT INTO `product_author` (`product_author_id`, `product_id`, `author_id`) VALUES ('124', '150', '41');

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
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Dumping data for table `product_genre`
-- 

INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('1', '1', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('2', '2', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('3', '3', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('4', '4', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('5', '5', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('6', '6', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('7', '7', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('8', '8', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('9', '9', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('10', '10', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('11', '11', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('12', '12', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('13', '13', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('14', '14', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('15', '15', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('16', '16', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('17', '17', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('18', '18', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('19', '19', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('20', '20', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('21', '21', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('22', '22', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('23', '23', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('24', '24', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('25', '25', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('31', '31', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('32', '32', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('33', '33', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('34', '34', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('35', '35', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('36', '36', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('37', '37', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('160', '38', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('39', '39', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('40', '40', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('41', '41', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('42', '42', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('43', '43', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('44', '44', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('45', '45', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('46', '46', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('47', '47', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('48', '48', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('49', '49', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('51', '51', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('52', '52', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('53', '53', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('54', '54', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('55', '55', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('56', '56', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('61', '61', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('62', '62', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('63', '63', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('152', '64', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('65', '65', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('66', '66', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('67', '67', '9');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('68', '68', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('69', '69', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('70', '70', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('71', '71', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('72', '72', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('73', '73', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('74', '74', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('75', '75', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('76', '76', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('77', '77', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('78', '78', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('162', '79', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('80', '80', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('81', '81', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('82', '82', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('83', '83', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('84', '84', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('85', '85', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('86', '86', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('87', '87', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('88', '88', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('89', '89', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('90', '90', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('91', '91', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('92', '92', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('93', '93', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('94', '94', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('95', '95', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('96', '96', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('97', '97', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('98', '98', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('99', '99', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('100', '100', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('101', '101', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('102', '102', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('103', '103', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('104', '104', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('105', '105', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('106', '106', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('107', '107', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('108', '108', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('109', '109', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('110', '110', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('111', '111', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('112', '112', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('161', '113', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('114', '114', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('115', '115', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('116', '116', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('117', '117', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('118', '118', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('119', '119', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('121', '121', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('122', '122', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('123', '123', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('124', '124', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('125', '125', '10');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('126', '126', '1');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('127', '127', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('128', '128', '5');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('129', '129', '6');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('131', '131', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('132', '132', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('133', '133', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('134', '134', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('135', '135', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('136', '136', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('137', '137', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('138', '138', '3');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('139', '139', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('140', '140', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('155', '141', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('153', '142', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('156', '143', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('144', '144', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('154', '145', '11');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('146', '146', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('147', '147', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('148', '148', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('149', '149', '7');
INSERT INTO `product_genre` (`product_genre_id`, `product_id`, `genre_id`) VALUES ('150', '150', '7');

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

INSERT INTO `user` (`user_id`, `user_username`, `user_password_hash`, `user_role`, `user_email`, `user_last_login`, `user_created_at`, `user_is_active`) VALUES ('1', 'Admin', '$2y$10$J0jSNdu1QUebZT4KRq6yTOkwFQ4DyyIqO8Lj/o5KZuSTXUQ1MgCgu', '1', 'admin@karisantikvariat.fi', '2025-05-28 10:01:18', '2025-04-10 10:41:05', '1');
INSERT INTO `user` (`user_id`, `user_username`, `user_password_hash`, `user_role`, `user_email`, `user_last_login`, `user_created_at`, `user_is_active`) VALUES ('3', 'Redaktor', '$2y$10$Qx1YgizfEOSuzTAp3r5bd.qfGJbMcXjdneHL9Ge9icsPbIsm5uicO', '2', 'redaktor@karisantikvariat.fi', '2025-05-28 09:26:19', '2025-04-30 13:57:26', '1');

COMMIT;
