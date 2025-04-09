<?php
include '../includes/dbConn.php';
include '../includes/authUser.php';

$userID = $_SESSION['userID'];

// Get user info
$stmt = $conn->prepare("SELECT firstName, lastName, email, username FROM `Users` WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "No user data found for userID: $userID";
    exit();
}

$firstName = $user['firstName'];
$lastName = $user['lastName'];
$email = $user['email'];
$username = $user['username'];

// Get number of favorite restaurants
$favSql = "SELECT COUNT(*) FROM FavouriteRestaurant WHERE userID = ?";
$favStmt = $conn->prepare($favSql);
$favStmt->bind_param("i", $userID);
$favStmt->execute();
$favStmt->bind_result($favoriteCount);
$favStmt->fetch();
$favStmt->close();


// Get user reviews (FIXED query without extra join)
$reviewSql = "
    SELECT r.commentLeft, r.rating, r.datePosted, res.name AS restaurantName
    FROM Reviews r 
    JOIN Restaurant res ON r.restaurantID = res.restaurantID 
    WHERE r.userID = ?";
$reviewStmt = $conn->prepare($reviewSql);
$reviewStmt->bind_param("i", $userID);
$reviewStmt->execute();
$reviewResult = $reviewStmt->get_result();

// Get user's dietary preferences
$dietSql = "
    SELECT d.name, d.description 
    FROM FavouriteDiet fd 
    JOIN DietaryPreference d ON fd.dietID = d.dietID 
    WHERE fd.userID = ?";
$dietStmt = $conn->prepare($dietSql);
$dietStmt->bind_param("i", $userID);
$dietStmt->execute();
$dietResult = $dietStmt->get_result();

// Get favorite dishes with restaurant info
$favCombinedSql = "
    SELECT d.name AS dishName, r.name AS restaurantName 
    FROM FavouriteDish fd
    JOIN Dish d ON fd.dishID = d.dishID
    JOIN Restaurant r ON d.restaurantID = r.restaurantID
    WHERE fd.userID = ?";
$favCombinedStmt = $conn->prepare($favCombinedSql);
$favCombinedStmt->bind_param("i", $userID);
$favCombinedStmt->execute();
$favCombinedResult = $favCombinedStmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(89, 169, 255);
            color:rgb(2, 1, 1);
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
            border: 3px solid rgb(4, 4, 4);
        }
        h2, h3 {
            text-align: center;
            color:rgb(2, 2, 2);
        }
        .info p {
            font-size: 16px;
        }
        .back-btn, .logout-btn {
            display: inline-block;
            margin: 10px 5px;
            padding: 10px 15px;
            background-color: rgb(76, 175, 80);
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        .back-btn:hover, .logout-btn:hover {
            background-color: rgb(244, 119, 182);
        }
        .reviews {
            margin-top: 25px;
        }
        .review-box {
            background-color: rgb(206, 241, 250);
            padding: 12px;
            margin-bottom: 15px;
            border-left: 4px solid rgb(245, 174, 209);
        }
        .review-box strong {
            color:rgb(10, 10, 10);
            font-weight: bold
            
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: rgb(200, 239, 249);
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solidrgb(0, 0, 0);
            text-align: left;
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

            <?php if ($dietResult->num_rows > 0): ?>
                <p><strong>Dietary Preferences:</strong></p>
                <ul>
                    <?php while ($diet = $dietResult->fetch_assoc()): ?>
                       <li>
                          <strong><?php echo htmlspecialchars($diet['name']); ?></strong>
                          <?php if ($diet['description']): ?>
                            - <?php echo htmlspecialchars($diet['description']); ?>
                          <?php endif; ?>
                       </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p><strong>Dietary Preferences:</strong> You haven't selected any.</p>
            <?php endif; ?>      
    
        </div>

        <div class="reviews">
            <h3>Your Favorite Dishes & Restaurants</h3>
            <?php if ($favCombinedResult->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Dish</th>
                        <th>Restaurant</th>
                    </tr>
                    <?php while ($row = $favCombinedResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['dishName']); ?></td>
                        <td><?php echo htmlspecialchars($row['restaurantName']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>You haven't favorited any dishes yet.</p>
            <?php endif; ?>

            <h3>Your Reviews</h3>
            <?php 
            if ($reviewResult->num_rows > 0) {
                while ($row = $reviewResult->fetch_assoc()) {
                    echo "<div class='review-box'>";
                    echo "<strong>Restaurant:</strong> " . htmlspecialchars($row['restaurantName']) . "<br>";
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
            <a href="../public/browse.php" class="back-btn">← Back to Browse</a>
            <a href="../public/index.php" class="logout-btn">Logout</a>
        </div>
    </div>

</body>
</html>