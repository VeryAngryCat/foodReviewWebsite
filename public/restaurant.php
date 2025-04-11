<?php
// Database and authentication connection
include '../includes/dbConn.php';
include '../includes/authUser.php';

// -----CODE TO GET THE RESTAURANT ID-----

// Using "GET", checks if restaurantId is given in the URL of page
if (isset($_GET['restaurantID'])) {
    $restaurantID = $_GET['restaurantID'];

    // SQL statement to get the restaurent with given ID
    $query = "SELECT * FROM Restaurant WHERE restaurantID = ?";
    $stmt = $conn->prepare($query);

    // Bind the restaurant ID as an integer to the SQL query (tells the database what the parameters are)
    $stmt->bind_param("i", $restaurantID);
    // Execute the query
    $stmt->execute();
    // Get the result of the query
    $result = $stmt->get_result();
    // Fetches the restaurant data as an array
    $restaurant = $result->fetch_assoc();
 
    // If no restaurant is found, prints error message and exits
    if (!$restaurant) {
        echo "Restaurant not found.";
        exit;
    }
// If no restaurantID is provided in the URL, prints error message and exits
} else {
    echo "No restaurant ID provided.";
    exit();
}

// Gets the currently logged-in user's ID from the session
$userID = $_SESSION['userID'];

// -----CODE TO FETCH THE RESTAURANT'S CUISINES-----

// SQL query to get cuisine names linked to the current restaurant
$cuisineQuery = "
    SELECT c.name 
    FROM Cuisine c 
    JOIN RestaurantCuisine rc ON c.cuisineID = rc.cuisineID
    WHERE rc.restaurantID = ?
";
$cuisineStmt = $conn->prepare($cuisineQuery);
$cuisineStmt->bind_param("i", $restaurantID);
$cuisineStmt->execute();
$cuisineResult = $cuisineStmt->get_result();

// Initialises array to store cuisine names
$cuisines = [];
while ($cuisine = $cuisineResult->fetch_assoc()) {
    $cuisines[] = $cuisine['name'];
}

// If restaurant is not found this time, redirects to browse page
if (!$restaurant) {
    header("Location: browse.php");
    exit();
}

// -----CODE TO GET AVERAGE RATING OF THE RESTAURANT FROM REVIEWS TABLE-----

$ratingQuery = "SELECT AVG(rating) AS average_rating FROM Reviews WHERE restaurantID = ?"; 
$ratingStmt = $conn->prepare($ratingQuery);
$ratingStmt->bind_param("i", $restaurantID);
$ratingStmt->execute();
$ratingResult = $ratingStmt->get_result();
$ratingData = $ratingResult->fetch_assoc();

$averageRating = $ratingData['average_rating']; // Saves average rating to a variable


$stmt->close(); // Close the statement 
?>

<!DOCTYPE html> 
<!--HTML-->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['name']); ?> - Details</title>

    <!--CSS STYLING FOR THE PAGE-->
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color:rgb(89, 169, 255);
            color: #333;
        }

        .header {
            background-color: rgb(253, 204, 211);
            color: black;
            padding: 30px 20px;
            position: relative;
            text-align: center;
        }

        .header h1 {
            font-size: 45px;
            margin-bottom: 10px;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background:rgb(190, 242, 255);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .restaurant-info {
            text-align: center;
        }

        .restaurant-info p {
            font-size: 18px;
            margin: 12px 0;
        }

        .highlight {
            color:rgb #b03a5b ;
            font-weight: bold;
        }

        .rating {
            font-size: 24px;
            color:rgb(0, 0, 0);
            margin: 15px 0;
        }

        .btn-container {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn {
            background-color: rgb(253, 204, 211);
            color: black;
            text-decoration: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #457b9d;
        }

        
    </style>
</head>

<body>
<!-- Header with restaurant name -->
<div class="header">
    <h1><?php echo htmlspecialchars($restaurant['name']); ?></h1>
</div>

<!-- Main content -->
<div class="container">
    <div class="restaurant-info">
        <p><span class="highlight">Location:</span> <?php echo htmlspecialchars($restaurant['location']); ?></p> <!--Shows location-->
        <p><span class="highlight">Operation Status:</span> <?php echo htmlspecialchars($restaurant['operationStatus']); ?></p> <!--Shows operation status-->
        
        <!-- List of cuisines -->
        <p><span class="highlight">Cuisines:</span>
            <?php echo implode(", ", $cuisines); // prints all the cuisines of the restaurant
            ?> 
        </p>
        <!-- Shows average rating -->
        <p><strong>Average Rating:</strong>
            <?php 
            echo $averageRating !== null // if avg rating is not null, prints it out (nearest value)
                ? number_format($averageRating, 1) . " ⭐" 
                : "Not Rated Yet ⭐"; //else, it prints "not rated yet"
            ?>
        </p>
    </div>

    <!-- Navigation Buttons -->
    <div class="btn-container">
        <a href="dishes.php?restaurantID=<?php echo $restaurant['restaurantID']; ?>" class="btn">View Dishes</a>
        <a href="review.php?restaurantID=<?php echo $restaurant['restaurantID']; ?>" class="btn">View Reviews</a>
    </div>
</div>

<!-- Back to Browse page -->
<div style="margin-top: 30px; text-align: center;">
    <a href="browse.php" 
       style="padding: 10px 20px; background-color: rgb(76, 175, 80); color: white; text-decoration: none; border-radius: 6px;">
        ← Back to Browse Page
    </a>
</div>
</body>
</html>

<?php
// Closes DB connection
 $conn->close();
?>