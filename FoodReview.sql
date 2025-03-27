-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 27, 2025 at 02:07 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;
--
-- Database: `FoodReview`
--
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS Cuisine;
DROP TABLE IF EXISTS DietaryPreference;
DROP TABLE IF EXISTS Dish;
DROP TABLE IF EXISTS FavouriteDish;
DROP TABLE IF EXISTS FavouriteRestaurant;
DROP TABLE IF EXISTS Restaurant;
DROP TABLE IF EXISTS RestaurantCuisine;
DROP TABLE IF EXISTS Reviews;
DROP TABLE IF EXISTS UserPreference;
DROP TABLE IF EXISTS Users;
SET FOREIGN_KEY_CHECKS = 1;
-- --------------------------------------------------------
--
-- Table structure for table `Cuisine`
--

CREATE TABLE `Cuisine` (
  `cuisineID` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `description` varchar(75) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `DietaryPreference`
--

CREATE TABLE `DietaryPreference` (
  `dietID` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `description` varchar(75) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `Dish`
--

CREATE TABLE `Dish` (
  `dishID` int(11) NOT NULL,
  `restaurantID` int(11) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `description` varchar(75) DEFAULT NULL,
  `isAvailable` tinyint(1) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `FavouriteDish`
--

CREATE TABLE `FavouriteDish` (
  `userID` int(11) DEFAULT NULL,
  `dishID` int(11) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `FavouriteRestaurant`
--

CREATE TABLE `FavouriteRestaurant` (
  `userID` int(11) DEFAULT NULL,
  `restaurantID` int(11) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `Restaurant`
--

CREATE TABLE `Restaurant` (
  `restaurantID` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `operationStatus` varchar(40) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `Restaurant`
--

INSERT INTO `Restaurant` (
    `restaurantID`,
    `name`,
    `location`,
    `operationStatus`
  )
VALUES (1, 'MacDonalds', 'New York, 15th Avenue', 'Open'),
  (
    2,
    'Mamma Mias Desserts',
    'Italy, Liguria, Cinque Terre',
    'Open'
  ),
  (
    3,
    'Mario Pizza Place',
    'Australia, Dangerville, Rattlesnake st.',
    'Temporarily closed'
  ),
  (4, 'Chin chin', 'Shanghai, 14P', 'Open'),
  (
    5,
    'Indo-Asian Buffet',
    'UK, Manchester, Hottea st.',
    'Open'
  ),
  (
    6,
    'Shi-sh kebab',
    'India, Andhra Pradesg, Greams Road',
    'Open'
  ),
  (
    7,
    'Rheas saladeria - Southern Branch',
    'Texas, Yorkshire, Lune st.',
    'Permanently closed'
  ),
  (
    8,
    'Rheas saladeria - Northern Branch',
    'Texas, Yorkshire, 25th st.',
    'Open'
  ),
  (
    9,
    'See Food Eat Food: Best Fish and Lobster Place',
    'Spain, Costa Brava, Calle Verde',
    'Open'
  ),
  (
    10,
    'Isthis Theright Place',
    'Alabama, Whoknowswhere Road',
    'Open'
  ),
  (
    11,
    'Chop House',
    'Brazil, Rio de Janeiro, Avenida Vieira Souto',
    'Open'
  ),
  (
    12,
    'Sakura Asian Bistro',
    'Alaska, Anchorage, Frostbite Boulevard',
    'Permanantly closed'
  ),
  (
    13,
    'Chisou Nishi Kenichi',
    'Japan, Kyoto, Sannenzaka.',
    'Open'
  ),
  (
    14,
    'Song Fa Bak Kut Teh',
    'Singapore, New Bridge Road',
    'Temporarily closed'
  ),
  (
    15,
    'Wise Kwai Thai Streetfood',
    'Thailand, Rongmuang',
    'Open'
  ),
  (
    16,
    'No Imagination',
    'The Default City, Street st.',
    'Open'
  ),
  (
    17,
    'Dunedin New Zealand Eats',
    'San Diego, 3501 30th',
    'Open'
  ),
  (
    18,
    'Mamas Tasty Meatballs',
    'Italy, Milan, Gelato Boulevard',
    'Temporarily closed'
  ),
  (
    19,
    'Wan Li',
    'Beijing, Renaissance Beijing Wangfujing Hotel',
    'Open'
  ),
  (
    20,
    'Goubuli Restaurant',
    'China, Tuanjin, Shandong Road',
    'Open'
  ),
  (
    21,
    'WelcomCafe Cambay',
    'India, Vadodara',
    'Open'
  ),
  (
    22,
    'Grasslovers',
    'Namibia, Windhoek, Khomas st.',
    'Open'
  ),
  (
    23,
    'Bebek Restaurant',
    'Saudi Arabia, Tabuk, Adenia Lane',
    'Open'
  );
-- --------------------------------------------------------
--
-- Table structure for table `RestaurantCuisine`
--

CREATE TABLE `RestaurantCuisine` (
  `restaurantID` int(11) DEFAULT NULL,
  `cuisineID` int(11) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `Reviews`
--

CREATE TABLE `Reviews` (
  `reviewID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `restaurantID` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `commentLeft` varchar(1000) DEFAULT NULL,
  `datePosted` date DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `UserPreference`
--

CREATE TABLE `UserPreference` (
  `preferenceID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `dietID` int(11) DEFAULT NULL,
  `cuisineID` int(11) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- --------------------------------------------------------
--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `userID` int(11) NOT NULL,
  `firstName` varchar(25) DEFAULT NULL,
  `lastName` varchar(35) DEFAULT NULL,
  `email` varchar(35) DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `userPassword` varchar(20) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (
    `userID`,
    `firstName`,
    `lastName`,
    `email`,
    `username`,
    `userPassword`
  )
VALUES (
    1,
    'Alan',
    'Poe',
    'aPope@holicorp.com',
    'aPoe',
    'aP138693'
  ),
  (
    2,
    'Paul',
    'Brook',
    'pBrook10@company.com',
    'brookPaul',
    'Qwerty123'
  ),
  (
    3,
    'Sally',
    'Tims',
    'sTim1980@gmail.com',
    'foodCritic',
    'fcT354kon'
  ),
  (
    4,
    'Ron',
    'White',
    'ronny96@me.com',
    'Nom Lover',
    's000pGone'
  ),
  (
    5,
    'Gina',
    'Lopez',
    'lPezto3459@company.com',
    'MunchForLunch',
    'PassWord1'
  ),
  (
    6,
    'Donald',
    'Morris',
    'dMorris1990@gmail.com',
    'Donald Duck',
    'catsAreLife42'
  ),
  (
    7,
    'Olaf',
    'Snow',
    'letItSnow@gmail.com',
    'Olaf Snow',
    'cancelSpring302'
  ),
  (
    8,
    'Tony',
    'Stark',
    'ironMan250@starkcorp.com',
    'Iron Man',
    'nukE333333'
  ),
  (
    9,
    'Newt',
    'Pearson',
    'someone3ls3@company.com',
    'kkkkk2',
    'sFgHii56249'
  ),
  (
    10,
    'Remy',
    'Cooks',
    'remyRat55@company.com',
    'RatatouilleDaBest23',
    ''
  ),
  (
    11,
    'Steve',
    'Bobs',
    'sBobs4ever30@gmail.com',
    'AnAppleADayIsNotEnough',
    'SBisB3456'
  ),
  (
    12,
    'Liam',
    'King',
    'liKing217@somecorp.com',
    'CertifiedFoodie',
    'fooD12345'
  ),
  (
    13,
    'Volk',
    'Swagen',
    'cars4Cheap3r@insurcorp.com',
    'OilOilOil',
    'le45hTT56'
  ),
  (
    14,
    'Andrea',
    'LLowene',
    'aLL23968@reple.com',
    'Andrea<3Pizza',
    'ALready8080'
  ),
  (
    15,
    'Gary',
    'Rival',
    'gRavel42@gmail.com',
    'PokeCuisine',
    'Hawk2ah2025'
  ),
  (
    16,
    'Zach',
    'Wood',
    'zwood2003@company.com',
    'Zach2003',
    'Zwood2003'
  ),
  (
    17,
    'Mac',
    'Bigs',
    'bigMacs11@corp.com',
    'fastFoodIsLife',
    'mGTree20'
  );
--
-- Indexes for dumped tables
--

--
-- Indexes for table `Cuisine`
--
ALTER TABLE `Cuisine`
ADD PRIMARY KEY (`cuisineID`);
--
-- Indexes for table `DietaryPreference`
--
ALTER TABLE `DietaryPreference`
ADD PRIMARY KEY (`dietID`);
--
-- Indexes for table `Dish`
--
ALTER TABLE `Dish`
ADD PRIMARY KEY (`dishID`),
  ADD KEY `rest1_fk_key` (`restaurantID`);
--
-- Indexes for table `FavouriteDish`
--
ALTER TABLE `FavouriteDish`
ADD KEY `usr2_fk_key` (`userID`),
  ADD KEY `dsh_fk_key` (`dishID`);
--
-- Indexes for table `FavouriteRestaurant`
--
ALTER TABLE `FavouriteRestaurant`
ADD KEY `usr3_fk_key` (`userID`),
  ADD KEY `rest2_fk_key` (`restaurantID`);
--
-- Indexes for table `Restaurant`
--
ALTER TABLE `Restaurant`
ADD PRIMARY KEY (`restaurantID`);
--
-- Indexes for table `RestaurantCuisine`
--
ALTER TABLE `RestaurantCuisine`
ADD KEY `rest3_fk_key` (`restaurantID`),
  ADD KEY `csn2_fk_key` (`cuisineID`);
--
-- Indexes for table `Reviews`
--
ALTER TABLE `Reviews`
ADD PRIMARY KEY (`reviewID`),
  ADD KEY `usr_fk_key` (`userID`),
  ADD KEY `rest_fk_key` (`restaurantID`);
--
-- Indexes for table `UserPreference`
--
ALTER TABLE `UserPreference`
ADD PRIMARY KEY (`preferenceID`),
  ADD KEY `csn_fk_key` (`cuisineID`),
  ADD KEY `dt_fk_key` (`dietID`),
  ADD KEY `usr1_fk_key` (`userID`);
--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
ADD PRIMARY KEY (`userID`);
--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Cuisine`
--
ALTER TABLE `Cuisine`
MODIFY `cuisineID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `DietaryPreference`
--
ALTER TABLE `DietaryPreference`
MODIFY `dietID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Dish`
--
ALTER TABLE `Dish`
MODIFY `dishID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Restaurant`
--
ALTER TABLE `Restaurant`
MODIFY `restaurantID` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 24;
--
-- AUTO_INCREMENT for table `Reviews`
--
ALTER TABLE `Reviews`
MODIFY `reviewID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `UserPreference`
--
ALTER TABLE `UserPreference`
MODIFY `preferenceID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 18;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `Dish`
--
ALTER TABLE `Dish`
ADD CONSTRAINT `rest1_fk_key` FOREIGN KEY (`restaurantID`) REFERENCES `Restaurant` (`restaurantID`);
--
-- Constraints for table `FavouriteDish`
--
ALTER TABLE `FavouriteDish`
ADD CONSTRAINT `dsh_fk_key` FOREIGN KEY (`dishID`) REFERENCES `Dish` (`dishID`),
  ADD CONSTRAINT `usr2_fk_key` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`);
--
-- Constraints for table `FavouriteRestaurant`
--
ALTER TABLE `FavouriteRestaurant`
ADD CONSTRAINT `rest2_fk_key` FOREIGN KEY (`restaurantID`) REFERENCES `Restaurant` (`restaurantID`),
  ADD CONSTRAINT `usr3_fk_key` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`);
--
-- Constraints for table `RestaurantCuisine`
--
ALTER TABLE `RestaurantCuisine`
ADD CONSTRAINT `csn2_fk_key` FOREIGN KEY (`cuisineID`) REFERENCES `Cuisine` (`cuisineID`),
  ADD CONSTRAINT `rest3_fk_key` FOREIGN KEY (`restaurantID`) REFERENCES `Restaurant` (`restaurantID`);
--
-- Constraints for table `Reviews`
--
ALTER TABLE `Reviews`
ADD CONSTRAINT `rest_fk_key` FOREIGN KEY (`restaurantID`) REFERENCES `Restaurant` (`restaurantID`),
  ADD CONSTRAINT `usr_fk_key` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`);
--
-- Constraints for table `UserPreference`
--
ALTER TABLE `UserPreference`
ADD CONSTRAINT `csn_fk_key` FOREIGN KEY (`cuisineID`) REFERENCES `Cuisine` (`cuisineID`),
  ADD CONSTRAINT `dt_fk_key` FOREIGN KEY (`dietID`) REFERENCES `DietaryPreference` (`dietID`),
  ADD CONSTRAINT `usr1_fk_key` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`);
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;