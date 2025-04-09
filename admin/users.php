<?php
// Database connection
include '../includes/dbConn.php';
include '../includes/authAdmin.php';

// View info on a user
// Disabke user's comments
// Delete user
// User info
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

// For user comments
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


// Closes the database connection
mysqli_close($conn);
?>

<!DOCTYPE  html>
<html>
    <head></head>
    <body>
        <div class="container">
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['firstName'] . " " . $user['lastName'];); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <?php if ($reviewResult->num_rows > 0) {
                <div class="user-info">
                    while ($row = $reviewResult->fetch_assoc()) {
                        echo "<div class='review-box'>";
                        echo "Restaurant:" . htmlspecialchars($row['restaurantName']) . "<br>";
                        echo "Rating:" . htmlspecialchars($row['rating']) . "/5<br>";
                        echo "Date:" . htmlspecialchars($row['datePosted']) . "<br>";
                        echo "Comment:" . nl2br(htmlspecialchars($row['commentLeft']));
                        echo "</div>";
                    }
                </div>    
                } else {
                    echo "<p>This person hasn't written any reviews.</p>";
                } ?>
        </div>
    </body>
</html>