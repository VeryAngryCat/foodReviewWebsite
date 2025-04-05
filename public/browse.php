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

// Fetch all cuisines and dietary preferences for filter (Even though it's not linked to restaurants in DB yet)
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
    <link rel="stylesheet" href="assets/styles.css">
    <script src="assets/search.js"></script> <!-- Link to JS file -->
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

    <!-- Filter Bar (No actual filtering functionality yet) -->
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
    <div class="restaurant-list">
        <?php while ($restaurant = mysqli_fetch_assoc($restaurants)): ?>
            <div class="restaurant-item">
                <a href="restaurant.php?id=<?= $restaurant['restaurantID'] ?>">
                    <h3><?= $restaurant['name'] ?></h3>
                    <p><?= $restaurant['location'] ?></p>
                    <p>Status: <?= $restaurant['operationStatus'] ?></p>
                </a>
            </div>
        <?php endwhile; ?>
    </div>

</body>
</html>
