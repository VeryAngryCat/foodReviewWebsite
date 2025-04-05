<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP is working!";

// Include database connection
include('../includes/dbConn.php');

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

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="GET" action="">
            <label for="cuisine">Cuisine:</label>
            <select name="cuisine" id="cuisine">
                <option value="">All Cuisines</option>
                <?php while ($cuisine = mysqli_fetch_assoc($cuisines)): ?>
                    <option value="<?= $cuisine['id'] ?>"><?= $cuisine['name'] ?></option>
                <?php endwhile; ?>
            </select>

            <label for="dietary">Dietary Preferences:</label>
            <select name="dietary" id="dietary">
                <option value="">All Preferences</option>
                <?php while ($diet = mysqli_fetch_assoc($dietaryPreferences)): ?>
                    <option value="<?= $diet['id'] ?>"><?= $diet['name'] ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- List of Restaurants -->
    <div class="restaurant-list">
        <?php while ($restaurant = mysqli_fetch_assoc($restaurants)): ?>
            <div class="restaurant-item">
                <a href="restaurant.php?id=<?= $restaurant['id'] ?>">
                    <h3><?= $restaurant['name'] ?></h3>
                    <p><?= $restaurant['description'] ?></p>
                </a>
            </div>
        <?php endwhile; ?>
    </div>

</body>
</html>
