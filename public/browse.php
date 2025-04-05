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

// Handle search functionality
$searchTerm = "";
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
    $restaurants = mysqli_query($conn, "SELECT * FROM Restaurant WHERE name LIKE '%$searchTerm%'");
} else {
    $restaurants = mysqli_query($conn, "SELECT * FROM Restaurant");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Restaurants</title>
    <style>
        body {
            background-color:rgb(153, 219, 215); /* light beige */
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #5a2a27; /* deep brown */
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .card {
            background-color:rgb(255, 225, 238); /* light pink */
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: scale(1.03);
        }

        .card h2 {
            color: #b03a5b;
        }

        .card p {
            color: #333;
        }

        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-bar input {
            padding: 10px;
            font-size: 16px;
            width: 300px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-bar button {
            padding: 10px 20px;
            background-color: #b03a5b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #a02a4f;
        }

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

        .filter-bar {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .filter-bar select {
            padding: 10px;
            font-size: 16px;
            margin: 0 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .filter-bar button {
            padding: 10px 20px;
            background-color: #b03a5b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .filter-bar button:hover {
            background-color: #a02a4f;
        }
    </style>
</head>
<body>

    <!-- Search Bar -->
    <div class="search-bar">
        <form method="POST" action="">
            <input type="text" name="search" value="<?= $searchTerm ?>" placeholder="Search Restaurants...">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Profile Icon -->
    <div class="profile-icon">
        <a href="usprofile.php">
            <img src="assets/profile-icon.png" alt="Profile Icon">
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="GET" action="">
            <label for="cuisine">Cuisine:</label>
            <select name="cuisine" id="cuisine">
                <option value="">All Cuisines</option>
                <?php while ($cuisine = mysqli_fetch_assoc($cuisines)): ?>
                    <option value="<?= $cuisine['cuisineID'] ?>"><?= $cuisine['name'] ?></option>
                <?php endwhile; ?>
            </select>

            <label for="dietary">Dietary Preferences:</label>
            <select name="dietary" id="dietary">
                <option value="">All Preferences</option>
                <?php while ($diet = mysqli_fetch_assoc($dietaryPreferences)): ?>
                    <option value="<?= $diet['dietID'] ?>"><?= $diet['name'] ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- List of Restaurants -->
    <div class="container">
        <?php while ($restaurant = mysqli_fetch_assoc($restaurants)): ?>
            <div class="card">
                <a href="restaurant.php?restaurantID=<?= $restaurant['restaurantID'] ?>">
                    <h2><?= $restaurant['name'] ?></h2>
                    <p><strong>Location:</strong> <?= $restaurant['location'] ?></p>
                    <p><strong>Status:</strong> <?= $restaurant['operationStatus'] ?></p>
                </a>
            </div>
        <?php endwhile; ?>
    </div>

</body>
</html>
