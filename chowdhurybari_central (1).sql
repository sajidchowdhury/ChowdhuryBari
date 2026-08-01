-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2026 at 08:55 AM
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
-- Database: `chowdhurybari_central`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_infos`
--

CREATE TABLE `about_infos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `headline` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `about_infos`
--

INSERT INTO `about_infos` (`id`, `headline`, `description`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 'আমরা কারা?', 'চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা একটি সম্পূর্ণ স্বেচ্ছাসেবী, সমাজ-চালিত সংগঠন।', NULL, '2026-07-10 10:25:48', '2026-07-10 10:25:48');

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

CREATE TABLE `buildings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `road_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_phone` varchar(255) NOT NULL,
  `caretaker_name` varchar(255) DEFAULT NULL,
  `caretaker_phone` varchar(255) DEFAULT NULL,
  `structure_type` enum('building','tin_shed','other') NOT NULL DEFAULT 'building',
  `usage_type` enum('residential','shop','mixed') NOT NULL DEFAULT 'residential',
  `building_category` varchar(255) DEFAULT NULL,
  `per_family_amount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `billing_family_count` int(30) UNSIGNED DEFAULT NULL,
  `floor_count` int(30) UNSIGNED NOT NULL DEFAULT 1,
  `families_per_floor` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `has_security` tinyint(1) NOT NULL DEFAULT 0,
  `has_cleaning` tinyint(1) NOT NULL DEFAULT 0,
  `google_lt` varchar(255) DEFAULT NULL,
  `google_ln` varchar(255) DEFAULT NULL,
  `extra_information` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `buildings`
--

INSERT INTO `buildings` (`id`, `road_id`, `name`, `owner_name`, `owner_phone`, `caretaker_name`, `caretaker_phone`, `structure_type`, `usage_type`, `building_category`, `per_family_amount`, `billing_family_count`, `floor_count`, `families_per_floor`, `has_security`, `has_cleaning`, `google_lt`, `google_ln`, `extra_information`, `image_path`, `created_at`, `updated_at`) VALUES
(8, 7, 'Kohinur Mention', 'Kohinur Akter', '01720628516', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 2, 1, 1, '23.010621845713267', '91.38035254432778', NULL, 'buildings/oPzh3hDs6Rl5JoQjIVHJDz8sfoBJb4G9ybKOeZVK.jpg', '2026-07-10 23:05:51', '2026-07-11 02:27:40'),
(9, 7, 'Delowar Hossain', 'Delowar Hossain', '01979750844', NULL, NULL, 'tin_shed', 'residential', 'tin_shed', 0, NULL, 1, 4, 1, 1, '23.01121846293226', '91.37997264654915', NULL, 'buildings/HbEew3ADfwIoIPi3HKwbOdXw6DLchFRQVNo0Ok4u.jpg', '2026-07-10 23:12:13', '2026-07-11 02:29:53'),
(10, 7, 'MOjummdar Monjil', 'Owahidur Rahman', '01818331860', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 2, 1, 1, NULL, NULL, NULL, 'buildings/MOF3VxaRL1D89ZtUpPxOFrFOTTu9eEHs5WZcwaOB.jpg', '2026-07-10 23:22:02', '2026-07-11 02:30:45'),
(11, 7, 'MOjummdar Monjil tin sheed', 'MOjummdar', '01818331860', NULL, NULL, 'tin_shed', 'residential', 'tin_shed', 0, NULL, 1, 1, 1, 1, '23.010639', '91.3815', NULL, 'buildings/suQ820e5lNBLWHuXmVdutZlgEF9uFcaycf7twiJB.jpg', '2026-07-10 23:22:02', '2026-07-11 02:31:38'),
(12, 7, 'miya bobhon', 'Ajmir Dider', '01782695702', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 5, 4, 1, 1, '23.011112746055943', '91.37991282965507', NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(13, 7, 'Sobu', 'Sobuj', '01819333713', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 1, 1, 1, 1, NULL, NULL, 'under cocntruction', NULL, '2026-07-10 23:41:57', '2026-07-10 23:41:57'),
(14, 7, 'hUMAIUN KOBIR', 'Humayun', '01518300579', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 1, 1, NULL, NULL, NULL, NULL, '2026-07-10 23:41:57', '2026-07-10 23:41:57'),
(15, 7, 'Balla', 'Billal', '01975338849', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 1, 1, NULL, NULL, NULL, NULL, '2026-07-10 23:41:57', '2026-07-10 23:41:57'),
(16, 7, 'Jahangir Alam', 'Jahingir Alam', '01718781479', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 0, 0, '23.011058099089823', '91.38001470379484', NULL, NULL, '2026-07-10 23:41:57', '2026-07-10 23:41:57'),
(17, 7, '185/Rofikur Rahman Patwary', 'Rofikur Rahman Patwary', '01768225594', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 1, 1, 1, 1, NULL, NULL, NULL, NULL, '2026-07-10 23:46:57', '2026-07-10 23:46:57'),
(18, 7, 'Humayn chow tin shad', 'Humaun Chowdhury', '01814700699', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 1, 1, '23.010887366064406', '91.38026957106979', NULL, NULL, '2026-07-10 23:53:10', '2026-07-10 23:53:10'),
(19, 7, 'hassan medical', 'hasan', '01711374831', NULL, NULL, 'building', 'residential', NULL, 0, NULL, 1, 1, 1, 1, '23.010951742145696', '91.38018048556393', NULL, NULL, '2026-07-10 23:53:10', '2026-07-10 23:53:10'),
(20, 7, 'Patwary Monjil', 'Johir Patwary', '01790001111', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 5, 1, 1, 1, '23.01096335673807', '91.38109395636404', NULL, NULL, '2026-07-10 23:59:40', '2026-07-10 23:59:40'),
(21, 7, 'OC Robiul Haq', 'OC Robiul Haq', '01815599466', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 1, 1, 1, '23.011075615083005', '91.38094706107944', NULL, NULL, '2026-07-11 00:02:51', '2026-07-11 00:02:51'),
(22, 7, 'Shahidul Islam Chowdhurry (DC)', 'Shahid Chowdhury', '01819610936', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 1, 1, 1, '23.01113828481851', '91.38068137582795', NULL, NULL, '2026-07-11 00:07:40', '2026-07-11 00:07:40'),
(23, 7, 'Khondokar Mention', 'Khondokor Mention', '01815497833', 'Mohammad Mortuza', NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 4, 4, 1, 1, '23.011604112359553', '91.38046532347722', 'clam 10 poribar', NULL, '2026-07-11 00:15:49', '2026-07-11 00:15:49'),
(24, 7, 'HAYDAYE ULLAH', 'MOHAMMAD HAYDAYT ULLAH', '01840495082', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 3, 2, 1, 1, '23.012073', '91.381542', NULL, NULL, '2026-07-11 00:20:51', '2026-07-11 00:20:51'),
(25, 7, 'ShaAlam Tin Shad', 'Sha Alam', '01518300579', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 7, 1, 1, '23.012234', '91.381645', NULL, NULL, '2026-07-11 00:27:41', '2026-07-11 00:27:41'),
(26, 7, 'Fatema Mention', 'Jamal Uddin Manager', '01817103153', 'ShaAlam', '01887035869', 'building', 'residential', 'above_4_floor', 0, NULL, 7, 3, 0, 0, '23.011917500000003', '91.38137599999999', '20 family', NULL, '2026-07-11 00:34:21', '2026-07-11 00:34:21'),
(27, 7, 'Shorwardi Villa', 'SHA ALAM', '01823573642', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 3, 1, 1, 1, '23.01223959822412', '91.3816947166612', NULL, NULL, '2026-07-11 00:36:25', '2026-07-11 00:36:25'),
(28, 7, 'rubel chow colony', 'sajid', '01787492561', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 1, 1, '23.012146264211914', '91.38173549916696', NULL, NULL, '2026-07-11 00:44:42', '2026-07-11 00:44:42'),
(29, 10, 'Patwary Mention 2', 'Shamima Sultana', '01867666647', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 1, 0, 0, '23.011808', '91.38186833333332', NULL, NULL, '2026-07-11 00:54:09', '2026-07-11 00:54:09'),
(30, 10, 'Patwary Mention 2 TIN 1', 'Shamima Sultana', '01867666647', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 3, 0, 0, NULL, NULL, NULL, NULL, '2026-07-11 00:54:09', '2026-07-11 00:54:09'),
(31, 10, 'Patwary Mention 2 TIN 2', 'Shamima Sultana', '01867666647', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 3, 0, 0, '23.011502', '91.38207', NULL, NULL, '2026-07-11 00:54:09', '2026-07-11 00:54:09'),
(32, 10, 'Patwary Mention 2 TIN 3', 'Shamima Sultana', '01867666647', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 2, 0, 0, '23.011502', '91.38207', NULL, NULL, '2026-07-11 00:54:09', '2026-07-11 00:54:09'),
(33, 10, 'nurjahan bobhon', 'Abul Hossain Sobuj', '01841915476', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 4, 2, 0, 0, NULL, NULL, 'total 7', NULL, '2026-07-11 01:02:50', '2026-07-11 01:02:50'),
(34, 10, 'Mosharof Hossain Daroga', 'Mosharof Hossain', '01728886568', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 2, 0, 0, '23.011502', '91.38207', 'nicher 3 , duplext , uporer 2 totay 3ta kore 6ta', NULL, '2026-07-11 01:10:05', '2026-07-11 01:10:05'),
(35, 10, 'bilkis monjil', 'jakir ahmed', '01816432343', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 7, 2, 1, 1, '23.011856241218865', '91.3805718486452', '12 family', NULL, '2026-07-11 01:14:45', '2026-07-11 01:14:45'),
(36, 10, 'Altaf  bobon', 'Sorwar Hossain Bahar', '01854422019', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 2, 1, 1, '23.011616945382436', '91.38069836177928', NULL, NULL, '2026-07-11 01:25:31', '2026-07-11 01:25:31'),
(37, 10, 'Tasnim Bobon', 'Forid Ahmad`', '01811262666', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 2, 1, 1, '23.012209956792038', '91.3813386087924', '11 family', NULL, '2026-07-11 01:33:48', '2026-07-11 01:33:48'),
(38, 10, 'Ahmed mention', 'Samnina Aktar', '01837618111', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 1, 1, 0, 0, '23.012342597111004', '91.38128868334903', NULL, NULL, '2026-07-11 01:38:58', '2026-07-11 01:38:58'),
(39, 11, 'HRS Bhuiyan Mention', 'Eng Jamal Ahmed Bhuiyan Roni', '01819138257', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 4, 1, 1, 1, '23.0129680269031', '91.3817980677204', '4 family', NULL, '2026-07-18 00:23:11', '2026-07-18 00:23:11'),
(40, 11, 'Nahar  Bahavon', 'Eng Mohammad Nuruzzaman', '01811606622', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 4, 4, 1, 1, NULL, NULL, 'full family 14 confirm', 'uploads/field-data/field_1784354714_6a5b179a130cb.jpeg', '2026-07-18 00:23:11', '2026-07-18 00:23:11'),
(41, 11, 'Sorwas colony', 'Sarwar', '01864228800', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 2, 1, 1, '23.01324537565003', '91.38172821859318', NULL, 'uploads/field-data/field_1784354937_6a5b18797419c.jpeg', '2026-07-18 00:23:11', '2026-07-18 00:23:11'),
(42, 11, 'Roni colony 1', 'Eng Jamal Ahmed Bhuiyan Roni', '01819138257', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 6, 1, 1, '23.013206500571965', '91.3816964116578', NULL, 'uploads/field-data/field_1784355086_6a5b190e7a52f.jpeg', '2026-07-18 00:23:11', '2026-07-18 00:23:11'),
(43, 11, 'Eng Rony Colony 2', 'Eng Mohammad Nuruzzaman', '01811606622', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 6, 1, 1, '23.01322713304355', '91.38168166880183', NULL, 'uploads/field-data/field_1784355308_6a5b19ec48e26.jpeg', '2026-07-18 00:23:11', '2026-07-18 00:23:11'),
(44, 11, 'eng rony collony 3', 'eng rony', '01811606622', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 5, 0, 0, NULL, NULL, NULL, NULL, '2026-07-18 00:23:11', '2026-07-18 00:23:11'),
(45, 11, 'eng rony colly 4', 'eng rony coly 4', '01811606622', NULL, NULL, 'building', 'residential', NULL, 0, NULL, 1, 4, 0, 0, NULL, NULL, NULL, NULL, '2026-07-18 00:23:11', '2026-07-18 00:23:11'),
(46, 11, 'eng rony coloy 5', 'Eng rony', '01811606622', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 10, 1, 1, '23.013308545114494', '91.38174671790894', NULL, NULL, '2026-07-18 00:23:11', '2026-07-18 00:23:11'),
(47, 11, 'eng rony coloy 6', 'eng rony', '01811606622', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 5, 0, 0, '23.013308545114494', '91.38174671790894', 'total family 32 need tro adjust', NULL, '2026-07-18 00:23:11', '2026-07-18 00:23:11'),
(48, 11, 'SA Mention', 'Shohel', '01823980642', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 2, 1, 1, '23.013314197700165', '91.3816514087572', '13 family', 'uploads/field-data/field_1784355781_6a5b1bc57db27.jpeg', '2026-07-18 00:23:11', '2026-07-18 00:23:11'),
(49, 11, 'Miltali House', 'mohammad naymot ullad', '01712277225', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 3, 3, 0, 0, '23.013307808738524', '91.38154832420946', 'family 7', 'uploads/field-data/field_1784355953_6a5b1c71b1b8f.jpeg', '2026-07-18 00:48:05', '2026-07-18 00:48:05'),
(50, 11, 'amena villa', 'Sirazul islam bhuya', '01746408327', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 12, 1, 1, '23.0127455', '91.3815615', NULL, 'uploads/field-data/field_1784356131_6a5b1d232c303.jpeg', '2026-07-18 00:48:05', '2026-07-18 00:48:05'),
(51, 11, 'Firoza Villa', 'Ratin', '01898809282', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 0, 0, '23.013575260058385', '91.38247975563361', NULL, 'uploads/field-data/field_1784356240_6a5b1d903c3be.jpeg', '2026-07-18 00:48:05', '2026-07-18 00:48:05'),
(52, 11, 'Faruk chowdhury collony', 'Faruk chowdhury', '01715809071', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 1, 1, '23.013575260058385', '91.38247975563361', NULL, 'uploads/field-data/field_1784356350_6a5b1dfe8c8ab.jpeg', '2026-07-18 00:48:05', '2026-07-18 00:48:05'),
(53, 11, 'Tahmina vobhon', 'Mohammad golumn kibria', '01718464813', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 5, 2, 0, 0, '23.0127455', '91.3815615', '9 family', 'uploads/field-data/field_1784356512_6a5b1ea0e8601.jpeg', '2026-07-18 00:48:05', '2026-07-18 00:48:05'),
(54, 11, 'Sattar mention', 'billall hosain', '01858549149', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 4, 2, 0, 0, '23.013174', '91.38118', '7 family', 'uploads/field-data/field_1784356590_6a5b1eeea7f47.jpeg', '2026-07-18 00:48:05', '2026-07-18 00:48:05'),
(55, 11, 'Soiyd monjil', 'Mojammel haq', '01828144287', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 2, 1, 1, '23.01323363252159', '91.38027658841852', '10 family', 'uploads/field-data/field_1784356756_6a5b1f942ce54.jpeg', '2026-07-18 00:48:05', '2026-07-18 00:48:05'),
(56, 11, 'new building', 'mr x', '01787492561', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 1, 1, 1, '23.013219727312666', '91.38019820981903', NULL, 'uploads/field-data/field_1784356894_6a5b201e2a7c0.jpeg', '2026-07-18 00:48:05', '2026-07-18 00:48:05'),
(57, 11, 'Hanif mention', 'Hanif', '01855703563', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 2, 1, 1, '23.013228006375012', '91.3802349316827', '12 family', 'uploads/field-data/field_1784357251_6a5b2183cdae1.jpeg', '2026-07-18 00:48:05', '2026-07-18 00:48:05'),
(58, 11, 'Shopno mohol', 'Johir Gong', '01815128374', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 2, 0, 1, '23.01303950595926', '91.38021503297458', '11 family', 'uploads/field-data/field_1784357435_6a5b223b0e268.jpeg', '2026-07-18 00:57:41', '2026-07-18 00:57:41'),
(59, 11, 'new building', 'new', '000000000', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 4, 0, 0, '23.01273525', '91.37996725', NULL, 'uploads/field-data/field_1784357515_6a5b228b5eeab.jpeg', '2026-07-18 00:57:41', '2026-07-18 00:57:41'),
(60, 11, 'Turky Bobhon', 'Mohammad Forid', '01867178852', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 3, 0, 0, '23.01273525', '91.37996725', '16 family', 'uploads/field-data/field_1784357713_6a5b2351ba4b8.jpeg', '2026-07-18 00:57:41', '2026-07-18 00:57:41'),
(61, 11, 'Mubarok Ali Alam', 'Mobarok ALi alm', '01821925784', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 1, 0, 0, '23.012418', '91.379974', 'famiky 3', 'uploads/field-data/field_1784357850_6a5b23da2b55c.jpeg', '2026-07-18 00:57:41', '2026-07-18 00:57:41'),
(62, 11, 'mojibul haq', 'Mojibul haq', '01580515716', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 1, 1, 1, '23.013115841869958', '91.37975984555057', '3 family', NULL, '2026-07-18 00:59:56', '2026-07-18 00:59:56'),
(63, 11, 'haydayt monjil', 'Haydayt Ullah', '01892027513', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 4, 1, 1, 1, '23.01302887689433', '91.37969582665671', NULL, NULL, '2026-07-18 01:20:16', '2026-07-18 01:20:16'),
(64, 11, '/manik colony', 'mohammad yeakub', '01716345543', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 0, 0, '23.013052311955416', '91.37905666534952', NULL, NULL, '2026-07-18 01:20:16', '2026-07-18 01:20:16'),
(65, 11, 'Ekramul Haq', 'Mohammad Nurlul Haq', '01918581113', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 1, 0, 0, '23.01321257135671', '91.37992258767429', NULL, NULL, '2026-07-18 01:20:16', '2026-07-18 01:20:16'),
(66, 11, 'Babar bobhom', 'babar', '010000000', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 3, 2, 0, 0, '23.013561000000003', '91.3790245', '5 family', NULL, '2026-07-18 01:20:16', '2026-07-18 01:20:16'),
(67, 11, 'jahan villa', 'kazi mohammad ullah azad', '01764376911', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 2, 0, 0, '23.013313994257437', '91.37905304374122', '3 family', NULL, '2026-07-18 01:20:16', '2026-07-18 01:20:16'),
(68, 11, 'jannat mention', 'BB kadiza Abdur Rawf', '01819830616', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 7, 2, 0, 0, '23.01360102167382', '91.37897410858369', '12 family', NULL, '2026-07-18 01:20:16', '2026-07-18 01:20:16'),
(69, 12, 'Tokir chowdhury', 'Tokir chowdhury', '01838823979', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 3, 1, 0, 1, '23.0117565', '91.382182', NULL, 'buildings/4ucb9HeaoO4slZEGY1FjgvVDFkLjc3u1Ry7OKhUm.jpg', '2026-07-24 23:00:13', '2026-07-25 00:57:47'),
(70, 12, 'Pervin Cortege', 'Pervin chowdhury', '01819953354', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 3, 1, 0, 0, '23.0117685', '91.382149', '2 family', NULL, '2026-07-24 23:00:13', '2026-07-24 23:00:13'),
(71, 12, 'Shams Monjil', 'kam shamsuddin', '01601300467', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 1, 0, 0, '23.011759', '91.382229', NULL, NULL, '2026-07-24 23:00:13', '2026-07-24 23:00:13'),
(72, 12, 'Hassan chowdhury', 'Hassan chowdhury', '01716150290', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 1, 1, 0, 0, '23.011757361051504', '91.38216789077254', NULL, NULL, '2026-07-24 23:00:13', '2026-07-24 23:00:13'),
(73, 12, 'Aftab uddin chowdhury', 'Mayhad chowdhury', '0100000', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 15, 1, 1, '23.011775453985308', '91.38214138162937', 'receive 800 taka', NULL, '2026-07-24 23:00:13', '2026-07-24 23:00:13'),
(74, 12, 'Chompa chowdhury', 'ohin', '01971676314', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 0, 0, '23.011781737651198', '91.38212618674888', NULL, NULL, '2026-07-24 23:00:13', '2026-07-24 23:00:13'),
(75, 12, 'Shopon chowdhury', 'Shapon chowdhury', '01700625052', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 1, 3, 0, 0, '23.01175902227746', '91.38216232557863', NULL, NULL, '2026-07-24 23:00:13', '2026-07-24 23:00:13'),
(76, 12, 'israfil chowdhury', 'Sajid chowdhury', '01787492561', NULL, NULL, 'building', 'residential', 'above_4_floor', 100, 11, 4, 4, 1, 1, '23.011926097955346', '91.38194821200491', '11 family', NULL, '2026-07-24 23:00:13', '2026-07-25 01:10:35'),
(77, 12, 'Abdul Kadir JIlani Madrasha', 'Md josim uddin', '01913391014', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 1, 1, '23.0118983096371', '91.38190702465387', NULL, NULL, '2026-07-24 23:07:36', '2026-07-24 23:07:36'),
(78, 12, 'Mauf chowdhury collony', 'Maruf chowdhury', '017', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 9, 0, 0, '23.012025496167606', '91.38194294949753', NULL, NULL, '2026-07-24 23:07:36', '2026-07-24 23:07:36'),
(79, 12, 'Youmlikha chowdhury', 'Younmilkha chowdhury', '017', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 10, 0, 0, '23.012014276080382', '91.38195227535304', NULL, NULL, '2026-07-24 23:07:36', '2026-07-24 23:07:36'),
(80, 12, 'Tasir chowdhury', 'Tasir chowdhuru', '017', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 10, 0, 0, '23.011864800203206', '91.38201578652512', NULL, NULL, '2026-07-24 23:07:36', '2026-07-24 23:07:36'),
(81, 12, 'SN Towerf', 'Shahed Chowdhury', '01710312518', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 4, 0, 0, '23.012048048378798', '91.38193332392412', '19 family', NULL, '2026-07-24 23:10:27', '2026-07-24 23:10:27'),
(82, 12, 'Bayzid Village', 'Pavel Chowdhury', '01711783924', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 1, 2, 0, 0, '23.011999799644606', '91.38201971612617', '2 family', NULL, '2026-07-24 23:31:10', '2026-07-24 23:31:10'),
(83, 12, 'Bitul Amirat', 'AKM Anawarullah', '01915392534', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 4, 4, 1, 1, '23.01235670821647', '91.3818067616132', '11 family', NULL, '2026-07-24 23:31:10', '2026-07-24 23:31:10'),
(84, 12, 'Semlima', 'Rtib Chowdhury', '01741859891', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 4, 0, 0, '23.01246969152303', '91.38182393170428', '19 family', NULL, '2026-07-24 23:31:10', '2026-07-24 23:31:10'),
(85, 12, 'Rahela Mention', 'Mojibur Rahman', '01726495216', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 3, 1, 1, 1, '23.013001146416173', '91.38193009270982', '4 family', NULL, '2026-07-24 23:31:10', '2026-07-24 23:31:10'),
(86, 12, 'Faruk CHowdhury', 'Faruk Chowdhury', '01715809071', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 3, 2, 1, 1, '23.01293074666662', '91.38194012760628', '4 family', NULL, '2026-07-24 23:43:06', '2026-07-24 23:43:06'),
(87, 12, 'JUniyad Chowdhury Bobhon', 'Didar Chowdhury', '01711012037', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 5, 2, 0, 0, '23.01291900064707', '91.38194864787063', '7 family', NULL, '2026-07-25 00:23:50', '2026-07-25 00:23:50'),
(88, 12, 'Nur Monzil', 'Nurullah DGM', '01711735301', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 1, 0, 0, '23.012912118978726', '91.38194314054151', '10 family', NULL, '2026-07-25 00:23:50', '2026-07-25 00:23:50'),
(89, 12, 'Shahana Mention', 'mizanur rahman', '01711371497', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 2, 0, 0, '23.01298184704166', '91.38198336562877', '10 FAMILY', NULL, '2026-07-25 00:23:50', '2026-07-25 00:23:50'),
(90, 12, 'Abdul batin', 'Abdul batin', '0100', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 1, 1, 0, 0, '23.012954044250566', '91.38197080288614', NULL, NULL, '2026-07-25 00:23:50', '2026-07-25 00:23:50'),
(91, 12, 'Bohiya cotage', 'Shakil bouhaya', '01857255151', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 2, 0, 0, '23.01273788275885', '91.38196883861225', '4 family', NULL, '2026-07-25 00:23:50', '2026-07-25 00:23:50'),
(92, 12, 'Mizan colonoy', 'Mizanur rahman', '01711371497', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 0, 0, '23.013056495045635', '91.38211818977463', NULL, NULL, '2026-07-25 00:23:50', '2026-07-25 00:23:50'),
(93, 12, 'Jafor Mention', 'MOhammad Rasel', '01875919533', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 5, 2, 0, 0, '23.01331246764004', '91.38247414876692', '9 family', NULL, '2026-07-25 00:23:50', '2026-07-25 00:23:50'),
(94, 12, 'Nazma VIlla', 'Soriyot Ullah', '0177', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 0, 0, '23.013404824752964', '91.38247164073648', 'tk dayna', NULL, '2026-07-25 00:23:50', '2026-07-25 00:23:50'),
(95, 12, 'Kazi azizul haq bobhon', 'Azaijul haq', '01715146834', NULL, NULL, 'building', 'residential', NULL, 0, NULL, 6, 2, 0, 0, '23.01319181988643', '91.38203065748073', '2 family', NULL, '2026-07-25 00:23:50', '2026-07-25 00:23:50'),
(96, 12, 'Taher mention', 'Kazi abu taher', '017', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 2, 0, 0, '23.01297795981208', '91.38195603714954', NULL, NULL, '2026-07-25 00:23:50', '2026-07-25 00:23:50'),
(97, 12, 'Mojila feroza', 'Iqbal hosain', '01821994733', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 5, 1, 0, 0, '23.013195454009', '91.38201653284136', NULL, NULL, '2026-07-25 00:23:50', '2026-07-25 00:23:50'),
(98, 12, 'maionuddin villa', 'Md minuddon', '01843733346', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 5, 2, 0, 0, '23.013005807713125', '91.38196947366349', '7 family', NULL, '2026-07-25 00:23:50', '2026-07-25 00:23:50'),
(99, 12, 'KHan Billash', 'Suruj Khan', '01829659352', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 2, 0, 0, '23.01289987129083', '91.3819366570593', '9 fmaily', NULL, '2026-07-25 00:23:50', '2026-07-25 00:23:50'),
(100, 12, 'Arib shafwon villa', 'Dr rasel', '01746408327', NULL, NULL, 'building', 'residential', NULL, 0, NULL, 1, 5, 0, 0, '23.012871702400552', '91.38193864305114', '5 family', NULL, '2026-07-25 00:23:51', '2026-07-25 00:23:51'),
(101, 12, 'Borosr Villa', 'Shaeda chowdhury', '017', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 1, 1, 0, 0, '23.01275341817314', '91.38186747390863', NULL, NULL, '2026-07-25 00:23:51', '2026-07-25 00:23:51'),
(102, 12, 'Mahmudul Haq Buhaya Colony', 'Mahmudil haq buhaya', '010000', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 9, 1, 1, '23.012734478295364', '91.38186590105404', '9 family', NULL, '2026-07-31 23:03:35', '2026-07-31 23:03:35'),
(103, 12, 'zom zom cortege', 'Sheuly', '01782559010', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 0, 0, '23.012525119147266', '91.38185947462725', '1 unut', NULL, '2026-07-31 23:03:35', '2026-07-31 23:03:35'),
(104, 12, 'Eng rony', 'Eng roni', '01819138257', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 2, 1, 0, 0, '23.012407309507285', '91.38181373240805', '1', NULL, '2026-07-31 23:03:35', '2026-07-31 23:03:35'),
(105, 12, 'Nur amin luxury colony', 'Nur Alam Luxury', '0170000', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 0, 0, '23.0131283', '91.3822037', '1 family', NULL, '2026-07-31 23:03:35', '2026-07-31 23:03:35'),
(106, 12, 'Kamrul Honda', 'Kamrul Hound', '01819903572', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 12, 0, 0, '23.013148642857146', '91.38227635714289', NULL, NULL, '2026-07-31 23:03:35', '2026-07-31 23:03:35'),
(107, 12, 'Anamul Korim Bulding', 'Anamul KOrim', '0170000000', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 7, 2, 0, 0, '23.012802', '91.382401', 'Under Construction', NULL, '2026-07-31 23:03:35', '2026-07-31 23:03:35'),
(108, 12, 'Vision Orbit Tower', 'Sanaullah (SHovaputi)', '01730197227', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 10, 3, 0, 0, '23.013601310319338', '91.38243721924897', '30 family', NULL, '2026-07-31 23:03:35', '2026-07-31 23:03:35'),
(109, 14, 'Sufi Saleha garden', 'Delwar hossain manik', '01711799433', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 7, 2, 0, 0, '23.013658721030605', '91.38242808095737', 'under constriction', NULL, '2026-07-31 23:03:35', '2026-07-31 23:03:35'),
(110, 14, 'Nahar Buhaya Monjil', 'Shabuddin', '01864129667', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 2, 0, 0, '23.01257410173598', '91.38236367268276', '11 family', NULL, '2026-07-31 23:14:13', '2026-07-31 23:14:13'),
(111, 14, 'Siraz and hariz', 'Sirazul islam and haris', '01819138257', NULL, NULL, 'building', 'residential', NULL, 0, NULL, 1, 1, 0, 0, '23.01292105679237', '91.38241660275138', 'under counteraction', NULL, '2026-07-31 23:14:13', '2026-07-31 23:14:13'),
(112, 14, 'Nurjahan Alow', 'Mojibur Rahman', '01711306424', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 4, 4, 0, 0, '23.012073823136653', '91.38229138183686', '14 unit', NULL, '2026-07-31 23:14:13', '2026-07-31 23:14:13'),
(113, 14, 'Master Decorator Colony', 'Sumon', '01817001622', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 5, 0, 0, NULL, NULL, '5 family', NULL, '2026-07-31 23:14:13', '2026-07-31 23:14:13'),
(114, 14, 'Mahbubur rahman', 'Kochi', '01921116564', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 1, 1, 0, 0, '23.01223772368473', '91.3823229249578', '1 family', NULL, '2026-07-31 23:28:14', '2026-07-31 23:28:14'),
(115, 14, 'Babul commissioner', 'Babul comissionr', '01314336067', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 3, 1, 0, 0, '23.013434', '91.38236624999999', NULL, NULL, '2026-07-31 23:28:14', '2026-07-31 23:28:14'),
(116, 14, 'Kiron Vobon', 'Kiron chowdhury', '01717990425', NULL, NULL, 'building', 'residential', 'below_or_equal_4_floor', 0, NULL, 3, 1, 0, 0, '23.013113489035327', '91.38272962724739', '3 family', NULL, '2026-07-31 23:28:14', '2026-07-31 23:28:14'),
(117, 14, 'Surma bobon', 'Surma chowdhury', '01711012037', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 6, 2, 0, 0, '23.013387788929236', '91.38263514885354', '5 family', NULL, '2026-07-31 23:28:14', '2026-07-31 23:28:14'),
(118, 14, 'Nilufa Bobhon', 'Kazi Jahid', '01876092950', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 0, 0, '23.0132125', '91.3827515', '1 family', NULL, '2026-07-31 23:28:14', '2026-07-31 23:28:14'),
(119, 14, 'Mahbubur rahman colony', 'Mahbur rahman', '01921116564', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 2, 0, 0, '23.013462', '91.383064', 'under conn', NULL, '2026-07-31 23:37:55', '2026-07-31 23:37:55'),
(120, 14, 'Mahbubur rahman colony', 'Mahbubur rahman colony', '01921116564', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 12, 0, 0, '23.013095', '91.382672', NULL, NULL, '2026-07-31 23:37:55', '2026-07-31 23:37:55'),
(121, 14, 'Kurshid Alam Buhaya Colony', 'Kurshid Alam Buhaya', '01714279094', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 5, 2, 0, 0, '30.221195', '47.767723', 'jami anwar 01720093295\r\n9 family', NULL, '2026-07-31 23:48:30', '2026-07-31 23:48:30'),
(122, 14, 'Kurshid Alam Buhaya Colony 2', 'Kurshid Alam Buhaya Colony 2', '01714279094', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 12, 0, 0, '23.013437', '91.383881', NULL, NULL, '2026-07-31 23:48:30', '2026-07-31 23:48:30'),
(123, 14, 'Kurshid Alam Buhaya Colony 2', 'Kurshid Alam Buhaya', '01714279094', NULL, NULL, 'building', 'residential', NULL, 0, NULL, 1, 10, 0, 0, '1.2894', '103.8499', '10 family', NULL, '2026-07-31 23:48:30', '2026-07-31 23:48:30'),
(124, 14, 'Kurshid Alam Buhaya Colony 4', 'Kurshid Alam Buhaya', '01714279094', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 7, 0, 0, '1.2894', '103.8499', NULL, NULL, '2026-07-31 23:48:30', '2026-07-31 23:48:30'),
(125, 14, 'Oli Ahmand COmpany', 'Oli Ahmed COmpany', '01819794777', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 1, 0, 0, '23.01315375', '91.38271175', '1 family', NULL, '2026-07-31 23:55:25', '2026-07-31 23:55:25'),
(126, 14, 'Nur jahan villa', 'NUr Amin', '01715641718', NULL, NULL, 'building', 'residential', 'above_4_floor', 0, NULL, 4, 3, 0, 0, '23.013474850275227', '91.3831949038532', '10 family', NULL, '2026-07-31 23:55:25', '2026-07-31 23:55:25'),
(127, 14, 'buhaya corteg', 'Ishak', '01830494240', 'ilis', '01881844317', 'building', 'residential', 'above_4_floor', 0, NULL, 6, 3, 0, 0, '23.0134735', '91.3826335', '18 family', NULL, '2026-08-01 00:02:34', '2026-08-01 00:02:34'),
(128, 14, 'nur jahan plooi', 'Mojibur rahaman', '01711306424', NULL, NULL, 'building', 'residential', 'tin_shed', 0, NULL, 1, 10, 0, 0, '23.0132125', '91.3827515', NULL, NULL, '2026-08-01 00:02:34', '2026-08-01 00:02:34');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_infos`
--

CREATE TABLE `contact_infos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `office_hours` varchar(255) DEFAULT NULL,
  `recipient_email` varchar(255) DEFAULT NULL,
  `form_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_infos`
--

INSERT INTO `contact_infos` (`id`, `address`, `phone`, `email`, `whatsapp`, `office_hours`, `recipient_email`, `form_active`, `created_at`, `updated_at`) VALUES
(1, 'চৌধুরীপাড়া', '০১৭১১-২২৩৩৪৪', 'info@chowdhuripara.org', '8801711223344', 'সকাল ৮টা — রাত ১০টা', NULL, 1, '2026-07-07 13:45:13', '2026-07-07 13:45:13');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `family_reduction_applications`
--

CREATE TABLE `family_reduction_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `building_id` bigint(20) UNSIGNED NOT NULL,
  `current_family_count` int(10) UNSIGNED NOT NULL,
  `requested_family_count` int(10) UNSIGNED NOT NULL,
  `vacant_flat_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vacant_flat_ids`)),
  `requested_flat_states` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`requested_flat_states`)),
  `reason` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `field_data_collections`
--

CREATE TABLE `field_data_collections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `road_id` bigint(20) UNSIGNED DEFAULT NULL,
  `new_road_name` varchar(255) DEFAULT NULL,
  `building_name` varchar(255) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_phone` varchar(255) NOT NULL,
  `caretaker_name` varchar(255) DEFAULT NULL,
  `caretaker_phone` varchar(255) DEFAULT NULL,
  `building_category` varchar(255) DEFAULT NULL,
  `structure_type` varchar(255) NOT NULL DEFAULT 'building',
  `usage_type` varchar(255) NOT NULL DEFAULT 'residential',
  `floor_count` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `families_per_floor` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `has_security` tinyint(1) NOT NULL DEFAULT 0,
  `has_cleaning` tinyint(1) NOT NULL DEFAULT 0,
  `google_lt` varchar(255) DEFAULT NULL,
  `google_ln` varchar(255) DEFAULT NULL,
  `extra_information` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `flats_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`flats_data`)),
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `collected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `migrated_at` timestamp NULL DEFAULT NULL,
  `migrated_building_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `field_data_collections`
--

INSERT INTO `field_data_collections` (`id`, `road_id`, `new_road_name`, `building_name`, `owner_name`, `owner_phone`, `caretaker_name`, `caretaker_phone`, `building_category`, `structure_type`, `usage_type`, `floor_count`, `families_per_floor`, `has_security`, `has_cleaning`, `google_lt`, `google_ln`, `extra_information`, `image_path`, `flats_data`, `status`, `collected_by`, `migrated_at`, `migrated_building_id`, `created_at`, `updated_at`) VALUES
(2, NULL, 'FishariRoad', 'Kohinur Mention', 'Kohinur Akter', '01720628516', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 2, 1, 1, '23.010621845713267', '91.38035254432778', NULL, 'uploads/field-data/field_1783746040_6a51cdf888556.jpeg', '[{\"floor\":1,\"flat_number\":\"Floor 1 - Flat A\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112469466\",\"provider\":\"bpdb\"},{\"floor\":1,\"flat_number\":\"Floor 1 - Flat B\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112469465\",\"provider\":\"bpdb\"},{\"floor\":1,\"flat_number\":\"Floor 1\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"012010045444\",\"provider\":\"bpdb\"}]', 'migrated', 8, '2026-07-10 23:05:51', 8, '2026-07-10 23:00:40', '2026-07-10 23:05:51'),
(3, NULL, 'Fishery Road', 'Delowar Hossain', 'Delowar Hossain', '01979750844', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 4, 1, 1, '23.01121846293226', '91.37997264654915', NULL, NULL, '[]', 'migrated', 8, '2026-07-10 23:12:13', 9, '2026-07-10 23:11:56', '2026-07-10 23:12:13'),
(4, NULL, 'Fisheri Road', 'MOjummdar Monjil', 'Owahidur Rahman', '01818331860', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 2, 1, 1, NULL, NULL, NULL, 'uploads/field-data/field_1783747157_6a51d25503163.jpeg', '[{\"floor\":1,\"flat_number\":\"Floor 1\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"9512093\",\"provider\":\"bpdb\"}]', 'migrated', 8, '2026-07-10 23:22:02', 10, '2026-07-10 23:19:17', '2026-07-10 23:22:02'),
(5, NULL, 'fr', 'MOjummdar Monjil tin sheed', 'MOjummdar', '01818331860', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 1, 1, '23.010639', '91.3815', NULL, NULL, '[]', 'migrated', 8, '2026-07-10 23:22:02', 11, '2026-07-10 23:21:56', '2026-07-10 23:22:02'),
(6, NULL, 'fr', 'miya bobhon', 'Ajmir Dider', '01782695702', NULL, NULL, 'above_4_floor', 'building', 'residential', 5, 4, 1, 1, '23.011112746055943', '91.37991282965507', NULL, NULL, '[{\"floor\":1,\"flat_number\":\"Floor 1 - Flat A\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"00093035\",\"provider\":\"bpdb\"},{\"floor\":1,\"flat_number\":\"Floor 1 - Flat B\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112445791\",\"provider\":\"bpdb\"},{\"floor\":1,\"flat_number\":\"Floor 1 - Flat C\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112445786\",\"provider\":\"bpdb\"},{\"floor\":1,\"flat_number\":\"Floor 1 - Flat D\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112402477\",\"provider\":\"bpdb\"},{\"floor\":2,\"flat_number\":\"Floor 2 - Flat A\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112445789\",\"provider\":\"bpdb\"},{\"floor\":2,\"flat_number\":\"Floor 2 - Flat B\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112445790\",\"provider\":\"bpdb\"},{\"floor\":2,\"flat_number\":\"Floor 2 - Flat C\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112402479\",\"provider\":\"bpdb\"},{\"floor\":2,\"flat_number\":\"Floor 2 - Flat D\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112402481\",\"provider\":\"bpdb\"},{\"floor\":3,\"flat_number\":\"Floor 3 - Flat A\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112445792\",\"provider\":\"bpdb\"},{\"floor\":3,\"flat_number\":\"Floor 3 - Flat B\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"01011240245785\",\"provider\":\"bpdb\"},{\"floor\":3,\"flat_number\":\"Floor 3 - Flat C\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112402478\",\"provider\":\"bpdb\"},{\"floor\":3,\"flat_number\":\"Floor 3 - Flat D\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"01011240219407\",\"provider\":\"bpdb\"},{\"floor\":4,\"flat_number\":\"Floor 4 - Flat A\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"01011240245787\",\"provider\":\"bpdb\"},{\"floor\":4,\"flat_number\":\"Floor 4 - Flat B\",\"resident_name\":\"010112433554\",\"resident_phone\":null,\"meter_number\":null,\"provider\":\"bpdb\"},{\"floor\":4,\"flat_number\":\"Floor 4 - Flat C\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112402476\",\"provider\":\"bpdb\"},{\"floor\":4,\"flat_number\":\"Floor 4 - Flat D\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112402480\",\"provider\":\"bpdb\"},{\"floor\":5,\"flat_number\":\"Floor 5 - Flat A\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"0101124022476\",\"provider\":\"bpdb\"},{\"floor\":5,\"flat_number\":\"Floor 5 - Flat B\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"01011240245793\",\"provider\":\"bpdb\"},{\"floor\":5,\"flat_number\":\"Floor 5 - Flat C\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"01011240245788\",\"provider\":\"bpdb\"},{\"floor\":5,\"flat_number\":\"Floor 5 - Flat D\",\"resident_name\":null,\"resident_phone\":null,\"meter_number\":\"010112402482\",\"provider\":\"bpdb\"}]', 'migrated', 8, '2026-07-10 23:35:19', 12, '2026-07-10 23:34:00', '2026-07-10 23:35:19'),
(7, NULL, 'ffr', 'Sobu', 'Sobuj', '01819333713', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 1, 1, 1, 1, NULL, NULL, 'under cocntruction', NULL, '[]', 'migrated', 8, '2026-07-10 23:41:57', 13, '2026-07-10 23:37:58', '2026-07-10 23:41:57'),
(8, NULL, 'fs', 'hUMAIUN KOBIR', 'Humayun', '01518300579', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 1, 1, NULL, NULL, NULL, NULL, '[]', 'migrated', 8, '2026-07-10 23:41:57', 14, '2026-07-10 23:39:01', '2026-07-10 23:41:57'),
(9, NULL, 'fr', 'Balla', 'Billal', '01975338849', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 1, 1, NULL, NULL, NULL, NULL, '[]', 'migrated', 8, '2026-07-10 23:41:57', 15, '2026-07-10 23:40:25', '2026-07-10 23:41:57'),
(10, NULL, 'fr', 'Jahangir Alam', 'Jahingir Alam', '01718781479', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 0, 0, '23.011058099089823', '91.38001470379484', NULL, NULL, '[]', 'migrated', 8, '2026-07-10 23:41:57', 16, '2026-07-10 23:41:49', '2026-07-10 23:41:57'),
(11, NULL, 'fr', '185/Rofikur Rahman Patwary', 'Rofikur Rahman Patwary', '01768225594', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 1, 1, 1, 1, NULL, NULL, NULL, NULL, '[]', 'migrated', 8, '2026-07-10 23:46:57', 17, '2026-07-10 23:46:51', '2026-07-10 23:46:57'),
(12, NULL, 'fr', 'Humayn chow tin shad', 'Humaun Chowdhury', '01814700699', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 1, 1, '23.010887366064406', '91.38026957106979', NULL, NULL, '[]', 'migrated', 8, '2026-07-10 23:53:10', 18, '2026-07-10 23:51:42', '2026-07-10 23:53:10'),
(13, NULL, 'fr', 'hassan medical', 'hasan', '01711374831', NULL, NULL, NULL, 'building', 'residential', 1, 1, 1, 1, '23.010951742145696', '91.38018048556393', NULL, NULL, '[]', 'migrated', 8, '2026-07-10 23:53:10', 19, '2026-07-10 23:53:04', '2026-07-10 23:53:10'),
(14, NULL, 'fr', 'Patwary Monjil', 'Johir Patwary', '01790001111', NULL, NULL, 'above_4_floor', 'building', 'residential', 5, 1, 1, 1, '23.01096335673807', '91.38109395636404', NULL, NULL, '[]', 'migrated', 8, '2026-07-10 23:59:40', 20, '2026-07-10 23:59:31', '2026-07-10 23:59:40'),
(15, NULL, 'fr', 'OC Robiul Haq', 'OC Robiul Haq', '01815599466', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 1, 1, 1, '23.011075615083005', '91.38094706107944', NULL, NULL, '[]', 'migrated', 8, '2026-07-11 00:02:51', 21, '2026-07-11 00:02:38', '2026-07-11 00:02:51'),
(16, NULL, 'fr', 'Shahidul Islam Chowdhurry (DC)', 'Shahid Chowdhury', '01819610936', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 1, 1, 1, '23.01113828481851', '91.38068137582795', NULL, NULL, '[]', 'migrated', 8, '2026-07-11 00:07:40', 22, '2026-07-11 00:07:34', '2026-07-11 00:07:40'),
(17, NULL, 'fr', 'Khondokar Mention', 'Khondokor Mention', '01815497833', 'Mohammad Mortuza', NULL, 'above_4_floor', 'building', 'residential', 4, 4, 1, 1, '23.011604112359553', '91.38046532347722', 'clam 10 poribar', NULL, '[]', 'migrated', 8, '2026-07-11 00:15:49', 23, '2026-07-11 00:15:43', '2026-07-11 00:15:49'),
(18, NULL, 'fr', 'HAYDAYE ULLAH', 'MOHAMMAD HAYDAYT ULLAH', '01840495082', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 3, 2, 1, 1, '23.012073', '91.381542', NULL, NULL, '[]', 'migrated', 8, '2026-07-11 00:20:51', 24, '2026-07-11 00:19:53', '2026-07-11 00:20:51'),
(19, NULL, 'FR', 'ShaAlam Tin Shad', 'Sha Alam', '01518300579', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 7, 1, 1, '23.012234', '91.381645', NULL, NULL, '[]', 'migrated', 8, '2026-07-11 00:27:41', 25, '2026-07-11 00:27:35', '2026-07-11 00:27:41'),
(20, NULL, 'fr', 'Fatema Mention', 'Jamal Uddin Manager', '01817103153', 'ShaAlam', '01887035869', 'above_4_floor', 'building', 'residential', 7, 3, 0, 0, '23.011917500000003', '91.38137599999999', '20 family', NULL, '[]', 'migrated', 8, '2026-07-11 00:34:21', 26, '2026-07-11 00:33:48', '2026-07-11 00:34:21'),
(21, NULL, 'fr', 'Shorwardi Villa', 'SHA ALAM', '01823573642', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 3, 1, 1, 1, '23.01223959822412', '91.3816947166612', NULL, NULL, '[]', 'migrated', 8, '2026-07-11 00:36:25', 27, '2026-07-11 00:36:20', '2026-07-11 00:36:25'),
(22, NULL, 'FR', 'rubel chow colony', 'sajid', '01787492561', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 1, 1, '23.012146264211914', '91.38173549916696', NULL, NULL, '[]', 'migrated', 8, '2026-07-11 00:44:42', 28, '2026-07-11 00:38:43', '2026-07-11 00:44:42'),
(23, NULL, 'fr', 'Patwary Mention 2', 'Shamima Sultana', '01867666647', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 1, 0, 0, '23.011808', '91.38186833333332', NULL, NULL, '[]', 'migrated', 8, '2026-07-11 00:54:09', 29, '2026-07-11 00:48:29', '2026-07-11 00:54:09'),
(24, NULL, 'fr_link', 'Patwary Mention 2 TIN 1', 'Shamima Sultana', '01867666647', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 3, 0, 0, NULL, NULL, NULL, NULL, '[]', 'migrated', 8, '2026-07-11 00:54:09', 30, '2026-07-11 00:51:13', '2026-07-11 00:54:09'),
(25, NULL, 'fr_link', 'Patwary Mention 2 TIN 2', 'Shamima Sultana', '01867666647', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 3, 0, 0, '23.011502', '91.38207', NULL, NULL, '[]', 'migrated', 8, '2026-07-11 00:54:09', 31, '2026-07-11 00:52:24', '2026-07-11 00:54:09'),
(26, NULL, 'fr_link', 'Patwary Mention 2 TIN 3', 'Shamima Sultana', '01867666647', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 2, 0, 0, '23.011502', '91.38207', NULL, NULL, '[]', 'migrated', 8, '2026-07-11 00:54:09', 32, '2026-07-11 00:54:04', '2026-07-11 00:54:09'),
(27, NULL, 'fr_link', 'nurjahan bobhon', 'Abul Hossain Sobuj', '01841915476', NULL, NULL, 'above_4_floor', 'building', 'residential', 4, 2, 0, 0, NULL, NULL, 'total 7', NULL, '[]', 'migrated', 8, '2026-07-11 01:02:50', 33, '2026-07-11 01:02:45', '2026-07-11 01:02:50'),
(28, NULL, 'fr_link', 'Mosharof Hossain Daroga', 'Mosharof Hossain', '01728886568', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 2, 0, 0, '23.011502', '91.38207', 'nicher 3 , duplext , uporer 2 totay 3ta kore 6ta', NULL, '[]', 'migrated', 8, '2026-07-11 01:10:05', 34, '2026-07-11 01:09:52', '2026-07-11 01:10:05'),
(29, NULL, 'fr_link', 'bilkis monjil', 'jakir ahmed', '01816432343', NULL, NULL, 'above_4_floor', 'building', 'residential', 7, 2, 1, 1, '23.011856241218865', '91.3805718486452', '12 family', NULL, '[]', 'migrated', 8, '2026-07-11 01:14:45', 35, '2026-07-11 01:14:39', '2026-07-11 01:14:45'),
(30, NULL, 'fr_link', 'Altaf  bobon', 'Sorwar Hossain Bahar', '01854422019', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 2, 1, 1, '23.011616945382436', '91.38069836177928', NULL, NULL, '[]', 'migrated', 8, '2026-07-11 01:25:31', 36, '2026-07-11 01:25:25', '2026-07-11 01:25:31'),
(31, NULL, 'fr_link', 'Tasnim Bobon', 'Forid Ahmad`', '01811262666', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 2, 1, 1, '23.012209956792038', '91.3813386087924', '11 family', NULL, '[]', 'migrated', 8, '2026-07-11 01:33:48', 37, '2026-07-11 01:33:37', '2026-07-11 01:33:48'),
(32, NULL, 'fr_link', 'Ahmed mention', 'Samnina Aktar', '01837618111', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 1, 1, 0, 0, '23.012342597111004', '91.38128868334903', NULL, NULL, '[]', 'migrated', 8, '2026-07-11 01:38:58', 38, '2026-07-11 01:38:53', '2026-07-11 01:38:58'),
(33, NULL, 'nm', 'HRS Bhuiyan Mention', 'Eng Jamal Ahmed Bhuiyan Roni', '01819138257', NULL, NULL, 'above_4_floor', 'building', 'residential', 4, 1, 1, 1, '23.0129680269031', '91.3817980677204', '4 family', NULL, '[]', 'migrated', 8, '2026-07-18 00:23:11', 39, '2026-07-17 23:57:21', '2026-07-18 00:23:11'),
(34, NULL, 'nm', 'Nahar  Bahavon', 'Eng Mohammad Nuruzzaman', '01811606622', NULL, NULL, 'above_4_floor', 'building', 'residential', 4, 4, 1, 1, NULL, NULL, 'full family 14 confirm', 'uploads/field-data/field_1784354714_6a5b179a130cb.jpeg', '[]', 'migrated', 8, '2026-07-18 00:23:11', 40, '2026-07-18 00:05:14', '2026-07-18 00:23:11'),
(35, NULL, 'nm', 'Sorwas colony', 'Sarwar', '01864228800', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 2, 1, 1, '23.01324537565003', '91.38172821859318', NULL, 'uploads/field-data/field_1784354937_6a5b18797419c.jpeg', '[]', 'migrated', 8, '2026-07-18 00:23:11', 41, '2026-07-18 00:08:57', '2026-07-18 00:23:11'),
(36, NULL, 'nm', 'Roni colony 1', 'Eng Jamal Ahmed Bhuiyan Roni', '01819138257', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 6, 1, 1, '23.013206500571965', '91.3816964116578', NULL, 'uploads/field-data/field_1784355086_6a5b190e7a52f.jpeg', '[]', 'migrated', 8, '2026-07-18 00:23:11', 42, '2026-07-18 00:11:26', '2026-07-18 00:23:11'),
(37, NULL, 'nm', 'Eng Rony Colony 2', 'Eng Mohammad Nuruzzaman', '01811606622', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 6, 1, 1, '23.01322713304355', '91.38168166880183', NULL, 'uploads/field-data/field_1784355308_6a5b19ec48e26.jpeg', '[]', 'migrated', 8, '2026-07-18 00:23:11', 43, '2026-07-18 00:15:08', '2026-07-18 00:23:11'),
(38, NULL, 'nm', 'eng rony collony 3', 'eng rony', '01811606622', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 5, 0, 0, NULL, NULL, NULL, NULL, '[]', 'migrated', 8, '2026-07-18 00:23:11', 44, '2026-07-18 00:15:59', '2026-07-18 00:23:11'),
(39, NULL, 'nm', 'eng rony colly 4', 'eng rony coly 4', '01811606622', NULL, NULL, NULL, 'building', 'residential', 1, 4, 0, 0, NULL, NULL, NULL, NULL, '[]', 'migrated', 8, '2026-07-18 00:23:11', 45, '2026-07-18 00:16:52', '2026-07-18 00:23:11'),
(40, NULL, 'nm', 'eng rony coloy 5', 'Eng rony', '01811606622', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 10, 1, 1, '23.013308545114494', '91.38174671790894', NULL, NULL, '[]', 'migrated', 8, '2026-07-18 00:23:11', 46, '2026-07-18 00:18:14', '2026-07-18 00:23:11'),
(41, NULL, 'nm', 'eng rony coloy 6', 'eng rony', '01811606622', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 5, 0, 0, '23.013308545114494', '91.38174671790894', 'total family 32 need tro adjust', NULL, '[]', 'migrated', 8, '2026-07-18 00:23:11', 47, '2026-07-18 00:19:42', '2026-07-18 00:23:11'),
(42, NULL, 'nm', 'SA Mention', 'Shohel', '01823980642', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 2, 1, 1, '23.013314197700165', '91.3816514087572', '13 family', 'uploads/field-data/field_1784355781_6a5b1bc57db27.jpeg', '[]', 'migrated', 8, '2026-07-18 00:23:11', 48, '2026-07-18 00:23:01', '2026-07-18 00:23:11'),
(43, NULL, 'nm', 'Miltali House', 'mohammad naymot ullad', '01712277225', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 3, 3, 0, 0, '23.013307808738524', '91.38154832420946', 'family 7', 'uploads/field-data/field_1784355953_6a5b1c71b1b8f.jpeg', '[]', 'migrated', 8, '2026-07-18 00:48:05', 49, '2026-07-18 00:25:53', '2026-07-18 00:48:05'),
(44, NULL, 'nm', 'amena villa', 'Sirazul islam bhuya', '01746408327', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 12, 1, 1, '23.0127455', '91.3815615', NULL, 'uploads/field-data/field_1784356131_6a5b1d232c303.jpeg', '[]', 'migrated', 8, '2026-07-18 00:48:05', 50, '2026-07-18 00:28:51', '2026-07-18 00:48:05'),
(45, NULL, 'nm', 'Firoza Villa', 'Ratin', '01898809282', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 0, 0, '23.013575260058385', '91.38247975563361', NULL, 'uploads/field-data/field_1784356240_6a5b1d903c3be.jpeg', '[]', 'migrated', 8, '2026-07-18 00:48:05', 51, '2026-07-18 00:30:40', '2026-07-18 00:48:05'),
(46, NULL, 'nm', 'Faruk chowdhury collony', 'Faruk chowdhury', '01715809071', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 1, 1, '23.013575260058385', '91.38247975563361', NULL, 'uploads/field-data/field_1784356350_6a5b1dfe8c8ab.jpeg', '[]', 'migrated', 8, '2026-07-18 00:48:05', 52, '2026-07-18 00:32:30', '2026-07-18 00:48:05'),
(47, NULL, 'nm', 'Tahmina vobhon', 'Mohammad golumn kibria', '01718464813', NULL, NULL, 'above_4_floor', 'building', 'residential', 5, 2, 0, 0, '23.0127455', '91.3815615', '9 family', 'uploads/field-data/field_1784356512_6a5b1ea0e8601.jpeg', '[]', 'migrated', 8, '2026-07-18 00:48:05', 53, '2026-07-18 00:35:12', '2026-07-18 00:48:05'),
(48, NULL, 'nm', 'Sattar mention', 'billall hosain', '01858549149', NULL, NULL, 'above_4_floor', 'building', 'residential', 4, 2, 0, 0, '23.013174', '91.38118', '7 family', 'uploads/field-data/field_1784356590_6a5b1eeea7f47.jpeg', '[]', 'migrated', 8, '2026-07-18 00:48:05', 54, '2026-07-18 00:36:30', '2026-07-18 00:48:05'),
(49, NULL, 'nm', 'Soiyd monjil', 'Mojammel haq', '01828144287', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 2, 1, 1, '23.01323363252159', '91.38027658841852', '10 family', 'uploads/field-data/field_1784356756_6a5b1f942ce54.jpeg', '[]', 'migrated', 8, '2026-07-18 00:48:05', 55, '2026-07-18 00:39:16', '2026-07-18 00:48:05'),
(50, NULL, 'nm', 'new building', 'mr x', '01787492561', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 1, 1, 1, '23.013219727312666', '91.38019820981903', NULL, 'uploads/field-data/field_1784356894_6a5b201e2a7c0.jpeg', '[]', 'migrated', 8, '2026-07-18 00:48:05', 56, '2026-07-18 00:41:34', '2026-07-18 00:48:05'),
(51, NULL, 'nm', 'Hanif mention', 'Hanif', '01855703563', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 2, 1, 1, '23.013228006375012', '91.3802349316827', '12 family', 'uploads/field-data/field_1784357251_6a5b2183cdae1.jpeg', '[]', 'migrated', 8, '2026-07-18 00:48:05', 57, '2026-07-18 00:47:31', '2026-07-18 00:48:05'),
(52, NULL, 'nm', 'Shopno mohol', 'Johir Gong', '01815128374', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 2, 0, 1, '23.01303950595926', '91.38021503297458', '11 family', 'uploads/field-data/field_1784357435_6a5b223b0e268.jpeg', '[]', 'migrated', 8, '2026-07-18 00:57:41', 58, '2026-07-18 00:50:35', '2026-07-18 00:57:41'),
(53, NULL, 'nm', 'new building', 'new', '000000000', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 4, 0, 0, '23.01273525', '91.37996725', NULL, 'uploads/field-data/field_1784357515_6a5b228b5eeab.jpeg', '[]', 'migrated', 8, '2026-07-18 00:57:41', 59, '2026-07-18 00:51:55', '2026-07-18 00:57:41'),
(54, NULL, 'nm', 'Turky Bobhon', 'Mohammad Forid', '01867178852', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 3, 0, 0, '23.01273525', '91.37996725', '16 family', 'uploads/field-data/field_1784357713_6a5b2351ba4b8.jpeg', '[]', 'migrated', 8, '2026-07-18 00:57:41', 60, '2026-07-18 00:55:13', '2026-07-18 00:57:41'),
(55, NULL, 'nm', 'Mubarok Ali Alam', 'Mobarok ALi alm', '01821925784', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 1, 0, 0, '23.012418', '91.379974', 'famiky 3', 'uploads/field-data/field_1784357850_6a5b23da2b55c.jpeg', '[]', 'migrated', 8, '2026-07-18 00:57:41', 61, '2026-07-18 00:57:30', '2026-07-18 00:57:41'),
(56, NULL, 'nm', 'mojibul haq', 'Mojibul haq', '01580515716', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 1, 1, 1, '23.013115841869958', '91.37975984555057', '3 family', NULL, '[]', 'migrated', 8, '2026-07-18 00:59:56', 62, '2026-07-18 00:59:50', '2026-07-18 00:59:56'),
(57, NULL, 'nm', 'haydayt monjil', 'Haydayt Ullah', '01892027513', NULL, NULL, 'above_4_floor', 'building', 'residential', 4, 1, 1, 1, '23.01302887689433', '91.37969582665671', NULL, NULL, '[]', 'migrated', 8, '2026-07-18 01:20:16', 63, '2026-07-18 01:04:08', '2026-07-18 01:20:16'),
(58, NULL, 'nm', '/manik colony', 'mohammad yeakub', '01716345543', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 0, 0, '23.013052311955416', '91.37905666534952', NULL, NULL, '[]', 'migrated', 8, '2026-07-18 01:20:16', 64, '2026-07-18 01:06:30', '2026-07-18 01:20:16'),
(59, NULL, 'nm', 'Ekramul Haq', 'Mohammad Nurlul Haq', '01918581113', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 1, 0, 0, '23.01321257135671', '91.37992258767429', NULL, NULL, '[]', 'migrated', 8, '2026-07-18 01:20:16', 65, '2026-07-18 01:08:12', '2026-07-18 01:20:16'),
(60, NULL, 'nm', 'Babar bobhom', 'babar', '010000000', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 3, 2, 0, 0, '23.013561000000003', '91.3790245', '5 family', NULL, '[]', 'migrated', 8, '2026-07-18 01:20:16', 66, '2026-07-18 01:12:22', '2026-07-18 01:20:16'),
(61, NULL, 'nm', 'jahan villa', 'kazi mohammad ullah azad', '01764376911', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 2, 0, 0, '23.013313994257437', '91.37905304374122', '3 family', NULL, '[]', 'migrated', 8, '2026-07-18 01:20:16', 67, '2026-07-18 01:17:27', '2026-07-18 01:20:16'),
(62, NULL, 'nm', 'jannat mention', 'BB kadiza Abdur Rawf', '01819830616', NULL, NULL, 'above_4_floor', 'building', 'residential', 7, 2, 0, 0, '23.01360102167382', '91.37897410858369', '12 family', NULL, '[]', 'migrated', 8, '2026-07-18 01:20:16', 68, '2026-07-18 01:20:10', '2026-07-18 01:20:16'),
(63, NULL, 'cb', 'Tokir chowdhury', 'Tokir chowdhury', '01838823979', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 3, 1, 0, 1, '23.0117565', '91.382182', NULL, NULL, '[]', 'migrated', 8, '2026-07-24 23:00:13', 69, '2026-07-24 22:31:45', '2026-07-24 23:00:13'),
(64, NULL, 'cb', 'Pervin Cortege', 'Pervin chowdhury', '01819953354', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 3, 1, 0, 0, '23.0117685', '91.382149', '2 family', NULL, '[]', 'migrated', 8, '2026-07-24 23:00:13', 70, '2026-07-24 22:35:29', '2026-07-24 23:00:13'),
(65, NULL, 'cb', 'Shams Monjil', 'kam shamsuddin', '01601300467', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 1, 0, 0, '23.011759', '91.382229', NULL, NULL, '[]', 'migrated', 8, '2026-07-24 23:00:13', 71, '2026-07-24 22:39:51', '2026-07-24 23:00:13'),
(66, NULL, 'cb', 'Hassan chowdhury', 'Hassan chowdhury', '01716150290', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 1, 1, 0, 0, '23.011757361051504', '91.38216789077254', NULL, NULL, '[]', 'migrated', 8, '2026-07-24 23:00:13', 72, '2026-07-24 22:46:20', '2026-07-24 23:00:13'),
(67, NULL, 'cb', 'Aftab uddin chowdhury', 'Mayhad chowdhury', '0100000', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 15, 1, 1, '23.011775453985308', '91.38214138162937', 'receive 800 taka', NULL, '[]', 'migrated', 8, '2026-07-24 23:00:13', 73, '2026-07-24 22:52:36', '2026-07-24 23:00:13'),
(68, NULL, 'cb', 'Chompa chowdhury', 'ohin', '01971676314', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 0, 0, '23.011781737651198', '91.38212618674888', NULL, NULL, '[]', 'migrated', 8, '2026-07-24 23:00:13', 74, '2026-07-24 22:54:42', '2026-07-24 23:00:13'),
(69, NULL, 'cb', 'Shopon chowdhury', 'Shapon chowdhury', '01700625052', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 1, 3, 0, 0, '23.01175902227746', '91.38216232557863', NULL, NULL, '[]', 'migrated', 8, '2026-07-24 23:00:13', 75, '2026-07-24 22:57:23', '2026-07-24 23:00:13'),
(70, NULL, 'cb', 'israfil chowdhury', 'Sajid chowdhury', '01787492561', NULL, NULL, 'above_4_floor', 'building', 'residential', 4, 4, 0, 0, '23.011926097955346', '91.38194821200491', '11 family', NULL, '[]', 'migrated', 8, '2026-07-24 23:00:13', 76, '2026-07-24 23:00:08', '2026-07-24 23:00:13'),
(71, NULL, 'cb', 'Abdul Kadir JIlani Madrasha', 'Md josim uddin', '01913391014', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 1, 1, '23.0118983096371', '91.38190702465387', NULL, NULL, '[]', 'migrated', 8, '2026-07-24 23:07:36', 77, '2026-07-24 23:02:43', '2026-07-24 23:07:36'),
(72, NULL, 'cb', 'Mauf chowdhury collony', 'Maruf chowdhury', '017', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 9, 0, 0, '23.012025496167606', '91.38194294949753', NULL, NULL, '[]', 'migrated', 8, '2026-07-24 23:07:36', 78, '2026-07-24 23:05:04', '2026-07-24 23:07:36'),
(73, NULL, 'cb', 'Youmlikha chowdhury', 'Younmilkha chowdhury', '017', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 10, 0, 0, '23.012014276080382', '91.38195227535304', NULL, NULL, '[]', 'migrated', 8, '2026-07-24 23:07:36', 79, '2026-07-24 23:06:25', '2026-07-24 23:07:36'),
(74, NULL, 'cb', 'Tasir chowdhury', 'Tasir chowdhuru', '017', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 10, 0, 0, '23.011864800203206', '91.38201578652512', NULL, NULL, '[]', 'migrated', 8, '2026-07-24 23:07:36', 80, '2026-07-24 23:07:30', '2026-07-24 23:07:36'),
(75, NULL, 'cb', 'SN Towerf', 'Shahed Chowdhury', '01710312518', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 4, 0, 0, '23.012048048378798', '91.38193332392412', '19 family', NULL, '[]', 'migrated', 8, '2026-07-24 23:10:27', 81, '2026-07-24 23:10:12', '2026-07-24 23:10:27'),
(76, NULL, 'cb', 'Bayzid Village', 'Pavel Chowdhury', '01711783924', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 1, 2, 0, 0, '23.011999799644606', '91.38201971612617', '2 family', NULL, '[]', 'migrated', 8, '2026-07-24 23:31:10', 82, '2026-07-24 23:14:33', '2026-07-24 23:31:10'),
(77, NULL, 'cb', 'Bitul Amirat', 'AKM Anawarullah', '01915392534', NULL, NULL, 'above_4_floor', 'building', 'residential', 4, 4, 1, 1, '23.01235670821647', '91.3818067616132', '11 family', NULL, '[]', 'migrated', 8, '2026-07-24 23:31:10', 83, '2026-07-24 23:22:27', '2026-07-24 23:31:10'),
(78, NULL, 'cb', 'Semlima', 'Rtib Chowdhury', '01741859891', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 4, 0, 0, '23.01246969152303', '91.38182393170428', '19 family', NULL, '[]', 'migrated', 8, '2026-07-24 23:31:10', 84, '2026-07-24 23:26:22', '2026-07-24 23:31:10'),
(79, NULL, 'cb', 'Rahela Mention', 'Mojibur Rahman', '01726495216', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 3, 1, 1, 1, '23.013001146416173', '91.38193009270982', '4 family', NULL, '[]', 'migrated', 8, '2026-07-24 23:31:10', 85, '2026-07-24 23:30:30', '2026-07-24 23:31:10'),
(80, NULL, 'cb', 'Faruk CHowdhury', 'Faruk Chowdhury', '01715809071', NULL, NULL, 'above_4_floor', 'building', 'residential', 3, 2, 1, 1, '23.01293074666662', '91.38194012760628', '4 family', NULL, '[]', 'migrated', 8, '2026-07-24 23:43:06', 86, '2026-07-24 23:39:42', '2026-07-24 23:43:06'),
(81, NULL, 'cb', 'JUniyad Chowdhury Bobhon', 'Didar Chowdhury', '01711012037', NULL, NULL, 'above_4_floor', 'building', 'residential', 5, 2, 0, 0, '23.01291900064707', '91.38194864787063', '7 family', NULL, '[]', 'migrated', 8, '2026-07-25 00:23:50', 87, '2026-07-24 23:44:35', '2026-07-25 00:23:50'),
(82, NULL, 'cb', 'Nur Monzil', 'Nurullah DGM', '01711735301', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 1, 0, 0, '23.012912118978726', '91.38194314054151', '10 family', NULL, '[]', 'migrated', 8, '2026-07-25 00:23:50', 88, '2026-07-24 23:46:35', '2026-07-25 00:23:50'),
(83, NULL, 'cb', 'Shahana Mention', 'mizanur rahman', '01711371497', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 2, 0, 0, '23.01298184704166', '91.38198336562877', '10 FAMILY', NULL, '[]', 'migrated', 8, '2026-07-25 00:23:50', 89, '2026-07-24 23:48:32', '2026-07-25 00:23:50'),
(84, NULL, 'cb', 'Abdul batin', 'Abdul batin', '0100', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 1, 1, 0, 0, '23.012954044250566', '91.38197080288614', NULL, NULL, '[]', 'migrated', 8, '2026-07-25 00:23:50', 90, '2026-07-24 23:50:26', '2026-07-25 00:23:50'),
(85, NULL, 'cb', 'Bohiya cotage', 'Shakil bouhaya', '01857255151', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 2, 0, 0, '23.01273788275885', '91.38196883861225', '4 family', NULL, '[]', 'migrated', 8, '2026-07-25 00:23:50', 91, '2026-07-24 23:52:18', '2026-07-25 00:23:50'),
(86, NULL, 'cb', 'Mizan colonoy', 'Mizanur rahman', '01711371497', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 0, 0, '23.013056495045635', '91.38211818977463', NULL, NULL, '[]', 'migrated', 8, '2026-07-25 00:23:50', 92, '2026-07-24 23:53:38', '2026-07-25 00:23:50'),
(87, NULL, 'cb', 'Jafor Mention', 'MOhammad Rasel', '01875919533', NULL, NULL, 'above_4_floor', 'building', 'residential', 5, 2, 0, 0, '23.01331246764004', '91.38247414876692', '9 family', NULL, '[]', 'migrated', 8, '2026-07-25 00:23:50', 93, '2026-07-24 23:56:39', '2026-07-25 00:23:50'),
(88, NULL, 'cb', 'Nazma VIlla', 'Soriyot Ullah', '0177', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 0, 0, '23.013404824752964', '91.38247164073648', 'tk dayna', NULL, '[]', 'migrated', 8, '2026-07-25 00:23:50', 94, '2026-07-24 23:59:53', '2026-07-25 00:23:50'),
(89, NULL, 'cb', 'Kazi azizul haq bobhon', 'Azaijul haq', '01715146834', NULL, NULL, NULL, 'building', 'residential', 6, 2, 0, 0, '23.01319181988643', '91.38203065748073', '2 family', NULL, '[]', 'migrated', 8, '2026-07-25 00:23:50', 95, '2026-07-25 00:01:15', '2026-07-25 00:23:50'),
(90, NULL, 'cb', 'Taher mention', 'Kazi abu taher', '017', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 2, 0, 0, '23.01297795981208', '91.38195603714954', NULL, NULL, '[]', 'migrated', 8, '2026-07-25 00:23:50', 96, '2026-07-25 00:02:51', '2026-07-25 00:23:50'),
(91, NULL, 'cb', 'Mojila feroza', 'Iqbal hosain', '01821994733', NULL, NULL, 'above_4_floor', 'building', 'residential', 5, 1, 0, 0, '23.013195454009', '91.38201653284136', NULL, NULL, '[]', 'migrated', 8, '2026-07-25 00:23:50', 97, '2026-07-25 00:04:04', '2026-07-25 00:23:50'),
(92, NULL, 'cb', 'maionuddin villa', 'Md minuddon', '01843733346', NULL, NULL, 'above_4_floor', 'building', 'residential', 5, 2, 0, 0, '23.013005807713125', '91.38196947366349', '7 family', NULL, '[]', 'migrated', 8, '2026-07-25 00:23:50', 98, '2026-07-25 00:05:38', '2026-07-25 00:23:50'),
(93, NULL, 'cb', 'KHan Billash', 'Suruj Khan', '01829659352', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 2, 0, 0, '23.01289987129083', '91.3819366570593', '9 fmaily', NULL, '[]', 'migrated', 8, '2026-07-25 00:23:51', 99, '2026-07-25 00:06:58', '2026-07-25 00:23:51'),
(94, NULL, 'cb', 'Arib shafwon villa', 'Dr rasel', '01746408327', NULL, NULL, NULL, 'building', 'residential', 1, 5, 0, 0, '23.012871702400552', '91.38193864305114', '5 family', NULL, '[]', 'migrated', 8, '2026-07-25 00:23:51', 100, '2026-07-25 00:10:44', '2026-07-25 00:23:51'),
(97, NULL, 'cb', 'Borosr Villa', 'Shaeda chowdhury', '017', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 1, 1, 0, 0, '23.01275341817314', '91.38186747390863', NULL, NULL, '[]', 'migrated', 8, '2026-07-25 00:23:51', 101, '2026-07-25 00:14:06', '2026-07-25 00:23:51'),
(100, NULL, 'cb', 'Mahmudul Haq Buhaya Colony', 'Mahmudil haq buhaya', '010000', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 9, 1, 1, '23.012734478295364', '91.38186590105404', '9 family', NULL, '[]', 'migrated', 8, '2026-07-31 23:03:35', 102, '2026-07-31 22:40:10', '2026-07-31 23:03:35'),
(101, NULL, 'cb', 'zom zom cortege', 'Sheuly', '01782559010', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 0, 0, '23.012525119147266', '91.38185947462725', '1 unut', NULL, '[]', 'migrated', 8, '2026-07-31 23:03:35', 103, '2026-07-31 22:42:32', '2026-07-31 23:03:35'),
(102, NULL, 'cb', 'Eng rony', 'Eng roni', '01819138257', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 2, 1, 0, 0, '23.012407309507285', '91.38181373240805', '1', NULL, '[]', 'migrated', 8, '2026-07-31 23:03:35', 104, '2026-07-31 22:44:21', '2026-07-31 23:03:35'),
(103, NULL, 'cb', 'Nur amin luxury colony', 'Nur Alam Luxury', '0170000', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 0, 0, '23.0131283', '91.3822037', '1 family', NULL, '[]', 'migrated', 8, '2026-07-31 23:03:35', 105, '2026-07-31 22:47:27', '2026-07-31 23:03:35'),
(104, NULL, 'cb', 'Kamrul Honda', 'Kamrul Hound', '01819903572', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 12, 0, 0, '23.013148642857146', '91.38227635714289', NULL, NULL, '[]', 'migrated', 8, '2026-07-31 23:03:35', 106, '2026-07-31 22:49:44', '2026-07-31 23:03:35'),
(105, NULL, 'cb', 'Anamul Korim Bulding', 'Anamul KOrim', '0170000000', NULL, NULL, 'above_4_floor', 'building', 'residential', 7, 2, 0, 0, '23.012802', '91.382401', 'Under Construction', NULL, '[]', 'migrated', 8, '2026-07-31 23:03:35', 107, '2026-07-31 22:53:01', '2026-07-31 23:03:35'),
(106, NULL, 'cb', 'Vision Orbit Tower', 'Sanaullah (SHovaputi)', '01730197227', NULL, NULL, 'above_4_floor', 'building', 'residential', 10, 3, 0, 0, '23.013601310319338', '91.38243721924897', '30 family', NULL, '[]', 'migrated', 8, '2026-07-31 23:03:35', 108, '2026-07-31 22:56:25', '2026-07-31 23:03:35'),
(107, NULL, 'mrs', 'Sufi Saleha garden', 'Delwar hossain manik', '01711799433', NULL, NULL, 'above_4_floor', 'building', 'residential', 7, 2, 0, 0, '23.013658721030605', '91.38242808095737', 'under constriction', NULL, '[]', 'migrated', 8, '2026-07-31 23:03:35', 109, '2026-07-31 22:59:17', '2026-07-31 23:03:35'),
(108, NULL, 'mrs', 'Nahar Buhaya Monjil', 'Shabuddin', '01864129667', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 2, 0, 0, '23.01257410173598', '91.38236367268276', '11 family', NULL, '[]', 'migrated', 8, '2026-07-31 23:14:13', 110, '2026-07-31 23:06:32', '2026-07-31 23:14:13'),
(109, NULL, 'mrs', 'Siraz and hariz', 'Sirazul islam and haris', '01819138257', NULL, NULL, NULL, 'building', 'residential', 1, 1, 0, 0, '23.01292105679237', '91.38241660275138', 'under counteraction', NULL, '[]', 'migrated', 8, '2026-07-31 23:14:13', 111, '2026-07-31 23:08:36', '2026-07-31 23:14:13'),
(110, NULL, 'mrs', 'Nurjahan Alow', 'Mojibur Rahman', '01711306424', NULL, NULL, 'above_4_floor', 'building', 'residential', 4, 4, 0, 0, '23.012073823136653', '91.38229138183686', '14 unit', NULL, '[]', 'migrated', 8, '2026-07-31 23:14:13', 112, '2026-07-31 23:11:13', '2026-07-31 23:14:13'),
(115, NULL, 'mrs', 'Master Decorator Colony', 'Sumon', '01817001622', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 5, 0, 0, NULL, NULL, '5 family', NULL, '[]', 'migrated', 8, '2026-07-31 23:14:13', 113, '2026-07-31 23:13:30', '2026-07-31 23:14:13'),
(116, NULL, 'mrs', 'Mahbubur rahman', 'Kochi', '01921116564', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 1, 1, 0, 0, '23.01223772368473', '91.3823229249578', '1 family', NULL, '[]', 'migrated', 8, '2026-07-31 23:28:14', 114, '2026-07-31 23:16:25', '2026-07-31 23:28:14'),
(117, NULL, 'mrs', 'Babul commissioner', 'Babul comissionr', '01314336067', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 3, 1, 0, 0, '23.013434', '91.38236624999999', NULL, NULL, '[]', 'migrated', 8, '2026-07-31 23:28:14', 115, '2026-07-31 23:17:59', '2026-07-31 23:28:14'),
(118, NULL, 'mrs', 'Kiron Vobon', 'Kiron chowdhury', '01717990425', NULL, NULL, 'below_or_equal_4_floor', 'building', 'residential', 3, 1, 0, 0, '23.013113489035327', '91.38272962724739', '3 family', NULL, '[]', 'migrated', 8, '2026-07-31 23:28:14', 116, '2026-07-31 23:20:30', '2026-07-31 23:28:14'),
(119, NULL, 'mrs', 'Surma bobon', 'Surma chowdhury', '01711012037', NULL, NULL, 'above_4_floor', 'building', 'residential', 6, 2, 0, 0, '23.013387788929236', '91.38263514885354', '5 family', NULL, '[]', 'migrated', 8, '2026-07-31 23:28:14', 117, '2026-07-31 23:21:40', '2026-07-31 23:28:14'),
(120, NULL, 'mrs', 'Nilufa Bobhon', 'Kazi Jahid', '01876092950', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 0, 0, '23.0132125', '91.3827515', '1 family', NULL, '[]', 'migrated', 8, '2026-07-31 23:28:14', 118, '2026-07-31 23:27:57', '2026-07-31 23:28:14'),
(121, NULL, 'mrs', 'Mahbubur rahman colony', 'Mahbur rahman', '01921116564', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 2, 0, 0, '23.013462', '91.383064', 'under conn', NULL, '[]', 'migrated', 8, '2026-07-31 23:37:55', 119, '2026-07-31 23:32:16', '2026-07-31 23:37:55'),
(122, NULL, 'mrs', 'Mahbubur rahman colony', 'Mahbubur rahman colony', '01921116564', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 12, 0, 0, '23.013095', '91.382672', NULL, NULL, '[]', 'migrated', 8, '2026-07-31 23:37:55', 120, '2026-07-31 23:34:58', '2026-07-31 23:37:55'),
(123, NULL, 'mrs', 'Kurshid Alam Buhaya Colony', 'Kurshid Alam Buhaya', '01714279094', NULL, NULL, 'tin_shed', 'building', 'residential', 5, 2, 0, 0, '30.221195', '47.767723', 'jami anwar 01720093295\r\n9 family', NULL, '[]', 'migrated', 8, '2026-07-31 23:48:30', 121, '2026-07-31 23:45:20', '2026-07-31 23:48:30'),
(124, NULL, 'mrs', 'Kurshid Alam Buhaya Colony 2', 'Kurshid Alam Buhaya Colony 2', '01714279094', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 12, 0, 0, '23.013437', '91.383881', NULL, NULL, '[]', 'migrated', 8, '2026-07-31 23:48:30', 122, '2026-07-31 23:46:36', '2026-07-31 23:48:30'),
(125, NULL, 'mrs', 'Kurshid Alam Buhaya Colony 2', 'Kurshid Alam Buhaya', '01714279094', NULL, NULL, NULL, 'building', 'residential', 1, 10, 0, 0, '1.2894', '103.8499', '10 family', NULL, '[]', 'migrated', 8, '2026-07-31 23:48:30', 123, '2026-07-31 23:47:30', '2026-07-31 23:48:30'),
(126, NULL, 'mrs', 'Kurshid Alam Buhaya Colony 4', 'Kurshid Alam Buhaya', '01714279094', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 7, 0, 0, '1.2894', '103.8499', NULL, NULL, '[]', 'migrated', 8, '2026-07-31 23:48:30', 124, '2026-07-31 23:48:23', '2026-07-31 23:48:30'),
(127, NULL, 'mrs', 'Oli Ahmand COmpany', 'Oli Ahmed COmpany', '01819794777', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 1, 0, 0, '23.01315375', '91.38271175', '1 family', NULL, '[]', 'migrated', 8, '2026-07-31 23:55:25', 125, '2026-07-31 23:50:54', '2026-07-31 23:55:25'),
(128, NULL, 'mrs', 'Nur jahan villa', 'NUr Amin', '01715641718', NULL, NULL, 'above_4_floor', 'building', 'residential', 4, 3, 0, 0, '23.013474850275227', '91.3831949038532', '10 family', NULL, '[]', 'migrated', 8, '2026-07-31 23:55:25', 126, '2026-07-31 23:55:14', '2026-07-31 23:55:25'),
(129, NULL, 'mrs', 'buhaya corteg', 'Ishak', '01830494240', 'ilis', '01881844317', 'above_4_floor', 'building', 'residential', 6, 3, 0, 0, '23.0134735', '91.3826335', '18 family', NULL, '[]', 'migrated', 8, '2026-08-01 00:02:34', 127, '2026-07-31 23:59:24', '2026-08-01 00:02:34'),
(130, NULL, 'mrs', 'nur jahan plooi', 'Mojibur rahaman', '01711306424', NULL, NULL, 'tin_shed', 'building', 'residential', 1, 10, 0, 0, '23.0132125', '91.3827515', NULL, NULL, '[]', 'migrated', 8, '2026-08-01 00:02:34', 128, '2026-08-01 00:02:29', '2026-08-01 00:02:34');

-- --------------------------------------------------------

--
-- Table structure for table `flats`
--

CREATE TABLE `flats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `building_id` bigint(20) UNSIGNED NOT NULL,
  `flat_number` varchar(255) NOT NULL,
  `resident_name` varchar(255) DEFAULT NULL,
  `resident_phone` varchar(255) DEFAULT NULL,
  `floor_number` int(10) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `vacated_at` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `flats`
--

INSERT INTO `flats` (`id`, `building_id`, `flat_number`, `resident_name`, `resident_phone`, `floor_number`, `is_active`, `vacated_at`, `notes`, `created_at`, `updated_at`) VALUES
(96, 8, 'Floor 1 - Flat A', NULL, NULL, 1, 1, NULL, NULL, '2026-07-10 23:05:51', '2026-07-10 23:05:51'),
(97, 8, 'Floor 1 - Flat B', NULL, NULL, 1, 1, NULL, NULL, '2026-07-10 23:05:51', '2026-07-10 23:05:51'),
(98, 8, 'Floor 1', NULL, NULL, 1, 1, NULL, NULL, '2026-07-10 23:05:51', '2026-07-10 23:05:51'),
(99, 10, 'Floor 1', NULL, NULL, 1, 1, NULL, NULL, '2026-07-10 23:22:02', '2026-07-10 23:22:02'),
(100, 12, 'Floor 1 - Flat A', NULL, NULL, 1, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(101, 12, 'Floor 1 - Flat B', NULL, NULL, 1, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(102, 12, 'Floor 1 - Flat C', NULL, NULL, 1, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(103, 12, 'Floor 1 - Flat D', NULL, NULL, 1, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(104, 12, 'Floor 2 - Flat A', NULL, NULL, 2, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(105, 12, 'Floor 2 - Flat B', NULL, NULL, 2, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(106, 12, 'Floor 2 - Flat C', NULL, NULL, 2, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(107, 12, 'Floor 2 - Flat D', NULL, NULL, 2, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(108, 12, 'Floor 3 - Flat A', NULL, NULL, 3, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(109, 12, 'Floor 3 - Flat B', NULL, NULL, 3, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(110, 12, 'Floor 3 - Flat C', NULL, NULL, 3, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(111, 12, 'Floor 3 - Flat D', NULL, NULL, 3, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(112, 12, 'Floor 4 - Flat A', NULL, NULL, 4, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(113, 12, 'Floor 4 - Flat B', '010112433554', NULL, 4, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(114, 12, 'Floor 4 - Flat C', NULL, NULL, 4, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(115, 12, 'Floor 4 - Flat D', NULL, NULL, 4, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(116, 12, 'Floor 5 - Flat A', NULL, NULL, 5, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(117, 12, 'Floor 5 - Flat B', NULL, NULL, 5, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(118, 12, 'Floor 5 - Flat C', NULL, NULL, 5, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(119, 12, 'Floor 5 - Flat D', NULL, NULL, 5, 1, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_items`
--

CREATE TABLE `gallery_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `started_from` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_uploads`
--

CREATE TABLE `member_uploads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `month_key` varchar(7) NOT NULL,
  `star_rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `rated_at` timestamp NULL DEFAULT NULL,
  `rated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meters`
--

CREATE TABLE `meters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `flat_id` bigint(20) UNSIGNED NOT NULL,
  `meter_number` varchar(255) NOT NULL,
  `provider` enum('bpdb','desco','other') NOT NULL DEFAULT 'bpdb',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_recharge_amount` decimal(10,2) DEFAULT NULL,
  `last_recharge_at` timestamp NULL DEFAULT NULL,
  `last_checked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meters`
--

INSERT INTO `meters` (`id`, `flat_id`, `meter_number`, `provider`, `is_active`, `last_recharge_amount`, `last_recharge_at`, `last_checked_at`, `created_at`, `updated_at`) VALUES
(17, 96, '010112469466', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:05:51', '2026-07-10 23:05:51'),
(18, 97, '010112469465', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:05:51', '2026-07-10 23:05:51'),
(19, 98, '012010045444', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:05:51', '2026-07-10 23:05:51'),
(20, 99, '9512093', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:22:02', '2026-07-10 23:22:02'),
(21, 100, '00093035', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(22, 101, '010112445791', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(23, 102, '010112445786', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(24, 103, '010112402477', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(25, 104, '010112445789', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(26, 105, '010112445790', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(27, 106, '010112402479', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(28, 107, '010112402481', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(29, 108, '010112445792', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(30, 109, '01011240245785', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(31, 110, '010112402478', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(32, 111, '01011240219407', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(33, 112, '01011240245787', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(34, 114, '010112402476', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(35, 115, '010112402480', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(36, 116, '0101124022476', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(37, 117, '01011240245793', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(38, 118, '01011240245788', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19'),
(39, 119, '010112402482', 'bpdb', 1, NULL, NULL, NULL, '2026-07-10 23:35:19', '2026-07-10 23:35:19');

-- --------------------------------------------------------

--
-- Table structure for table `meter_readings`
--

CREATE TABLE `meter_readings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meter_id` bigint(20) UNSIGNED NOT NULL,
  `reading_date` date NOT NULL,
  `recharge_amount` decimal(10,2) DEFAULT NULL,
  `recharged_at` timestamp NULL DEFAULT NULL,
  `source` enum('manual','bpdb_api') NOT NULL DEFAULT 'manual',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_06_14_add_admin_fields_to_users', 1),
(5, '2026_06_15_043924_create_roads_table', 1),
(6, '2026_06_15_043925_create_buildings_table', 1),
(7, '2026_07_06_000001_create_super_admins_table', 1),
(8, '2026_07_06_000002_create_products_table', 1),
(9, '2026_07_06_000003_create_orders_table', 1),
(10, '2026_07_06_000004_create_payments_table', 1),
(11, '2026_07_06_000005_create_super_admin_password_reset_tokens_table', 1),
(12, '2026_07_07_000001_add_tags_to_roads_table', 1),
(13, '2026_07_07_000002_update_buildings_table_with_new_fields', 1),
(14, '2026_07_07_000003_create_flats_table', 1),
(15, '2026_07_07_000004_create_meters_table', 1),
(16, '2026_07_07_000005_create_meter_readings_table', 1),
(17, '2026_07_07_000006_add_auto_flat_fields', 1),
(18, '2026_07_07_000007_create_members_table', 2),
(19, '2026_07_07_000008_create_notices_table', 3),
(20, '2026_07_07_000009_create_gallery_items_table', 4),
(21, '2026_07_07_000010_create_about_infos_table', 5),
(22, '2026_07_07_000011_create_contact_infos_table', 6),
(23, '2026_07_07_000012_create_site_settings_table', 6),
(24, '2026_07_07_000013_create_sessions_table', 7),
(25, '2026_07_07_000014_create_service_charges_table', 7),
(26, '2026_07_07_000015_create_member_uploads_table', 8),
(27, '2026_07_07_000016_add_building_category_to_buildings_and_service_charges', 9),
(28, '2026_07_07_000017_add_billing_fields_to_buildings', 10),
(29, '2026_07_07_000018_create_family_reduction_applications_table', 10),
(30, '2026_07_07_000019_add_charge_type_to_service_charges', 11),
(31, '2026_07_07_000020_add_requested_flat_states_to_applications', 12),
(32, '2026_07_07_000021_create_field_data_collections_table', 13);

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `headline` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `published_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `active_till_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` char(36) DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','fulfilled','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` char(36) DEFAULT NULL,
  `payable_type` varchar(255) NOT NULL,
  `payable_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'BDT',
  `gateway` enum('sslcommerz','bkash','nagad','manual') NOT NULL,
  `gateway_txn_id` varchar(255) DEFAULT NULL,
  `gateway_payment_id` varchar(255) DEFAULT NULL,
  `gateway_trx_ref` varchar(255) DEFAULT NULL,
  `status` enum('initiated','pending','successful','failed','cancelled','refunded') NOT NULL DEFAULT 'initiated',
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('physical','service','subscription') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'BDT',
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roads`
--

CREATE TABLE `roads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roads`
--

INSERT INTO `roads` (`id`, `name`, `image_path`, `description`, `tags`, `created_at`, `updated_at`) VALUES
(7, 'Fisheri Road', NULL, NULL, NULL, '2026-07-10 23:22:02', '2026-07-10 23:22:02'),
(10, 'Fisheri Link Road', NULL, NULL, NULL, '2026-07-11 00:54:09', '2026-07-11 00:54:09'),
(11, 'Nojor Mohon Road', NULL, NULL, NULL, '2026-07-18 00:23:11', '2026-07-18 00:23:11'),
(12, 'Chowdhury Bari Road', NULL, NULL, NULL, '2026-07-24 23:00:13', '2026-07-24 23:00:13'),
(14, 'Muksedur Rahman Road', NULL, NULL, NULL, '2026-07-31 23:03:35', '2026-07-31 23:03:35');

-- --------------------------------------------------------

--
-- Table structure for table `service_charges`
--

CREATE TABLE `service_charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `building_category` varchar(255) DEFAULT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `charge_type` varchar(255) NOT NULL DEFAULT 'fixed',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `nav_color` varchar(255) DEFAULT NULL,
  `whatsapp_link` varchar(255) DEFAULT NULL,
  `facebook_link` varchar(255) DEFAULT NULL,
  `youtube_link` varchar(255) DEFAULT NULL,
  `footer_address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `logo_path`, `nav_color`, `whatsapp_link`, `facebook_link`, `youtube_link`, `footer_address`, `created_at`, `updated_at`) VALUES
(1, NULL, '#fbf9f9', NULL, NULL, NULL, 'চৌধুরীপাড়া', '2026-07-07 13:45:13', '2026-07-07 13:46:46');

-- --------------------------------------------------------

--
-- Table structure for table `super_admins`
--

CREATE TABLE `super_admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `avatar_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `super_admin_password_reset_tokens`
--

CREATE TABLE `super_admin_password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `role`, `is_active`, `permissions`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(8, 'Sajid Chowdhury', 'sajid@gmail.com', '01787492562', 'Dhaka, Bangladesh', 'admin', 1, NULL, NULL, '$2y$12$4yb6KqDa6ZQlyz2PTkVsKeD3aN9ki6hswvkdkMhA3iY2HHH8T3phi', 'BpD2i830VhTqY8U8UfTuoOpDOkt6reyT3rnPZNUMnnmL9iC6lzNWv5Rk4PDC', '2026-07-07 20:59:55', '2026-07-07 20:59:55'),
(9, 'Rahim Uddin', 'rahim@example.com', '01711112222', 'Chittagong', 'user', 1, NULL, NULL, '$2y$12$4K/9oKaX.ZEdVqHHuV0I4OjKtY4zxrlbUeAcLD5xghYDJyGOl8Di2', 'fpc1fFhUJc9SWrOKClKGupwWmubOXm6AXy1zOkCEWKXWpJn8SfVGSUJ2ZU4g', '2026-07-07 20:59:56', '2026-07-07 20:59:56'),
(10, 'Karim Mia', 'karim@example.com', '01733334444', 'Sylhet', 'moderator', 1, NULL, NULL, '$2y$12$FJ49M9IDR1Bc6Z6BQ7Tr5.lkT0H888Et1P6fZX0xN4AjVXkItp90G', NULL, '2026-07-07 20:59:56', '2026-07-07 20:59:56'),
(11, 'asda', '22222@chowdhuripara.local', '22222', NULL, 'user', 1, NULL, NULL, '$2y$12$3kze.6lfc400A53tli6YT.6/dM8pCG11gmO5xoX5ErjPHDtl4e6Ti', 'fqxb5YfX2WplAWbQvwcYvl8Wr9YMOqZJkVDtgfwgh99b2UoPNaV6FBgaMcUi', '2026-07-07 22:39:09', '2026-07-07 22:39:09'),
(12, 'Sajid Chodhury', '01787492561@chowdhuripara.local', '01787492561', NULL, 'user', 1, NULL, NULL, '$2y$12$LhbkpBc.OzQN3kS9P5YSMOS26iGqeCI/Bo8WQ.b/EImVMgzuzBYcy', 'gU6FmxrqGMDkaIRkcWGAuddSLRAZ4SiPFAh3aUrLCDvupsNJfVJpIfAUGt41', '2026-07-07 23:37:51', '2026-07-07 23:37:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_infos`
--
ALTER TABLE `about_infos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buildings_road_id_foreign` (`road_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `contact_infos`
--
ALTER TABLE `contact_infos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `family_reduction_applications`
--
ALTER TABLE `family_reduction_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `family_reduction_applications_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `family_reduction_applications_building_id_status_index` (`building_id`,`status`),
  ADD KEY `family_reduction_applications_user_id_status_index` (`user_id`,`status`);

--
-- Indexes for table `field_data_collections`
--
ALTER TABLE `field_data_collections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `field_data_collections_road_id_foreign` (`road_id`),
  ADD KEY `field_data_collections_collected_by_foreign` (`collected_by`);

--
-- Indexes for table `flats`
--
ALTER TABLE `flats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `flats_building_id_flat_number_unique` (`building_id`,`flat_number`),
  ADD KEY `flats_building_id_is_active_index` (`building_id`,`is_active`);

--
-- Indexes for table `gallery_items`
--
ALTER TABLE `gallery_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_uploads`
--
ALTER TABLE `member_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_uploads_rated_by_foreign` (`rated_by`),
  ADD KEY `member_uploads_user_id_month_key_index` (`user_id`,`month_key`),
  ADD KEY `member_uploads_month_key_star_rating_index` (`month_key`,`star_rating`),
  ADD KEY `member_uploads_month_key_index` (`month_key`);

--
-- Indexes for table `meters`
--
ALTER TABLE `meters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `meters_meter_number_unique` (`meter_number`),
  ADD KEY `meters_flat_id_index` (`flat_id`),
  ADD KEY `meters_last_recharge_at_index` (`last_recharge_at`);

--
-- Indexes for table `meter_readings`
--
ALTER TABLE `meter_readings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `meter_readings_meter_id_reading_date_unique` (`meter_id`,`reading_date`),
  ADD KEY `meter_readings_reading_date_index` (`reading_date`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_product_id_foreign` (`product_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_gateway_txn_id_unique` (`gateway_txn_id`),
  ADD KEY `payments_payable_type_payable_id_index` (`payable_type`,`payable_id`),
  ADD KEY `payments_tenant_id_status_index` (`tenant_id`,`status`),
  ADD KEY `payments_gateway_status_index` (`gateway`,`status`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`);

--
-- Indexes for table `roads`
--
ALTER TABLE `roads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_charges`
--
ALTER TABLE `service_charges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `super_admins`
--
ALTER TABLE `super_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `super_admins_email_unique` (`email`);

--
-- Indexes for table `super_admin_password_reset_tokens`
--
ALTER TABLE `super_admin_password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_infos`
--
ALTER TABLE `about_infos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT for table `contact_infos`
--
ALTER TABLE `contact_infos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `family_reduction_applications`
--
ALTER TABLE `family_reduction_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `field_data_collections`
--
ALTER TABLE `field_data_collections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `flats`
--
ALTER TABLE `flats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `gallery_items`
--
ALTER TABLE `gallery_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `member_uploads`
--
ALTER TABLE `member_uploads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `meters`
--
ALTER TABLE `meters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `meter_readings`
--
ALTER TABLE `meter_readings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roads`
--
ALTER TABLE `roads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `service_charges`
--
ALTER TABLE `service_charges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `super_admins`
--
ALTER TABLE `super_admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buildings`
--
ALTER TABLE `buildings`
  ADD CONSTRAINT `buildings_road_id_foreign` FOREIGN KEY (`road_id`) REFERENCES `roads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `family_reduction_applications`
--
ALTER TABLE `family_reduction_applications`
  ADD CONSTRAINT `family_reduction_applications_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `family_reduction_applications_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `family_reduction_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `field_data_collections`
--
ALTER TABLE `field_data_collections`
  ADD CONSTRAINT `field_data_collections_collected_by_foreign` FOREIGN KEY (`collected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `field_data_collections_road_id_foreign` FOREIGN KEY (`road_id`) REFERENCES `roads` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `flats`
--
ALTER TABLE `flats`
  ADD CONSTRAINT `flats_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `member_uploads`
--
ALTER TABLE `member_uploads`
  ADD CONSTRAINT `member_uploads_rated_by_foreign` FOREIGN KEY (`rated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `member_uploads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meters`
--
ALTER TABLE `meters`
  ADD CONSTRAINT `meters_flat_id_foreign` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meter_readings`
--
ALTER TABLE `meter_readings`
  ADD CONSTRAINT `meter_readings_meter_id_foreign` FOREIGN KEY (`meter_id`) REFERENCES `meters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
