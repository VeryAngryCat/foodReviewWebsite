<?php
// Database connection
include '../includes/dbConn.php';

// Get all filter options
$cuisines = mysqli_query($conn, "SELECT * FROM Cuisine");
$dietaryPreferences = mysqli_query($conn, "SELECT * FROM DietaryPreference");

// Initialize variables
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';
$selectedCuisine = isset($_GET['cuisine']) ? (int)$_GET['cuisine'] : 0;
$selectedDiet = isset($_GET['dietary']) ? (int)$_GET['dietary'] : 0;

// Build the base query
$query = "SELECT DISTINCT r.* FROM Restaurant r";

// Add JOIN if dietary filter is selected
if ($selectedDiet > 0) {
    $query .= " JOIN RestaurantDietary rd ON r.restaurantID = rd.restaurantID";
}

// Start WHERE conditions
$where = [];

// Add search condition
if (!empty($searchTerm)) {
    $where[] = "r.name LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'";
}

// Add cuisine filter if selected
if ($selectedCuisine > 0) {
    $where[] = "r.cuisineID = " . $selectedCuisine;
}

// Add dietary filter if selected
if ($selectedDiet > 0) {
    $where[] = "rd.dietID = " . $selectedDiet;
}

// Combine WHERE conditions
if (!empty($where)) {
    $query .= " WHERE " . implode(' AND ', $where);
}

// Run the query
$restaurants = mysqli_query($conn, $query);

// Check for query errors
if (!$restaurants) {
    die("Database error: " . mysqli_error($conn));
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
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .search-bar {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .search-bar input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filter-bar {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .filter-bar select {
            padding: 8px;
            margin: 0 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            width: 280px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .card h2 {
            margin-top: 0;
            color: #333;
        }
        .diet-tags {
            margin: 10px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .diet-tag {
            background-color: #4CAF50;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        .profile-icon {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .profile-icon img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
    </style>
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
        <form method="POST">
            <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Search Restaurants...">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="GET">
            <label for="cuisine">Cuisine:</label>
            <select name="cuisine" id="cuisine">
                <option value="">All Cuisines</option>
                <?php 
                mysqli_data_seek($cuisines, 0);
                while ($cuisine = mysqli_fetch_assoc($cuisines)): ?>
                    <option value="<?= $cuisine['cuisineID'] ?>" 
                        <?= $selectedCuisine == $cuisine['cuisineID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cuisine['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="dietary">Dietary:</label>
            <select name="dietary" id="dietary">
                <option value="">All Preferences</option>
                <?php 
                mysqli_data_seek($dietaryPreferences, 0);
                while ($diet = mysqli_fetch_assoc($dietaryPreferences)): ?>
                    <option value="<?= $diet['dietID'] ?>" 
                        <?= $selectedDiet == $diet['dietID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($diet['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Filter</button>
            <button type="button" onclick="window.location.href='?'">Reset</button>
        </form>
    </div>

    <!-- List of Restaurants -->
    <div class="container">
        <?php if (mysqli_num_rows($restaurants) > 0): ?>
            <?php while ($restaurant = mysqli_fetch_assoc($restaurants)): ?>
                <?php
                // Get dietary preferences for this restaurant
                $dietQuery = "SELECT d.name FROM DietaryPreference d
                             JOIN RestaurantDietary rd ON d.dietID = rd.dietID
                             WHERE rd.restaurantID = " . $restaurant['restaurantID'];
                $dietResult = mysqli_query($conn, $dietQuery);
                ?>
                <div class="card">
                    <a href="restaurant.php?restaurantID=<?= $restaurant['restaurantID'] ?>" style="text-decoration: none; color: inherit;">
                        <h2><?= htmlspecialchars($restaurant['name']) ?></h2>
                        <p><strong>Location:</strong> <?= htmlspecialchars($restaurant['location']) ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($restaurant['operationStatus']) ?></p>
                        
                        <?php if (mysqli_num_rows($dietResult) > 0): ?>
                            <div class="diet-tags">
                                <?php while ($diet = mysqli_fetch_assoc($dietResult)): ?>
                                    <span class="diet-tag"><?= htmlspecialchars($diet['name']) ?></span>
                                <?php endwhile; ?>
                            </div>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; width: 100%; padding: 20px; background: white; border-radius: 8px;">
                <p>No restaurants found matching your criteria.</p>
                <a href="?">Clear all filters</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>