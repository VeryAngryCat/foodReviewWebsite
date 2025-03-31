-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 31, 2025 at 08:13 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

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

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `FoodReview`
--

-- --------------------------------------------------------

--
-- Table structure for table `Cuisine`
--

CREATE TABLE `Cuisine` (
  `cuisineID` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Cuisine`
--

INSERT INTO `Cuisine` (`cuisineID`, `name`, `description`) VALUES
(1, 'Indian ', 'A variety of regional cuisines native to the Indian subcontinent'),
(2, 'Chinese', 'Chinese cuisine comprises foods originating from China.'),
(3, 'Mediterranean', 'It refers to the culinary traditions of countries bordering the Mediterranean Sea, including Italy, Greece, Spain, Turkey, Lebanon.'),
(4, 'Middle Eastern', 'Middle Eastern cuisine includes a number of cuisines from the Middle East.'),
(5, 'American', 'American cuisine is a melting pot of various culinary traditions from around the world, with influences from Europe, Africa, Asia, and Latin America.'),
(6, 'British', 'British cuisine is a unique blend of cooking traditions and practices that originated from the United Kingdom, including England, Scotland, Wales.'),
(7, 'Japanese', 'The traditional cuisine of Japan (Japanese: washoku) is based on rice with miso soup and other dishes with an emphasis on seasonal ingredients.');

-- --------------------------------------------------------

--
-- Table structure for table `DietaryPreference`
--

CREATE TABLE `DietaryPreference` (
  `dietID` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `DietaryPreference`
--

INSERT INTO `DietaryPreference` (`dietID`, `name`, `description`) VALUES
(1, 'Vegetarian', 'A vegetarian diet does not include any meat, poultry, or seafood. It is a meal plan made up of foods that come mostly from plants'),
(2, 'Halal', 'The term halal is particularly associated with Islamic dietary laws and especially meat processed and prepared in accordance with those requirements.'),
(3, 'Gluten-Free', 'A gluten-free diet is a nutritional plan that strictly excludes gluten, which is a mixture of prolamin proteins found in wheat, as well as barley, rye, and oats.'),
(4, 'Vegan', 'Strict vegetarian diet where no food (such as meat, eggs, or dairy products) that comes from animals is consumed.'),
(5, 'Nut-Free', 'Does not contain nuts'),
(6, 'Dairy-Free', 'No milk, cheese, butter, or other dairy products.');

-- --------------------------------------------------------

--
-- Table structure for table `Dish`
--

CREATE TABLE `Dish` (
  `dishID` int(11) NOT NULL,
  `restaurantID` int(11) DEFAULT NULL,
  `name` varchar(40) NOT NULL,
  `price` float DEFAULT NULL,
  `description` varchar(85) DEFAULT NULL,
  `isAvailable` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Dish`
--

INSERT INTO `Dish` (`dishID`, `restaurantID`, `name`, `price`, `description`, `isAvailable`) VALUES
(1, 23, 'Kabsa', 38, 'Mixed rice dish made with basmati rice, meat, vegetables, and spices.', 1),
(2, 1, 'Big Tasty', 6.99, 'A beef burger', 1),
(3, 1, 'McFlurry', 5.99, 'Frozen soft serve ice cream with flavours of caramel, Oreo, etc.', 1),
(4, 2, 'Homemade Tiramisu', 12, 'Made of coffee-dipped bread, layered with whipped egg, sugar, mascarpone.', 1),
(5, 2, 'Summer Affogato', 4.5, 'Sweet vanilla ice cream topped, or drowned with hot espresso coffee.', 0),
(6, 22, 'Gingery Spring Soup', 8, 'Plant-based soup with sliced ginger, jalapeño, asparagus, and tofu.', 1),
(7, 11, 'Juicy Pan-Seared Steak', 23, 'Steak seared and caramelised, topped with garlic rosemary-infused butter.', 1),
(8, 9, 'Seafood Paella', 15.99, 'Rice made with clams, mussels and shrimp along with chorizo and saffron.', 1),
(9, 16, 'Fish and Chips', 2.5, 'Battered and fried fish, served with chips. Obviously.', 1),
(10, 16, 'Cereal with milk', 1.95, 'Got no time? The most accessible and realistic breakfast.', 0),
(11, 12, 'Special Sushi Set 40pcs', 30, 'California, Tempura and Philadelphia rolls, with complimentary sashimi', 1),
(12, 20, 'Traditional Goubuli', 7, 'Traditional Baozi steamed bun.', 1),
(13, 14, 'Pork Ribs Soup', 9, 'Chinese soup made from pork ribs, red dates (jujubes), daikon, and carrots.', 1),
(14, 14, 'Braised Chicken Feet', 7.8, 'Chicken feet stewed with a variety of spices, deeply infused with flavour.', 0),
(15, 12, 'Osmanthus Oolong', 5, 'Freshly brewed Kung Fu tea, slightly sweet, scented with Osmanthus flowers', 1),
(16, 12, 'Kings Garden Tea', 5, 'A special curation of Chrysanthemum flowers, green tea and oolong tea.', 1),
(17, 1, 'Chicken Mac Meal', 11, 'Has two Halal chicken patties and middle bun. Served with fries and a soft drink.', 1),
(18, 4, 'American Chopsuey', 9.5, 'Crispy fried noodles, with wok tossed vegetables & chicken in Sweet & Sour sauce.', 1),
(19, 4, 'Schezwan Egg Fried Rice', 5.5, 'Steamed Rice, tossed with eggs, seasoning, spring onions, and a homemade sauce.', 1),
(20, 4, 'Vegetable Spring Rolls', 8.75, 'Fresh tossed vegetables rolled & fried in spring roll sheets. The perfect snack.', 1),
(21, 4, 'Wonton Chicken Soup', 7.2, 'A clear soup with Chicken Wontons, blanched & cooked to perfection.', 0),
(22, 17, 'Kiwilango', 23, 'Organic grass fed beef, jalapeños, blue cheese, tortilla chips, hot sauce.', 1),
(23, 17, 'Breakfast Poutine', 17, 'Tots, parm, sausage gravy, fried egg.', 1),
(24, 17, 'Nicoise', 24, 'Seared rare Ahi, potatoes, green beans, nicoise olives, capers, tomato, egg.', 1),
(25, 17, 'Flightless Bird', 16, 'Drink with kraken, khalua, cream float.', 0),
(26, 23, 'Mix Grill', 86.85, 'Kebab, cheese patty, chicken shish, lamb shish, lamb chops', 1),
(27, 23, 'Gavurdagi Salad', 14.1, 'Tomato, cucumber, green capsicum, onion, walnut, pomegranate, parsley, feta cheese', 1),
(28, 23, 'Çökertme', 35.12, 'Beef tenderloin, strained yogurt, eggplant, capsicum, red onion, tomato sauce.', 1),
(29, 13, 'Chefs Tasting Course', 95.45, 'Seasonal omakase shaped by the finest seafood available each day. ', 1),
(30, 5, 'Evening Buffet', 40.5, 'A collection of Indo-Chinese dishes like Manchurian, Fried Rice, Hakka Noodles.', 1),
(31, 10, 'Somefood', 5, 'A delicious meal of Somefood bringing with a taste of someflavour.', 0),
(32, 18, 'Spaghetti with Meatballs', 9.25, 'Spaghetti meatballs recipe done in an authentic way, just like Nonna makes it!', 1),
(33, 3, 'Margherita Pizza', 17, 'Pizza with tomatoes, basil, and mozzarella cheese.', 1),
(34, 3, 'Diavola Pizza', 17, 'Pizza with kalamata olives, spicy peppers, and gooey mozzarella cheese.', 1),
(35, 3, 'Pepperoni Pizza', 17, 'Tomato based pizza topped with beef pepperoni, and cheesy mozzarella.', 1),
(36, 3, 'Pizza Quattro Formaggi', 17, 'Pizza with mozzarella, gorgonzola, Parmigiano Reggiano, and goat cheese.', 1),
(37, 8, 'Ceasar Salad', 15, 'Romaine lettuce, croutons, Parmesan cheese, and Caesar dressing', 1),
(38, 8, 'Greek Salad', 8.99, 'Sliced cucumbers, tomatoes, green bell pepper, red onion, olives, and feta cheese', 1),
(39, 7, 'Cobb Salad', 15, 'Lettuce topped with bacon, chicken, boiled eggs, tomatoes.', 0),
(40, 9, 'Fried Calamari', 18, 'Pieces of squid soaked in buttermilk, coated in flour and deep fried to golden brown.', NULL),
(41, 19, 'Marinated Sea Kelp', 8, 'Marinated sea kelp with black pepper vinegar and sesame dressing', 0),
(42, 19, 'Chicken with Fried Shallot', 13, 'Free range chicken with fried shallot and black truffle', 1),
(43, 21, 'Pita Wraps', 6.19, 'House renditions of pita rolls from Mediterranean', 1),
(44, 21, 'Sliced Lamb Hunan Styyle', 7, 'Spiced with native pepper, green chilli and oyster sauce', 1),
(45, 15, 'Chicken Siomai', 17, 'Ground chicken, shrimp, mushroom, carrots, onion, black pepper, sesame oil, and egg.', 1),
(46, 15, 'Dim Sum Ruam Mitr', 25, 'Choice of mixed assorted dim sum.', 1),
(47, 6, 'Lamb Kebab 7pcs', 7, 'Shish Kebab from lamb', 1);

-- --------------------------------------------------------

--
-- Table structure for table `FavouriteDish`
--

CREATE TABLE `FavouriteDish` (
  `userID` int(11) DEFAULT NULL,
  `dishID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `FavouriteRestaurant`
--

CREATE TABLE `FavouriteRestaurant` (
  `userID` int(11) DEFAULT NULL,
  `restaurantID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Restaurant`
--

CREATE TABLE `Restaurant` (
  `restaurantID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `operationStatus` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Restaurant`
--

INSERT INTO `Restaurant` (`restaurantID`, `name`, `location`, `operationStatus`) VALUES
(1, 'McDonalds', 'New York, 15th Avenue', 'Open'),
(2, 'Mamma Mias Desserts', 'Italy, Liguria, Cinque Terre', 'Open'),
(3, 'Mario Pizza Place', 'Australia, Dangerville, Rattlesnake st.', 'Temporarily closed'),
(4, 'Chin chin', 'Shanghai, 14P', 'Open'),
(5, 'Indo-Asian Buffet', 'UK, Manchester, Hottea st.', 'Open'),
(6, 'Shi-sh kebab', 'India, Andhra Pradesh, Greams Road', 'Open'),
(7, 'Rheas saladeria - Southern Branch', 'Texas, Yorkshire, Lune st.', 'Permanently closed'),
(8, 'Rheas saladeria - Northern Branch', 'Texas, Yorkshire, 25th st.', 'Open'),
(9, 'See Food Eat Food: Best Fish and Lobster Place', 'Spain, Costa Brava, Calle Verde', 'Open'),
(10, 'Isthis Theright Place', 'Alabama, Whoknowswhere Road', 'Open'),
(11, 'Chop House', 'Brazil, Rio de Janeiro, Avenida Vieira Souto', 'Open'),
(12, 'Sakura Asian Bistro', 'Alaska, Anchorage, Frostbite Boulevard', 'Permanantly closed'),
(13, 'Chisou Nishi Kenichi', 'Japan, Kyoto, Sannenzaka.', 'Open'),
(14, 'Song Fa Bak Kut Teh', 'Singapore, New Bridge Road', 'Temporarily closed'),
(15, 'Wise Kwai Thai Streetfood', 'Thailand, Rongmuang', 'Open'),
(16, 'No Imagination', 'The Default City, Street st.', 'Open'),
(17, 'Dunedin New Zealand Eats', 'San Diego, 3501 30th', 'Open'),
(18, 'Mamas Tasty Meatballs', 'Italy, Milan, Gelato Boulevard', 'Temporarily closed'),
(19, 'Wan Li', 'Beijing, Renaissance Beijing Wangfujing Hotel', 'Open'),
(20, 'Goubuli Restaurant', 'China, Tuanjin, Shandong Road', 'Open'),
(21, 'WelcomCafe Cambay', 'India, Vadodara', 'Open'),
(22, 'Grasslovers', 'Namibia, Windhoek, Khomas st.', 'Open'),
(23, 'Bebek Restaurant', 'Saudi Arabia, Tabuk, Adenia Lane', 'Open');

-- --------------------------------------------------------

--
-- Table structure for table `RestaurantCuisine`
--

CREATE TABLE `RestaurantCuisine` (
  `restaurantID` int(11) DEFAULT NULL,
  `cuisineID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Reviews`
--

CREATE TABLE `Reviews` (
  `reviewID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `restaurantID` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `commentLeft` varchar(1000) NOT NULL,
  `datePosted` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `UserPreference`
--

CREATE TABLE `UserPreference` (
  `preferenceID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `dietID` int(11) DEFAULT NULL,
  `cuisineID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `userID` int(11) NOT NULL,
  `firstName` varchar(25) NOT NULL,
  `lastName` varchar(35) NOT NULL,
  `email` varchar(35) NOT NULL,
  `username` varchar(20) NOT NULL,
  `userPassword` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`userID`, `firstName`, `lastName`, `email`, `username`, `userPassword`) VALUES
(1, 'Alan', 'Poe', 'aPope@holicorp.com', 'aPoe', 'aP138693'),
(2, 'Paul', 'Brook', 'pBrook10@company.com', 'brookPaul', 'Qwerty123'),
(3, 'Sally', 'Tims', 'sTim1980@gmail.com', 'foodCritic', 'fcT354kon'),
(4, 'Ron', 'White', 'ronny96@me.com', 'Nom Lover', 's000pGone'),
(5, 'Gina', 'Lopez', 'lPezto3459@company.com', 'MunchForLunch', 'PassWord1'),
(6, 'Donald', 'Morris', 'dMorris1990@gmail.com', 'Donald Duck', 'catsAreLife42'),
(7, 'Olaf', 'Snow', 'letItSnow@gmail.com', 'Olaf Snow', 'cancelSpring302'),
(8, 'Tony', 'Stark', 'ironMan250@starkcorp.com', 'Iron Man', 'nukE333333'),
(9, 'Newt', 'Pearson', 'someone3ls3@company.com', 'kkkkk2', 'sFgHii56249'),
(10, 'Remy', 'Cooks', 'remyRat55@company.com', 'RatatouilleDaBest23', 'franceSucks2021'),
(11, 'Steve', 'Bobs', 'sBobs4ever30@gmail.com', 'AnAppleADayIsNotEnou', 'SBisB3456'),
(12, 'Liam', 'King', 'liKing217@somecorp.com', 'CertifiedFoodie', 'fooD12345'),
(13, 'Volk', 'Swagen', 'cars4Cheap3r@insurcorp.com', 'OilOilOil', 'le45hTT56'),
(14, 'Andrea', 'LLowene', 'aLL23968@reple.com', 'Andrea<3Pizza', 'ALready8080'),
(15, 'Gary', 'Rival', 'gRavel42@gmail.com', 'PokeCuisine', 'Hawk2ah2025'),
(16, 'Zach', 'Wood', 'zwood2003@company.com', 'Zach2003', 'Zwood2003'),
(17, 'Mac', 'Bigs', 'bigMacs11@corp.com', 'fastFoodIsLife', 'mGTree20');

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
  MODIFY `cuisineID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `DietaryPreference`
--
ALTER TABLE `DietaryPreference`
  MODIFY `dietID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `Dish`
--
ALTER TABLE `Dish`
  MODIFY `dishID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `Restaurant`
--
ALTER TABLE `Restaurant`
  MODIFY `restaurantID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Dish`
--
ALTER TABLE `Dish`
  ADD CONSTRAINT `rest1_fk_key` FOREIGN KEY (`restaurantID`) REFERENCES `Restaurant` (`restaurantID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `FavouriteDish`
--
ALTER TABLE `FavouriteDish`
  ADD CONSTRAINT `dsh_fk_key` FOREIGN KEY (`dishID`) REFERENCES `Dish` (`dishID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usr2_fk_key` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `FavouriteRestaurant`
--
ALTER TABLE `FavouriteRestaurant`
  ADD CONSTRAINT `rest2_fk_key` FOREIGN KEY (`restaurantID`) REFERENCES `Restaurant` (`restaurantID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usr3_fk_key` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `RestaurantCuisine`
--
ALTER TABLE `RestaurantCuisine`
  ADD CONSTRAINT `csn2_fk_key` FOREIGN KEY (`cuisineID`) REFERENCES `Cuisine` (`cuisineID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rest3_fk_key` FOREIGN KEY (`restaurantID`) REFERENCES `Restaurant` (`restaurantID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Reviews`
--
ALTER TABLE `Reviews`
  ADD CONSTRAINT `rest_fk_key` FOREIGN KEY (`restaurantID`) REFERENCES `Restaurant` (`restaurantID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `usr_fk_key` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `UserPreference`
--
ALTER TABLE `UserPreference`
  ADD CONSTRAINT `csn_fk_key` FOREIGN KEY (`cuisineID`) REFERENCES `Cuisine` (`cuisineID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dt_fk_key` FOREIGN KEY (`dietID`) REFERENCES `DietaryPreference` (`dietID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `usr1_fk_key` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
