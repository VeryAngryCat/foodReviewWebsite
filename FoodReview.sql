-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 02, 2025 at 07:50 AM
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
  `name` varchar(30) NOT NULL,
  `description` varchar(200) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `Cuisine`
--

INSERT INTO `Cuisine` (`cuisineID`, `name`, `description`)
VALUES (
    1,
    'Indian',
    'A variety of regional cuisines native to the Indian subcontinent'
  ),
  (
    2,
    'Chinese',
    'Chinese cuisine comprises foods originating from China.'
  ),
  (
    3,
    'Mediterranean',
    'It refers to the culinary traditions of countries bordering the Mediterranean Sea, including Italy, Greece, Spain, Turkey, Lebanon.'
  ),
  (
    4,
    'Middle Eastern',
    'Middle Eastern cuisine includes a number of cuisines from the Middle East.'
  ),
  (
    5,
    'American',
    'American cuisine is a melting pot of various culinary traditions from around the world, with influences from Europe, Africa, Asia, and Latin America.'
  ),
  (
    6,
    'British',
    'British cuisine is a unique blend of cooking traditions and practices that originated from the United Kingdom, including England, Scotland, Wales.'
  ),
  (
    7,
    'Japanese',
    'The traditional cuisine of Japan (Japanese: washoku) is based on rice with miso soup and other dishes with an emphasis on seasonal ingredients.'
  ),
  (
    9,
    'Italian',
    'A Mediterranean cuisine consisting of the ingredients, recipes, and cooking techniques developed in Italy since Roman times'
  ),
  (
    10,
    'Thai',
    'Known for its amazing balance of sweet, sour, bitter, and salty flavours often finished with aromatic herbs'
  ),
  (
    11,
    'Fusion',
    'A cuisine that combines elements of different culinary traditions that originate from different countries, regions, or cultures.'
  );
-- --------------------------------------------------------
--
-- Table structure for table `DietaryPreference`
--

CREATE TABLE `DietaryPreference` (
  `dietID` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(200) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `DietaryPreference`
--

INSERT INTO `DietaryPreference` (`dietID`, `name`, `description`)
VALUES (
    1,
    'Vegetarian',
    'A vegetarian diet does not include any meat, poultry, or seafood. It is a meal plan made up of foods that come mostly from plants'
  ),
  (
    2,
    'Halal',
    'The term halal is particularly associated with Islamic dietary laws and especially meat processed and prepared in accordance with those requirements.'
  ),
  (
    3,
    'Gluten-Free',
    'A gluten-free diet is a nutritional plan that strictly excludes gluten, which is a mixture of prolamin proteins found in wheat, as well as barley, rye, and oats.'
  ),
  (
    4,
    'Vegan',
    'Strict vegetarian diet where no food (such as meat, eggs, or dairy products) that comes from animals is consumed.'
  ),
  (5, 'Nut-Free', 'Does not contain nuts'),
  (
    6,
    'Dairy-Free',
    'No milk, cheese, butter, or other dairy products.'
  );
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
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `Dish`
--

INSERT INTO `Dish` (
    `dishID`,
    `restaurantID`,
    `name`,
    `price`,
    `description`,
    `isAvailable`
  )
VALUES (
    1,
    23,
    'Kabsa',
    38,
    'Mixed rice dish made with basmati rice, meat, vegetables, and spices.',
    1
  ),
  (2, 1, 'Big Tasty', 6.99, 'A beef burger', 1),
  (
    3,
    1,
    'McFlurry',
    5.99,
    'Frozen soft serve ice cream with flavours of caramel, Oreo, etc.',
    1
  ),
  (
    4,
    2,
    'Homemade Tiramisu',
    12,
    'Made of coffee-dipped bread, layered with whipped egg, sugar, mascarpone.',
    1
  ),
  (
    5,
    2,
    'Summer Affogato',
    4.5,
    'Sweet vanilla ice cream topped, or drowned with hot espresso coffee.',
    0
  ),
  (
    6,
    22,
    'Gingery Spring Soup',
    8,
    'Plant-based soup with sliced ginger, jalapeño, asparagus, and tofu.',
    1
  ),
  (
    7,
    11,
    'Juicy Pan-Seared Steak',
    23,
    'Steak seared and caramelised, topped with garlic rosemary-infused butter.',
    1
  ),
  (
    8,
    9,
    'Seafood Paella',
    15.99,
    'Rice made with clams, mussels and shrimp along with chorizo and saffron.',
    1
  ),
  (
    9,
    16,
    'Fish and Chips',
    2.5,
    'Battered and fried fish, served with chips. Obviously.',
    1
  ),
  (
    10,
    16,
    'Cereal with milk',
    1.95,
    'Got no time? The most accessible and realistic breakfast.',
    0
  ),
  (
    11,
    12,
    'Special Sushi Set 40pcs',
    30,
    'California, Tempura and Philadelphia rolls, with complimentary sashimi',
    1
  ),
  (
    12,
    20,
    'Traditional Goubuli',
    7,
    'Traditional Baozi steamed bun.',
    1
  ),
  (
    13,
    14,
    'Pork Ribs Soup',
    9,
    'Chinese soup made from pork ribs, red dates (jujubes), daikon, and carrots.',
    1
  ),
  (
    14,
    14,
    'Braised Chicken Feet',
    7.8,
    'Chicken feet stewed with a variety of spices, deeply infused with flavour.',
    0
  ),
  (
    15,
    12,
    'Osmanthus Oolong',
    5,
    'Freshly brewed Kung Fu tea, slightly sweet, scented with Osmanthus flowers',
    1
  ),
  (
    16,
    12,
    'Kings Garden Tea',
    5,
    'A special curation of Chrysanthemum flowers, green tea and oolong tea.',
    1
  ),
  (
    17,
    1,
    'Chicken Mac Meal',
    11,
    'Has two Halal chicken patties and middle bun. Served with fries and a soft drink.',
    1
  ),
  (
    18,
    4,
    'American Chopsuey',
    9.5,
    'Crispy fried noodles, with wok tossed vegetables & chicken in Sweet & Sour sauce.',
    1
  ),
  (
    19,
    4,
    'Schezwan Egg Fried Rice',
    5.5,
    'Steamed Rice, tossed with eggs, seasoning, spring onions, and a homemade sauce.',
    1
  ),
  (
    20,
    4,
    'Vegetable Spring Rolls',
    8.75,
    'Fresh tossed vegetables rolled & fried in spring roll sheets. The perfect snack.',
    1
  ),
  (
    21,
    4,
    'Wonton Chicken Soup',
    7.2,
    'A clear soup with Chicken Wontons, blanched & cooked to perfection.',
    0
  ),
  (
    22,
    17,
    'Kiwilango',
    23,
    'Organic grass fed beef, jalapeños, blue cheese, tortilla chips, hot sauce.',
    1
  ),
  (
    23,
    17,
    'Breakfast Poutine',
    17,
    'Tots, parm, sausage gravy, fried egg.',
    1
  ),
  (
    24,
    17,
    'Nicoise',
    24,
    'Seared rare Ahi, potatoes, green beans, nicoise olives, capers, tomato, egg.',
    1
  ),
  (
    25,
    17,
    'Flightless Bird',
    16,
    'Drink with kraken, khalua, cream float.',
    0
  ),
  (
    26,
    23,
    'Mix Grill',
    86.85,
    'Kebab, cheese patty, chicken shish, lamb shish, lamb chops',
    1
  ),
  (
    27,
    23,
    'Gavurdagi Salad',
    14.1,
    'Tomato, cucumber, green capsicum, onion, walnut, pomegranate, parsley, feta cheese',
    1
  ),
  (
    28,
    23,
    'Çökertme',
    35.12,
    'Beef tenderloin, strained yogurt, eggplant, capsicum, red onion, tomato sauce.',
    1
  ),
  (
    29,
    13,
    'Chefs Tasting Course',
    95.45,
    'Seasonal omakase shaped by the finest seafood available each day. ',
    1
  ),
  (
    30,
    5,
    'Evening Buffet',
    40.5,
    'A collection of Indo-Chinese dishes like Manchurian, Fried Rice, Hakka Noodles.',
    1
  ),
  (
    31,
    10,
    'Somefood',
    5,
    'A delicious meal of Somefood bringing with a taste of someflavour.',
    0
  ),
  (
    32,
    18,
    'Spaghetti with Meatballs',
    9.25,
    'Spaghetti meatballs recipe done in an authentic way, just like Nonna makes it!',
    1
  ),
  (
    33,
    3,
    'Margherita Pizza',
    17,
    'Pizza with tomatoes, basil, and mozzarella cheese.',
    1
  ),
  (
    34,
    3,
    'Diavola Pizza',
    17,
    'Pizza with kalamata olives, spicy peppers, and gooey mozzarella cheese.',
    1
  ),
  (
    35,
    3,
    'Pepperoni Pizza',
    17,
    'Tomato based pizza topped with beef pepperoni, and cheesy mozzarella.',
    1
  ),
  (
    36,
    3,
    'Pizza Quattro Formaggi',
    17,
    'Pizza with mozzarella, gorgonzola, Parmigiano Reggiano, and goat cheese.',
    1
  ),
  (
    37,
    8,
    'Ceasar Salad',
    15,
    'Romaine lettuce, croutons, Parmesan cheese, and Caesar dressing',
    1
  ),
  (
    38,
    8,
    'Greek Salad',
    8.99,
    'Sliced cucumbers, tomatoes, green bell pepper, red onion, olives, and feta cheese',
    1
  ),
  (
    39,
    7,
    'Cobb Salad',
    15,
    'Lettuce topped with bacon, chicken, boiled eggs, tomatoes.',
    0
  ),
  (
    40,
    9,
    'Fried Calamari',
    18,
    'Pieces of squid soaked in buttermilk, coated in flour and deep fried to golden brown.',
    NULL
  ),
  (
    41,
    19,
    'Marinated Sea Kelp',
    8,
    'Marinated sea kelp with black pepper vinegar and sesame dressing',
    0
  ),
  (
    42,
    19,
    'Chicken with Fried Shallot',
    13,
    'Free range chicken with fried shallot and black truffle',
    1
  ),
  (
    43,
    21,
    'Pita Wraps',
    6.19,
    'House renditions of pita rolls from Mediterranean',
    1
  ),
  (
    44,
    21,
    'Sliced Lamb Hunan Styyle',
    7,
    'Spiced with native pepper, green chilli and oyster sauce',
    1
  ),
  (
    45,
    15,
    'Chicken Siomai',
    17,
    'Ground chicken, shrimp, mushroom, carrots, onion, black pepper, sesame oil, and egg.',
    1
  ),
  (
    46,
    15,
    'Dim Sum Ruam Mitr',
    25,
    'Choice of mixed assorted dim sum.',
    1
  ),
  (
    47,
    6,
    'Lamb Kebab 7pcs',
    7,
    'Shish Kebab from lamb',
    1
  );
-- --------------------------------------------------------
--
-- Table structure for table `FavouriteDish`
--

CREATE TABLE `FavouriteDish` (
  `userID` int(11) DEFAULT NULL,
  `dishID` int(11) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `FavouriteDish`
--

INSERT INTO `FavouriteDish` (`userID`, `dishID`)
VALUES (5, 1),
  (5, 20),
  (5, 27),
  (17, 17),
  (11, 30),
  (10, 29),
  (2, 29),
  (13, 31),
  (17, 2),
  (4, 38),
  (7, 4),
  (3, 32),
  (14, 6);
-- --------------------------------------------------------
--
-- Table structure for table `FavouriteRestaurant`
--

CREATE TABLE `FavouriteRestaurant` (
  `userID` int(11) DEFAULT NULL,
  `restaurantID` int(11) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `FavouriteRestaurant`
--

INSERT INTO `FavouriteRestaurant` (`userID`, `restaurantID`)
VALUES (1, 7),
  (14, 22),
  (6, 4),
  (15, 17),
  (5, 6),
  (5, 5),
  (12, 17),
  (17, 1),
  (11, 18),
  (13, 1),
  (16, 9),
  (4, 22);
-- --------------------------------------------------------
--
-- Table structure for table `Restaurant`
--

CREATE TABLE `Restaurant` (
  `restaurantID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
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
VALUES (1, 'McDonalds', 'New York, 15th Avenue', 'Open'),
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
    'India, Andhra Pradesh, Greams Road',
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
--
-- Dumping data for table `RestaurantCuisine`
--

INSERT INTO `RestaurantCuisine` (`restaurantID`, `cuisineID`)
VALUES (4, 2),
  (16, 6),
  (5, 1),
  (5, 2),
  (23, 4),
  (2, 9),
  (3, 9),
  (3, 3),
  (6, 4),
  (13, 7),
  (12, 7),
  (12, 2),
  (20, 2),
  (1, 5),
  (15, 10),
  (21, 1),
  (10, 5),
  (11, 5),
  (17, 6),
  (2, 9),
  (8, 3),
  (7, 3),
  (14, 10),
  (9, 3),
  (22, 11);
-- --------------------------------------------------------
--
-- Table structure for table `Reviews`
--

CREATE TABLE `Reviews` (
  `reviewID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `restaurantID` int(11) DEFAULT NULL,
  `rating` int(1) NOT NULL,
  `commentLeft` varchar(1000) NOT NULL,
  `datePosted` date NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `Reviews`
--

INSERT INTO `Reviews` (
    `reviewID`,
    `userID`,
    `restaurantID`,
    `rating`,
    `commentLeft`,
    `datePosted`
  )
VALUES (
    1,
    1,
    18,
    5,
    'The tastiest meatballs I ever had! Handmade from scratch, with a rich flavour, and full of love. Would recommend!',
    '2008-11-11'
  ),
  (
    2,
    1,
    15,
    3,
    'Was a very unique experience, although I don\'t like spicy food that much',
    '2008-05-02'
  ),
  (
    3,
    11,
    16,
    1,
    'Tasteless food and even more tasteless service: the food took over an hour to get here, and had hair in it. The menu was bland and the choices were close to none. No imagination at all! These places deserve to be closed.',
    '2011-01-07'
  ),
  (
    4,
    11,
    10,
    1,
    'No information at all was written on the website for this cafe, so decided to call in. Asked for location. When I arrived at the supposed place the cafe was at, found nothing. Spent two hours searching, gave up in the end.',
    '2009-07-21'
  ),
  (
    5,
    11,
    7,
    2,
    'This is the best restaurant to go to if you\'re a masochist. The salads are were so boring that no amount of service or interior design could make up for their utter ordinariness.',
    '2008-04-13'
  ),
  (6, 12, 22, 3, 'Nice', '2013-06-18'),
  (7, 12, 2, 3, 'Really sweet', '2014-05-31'),
  (
    8,
    12,
    4,
    4,
    'The noodles are noodling',
    '2023-02-27'
  ),
  (9, 12, 8, 4, 'Ate. Was good', '2020-03-12'),
  (
    10,
    6,
    3,
    5,
    'Itsa me a mariooo\r\nVery italian (o^-\')b',
    '2025-01-29'
  ),
  (
    11,
    6,
    22,
    4,
    'Was soooooo delicious\r\nNot a vegan but damn if i could eat here everyday I would abandon meat in a second',
    '2022-12-04'
  ),
  (
    12,
    13,
    19,
    5,
    'Worth driving three hours',
    '2010-03-03'
  ),
  (
    13,
    13,
    1,
    3,
    'The drive thru was packed, so I waited in line for twenty mins. I ate a chicken burger meal, and marvelled at how affordable it was. Not like back home in germany!! The ice cream machine was broken((',
    '2017-09-08'
  ),
  (
    14,
    8,
    13,
    5,
    'This was my 2nd visit to Chisou Nishi in Japan. In my opinion, the lunch was reasonably priced for a restaurant of this calibre and reputation. Many thanks to the Chef and his team for making my lunch a memorable experience.',
    '2019-08-12'
  ),
  (
    15,
    14,
    17,
    4,
    'The inventive dishes crafted from high-quality ingredients are a treat.',
    '2024-10-02'
  ),
  (
    16,
    14,
    2,
    4,
    'The modern vibe, art-filled decor, and attention to detail make it a charming spot.',
    '2025-04-03'
  ),
  (
    17,
    14,
    9,
    5,
    'See Food Eat Food combines ingredients fresh from the sea with creative cooking in a relaxed environment. The seasonal menu features beautifully presented dishes that are both high quality and reasonably priced. ',
    '2023-11-15'
  ),
  (
    18,
    14,
    5,
    4,
    'This restaurant offers a delightful fusion of Indian and Chinese flavours with a modern twist.',
    '2021-02-28'
  ),
  (
    19,
    4,
    22,
    4,
    'The homey decor and friendly staff welcome you as you walk in, and the menu features fresh vegan dishes served with care. If you\'re looking for where to start, diners say you have to try the jackfruit tacos and ginger soup.',
    '2011-12-04'
  ),
  (
    20,
    2,
    6,
    2,
    'The small was not nice at all. The table was dirty.\r\nAfter all we want it to drink at least one espresso. After 15 minutes the man told us they do not have anymore espresso!',
    '2012-01-19'
  ),
  (
    21,
    9,
    22,
    1,
    'It’s sad to say but our lunch today was the worst meal we’ve had yet & we are vegan...it took forever to get our meal once we ordered & my soup was basically coconut milk with a bunch of non edible herbs in it & a couple pieces of carrot...I also got the vegetable burger & it was flavourless plus the mushroom on it tasted like it got picked from a cow patty.. I don’t ever write reviews & i’m sure most stuff on the menu is great but I was extremely disappointed.',
    '2011-12-04'
  ),
  (
    22,
    15,
    3,
    3,
    'I don’t know what all the fuss is about. The pizza is nowhere near as good as made in New York pizza. What’s really disappointing is they don’t do vodka pizza as a topping!',
    '2008-08-31'
  ),
  (
    23,
    16,
    16,
    2,
    'Wasnt expecting bells and whistles but was not anticipating just how rude and horrible they are! The most unwelcoming experience ever.\r\n\r\nThey seemingly hate everyone who isn\'t a local. Avoid.',
    '2020-09-23'
  ),
  (
    24,
    5,
    16,
    1,
    'It\'s almost as if they take pride in poor service and low standards. Not even a thank you for a tip. Never again.',
    '2019-03-19'
  ),
  (
    25,
    10,
    1,
    3,
    'Excellent burger, hot when served, order was without errors',
    '2010-11-17'
  ),
  (
    26,
    13,
    2,
    4,
    'Great place for a quick pit stop!',
    '2011-06-06'
  ),
  (
    27,
    4,
    11,
    5,
    'The steak was absolutely cooked to perfection! My wife and I don\'t like our steaks to be bloody which sometimes results in it being tough- but this was tender and juicy. It came with a salad and rice- a perfect, inexpensive meal.',
    '2021-04-01'
  ),
  (
    28,
    7,
    11,
    5,
    'Had to visit this place after seeing this on various YouTube foodies videos - lived up to expectations & the special sauce added that extra touch - we’ll be back',
    '2012-09-24'
  ),
  (
    29,
    15,
    11,
    2,
    'when I recieved the trays from the \'cook\', he angrily said \"YOUR WELCOME\", as if he just did me a favour when he\'s the one taking my hard earned money for a overrated, over-priced,sub-par dish. What a joke!!! We will never eat there again nor should you',
    '2009-05-11'
  ),
  (
    30,
    7,
    3,
    2,
    'In Italy, the waiting staff are professional and treat you as a valued customer, here you seem to be an inconvenience with waiting staff racing to get you to the tipping part of the evening, that\'s if you can get even their attention without constantly being told, \'just a minute\' several times.',
    '2011-11-02'
  ),
  (
    31,
    15,
    19,
    1,
    'The service was terrible. As there was a table with 5-6 partying people, all staff was focused on this table. Other tables were forgotten.',
    '2021-12-29'
  ),
  (
    32,
    10,
    6,
    2,
    'I’m not sure who’s giving this place good ratings since their food was undercooked and very underwhelming service',
    '2020-08-02'
  ),
  (
    33,
    8,
    20,
    5,
    'Absolutely phenomenal! The food was bursting with flavor, the ambiance was perfect, and the service was impeccable. Will be coming back for sure!',
    '2021-08-30'
  ),
  (
    34,
    5,
    11,
    4,
    'The perfect blend of great food, fast service, and a welcoming atmosphere. 10/10!',
    '2019-10-10'
  ),
  (
    35,
    12,
    14,
    4,
    'Really enjoyed my meal! The only downside was the slightly high price, but the quality made up for it.',
    '2009-12-01'
  ),
  (
    36,
    16,
    17,
    3,
    'Nice place, but nothing special. I wouldn’t go out of my way to eat here again.',
    '2010-04-30'
  ),
  (
    37,
    4,
    12,
    2,
    'Not what I expected. The pictures online looked way better than what we got.',
    '2014-01-31'
  ),
  (
    38,
    7,
    11,
    5,
    'Loved everything about this place! Cozy atmosphere, friendly service, and the best steak I’ve ever had. The portions were generous, and every dish was packed with flavour. The homemade bread they served before the meal was so good that I would have been happy eating just that. Can’t wait to come back!',
    '2009-11-11'
  ),
  (
    39,
    6,
    7,
    1,
    'Walked in excited, walked out disappointed. Everything tasted like it was microwaved, and the portions were laughably small. Total waste of money.',
    '2013-06-18'
  ),
  (
    40,
    11,
    23,
    1,
    'Found a hair in my food, and the manager didn’t even apologise. Instead, they just offered to replace the dish, which I had already lost my appetite for. Absolutely disgusting.',
    '2022-09-11'
  ),
  (
    41,
    11,
    18,
    5,
    'Absolutely incredible! Mama’s Tasty Meatballs serves the best homemade spaghetti and meatballs I’ve ever had. The sauce is rich, slow-cooked to perfection, and packed with flavour, while the meatballs are tender and full of herbs and spices. The pasta itself tastes fresh, like it was made from scratch that morning. The atmosphere is warm and inviting, just like an Italian grandma’s kitchen. I could eat here every day and never get tired of it!',
    '2024-02-23'
  ),
  (
    42,
    10,
    18,
    5,
    'The meatballs are hand-rolled, the sauce is simmered for hours, and the homemade spaghetti is the perfect texture. Every bite tastes like love and tradition. The service is friendly and fast, and the atmosphere is cozy with that classic Italian family-style charm. If you haven’t tried this place yet, do yourself a favor and go—your taste buds will thank you!',
    '2025-03-20'
  );
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
    'franceSucks2021'
  ),
  (
    11,
    'Steve',
    'Bobs',
    'sBobs4ever30@gmail.com',
    'AnAppleADayIsNotEnou',
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
MODIFY `cuisineID` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 12;
--
-- AUTO_INCREMENT for table `DietaryPreference`
--
ALTER TABLE `DietaryPreference`
MODIFY `dietID` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 7;
--
-- AUTO_INCREMENT for table `Dish`
--
ALTER TABLE `Dish`
MODIFY `dishID` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 48;
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
MODIFY `reviewID` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 43;
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
ADD CONSTRAINT `rest_fk_key` FOREIGN KEY (`restaurantID`) REFERENCES `Restaurant` (`restaurantID`) ON DELETE
SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `usr_fk_key` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`) ON DELETE
SET NULL ON UPDATE CASCADE;
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;