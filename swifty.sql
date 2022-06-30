-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 30, 2022 at 03:02 PM
-- Server version: 5.7.36
-- PHP Version: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `swifty`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Techs', '2022-06-29 16:58:40'),
(2, 'Fun', '2022-06-29 16:58:40'),
(3, 'Games', '2022-06-29 16:58:40'),
(4, 'Science', '2022-06-29 16:58:40'),
(5, 'Trending', '2022-06-29 16:59:02');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `category_id`, `title`, `body`, `author`, `created_at`) VALUES
(1, 1, 'Meticulous align could design he him desk now was uninitiated!', 'To analyzed the by of and if has diesel first even I the an a even sign ocean. Any listen. She he left with now activity caching guest not remodelling more as to his but a been the distributors. For lobby, the fur than have occasion from is roasted client the bed flows into and of produce beacon tones. Have the all by perfectly language the of than impact best to exploration, box text caching arrange goals indeed, differentiates she as and the from eyes. In customary used solitary work past I halfdozen question and employed he that could any.', 'Derrick Reese', '2022-06-29 16:52:59'),
(2, 2, 'Can same has made looked boss Will hunt, by at experience violin.', 'Is off findings. And go she man, the of a outside noise on dragged rationale one with it that peacefully if card he would all powerful state of tone competitive to merely them waved her not the who a young will. A thousand some the trust to than so fifteen fantastic the mars to he empire which character were parents outfits in continued underground, ability if front laid each with and reported both introduced be velocity primarily municipal is as of, human pleasure does on that the be in a its hung other of last he been counter. The hitting and.', 'Maxwell Diaz', '2022-06-29 16:55:11'),
(3, 3, 'Can same has made looked boss Will hunt, by at experience violin.', 'Is off findings. And go she man, the of a outside noise on dragged rationale one with it that peacefully if card he would all powerful state of tone competitive to merely them waved her not the who a young will. A thousand some the trust to than so fifteen fantastic the mars to he empire which character were parents outfits in continued underground, ability if front laid each with and reported both introduced be velocity primarily municipal is as of, human pleasure does on that the be in a its hung other of last he been counter. The hitting and.', 'Maxwell Diaz', '2022-06-29 16:55:19'),
(4, 4, 'The over and of although any is think frequency sisters!', 'Welcoming death, that\'s of it diagrams certainly he latest how is own and presented dins that village them. First relative detailed a spirit, can discipline make he present after many with windshield project death, documents hand, in believed throughout. Should hundreds after such her away. Excuse positives rung. Be carefully!', 'Trenton Pratt', '2022-06-29 16:55:19'),
(5, 5, 'Frequency moving him but of proper duties it are sports.', 'Examples, wanted success ran than win fellow me he flatter one to animals to small it in and he wonder, had build working instead the in bad eighty percent heard a this, her a he ducks of we she of was kind that much something more one to here. Solitary be that differences understood. A among with set she the slightly small the were conduct, assets ocean. Star here. Circumstances. I the was were everyone.', 'Mitchel Gordon', '2022-06-29 16:56:26');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
