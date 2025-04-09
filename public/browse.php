<?php
// Database connection
include '../includes/dbConn.php';
include '../includes/authUser.php';

// Fetch all filter options
$cuisines = mysqli_query($conn, "SELECT * FROM Cuisine");
$dietaryPreferences = mysqli_query($conn, "SELECT * FROM DietaryPreference");

// Initialize filter variables
$searchTerm = $_POST['search'] ?? '';
$selectedCuisine = $_GET['cuisine'] ?? 0;
$selectedDiet = $_GET['dietary'] ?? 0;

// SQL query selection for buttons
$buttonQuery = $_GET['query'] ?? '';

// Handle predefined queries
$specialResults = null;
if ($buttonQuery === 'topRated') {
    $specialResults = mysqli_query($conn, "
        SELECT 
            r.name AS restaurant_name, 
            c.name AS cuisine_type, 
            AVG(rev.rating) AS average_rating,
            COUNT(rev.reviewID) AS review_count
        FROM 
            Restaurant r
        JOIN 
            RestaurantCuisine rc ON r.restaurantID = rc.restaurantID
        JOIN 
            Cuisine c ON rc.cuisineID = c.cuisineID
        LEFT JOIN 
            Reviews rev ON r.restaurantID = rev.restaurantID
        GROUP BY 
            r.restaurantID, c.name
        ORDER BY 
            average_rating DESC, review_count DESC
        LIMIT 5
    ");
} elseif ($buttonQuery === 'popularDishes') {
    $specialResults = mysqli_query($conn, "
        SELECT 
            d.name AS dish_name, 
            dp.name AS dietary_preference,
            COUNT(fd.userID) AS favorite_count,
            r.name AS restaurant_name
        FROM 
            Dish d
        JOIN 
            Restaurant r ON d.restaurantID = r.restaurantID
        JOIN 
            FavouriteDish fd ON d.dishID = fd.dishID
        JOIN 
            FavouriteDiet fdp ON fd.userID = fdp.userID
        JOIN 
            DietaryPreference dp ON fdp.dietID = dp.dietID
        WHERE 
            d.isAvailable = 1
        GROUP BY 
            d.dishID, dp.dietID
        ORDER BY 
            favorite_count DESC
        LIMIT 10
    ");
} elseif ($buttonQuery === 'diverseRestaurants') {
    $specialResults = mysqli_query($conn, "
        SELECT 
            r.name AS restaurant_name,
            COUNT(DISTINCT rc.cuisineID) AS cuisine_count,
            COUNT(DISTINCT rd.dietID) AS diet_count,
            COUNT(DISTINCT d.dishID) AS dish_count
        FROM 
            Restaurant r
        LEFT JOIN 
            RestaurantCuisine rc ON r.restaurantID = rc.restaurantID
        LEFT JOIN 
            RestaurantDietary rd ON r.restaurantID = rd.restaurantID
        LEFT JOIN 
            Dish d ON r.restaurantID = d.restaurantID
        GROUP BY 
            r.restaurantID
        ORDER BY 
            (COUNT(DISTINCT rc.cuisineID) + COUNT(DISTINCT rd.dietID)) DESC,
            COUNT(DISTINCT d.dishID) DESC
        LIMIT 10
    ");
}

// Normal search filter logic
$where = [];
$join = '';
if (!empty($searchTerm)) {
    $where[] = "r.name LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'";
}
if ($selectedCuisine > 0) {
    $join .= " JOIN RestaurantCuisine rc ON r.restaurantID = rc.restaurantID";
    $where[] = "rc.cuisineID = " . (int)$selectedCuisine;
}
if ($selectedDiet > 0) {
    $join .= " JOIN RestaurantDietary rd ON r.restaurantID = rd.restaurantID";
    $where[] = "rd.dietID = " . (int)$selectedDiet;
}
$query = "SELECT DISTINCT r.* FROM Restaurant r $join";
if (!empty($where)) {
    $query .= " WHERE " . implode(' AND ', $where);
}
$restaurants = mysqli_query($conn, $query);
if (!$restaurants) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Restaurants</title>
    <style>
        body { background: #59a9ff; margin: 0; font-family: Arial; }
        .search-bar, .filter-bar, .buttons-bar {
            background: #fdccd3; padding: 15px; border-radius: 8px; margin: 15px auto;
            width: 90%; max-width: 800px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        input, select {
            padding: 8px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 8px 15px;
            background-color: rgb(244, 119, 182);  /* Pink button */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 5px;
        }

        button:hover {
            background-color: rgb(254, 95, 180);  /* Darker pink on hover */
        }

        .profile-icon {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .profile-icon img {
            width: 70px;
            height: 70px;
            border-radius: 50%;   /* Circular image */
            border: 2px solid rgb(251, 121, 186);
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* Responsive grid */
            gap: 20px; 
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card { 
            background-color: rgb(190, 242, 255);  /* Light blue card */
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;    /* Smooth hover effect */
        }

        .card:hover {
            transform: translateY(-5px);    /* Lift card on hover */
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
            background-color:rgb(76, 175, 80);   /* Green tag */
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
        }

         /* "No results" message styling */
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

<div class="search-bar">
    <form method="POST">
        <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Search Restaurants..." style="width: 60%;">
        <button type="submit">Search</button>
    </form>
</div>

<div class="filter-bar">
    <form method="GET">
        <label>Cuisine:</label>
        <select name="cuisine">
            <option value="">All</option>
            <?php mysqli_data_seek($cuisines, 0);
            while ($cuisine = mysqli_fetch_assoc($cuisines)): ?>
                <option value="<?= $cuisine['cuisineID'] ?>" <?= ($selectedCuisine == $cuisine['cuisineID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cuisine['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <label>Dietary:</label>
        <select name="dietary">
            <option value="">All</option>
            <?php mysqli_data_seek($dietaryPreferences, 0);
            while ($diet = mysqli_fetch_assoc($dietaryPreferences)): ?>
                <option value="<?= $diet['dietID'] ?>" <?= ($selectedDiet == $diet['dietID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($diet['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Filter</button>
        <button type="button" onclick="window.location.href='browse.php'">Reset</button>
    </form>
</div>

<div class="buttons-bar">
    <button onclick="window.location.href='browse.php?query=topRated'">Top 5 Highest Rated Restaurants</button>
    <button onclick="window.location.href='browse.php?query=popularDishes'">Most Popular Dishes by Dietary Preferences</button>
    <button onclick="window.location.href='browse.php?query=diverseRestaurants'">Most Diverse Restaurants</button>
</div>

<?php if ($specialResults): ?>
    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <?php while ($col = mysqli_fetch_field($specialResults)) echo "<th>{$col->name}</th>"; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($specialResults)): ?>
                    <tr>
                        <?php foreach ($row as $value): ?>
                            <td><?= htmlspecialchars($value) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- Restaurant Cards -->
<div class="container">
<?php if (mysqli_num_rows($restaurants) > 0): ?>
    <?php while ($restaurant = mysqli_fetch_assoc($restaurants)): ?>
        <?php
        $cuisineQuery = mysqli_query($conn, "SELECT c.name FROM Cuisine c JOIN RestaurantCuisine rc ON c.cuisineID = rc.cuisineID WHERE rc.restaurantID = {$restaurant['restaurantID']}");
        $dietQuery = mysqli_query($conn, "SELECT d.name FROM DietaryPreference d JOIN RestaurantDietary rd ON d.dietID = rd.dietID WHERE rd.restaurantID = {$restaurant['restaurantID']}");
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
