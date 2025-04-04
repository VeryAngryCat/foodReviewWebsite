<?php
error_reporting(E_ALL); // Report all errors
ini_set('display_errors', 1); // Display errors on the page

session_start();


// Include the database connection file (adjust path if necessary)
include '../includes/dbConn.php'; // Adjust the path as needed

// Check if the userID session is set (for testing, it will always be set)
if (!isset($_SESSION['userID'])) {
    echo "Session variable 'userID' is not set.";
    exit();
}

$userID = $_SESSION['userID'];

// Get user info
$stmt = $conn->prepare("SELECT firstName, lastName, email, username FROM `Users` WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($firstName, $lastName, $email, $username);
$stmt->fetch();
$stmt->close();

// Check if the user data was fetched
if (!$firstName) {
    echo "No user data found for userID: $userID";
    exit();
}

// Get number of favorite restaurants
$favSql = "SELECT COUNT(*) FROM FavouriteRestaurant WHERE userID = ?";
$favStmt = $conn->prepare($favSql);
$favStmt->bind_param("i", $userID);
$favStmt->execute();
$favStmt->bind_result($favoriteCount);
$favStmt->fetch();
$favStmt->close();

// Check if the favorite count was fetched
if ($favoriteCount === NULL) {
    echo "Error fetching favorite restaurants count.";
    exit();
}

// Get user reviews
$reviewSql = "
    SELECT r.commentLeft, r.rating, r.datePosted, res.name 
    FROM Reviews r 
    JOIN FavouriteRestaurant fr ON r.restaurantID = fr.restaurantID 
    JOIN Restaurant res ON fr.restaurantID = res.restaurantID 
    WHERE r.userID = ?";
$reviewStmt = $conn->prepare($reviewSql);
$reviewStmt->bind_param("i", $userID);
$reviewStmt->execute();
$reviewResult = $reviewStmt->get_result();

// Check if reviews were fetched
if (!$reviewResult) {
    echo "Error fetching reviews for userID: $userID";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e8f5e9;
            color: #1b5e20;
            padding: 20px;
        }
        .container {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .profile-pic {
            text-align: center;
        }
        .profile-pic img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #66bb6a;
        }
        h2, h3 {
            text-align: center;
            color: #2e7d32;
        }
        .info p {
            font-size: 16px;
        }
        .back-btn, .logout-btn {
            display: inline-block;
            margin: 10px 5px;
            padding: 10px 15px;
            background-color: #66bb6a;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        .back-btn:hover, .logout-btn:hover {
            background-color: #388e3c;
        }
        .reviews {
            margin-top: 25px;
        }
        .review-box {
            background-color: #f1f8e9;
            padding: 12px;
            margin-bottom: 15px;
            border-left: 4px solid #81c784;
        }
        .review-box strong {
            color: #33691e;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="profile-pic">
            <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Profile Picture">
        </div>

        <h2><?php echo htmlspecialchars($firstName); ?>'s Profile</h2>
        <div class="info">
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($firstName . " " . $lastName); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
            <p><strong>Saved Favorites:</strong> <?php echo $favoriteCount; ?></p>
        </div>

        <div class="reviews">
            <h3>Your Reviews</h3>
            <?php 
            if ($reviewResult->num_rows > 0) {
                while ($row = $reviewResult->fetch_assoc()) {
                    echo "<div class='review-box'>";
                    echo "<strong>Restaurant:</strong> " . htmlspecialchars($row['name']) . "<br>";
                    echo "<strong>Rating:</strong> " . htmlspecialchars($row['rating']) . "/5<br>";
                    echo "<strong>Date:</strong> " . htmlspecialchars($row['datePosted']) . "<br>";
                    echo "<strong>Comment:</strong> " . nl2br(htmlspecialchars($row['commentLeft']));
                    echo "</div>";
                }
            } else {
                echo "<p>You haven’t written any reviews yet.</p>";
            }
            ?>
        </div>

        <div style="text-align: center;">
            <a href="browse.php" class="back-btn">← Back to Browse</a>
            <a href="index.php" class="logout-btn">Logout</a>
        </div>
    </div>

</body>
</html>