-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 29, 2025 at 12:07 AM
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
-- Database: `trading_card_library`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_card_with_price` (IN `p_name` VARCHAR(100), IN `p_card_type_id` INT, IN `p_rarity` VARCHAR(50), IN `p_set_name` VARCHAR(100), IN `p_description` TEXT, IN `p_price` DECIMAL(10,2), IN `p_source` VARCHAR(100), IN `p_date_checked` DATE, IN `p_image_url` VARCHAR(255))   BEGIN
    INSERT INTO cards (name, card_type_id, rarity, set_name, description, image_url)
    VALUES (p_name, p_card_type_id, p_rarity, p_set_name, p_description, p_image_url);

    SET @last_card_id = LAST_INSERT_ID();

    INSERT INTO market_prices (card_id, price, source, date_checked)
    VALUES (@last_card_id, p_price, p_source, p_date_checked);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `card_type_id` int(11) DEFAULT NULL,
  `rarity` enum('Common','Uncommon','Rare','Ultra Rare','Secret Rare','Hyper Rare','Double Rare','Promo','Reverse Holofoil','Super Rare','Ghost Rare','Ultimate Rare','Platinum Secret Rare','Ultra Secret Rare','Secret Ultra Rare','Prismatic Secret Rare','Extra Secret Rare','Quarter Century Secret Rare','Collector’s Rare','Mythic Rare','Legendary','Enchanted') NOT NULL,
  `set_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `pricing_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (`id`, `name`, `card_type_id`, `rarity`, `set_name`, `description`, `image_url`, `pricing_url`) VALUES
(22, 'Blue-Eyes White Dragon (SDWD-EN003)', 13, 'Common', 'Blue-Eyes White Destiny Structure Deck', '\"This legendary dragon is a powerful engine of destruction. Virtually invincible, very few have faced this awesome creature and lived to tell the tale.\"', 'https://tcgplayer-cdn.tcgplayer.com/product/616560_in_1000x1000.jpg', NULL),
(23, 'Sky Striker Ace - Raye', 14, 'Ultra Rare', 'Magnificent Mavens', '(Quick Effect): You can Tribute this card; Special Summon 1 \"Sky Striker Ace\" monster from your Extra Deck to the Extra Monster Zone. While this card is in your GY, if a face-up \"Sky Striker Ace\" Link Monster you control is destroyed by battle, or leaves the field because of an opponent\'s card effect: You can Special Summon this card. You can only use each effect of \"Sky Striker Ace - Raye\" once per turn.', 'https://tcgplayer-cdn.tcgplayer.com/product/450983_in_1000x1000.jpg', NULL),
(27, 'Graceful Charity', 20, 'Ultra Rare', 'YuGiOh Starter Deck: Pegasus', 'Draw 3 discard 2', 'https://storage.googleapis.com/images.pricecharting.com/bb2d1a910d8d1708b32564781df25933712b98b6c737bd7db7c59ec04093fa5b/1600.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `card_types`
--

CREATE TABLE `card_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `tcg` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `card_types`
--

INSERT INTO `card_types` (`id`, `type_name`, `tcg`) VALUES
(4, 'Basic Pokémon', 'Pokémon'),
(5, 'Stage 1', 'Pokémon'),
(6, 'Stage 2', 'Pokémon'),
(7, 'EX', 'Pokémon'),
(8, 'GX', 'Pokémon'),
(9, 'V', 'Pokémon'),
(10, 'VMAX', 'Pokémon'),
(11, 'Trainer', 'Pokémon'),
(12, 'Energy', 'Pokémon'),
(13, 'Normal Monster', 'Yu-Gi-Oh!'),
(14, 'Effect Monster', 'Yu-Gi-Oh!'),
(15, 'Fusion Monster', 'Yu-Gi-Oh!'),
(16, 'Synchro Monster', 'Yu-Gi-Oh!'),
(17, 'Xyz Monster', 'Yu-Gi-Oh!'),
(18, 'Pendulum Monster', 'Yu-Gi-Oh!'),
(19, 'Link Monster', 'Yu-Gi-Oh!'),
(20, 'Spell Card', 'Yu-Gi-Oh!'),
(21, 'Trap Card', 'Yu-Gi-Oh!'),
(22, 'Creature', 'Magic'),
(23, 'Instant', 'Magic'),
(24, 'Sorcery', 'Magic'),
(25, 'Enchantment', 'Magic'),
(26, 'Artifact', 'Magic'),
(27, 'Planeswalker', 'Magic'),
(28, 'Land', 'Magic'),
(29, 'Character', 'Lorcana'),
(30, 'Action', 'Lorcana'),
(31, 'Item', 'Lorcana'),
(32, 'Song', 'Lorcana'),
(33, 'Location', 'Lorcana');

-- --------------------------------------------------------

--
-- Table structure for table `market_prices`
--

CREATE TABLE `market_prices` (
  `id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `date_checked` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `market_prices`
--

INSERT INTO `market_prices` (`id`, `card_id`, `price`, `source`, `date_checked`) VALUES
(19, 22, 1.50, 'PriceCharting', '2025-04-23'),
(20, 23, 1.35, 'PriceCharting', '2025-04-23'),
(27, 27, 5.73, 'PriceCharting', '2025-04-27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `card_type_id` (`card_type_id`);

--
-- Indexes for table `card_types`
--
ALTER TABLE `card_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `market_prices`
--
ALTER TABLE `market_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `card_id` (`card_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `card_types`
--
ALTER TABLE `card_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `market_prices`
--
ALTER TABLE `market_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`card_type_id`) REFERENCES `card_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `market_prices`
--
ALTER TABLE `market_prices`
  ADD CONSTRAINT `market_prices_ibfk_1` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
