-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 27, 2025 at 11:35 AM
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

-- --------------------------------------------------------
--
-- Table structure for table `Cuisine`
--
DROP TABLE IF EXISTS `Cuisine`;
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
  `email` varchar(35) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
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
-- Indexes for table `Restaurant`
--
ALTER TABLE `Restaurant`
ADD PRIMARY KEY (`restaurantID`);
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
MODIFY `restaurantID` int(11) NOT NULL AUTO_INCREMENT;
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
MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `Dish`
--
ALTER TABLE `Dish`
ADD CONSTRAINT `rest1_fk_key` FOREIGN KEY (`restaurantID`) REFERENCES `Restaurant` (`restaurantID`);
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