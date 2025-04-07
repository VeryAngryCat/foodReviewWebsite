<?php
session_start();
// Database connection
include '../includes/dbConn.php';

if (isset($_GET['restaurantID'])) {
    $restaurantID = $_GET['restaurantID'];
    $query = "SELECT * FROM Restaurant WHERE restaurantID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $restaurantID);
    $stmt->execute();
    $result = $stmt->get_result();
    $restaurant = $result->fetch_assoc();

    if (!$restaurant) {
        echo "Restaurant not found.";
        exit;
    }
} else {
    echo "No restaurant ID provided.";
    exit();
}



if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

// Check if user already liked this restaurant
$liked = false;
$checkLike = $conn->prepare("SELECT * FROM FavouriteRestaurant WHERE userID = ? AND restaurantID = ?");
$checkLike->bind_param("ii", $userID, $restaurantID);
$checkLike->execute();
$likeResult = $checkLike->get_result();
if ($likeResult->num_rows > 0) {
    $liked = true;
}

// Handle Like Button POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['likeRestaurant'])) {
    $likeRestaurantID = intval($_POST['restaurantID']); // from hidden input

    // Check if already liked
    $check = $conn->prepare("SELECT * FROM FavouriteRestaurant WHERE userID = ? AND restaurantID = ?");
    $check->bind_param("ii", $userID, $likeRestaurantID);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        // Not already liked, insert into FavouriteRestaurent
        $insert = $conn->prepare("INSERT INTO FavouriteRestaurant (userID, restaurantID) VALUES (?, ?)");
        $insert->bind_param("ii", $userID, $likeRestaurantID);
        $insert->execute();
    } else {
        // Already liked, delete from FavouriteRestaurent (Unlike)
        $delete = $conn->prepare("DELETE FROM FavouriteRestaurant WHERE userID = ? AND restaurantID = ?");
        $delete->bind_param("ii", $userID, $likeRestaurantID);
        $delete->execute();
    }
}




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
            background-color:rgb(255, 225, 238);
            color: #333;
        }

        .header {
            background-color: rgb(153, 219, 215);
            color: black;
            padding: 30px 20px;
            position: relative;
            text-align: center;
        }

        .header h1 {
            font-size: 45px;
            margin-bottom: 10px;
        }
        .heart-form {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .heart-btn {
            background: none;
            border: none;
            font-size: 60px;
            cursor: pointer;
            padding: 0;
            margin: 0;
            color: black;
        }

        .heart-btn:hover {
            transform: scale(1.2);
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
    
    <!-- ❤️ Heart Button -->
    <?php if ($userID): ?>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="restaurantID" value="<?= $restaurantID ?>">
            <button type="submit" name="likeRestaurant" style="background:none; border:none; font-size:24px; cursor:pointer;">
                <?= $liked ? '❤️' : '🤍' ?>
            </button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Log in</a> to like this restaurant.</p>
    <?php endif; ?>
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