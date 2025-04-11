<?php
// Database connection
include '../includes/dbConn.php';      // Includes the database connection file
include '../includes/authUser.php';    // Includes user authentication file

// Fetch all filter options from database
$cuisines = mysqli_query($conn, "SELECT * FROM Cuisine");                     // Gets all cuisine types
$dietaryPreferences = mysqli_query($conn, "SELECT * FROM DietaryPreference"); // Gets all dietary preferences

// Initialize filter variables from user input
$searchTerm = $_POST['search'] ?? ''; // Gets search term from POST or sets empty string
$selectedCuisine = $_GET['cuisine'] ?? 0; // Gets selected cuisine from GET or sets to 0
$selectedDiet = $_GET['dietary'] ?? 0; // Gets selected diet from GET or sets to 0

// Checks for predefined query buttons
$buttonQuery = $_GET['query'] ?? ''; // Gets which predefined query button was clicked

// Handles predefined queries (special result sets)
$specialResults = null;  // Will hold results if a predefined query is selected

if ($buttonQuery === 'topRated') {
    // Query for top 5 highest rated restaurants with their average ratings
    $specialResults = mysqli_query($conn, "
        SELECT 
            r.name AS restaurant_name, 
            c.name AS cuisine_type, 
            AVG(rev.rating) AS average_rating
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
            average_rating DESC
        LIMIT 5
    ");
} elseif ($buttonQuery === 'popularDishes') {
    // Query for most popular dishes grouped by dietary preferences
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
    // Query for most diverse restaurants (by cuisine, diet, and dish count)
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

// Normal search filter logic (when not using predefined queries)
$where = []; // Array that holds WHERE conditions
$join = ''; // String that holds JOIN clauses

// Builds conditions based on user input
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

// Builds final query
$query = "SELECT DISTINCT r.* FROM Restaurant r $join";
if (!empty($where)) {
    $query .= " WHERE " . implode(' AND ', $where);
}

// Executes query
$restaurants = mysqli_query($conn, $query);
if (!$restaurants) {
    die("Query failed: " . mysqli_error($conn)); // Shows error if failure
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Restaurants</title>
    <style>
        /* Main page styling */
        body { background: #59a9ff; margin: 0; font-family: Arial; }
        
        /* Styling for search/filter sections */
        .search-bar, .filter-bar, .buttons-bar {
            background: #fdccd3; padding: 15px; border-radius: 8px; margin: 15px auto;
            width: 90%; max-width: 800px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Restaurant card grid layout */
        .container { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; padding: 20px; max-width: 1200px; margin: auto; }
        
        /* Individual restaurant card styling */
        .card { background: #bef2ff; border-radius: 10px; padding: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        
        /* Profile icon in top right */
        .profile-icon { position: absolute; top: 20px; right: 20px; }
        .profile-icon img { width: 70px; height: 70px; border-radius: 50%; border: 2px solid #fb79ba; }
        
        /* Dietary tags styling */
        .diet-tags { margin-top: 10px; display: flex; flex-wrap: wrap; gap: 5px; }
        .diet-tag { background: #4caf50; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px; }
        
        /* Form element styling */
        input, select, button { padding: 8px; margin: 5px; border-radius: 4px; }
        button { background: #f477b6; color: white; border: none; cursor: pointer; }
        button:hover { background: #fe5fb4; }
        
        /* Special results table styling */
        .table-box { margin: 20px auto; width: 90%; max-width: 1000px; background: pink; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
    </style>
</head>
<body>

<div class="profile-icon"><a href="usprofile.php"><img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Profile"></a></div>

<!-- SEARCH BAR SECTION -->
<div class="search-bar">
    <!-- Form uses POST method to submit search term -->
    <form method="POST">
        <!-- Search input field with preserved value after submission -->
        <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" 
               placeholder="Search Restaurants..." style="width: 60%;">
        <button type="submit">Search</button>
    </form>
</div>

<!-- FILTER OPTIONS SECTION -->
<div class="filter-bar">
    <!-- Form uses GET method so filters appear in URL -->
    <form method="GET">
        <!-- Cuisine dropdown filter -->
        <label>Cuisine:</label>
        <select name="cuisine">
            <option value="">All</option>  <!-- Default "All" option -->
            <?php 
            // Reset result pointer to beginning for re-use
            mysqli_data_seek($cuisines, 0);  
            // Loop through all cuisine options from database
            while ($cuisine = mysqli_fetch_assoc($cuisines)): ?>
                <!-- Each option shows cuisine name, with selected attribute if active -->
                <option value="<?= $cuisine['cuisineID'] ?>" 
                    <?= ($selectedCuisine == $cuisine['cuisineID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cuisine['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        
        <!-- Dietary preferences dropdown filter -->
        <label>Dietary:</label>
        <select name="dietary">
            <option value="">All</option>
            <?php 
            // Reset pointer for dietary preferences
            mysqli_data_seek($dietaryPreferences, 0);  
            while ($diet = mysqli_fetch_assoc($dietaryPreferences)): ?>
                <option value="<?= $diet['dietID'] ?>" 
                    <?= ($selectedDiet == $diet['dietID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($diet['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        
        <!-- Form submission buttons -->
        <button type="submit">Filter</button>  <!-- Applies selected filters -->
        <!-- Reset button clears filters by reloading page without parameters -->
        <button type="button" onclick="window.location.href='browse.php'">Reset</button>
    </form>
</div>

<!-- PRE-DEFINED QUERY BUTTONS -->
<div class="buttons-bar">
    <!-- Each button triggers a different special query by adding URL parameter -->
    <!-- 1. Shows top rated restaurants -->
    <button onclick="window.location.href='browse.php?query=topRated'">
        1. Top 5 Highest Rated Restaurants
    </button>
    
    <!-- 2. Shows popular dishes grouped by dietary preferences -->
    <button onclick="window.location.href='browse.php?query=popularDishes'">
        2. Most Popular Dishes by Dietary Preferences
    </button>
    
    <!-- 3. Shows restaurants with most variety -->
    <button onclick="window.location.href='browse.php?query=diverseRestaurants'">
        3. Most Diverse Restaurants
    </button>
</div>

<!-- SPECIAL RESULTS DISPLAY (for predefined queries) -->
<?php if ($specialResults): ?>  <!-- Only shows if a special query was run -->
    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <!-- Dynamically creates table headers based on query columns -->
                    <?php while ($col = mysqli_fetch_field($specialResults)) 
                        echo "<th>{$col->name}</th>"; ?>
                </tr>
            </thead>
            <tbody>
                <!-- Loops through each row of results -->
                <?php while ($row = mysqli_fetch_assoc($specialResults)): ?>
                    <tr>
                        <!-- Displays each value in the row -->
                        <?php foreach ($row as $value): ?>
                            <td><?= htmlspecialchars($value) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- MAIN RESTAURANT CARDS DISPLAY -->
<div class="container">
<?php if (mysqli_num_rows($restaurants) > 0): ?>  <!-- Check if any restaurants found -->
    <?php while ($restaurant = mysqli_fetch_assoc($restaurants)): ?>
        <?php
        // Get additional info for each restaurant:
        // 1. Query for all cuisine types this restaurant offers
        $cuisineQuery = mysqli_query($conn, 
            "SELECT c.name FROM Cuisine c 
             JOIN RestaurantCuisine rc ON c.cuisineID = rc.cuisineID 
             WHERE rc.restaurantID = {$restaurant['restaurantID']}");
        
        // 2. Query for all dietary options this restaurant accommodates
        $dietQuery = mysqli_query($conn, 
            "SELECT d.name FROM DietaryPreference d 
             JOIN RestaurantDietary rd ON d.dietID = rd.dietID 
             WHERE rd.restaurantID = {$restaurant['restaurantID']}");
        ?>
        
        <!-- Individual restaurant card -->
        <div class="card">
            <!-- Clickable card that links to restaurant detail page -->
            <a href="restaurant.php?restaurantID=<?= $restaurant['restaurantID'] ?>" 
               style="text-decoration:none; color:inherit;">
               
                <h2><?= htmlspecialchars($restaurant['name']) ?></h2>
                <p><strong>Location:</strong> <?= htmlspecialchars($restaurant['location']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($restaurant['operationStatus']) ?></p>
                
                <!-- Display dietary tags if available -->
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
<?php else: ?>  <!-- No restaurants found message -->
    <div class="no-results">
        <p>No restaurants found matching your criteria.</p>
        <!-- Button to reset all filters -->
        <a href="?"><button>Show All Restaurants</button></a>
    </div>
<?php endif; ?>
</div>