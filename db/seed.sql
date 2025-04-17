-- Initial Data
-- Seed data for testing


-- Karis Antikvariat - Database Seed Data
-- Based on the existing structure and reference data
-- Generated on: April 10, 2025

USE `ka_lagerhanteringssystem`;

-- Reset tables to ensure clean inserts
SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing data
TRUNCATE TABLE `product_author`;
TRUNCATE TABLE `product_genre`;
TRUNCATE TABLE `event_log`;
DELETE FROM `product`;
DELETE FROM `author`;
DELETE FROM `category`;
DELETE FROM `shelf`;
DELETE FROM `genre`;
DELETE FROM `condition`;
-- Status and user tables already have data, skip them

-- Reset AUTO_INCREMENT values
ALTER TABLE `product` AUTO_INCREMENT = 1;
ALTER TABLE `author` AUTO_INCREMENT = 1;
ALTER TABLE `product_author` AUTO_INCREMENT = 1;
ALTER TABLE `product_genre` AUTO_INCREMENT = 1;
ALTER TABLE `category` AUTO_INCREMENT = 1;
ALTER TABLE `shelf` AUTO_INCREMENT = 1;
ALTER TABLE `genre` AUTO_INCREMENT = 1;
ALTER TABLE `condition` AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- Insert Categories
-- From KarisAntikvariat.Categories
INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'Bok'),
(5, 'CD'),
(6, 'Vinyl'),
(7, 'DVD'),
(8, 'Serier'),
(9, 'Samlarobjekt');

-- Insert Shelves
-- From KarisAntikvariat.Shelves
INSERT INTO `shelf` (`shelf_id`, `shelf_name`) VALUES
(1, 'Finlandssvenska'),
(3, 'Lokalhistoria'),
(4, 'Sjöfart'),
(5, 'Barn/Ungdom'),
(6, 'Musik'),
(7, 'Film');

-- Insert Genres
-- From KarisAntikvariat.Genres
INSERT INTO `genre` (`genre_id`, `genre_name`) VALUES
(1, 'Romaner'),
(3, 'Historia'),
(4, 'Dikter'),
(5, 'Biografi'),
(6, 'Barnböcker'),
(7, 'Rock'),
(8, 'Jazz'),
(9, 'Klassisk'),
(10, 'Äventyr');

-- Insert Conditions
-- From KarisAntikvariat.Conditions
INSERT INTO `condition` (`condition_id`, `condition_name`, `condition_code`, `condition_description`) VALUES
(1, 'Nyskick', 'K-1', 'Like new, no visible wear'),
(2, 'Mycket bra', 'K-2', 'Very good, minimal signs of use'),
(3, 'Bra', 'K-3', 'Good condition, some signs of wear'),
(4, 'Acceptabelt', 'K-4', 'Acceptable, significant wear but functional');

-- Status values are already seeded in the provided SQL dump
-- Update status names to Swedish based on KarisAntikvariat.StatusTypes
UPDATE `status` SET `status_name` = 'Tillgänglig' WHERE `status_id` = 1;
UPDATE `status` SET `status_name` = 'Såld' WHERE `status_id` = 2;

-- Insert some sample authors
INSERT INTO `author` (`first_name`, `last_name`) VALUES
('Tove', 'Jansson'),
('Zacharias', 'Topelius'),
('Astrid', 'Lindgren'),
('J.K.', 'Rowling'),
('Ernest', 'Hemingway'),
('Edith', 'Södergran'),
('Bo', 'Carpelan');

-- Insert sample products
INSERT INTO `product` (`title`, `status`, `shelf_id`, `category_id`, `price`, `condition_id`, `language`, `year`, `publisher`, `special_price`, `rare`, `date_added`) VALUES
('Trollvinter', 1, 1, 1, 24.95, 1, 'Svenska', 1957, 'Schildts', 0, 0, NOW()),
('Muumipeikko ja pyrstötähti', 1, 1, 1, 19.95, 2, 'Suomi', 1946, 'WSOY', 0, 0, NOW()),
('Pippi Långstrump', 1, 5, 1, 14.95, 2, 'Svenska', 1965, 'Rabén & Sjögren', 0, 0, NOW()),
('Harry Potter och De Vises Sten', 1, 5, 1, 29.95, 1, 'Svenska', 1999, 'Tiden', 0, 0, NOW()),
('Jazz Classics', 1, 6, 5, 15.00, 3, NULL, 2010, 'Universal Music', 0, 0, NOW()),
('Sibelius Symphony No. 2', 1, 6, 5, 22.50, 1, NULL, 2005, 'BIS Records', 1, 0, NOW()),
('Åbo - En historisk resa', 1, 3, 1, 34.95, 1, 'Svenska', 2018, 'Schildts & Söderströms', 0, 0, NOW()),
('Dikter', 1, 1, 1, 45.00, 2, 'Svenska', 1916, 'Holger Schildts förlag', 0, 1, NOW());

-- Connect products with authors
INSERT INTO `product_author` (`product_id`, `author_id`) VALUES
(1, 1), -- Trollvinter - Tove Jansson
(2, 1), -- Muumipeikko ja pyrstötähti - Tove Jansson
(3, 3), -- Pippi Långstrump - Astrid Lindgren
(4, 4), -- Harry Potter - J.K. Rowling
(7, 2), -- Åbo - Zacharias Topelius
(8, 6); -- Dikter - Edith Södergran

-- Connect products with genres
INSERT INTO `product_genre` (`product_id`, `genre_id`) VALUES
(1, 6), -- Trollvinter - Barnböcker
(1, 10), -- Trollvinter - Äventyr
(2, 6), -- Muumipeikko - Barnböcker
(3, 6), -- Pippi - Barnböcker
(3, 10), -- Pippi - Äventyr
(4, 6), -- Harry Potter - Barnböcker
(4, 10), -- Harry Potter - Äventyr
(5, 8), -- Jazz Classics - Jazz
(6, 9), -- Sibelius - Klassisk
(7, 3), -- Åbo - Historia
(8, 4); -- Dikter - Dikter

-- Insert sample newsletter subscribers
INSERT INTO `newsletter_subscriber` (`subscriber_email`, `subscriber_name`, `subscriber_language_pref`) VALUES
('johanna.karlsson@example.com', 'Johanna Karlsson', 'sv'),
('mikko.nieminen@example.fi', 'Mikko Nieminen', 'fi'),
('anna.lindholm@example.com', 'Anna Lindholm', 'sv');

-- Add a few event log entries
INSERT INTO `event_log` (`user_id`, `event_type`, `event_description`, `product_id`) VALUES
(1, 'create', 'Skapade produkt: Trollvinter', 1),
(1, 'create', 'Skapade produkt: Muumipeikko ja pyrstötähti', 2),
(1, 'update', 'Uppdaterade pris på: Harry Potter och De Vises Sten', 4),
(1, 'create', 'Skapade produkt: Sibelius Symphony No. 2', 6);