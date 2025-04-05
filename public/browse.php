<?php
// Database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = ""; // Leave blank if no password is set
$database = "FoodReview"; // Replace with your actual database name


// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all cuisines and dietary preferences for filter
$cuisines = mysqli_query($conn, "SELECT * FROM Cuisine");
$dietaryPreferences = mysqli_query($conn, "SELECT * FROM DietaryPreference");

// Handle search and filter
$searchTerm = "";
$cuisineFilter = "";
$dietFilter = "";

$whereClauses = [];

if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
    $whereClauses[] = "name LIKE '%$searchTerm%'";
}
if (isset($_GET['cuisine']) && $_GET['cuisine'] !== "") {
    $cuisineFilter = $_GET['cuisine'];
    $whereClauses[] = "restaurantID IN (
        SELECT restaurantID FROM RestaurantCuisine WHERE cuisineID = $cuisineFilter
    )";
}
if (isset($_GET['dietary']) && $_GET['dietary'] !== "") {
    $dietFilter = $_GET['dietary'];
    $whereClauses[] = "restaurantID IN (
        SELECT restaurantID FROM RestaurantDiet WHERE dietID = $dietFilter
    )";
}

$whereSQL = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";

$restaurants = mysqli_query($conn, "SELECT * FROM Restaurant $whereSQL");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Restaurants</title>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5dc; /* Light beige background */
            color: #333;
        }

        /* Search Bar */
        .search-bar {
            text-align: center;
            margin: 40px 0;
        }
        .search-bar input {
            padding: 12px;
            font-size: 16px;
            width: 60%;
            max-width: 400px;
            border-radius: 20px;
            border: 2px solid #ccc;
            transition: border-color 0.3s;
        }
        .search-bar input:focus {
            outline: none;
            border-color: #007BFF;
        }
        .search-bar button {
            padding: 12px 20px;
            font-size: 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-bar button:hover {
            background-color: #0056b3;
        }

        /* Profile Icon */
        .profile-icon {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .profile-icon img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        /* Filter Bar */
        .filter-bar {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
            background-color: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .filter-bar label {
            font-weight: bold;
            color: #555;
        }
        .filter-bar select, .filter-bar button {
            padding: 12px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid #ddd;
            outline: none;
            transition: border-color 0.3s;
        }
        .filter-bar select:focus, .filter-bar button:focus {
            border-color: #007BFF;
        }
        .filter-bar button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }
        .filter-bar button:hover {
            background-color: #218838;
        }

        /* Restaurant List */
        .restaurant-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .restaurant-item {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .restaurant-item:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .restaurant-item a {
            text-decoration: none;
            color: #333;
        }
        .restaurant-item h3 {
            margin-bottom: 10px;
            font-size: 20px;
            color: #007BFF;
        }
        .restaurant-item p {
            margin: 5px 0;
            color: #555;
        }
    </style>

    <script src="assets/search.js"></script> <!-- Optional JavaScript -->
</head>
<body>

    <!-- Profile Icon -->
    <div class="profile-icon">
        <a href="usprofile.php">
            <img src="assets/profile-icon.png" alt="Profile Icon">
        </a>
    </div>

    <!-- Search Bar -->
    <div class="search-bar">
        <form method="POST" action="">
            <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Search Restaurants...">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="GET" action="">
            <label for="cuisine">Cuisine:</label>
            <select name="cuisine" id="cuisine">
                <option value="">All</option>
                <?php while ($cuisine = mysqli_fetch_assoc($cuisines)): ?>
                    <option value="<?= $cuisine['cuisineID'] ?>" <?= ($cuisineFilter == $cuisine['cuisineID']) ? 'selected' : '' ?>>
                        <?= $cuisine['name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="dietary">Dietary:</label>
            <select name="dietary" id="dietary">
                <option value="">All</option>
                <?php while ($diet = mysqli_fetch_assoc($dietaryPreferences)): ?>
                    <option value="<?= $diet['dietID'] ?>" <?= ($dietFilter == $diet['dietID']) ? 'selected' : '' ?>>
                        <?= $diet['name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- List of Restaurants -->
    <div class="restaurant-list">
        <?php if ($restaurants && mysqli_num_rows($restaurants) > 0): ?>
            <?php while ($restaurant = mysqli_fetch_assoc($restaurants)): ?>
                <div class="restaurant-item">
                    <a href="restaurant.php?id=<?= $restaurant['restaurantID'] ?>">
                        <h3><?= $restaurant['name'] ?></h3>
                        <p><?= $restaurant['location'] ?></p>
                        <p>Status: <?= $restaurant['operationStatus'] ?></p>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;">No restaurants found.</p>
        <?php endif; ?>
    </div>

</body>
</html>
