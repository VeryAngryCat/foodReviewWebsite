<?php
// Database connection
include '../includes/dbConn.php';

// Fetch all filter options
$cuisines = mysqli_query($conn, "SELECT * FROM Cuisine");
$dietaryPreferences = mysqli_query($conn, "SELECT * FROM DietaryPreference");

// Initialize filter variables
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';
$selectedCuisine = isset($_GET['cuisine']) ? (int)$_GET['cuisine'] : 0;
$selectedDiet = isset($_GET['dietary']) ? (int)$_GET['dietary'] : 0;

// Build the query
$where = [];
$join = '';

// Search filter
if (!empty($searchTerm)) {
    $where[] = "r.name LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'";
}

// Cuisine filter
if ($selectedCuisine > 0) {
    $join .= " JOIN RestaurantCuisine rc ON r.restaurantID = rc.restaurantID";
    $where[] = "rc.cuisineID = " . $selectedCuisine;
}

// Dietary filter
if ($selectedDiet > 0) {
    $join .= " JOIN RestaurantDietary rd ON r.restaurantID = rd.restaurantID";
    $where[] = "rd.dietID = " . $selectedDiet;
}

// Final SQL query
$query = "SELECT DISTINCT r.* FROM Restaurant r $join";
if (!empty($where)) {
    $query .= " WHERE " . implode(' AND ', $where);
}

// Execute query
$restaurants = mysqli_query($conn, $query);
if (!$restaurants) {
    die("Query failed: " . mysqli_error($conn));
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
            background-color: rgb(89, 169, 255);
            margin: 0;
            padding: 0;
        }

        .search-bar, .filter-bar {
            background-color: rgb(253, 204, 211);
            padding: 15px;
            border-radius: 8px;
            margin: 15px auto;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        input, select {
            padding: 8px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 8px 15px;
            background-color: rgb(244, 119, 182);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 5px;
        }

        button:hover {
            background-color: rgb(254, 95, 180);
        }

        .profile-icon {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .profile-icon img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 2px solid rgb(251, 121, 186);
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background-color: rgb(190, 242, 255);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .card h2 {
            color: #333;
            margin-top: 0;
        }

        .diet-tags {
            margin: 10px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .diet-tag {
            background-color:rgb(76, 175, 80);
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
        }

        .no-results {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 8px;
            margin: 20px auto;
            max-width: 800px;
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
        <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" 
               placeholder="Search Restaurants..." style="width: 60%;">
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

<!-- Restaurant Display -->
<div class="container">
<?php if (mysqli_num_rows($restaurants) > 0): ?>
    <?php while ($restaurant = mysqli_fetch_assoc($restaurants)): ?>
        <?php
        // Get cuisine (optional display)
        $cuisineQuery = mysqli_query($conn, 
            "SELECT c.name FROM Cuisine c
             JOIN RestaurantCuisine rc ON c.cuisineID = rc.cuisineID
             WHERE rc.restaurantID = " . $restaurant['restaurantID']);

        // Get dietary preferences
        $dietQuery = mysqli_query($conn,
            "SELECT d.name FROM DietaryPreference d
             JOIN RestaurantDietary rd ON d.dietID = rd.dietID
             WHERE rd.restaurantID = " . $restaurant['restaurantID']);
        ?>
        <div class="card">
            <a href="restaurant.php?restaurantID=<?= $restaurant['restaurantID'] ?>" style="text-decoration:none; color:inherit;">
                <h2><?= htmlspecialchars($restaurant['name']) ?></h2>
                <p><strong>Location:</strong> <?= htmlspecialchars($restaurant['location']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($restaurant['operationStatus']) ?></p>

                <?php if (mysqli_num_rows($dietQuery) > 0): ?>
                    <div class="diet-tags">
                        <?php while ($diet = mysqli_fetch_assoc($dietQuery)): ?>
                            <span class="diet-tag"><?= htmlspecialchars($diet['name']) ?></span>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </a>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="no-results">
        <p>No restaurants found matching your criteria.</p>
        <a href="?"><button>Show All Restaurants</button></a>
    </div>
<?php endif; ?>
</div>

</body>
</html>
