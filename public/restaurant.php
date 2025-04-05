<?php
session_start();
include('../includes/dbConn.php');

// Hardcoding a test restaurant ID (e.g., 1 for testing purposes)
$restaurantID = 1;

// Get the restaurant ID from the URL
if (isset($_GET['restaurantID'])) {
    $restaurantID = $_GET['restaurantID'];
} else {
    header("Location: browse.php");
    exit();
}

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

$query = "SELECT * FROM Restaurant WHERE restaurantID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $restaurantID);
$stmt->execute();
$result = $stmt->get_result();
$restaurant = $result->fetch_assoc();

if (!$restaurant) {
    header("Location: browse.php");
    exit();
}
// Get average rating from Review table
$ratingQuery = "SELECT AVG(rating) AS average_rating FROM Reviews WHERE restaurantID = ?";
$ratingStmt = $conn->prepare($ratingQuery);
$ratingStmt->bind_param("i", $restaurantID);
$ratingStmt->execute();
$ratingResult = $ratingStmt->get_result();
$ratingData = $ratingResult->fetch_assoc();

$averageRating = $ratingData['average_rating'];

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['name']); ?> - Details</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color:rgb(153, 219, 215);
            color: #333;
        }

        .header {
            background-color: rgb(255, 225, 238);
            color: black;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 45px;
            margin-bottom: 10px;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background:rgb(255, 255, 255);
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
            background-color: #b03a5b;
            color: white;
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

<div class="header">
    <h1><?php echo htmlspecialchars($restaurant['name']); ?></h1>
    
</div>

<div class="container">
    <div class="restaurant-info">
        <p><span class="highlight">Location:</span> <?php echo htmlspecialchars($restaurant['location']); ?></p>
        <p><span class="highlight">Operation Status:</span> <?php echo htmlspecialchars($restaurant['operationStatus']); ?></p>
        <p><strong>Average Rating:</strong>
            <?php 
            echo $averageRating !== null 
                ? number_format($averageRating, 1) . " ⭐" 
                : "Not Rated Yet ⭐"; 
            ?>
        </p>
    </div>

    <div class="btn-container">
        <a href="dishes.php?restaurantID=<?php echo $restaurant['restaurantID']; ?>" class="btn">View Dishes</a>
        <a href="review.php?restaurantID=<?php echo $restaurant['restaurantID']; ?>" class="btn">View Reviews</a>
    </div>
</div>



</body>
</html>

<?php $conn->close(); ?>