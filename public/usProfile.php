<?php
session_start();
include 'db.php';

//if (!isset($_SESSION['userID'])) {
   // header("Location: login.php");
   // exit();
//}

$userID = $_SESSION['userID'];

// Get user info
$stmt = $conn->prepare("SELECT firstName, lastName, email, username FROM user WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($firstName, $lastName, $email, $username);
$stmt->fetch();
$stmt->close();

// Get number of favorite restaurants
$favSql = "SELECT COUNT(*) FROM favorites WHERE userID = ?";
$favStmt = $conn->prepare($favSql);
$favStmt->bind_param("i", $userID);
$favStmt->execute();
$favStmt->bind_result($favoriteCount);
$favStmt->fetch();
$favStmt->close();

// Get user reviews
$reviewSql = "
    SELECT r.commentLeft, r.rating, r.datePosted, res.name 
    FROM review r 
    JOIN restaurants res ON r.restaurantID = res.restaurantID 
    WHERE r.userID = ?";
$reviewStmt = $conn->prepare($reviewSql);
$reviewStmt->bind_param("i", $userID);
$reviewStmt->execute();
$reviewResult = $reviewStmt->get_result();
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
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

</body>
</html>