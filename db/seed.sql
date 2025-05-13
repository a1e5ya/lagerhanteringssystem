-- Initial Data
-- Seed data for testing


-- Karis Antikvariat - Database Seed Data
-- Based on the existing structure and reference data
-- Generated on: May 13, 2025

USE `ka_lagerhanteringssystem`;

-- Reset tables to ensure clean inserts
SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing data
TRUNCATE TABLE `product_author`;
TRUNCATE TABLE `product_genre`;
TRUNCATE TABLE `product_image`;
TRUNCATE TABLE `event_log`;
DELETE FROM `product`;
DELETE FROM `author`;
DELETE FROM `category`;
DELETE FROM `shelf`;
DELETE FROM `genre`;
DELETE FROM `condition`;
DELETE FROM `language`;
DELETE FROM `image`;
DELETE FROM `status`;
-- user table already has data, skip it

-- Reset AUTO_INCREMENT values
ALTER TABLE `product` AUTO_INCREMENT = 1;
ALTER TABLE `author` AUTO_INCREMENT = 1;
ALTER TABLE `product_author` AUTO_INCREMENT = 1;
ALTER TABLE `product_genre` AUTO_INCREMENT = 1;
ALTER TABLE `product_image` AUTO_INCREMENT = 1;
ALTER TABLE `category` AUTO_INCREMENT = 1;
ALTER TABLE `shelf` AUTO_INCREMENT = 1;
ALTER TABLE `genre` AUTO_INCREMENT = 1;
ALTER TABLE `condition` AUTO_INCREMENT = 1;
ALTER TABLE `language` AUTO_INCREMENT = 1;
ALTER TABLE `image` AUTO_INCREMENT = 1;
ALTER TABLE `status` AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- Insert Languages
INSERT INTO `language` (`language_id`, `language_sv_name`, `language_fi_name`) VALUES
(1, 'Svenska', 'Ruotsi'),
(2, 'Finska', 'Suomi'),
(3, 'Engelska', 'Englanti'),
(4, 'Norska', 'Norja');

-- Insert Categories
INSERT INTO `category` (`category_id`, `category_sv_name`, `category_fi_name`) VALUES
(1, 'Bok', 'Kirja'),
(5, 'CD', 'CD'),
(6, 'Vinyl', 'Vinyyli'),
(7, 'DVD', 'DVD'),
(8, 'Serier', 'Sarjakuva'),
(9, 'Samlarobjekt', 'Keräilyesine');

-- Insert Shelves
INSERT INTO `shelf` (`shelf_id`, `shelf_sv_name`, `shelf_fi_name`) VALUES
(1, 'Finlandssvenska', 'Suomenruotsalainen'),
(3, 'Lokalhistoria', 'Paikallishistoria'),
(4, 'Sjöfart', 'Merenkulku'),
(5, 'Barn/Ungdom', 'Lapset/Nuoret'),
(6, 'Musik', 'Musiikki'),
(7, 'Film', 'Elokuva');

-- Insert Genres
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

-- Insert Conditions
INSERT INTO `condition` (`condition_id`, `condition_sv_name`, `condition_fi_name`, `condition_code`, `condition_description`) VALUES
(1, 'Nyskick', 'Uusi kunto', 'K-1', 'Like new, no visible wear'),
(2, 'Mycket bra', 'Erittäin hyvä', 'K-2', 'Very good, minimal signs of use'),
(3, 'Bra', 'Hyvä', 'K-3', 'Good condition, some signs of wear'),
(4, 'Acceptabelt', 'Tyydyttävä', 'K-4', 'Acceptable, significant wear but functional');

-- Insert Status values
INSERT INTO `status` (`status_id`, `status_sv_name`, `status_fi_name`) VALUES
(1, 'Tillgänglig', 'Saatavilla'),
(2, 'Såld', 'Myyty');

-- Insert some sample authors
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

-- Insert sample products (Note: language field is now INT for language_id)
INSERT INTO `product` (`prod_id`, `title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `notes`, `internal_notes`, `language`, `year`, `publisher`, `special_price`, `recommended`, `rare`, `date_added`, `image`) VALUES
(1, 'Harry Potter och De Vises Sten', 1, 5, 1, 24.95, 2, 'Första boken i Harry Potter-serien', 'Bra skick, populär bland yngre läsare', 1, 1997, 'Tiden', 0, 0, 0, '2025-05-13 12:37:38', NULL),
(2, 'Lysningen', 1, 1, 1, 19.95, 3, NULL, NULL, 1, 1977, 'Bra Böcker', 0, 0, 0, '2025-05-13 12:37:38', NULL),
(3, 'Tjänarinnans berättelse', 1, 1, 1, 22.50, 1, 'Dystopisk roman', NULL, 1, 1985, 'Norstedts', 1, 0, 0, '2025-05-13 12:37:38', NULL),
(4, 'Amerikanska gudar', 1, 1, 1, NULL, 2, 'Fantasyroman', 'Köpt på bokauktion i Helsingfors', 1, 2001, 'Bonnier Carlsen', 0, 0, 0, '2025-05-13 12:37:38', NULL),
(5, 'Älskade', 1, 1, 1, NULL, 2, NULL, NULL, 1, 1987, 'Trevi', 0, 0, 0, '2025-05-13 12:37:38', NULL),
(6, 'Trollvinter', 1, 1, 1, 26.50, 1, 'Mumin-roman', 'En av våra bästsäljare', 1, 1957, 'Schildts', 0, 0, 0, '2025-05-13 12:37:38', NULL),
(7, 'Pippi Långstrump', 1, 5, 1, 18.95, 1, 'Barnklassiker', NULL, 1, 1945, 'Rabén & Sjögren', 0, 0, 0, '2025-05-13 12:37:38', NULL);

-- Connect products with authors
INSERT INTO `product_author` (`product_id`, `author_id`) VALUES
(1, 1), -- Harry Potter - J.K. Rowling
(2, 2), -- Lysningen - Stephen King
(3, 3), -- Tjänarinnans berättelse - Margaret Atwood
(4, 4), -- Amerikanska gudar - Neil Gaiman
(5, 5), -- Älskade - Toni Morrison
(6, 6), -- Trollvinter - Tove Jansson
(7, 7); -- Pippi Långstrump - Astrid Lindgren

-- Connect products with genres
INSERT INTO `product_genre` (`product_id`, `genre_id`) VALUES
(1, 6), -- Harry Potter - Barnböcker
(1, 10), -- Harry Potter - Äventyr
(2, 1), -- Lysningen - Romaner
(3, 1), -- Tjänarinnans - Romaner
(4, 1), -- Amerikanska gudar - Romaner
(4, 10), -- Amerikanska gudar - Äventyr
(5, 1), -- Älskade - Romaner
(6, 6), -- Trollvinter - Barnböcker
(7, 6), -- Pippi - Barnböcker
(7, 10); -- Pippi - Äventyr

-- Insert sample newsletter subscribers
INSERT INTO `newsletter_subscriber` (`subscriber_email`, `subscriber_name`, `subscriber_language_pref`) VALUES
('johanna.karlsson@example.com', 'Johanna Karlsson', 'sv'),
('mikko.nieminen@example.fi', 'Mikko Nieminen', 'fi'),
('anna.lindholm@example.com', 'Anna Lindholm', 'sv'),
('erik.johansson@example.se', 'Erik Johansson', 'sv'),
('liisa.makinen@example.fi', 'Liisa Mäkinen', 'fi');

-- Add a few event log entries
INSERT INTO `event_log` (`event_type`, `event_description`, `event_timestamp`) VALUES
('create', 'Skapade produkt: Trollvinter', '2025-04-22 11:21:34'),
('create', 'Skapade produkt: Muumipeikko ja pyrstötähti', '2025-04-22 11:21:34'),
('update', 'Uppdaterade pris på: Harry Potter och De Vises Sten', '2025-04-22 11:21:34'),
('create', 'Skapade produkt: Sibelius Symphony No. 2', '2025-04-22 11:21:34'),
('login', 'Backdoor login used for admin', '2025-05-12 10:53:38');

-- Add sample user
INSERT INTO `user` (`user_username`, `user_password_hash`, `user_role`, `user_email`, `user_last_login`, `user_created_at`, `user_is_active`) VALUES
('Admin', '$2y$10$J0jSNdu1QUebZT4KRq6yTOkwFQ4DyyIqO8Lj/o5KZuSTXUQ1MgCgu', 1, 'admin@karisantikvariat.fi', '2025-05-06 10:04:21', '2025-04-10 10:41:05', 1),
('Redaktor', '$2y$10$Qx1YgizfEOSuzTAp3r5bd.qfGJbMcXjdneHL9Ge9icsPbIsm5uicO', 2, 'redaktor@karisantikvariat.fi', '2025-05-06 10:30:18', '2025-04-30 13:57:26', 1);