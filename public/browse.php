<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "TEST 1: PHP is working";
require_once '../db_connect.php'; // Adjust path as needed
echo "TEST 2: Database connected";
?>

// Database Helper Functions
function getRestaurantCuisines($pdo, $restaurantId) {
    $stmt = $pdo->prepare("SELECT c.name FROM Cuisine c
                          JOIN RestaurantCuisine rc ON c.cuisineID = rc.cuisineID
                          WHERE rc.restaurantID = ?");
    $stmt->execute([$restaurantId]);
    $cuisines = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return implode(', ', $cuisines);
}

function getRestaurantDiets($pdo, $restaurantId) {
    $stmt = $pdo->prepare("SELECT DISTINCT dp.name FROM DietaryPreference dp
                          JOIN UserPreference up ON dp.dietID = up.dietID
                          JOIN FavouriteDish fd ON up.userID = fd.userID
                          JOIN Dish d ON fd.dishID = d.dishID
                          WHERE d.restaurantID = ?");
    $stmt->execute([$restaurantId]);
    $diets = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return implode(', ', $diets);
}

// Rest of your PHP code (queries, etc.)...


// Initialize variables
$restaurants = [];
$filter_cuisine = $_GET['cuisine'] ?? '';
$filter_diet = $_GET['diet'] ?? '';
$search_query = $_GET['search'] ?? '';

// Build the base query
$query = "SELECT DISTINCT r.* FROM Restaurant r";
$conditions = [];
$params = [];

// Add search filter
if (!empty($search_query)) {
    $conditions[] = "(r.name LIKE ? OR r.location LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

// Add cuisine filter
if (!empty($filter_cuisine)) {
    $query .= " JOIN RestaurantCuisine rc ON r.restaurantID = rc.restaurantID
               JOIN Cuisine c ON rc.cuisineID = c.cuisineID";
    $conditions[] = "c.name = ?";
    $params[] = $filter_cuisine;
}

// Add dietary preference filter
if (!empty($filter_diet)) {
    $query .= " JOIN Dish d ON r.restaurantID = d.restaurantID
               JOIN FavouriteDish fd ON d.dishID = fd.dishID
               JOIN UserPreference up ON fd.userID = up.userID
               JOIN DietaryPreference dp ON up.dietID = dp.dietID";
    $conditions[] = "dp.name = ?";
    $params[] = $filter_diet;
}

// Combine conditions
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Add order by
$query .= " ORDER BY r.name ASC";

// Execute the query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$restaurants = $stmt->fetchAll();

// Get all cuisines and dietary preferences
$cuisines = $pdo->query("SELECT * FROM Cuisine")->fetchAll();
$diets = $pdo->query("SELECT * FROM DietaryPreference")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Restaurants - FoodRev</title>
    <link rel="stylesheet" href="assets/foodRev.css">
</head>
<body>
    <!-- Header Section -->
    <header class="foodrev-header">
        <div class="foodrev-search-container">
            <input type="text" class="foodrev-search-input" id="searchInput" 
                   placeholder="Search restaurants..." value="<?= htmlspecialchars($search_query) ?>">
            <button id="searchButton" class="foodrev-search-button">
                <i class="fas fa-search"></i> <!-- Font Awesome icon -->
            </button>
        </div>
        
        <div class="foodrev-profile-container">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="usprofile.php" class="foodrev-profile-icon">
                    <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                </a>
            <?php else: ?>
                <a href="login.php" class="foodrev-profile-icon">
                    <i class="fas fa-user"></i>
                </a>
            <?php endif; ?>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="foodrev-main-container">
        <h1 class="foodrev-main-title">Browse Restaurants</h1>
        
        <!-- Filter Section -->
        <section class="foodrev-filter-section">
            <div class="foodrev-filter-group">
                <label for="cuisineFilter" class="foodrev-filter-label">Cuisine:</label>
                <select name="cuisine" id="cuisineFilter" class="foodrev-filter-select">
                    <option value="">All Cuisines</option>
                    <?php foreach ($cuisines as $cuisine): ?>
                        <option value="<?= htmlspecialchars($cuisine['name']) ?>" 
                            <?= ($filter_cuisine == $cuisine['name']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cuisine['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="foodrev-filter-group">
                <label for="dietFilter" class="foodrev-filter-label">Dietary:</label>
                <select name="diet" id="dietFilter" class="foodrev-filter-select">
                    <option value="">Any Preference</option>
                    <?php foreach ($diets as $diet): ?>
                        <option value="<?= htmlspecialchars($diet['name']) ?>" 
                            <?= ($filter_diet == $diet['name']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($diet['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button id="applyFilters" class="foodrev-filter-button">
                Apply Filters
            </button>
            
            <button id="clearFilters" class="foodrev-filter-button foodrev-clear-button">
                Clear All
            </button>
        </section>
        
        <!-- Restaurant Listing -->
        <section class="foodrev-restaurant-list">
            <?php if (empty($restaurants)): ?>
                <p class="foodrev-no-results">No restaurants found matching your criteria.</p>
            <?php else: ?>
                <?php foreach ($restaurants as $restaurant): ?>
                    <article class="foodrev-restaurant-card" 
                             onclick="window.location.href='restaurant.php?id=<?= $restaurant['restaurantID'] ?>'"
                             data-cuisines="<?= htmlspecialchars(getRestaurantCuisines($pdo, $restaurant['restaurantID'])) ?>"
                             data-diets="<?= htmlspecialchars(getRestaurantDiets($pdo, $restaurant['restaurantID'])) ?>">
                        <h2 class="foodrev-restaurant-name"><?= htmlspecialchars($restaurant['name']) ?></h2>
                        <p class="foodrev-restaurant-location">
                            <i class="fas fa-map-marker-alt"></i> 
                            <?= htmlspecialchars($restaurant['location']) ?>
                        </p>
                        <p class="foodrev-restaurant-status status-<?= strtolower(str_replace(' ', '-', $restaurant['operationStatus'])) ?>">
                            <?= htmlspecialchars($restaurant['operationStatus']) ?>
                        </p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <!-- Include JavaScript at the end of body -->
    <script src="assets/foodRev.js"></script>
    <script>
        // Initialize browse page functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            
            searchButton.addEventListener('click', applySearchFilters);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') applySearchFilters();
            });
            
            // Filter functionality
            const applyFilters = document.getElementById('applyFilters');
            const clearFilters = document.getElementById('clearFilters');
            
            applyFilters.addEventListener('click', applySearchFilters);
            clearFilters.addEventListener('click', () => window.location.href = 'browse.php');
            
            function applySearchFilters() {
                const params = new URLSearchParams();
                const search = searchInput.value.trim();
                const cuisine = document.getElementById('cuisineFilter').value;
                const diet = document.getElementById('dietFilter').value;
                
                if (search) params.set('search', search);
                if (cuisine) params.set('cuisine', cuisine);
                if (diet) params.set('diet', diet);
                
                window.location.href = 'browse.php?' + params.toString();
            }
        });
        
        // Helper functions to get cuisines and diets for a restaurant
        function getRestaurantCuisines(pdo, restaurantId) {
            // This would be better implemented server-side
            return ''; // Placeholder
        }
        
        function getRestaurantDiets(pdo, restaurantId) {
            // This would be better implemented server-side
            return ''; // Placeholder
        }
    </script>
</body>
</html>
