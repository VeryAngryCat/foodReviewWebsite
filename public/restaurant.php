<?php
session_start();
include('../includes/dbConn.php');

// Hardcoding a test restaurant ID (e.g., 1 for testing purposes)
$restaurantID = 1;

// Get the restaurant ID from the URL
if (isset($_GET['restaurantID'])) {
    $restaurantID = $_GET['restaurantID'];
} else {
    // Redirect if no restaurant ID is provided
    header("Location: browse.php");
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID']; // Get logged-in user ID

// Fetch restaurant details from the database
$query = "SELECT * FROM Restaurant WHERE restaurantID = ?";
$stmt = $conn->prepare($sql);

// Bind the restaurantID to the prepared statement
$stmt->bind_param("i", $restaurantID); // "i" stands for integer
$stmt->execute();

// Get the result
$result = $stmt->get_result();
$restaurant = $result->fetch_assoc();


if (!$restaurant) {
    // If restaurant not found, redirect to browse page
    header("Location: browse.php");
    exit();
}

// Get the restaurant ID (from the URL or request)
$restaurantID = $_GET['restaurantID'];  // Ensure that you are passing the restaurantID correctly via URL

// Check if the restaurant is already in user's favorites
$favQuery = "SELECT * FROM FavouriteRestaurant WHERE userID = ? AND restaurantID = ?";
$favStmt = $conn->prepare($favQuery);

// Bind parameters (i = integer for both userID and restaurantID)
$favStmt->bind_param("ii", $userID, $restaurantID);

// Execute the statement
$favStmt->execute();

// Get the result
$favResult = $favStmt->get_result();

// Check if the restaurant is in the user's favorites
$isFavorite = $favResult->fetch_assoc();

// Close the statement
$favStmt->close();

// If $isFavorite is not null, the restaurant is already in the favorites
if ($isFavorite) {
    echo "This restaurant is in your favorites!";
} else {
    echo "This restaurant is not in your favorites yet.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['name']); ?> - Restaurant Details</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        h1, h2 {
            text-align: center;
            color: #2d6a4f;
            margin-bottom: 20px;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        .restaurant-details {
            text-align: center;
            padding-bottom: 20px;
        }

        .restaurant-details p {
            font-size: 18px;
            line-height: 1.6;
            margin: 10px 0;
        }

        .restaurant-details .btn {
            background-color: #2d6a4f;
            color: #ffffff;
            padding: 10px 20px;
            margin: 10px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .restaurant-details .btn:hover {
            background-color: #2a5c47;
        }

        .heart-button {
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
            color: #2d6a4f;
            transition: color 0.3s ease;
        }

        .heart-button:hover {
            color: #1b5e20;
        }

        .heart-button:focus {
            outline: none;
        }

        /* Styling for the navigation links and footer */
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            background-color: #2d6a4f;
            color: #ffffff;
        }
    </style>
</head>
<body>

<!-- Restaurant Details Section -->
<h1><?php echo htmlspecialchars($restaurant['name']); ?></h1>
<p><strong>Location:</strong> <?php echo htmlspecialchars($restaurant['location']); ?></p>
<p><strong>Operation Status:</strong> <?php echo htmlspecialchars($restaurant['operationStatus']); ?></p>

<!-- Average Rating Section (Optional, assuming it's available) -->
<p><strong>Average Rating:</strong> <?php echo number_format($restaurant['average_rating'], 1); ?> ⭐</p>

<!-- Navigation buttons -->
<a href="dishes.php?restaurantID=<?php echo $restaurant['restaurantID']; ?>" class="btn">View Dishes</a>
<a href="reviews.php?restaurantID=<?php echo $restaurant['restaurantID']; ?>" class="btn">View Reviews</a>
