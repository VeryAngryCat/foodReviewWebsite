<?php
// Database and authentication connection
include '../includes/dbConn.php';
include '../includes/authUser.php';

// Get restaurant ID from the URL
$restaurantID = $_GET['restaurantID'] ?? null;

// If no restaurant ID is given, prints error message and exits
if (!$restaurantID) {
    echo "No restaurant selected.";
    exit();
}

// Get restaurant name from ID
$nameQuery = "SELECT name FROM Restaurant WHERE restaurantID = ?"; 
$stmt = $conn->prepare($nameQuery);
// Bind the restaurant ID as an integer to the SQL query (tells the database what the parameters are)
$stmt->bind_param("i", $restaurantID);
// Execute the query
$stmt->execute();
// Get the result of the query and stores it in the variable "$restaurantName"
$stmt->bind_result($restaurantName);
// Fetches the data 
$stmt->fetch();
$stmt->close();

if (!$restaurantName) {
    echo "Restaurant not found.";
    exit();
}

// Get the user ID from session
$userID = $_SESSION['userID'] ?? null;

// If the review form is submitted and user is logged in
if ($_SERVER["REQUEST_METHOD"] == "POST" && $userID) {
    $rating = $_POST['rating']; // Gets rating (form input)
    $comment = trim($_POST['comment']); // Gets reviews

    // Only if both rating and comment are filled, the data is inserted into reviews table 
    if ($rating && $comment) {
        $insertQuery = "INSERT INTO Reviews (userID, restaurantID, rating, commentLeft, datePosted) 
                        VALUES (?, ?, ?, ?, CURDATE())";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("iiis", $userID, $restaurantID, $rating, $comment);
        $stmt->execute();
        $stmt->close();

        // Redirect back to the same page after submitting
        header("Location: review.php?restaurantID=$restaurantID");
        exit();
    }
}

// Fetch reviews for the restaurant
$reviewQuery = "SELECT R.rating, R.commentLeft, R.datePosted, U.username 
                FROM Reviews R
                JOIN Users U ON R.userID = U.userID
                WHERE R.restaurantID = ?
                ORDER BY R.datePosted DESC";

$stmt = $conn->prepare($reviewQuery);
$stmt->bind_param("i", $restaurantID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant Reviews</title>
    <link rel="stylesheet" type="text/css" href="../assets/foodRev2.css">
</head>
<body>

<div class="header">
    Reviews for <?= htmlspecialchars($restaurantName) ?>
</div>
<div class="container">
    <!-- Review Form -->
    <div class="review-form">
        <?php if ($userID): ?>
            <!-- Show form only if user is logged in -->
            <form action="" method="POST">
                <label for="rating">Rating:</label>
                <select name="rating" id="rating" required>
                    <option value="">Select</option>
                    <option value="5">5 - Excellent ⭐</option>
                    <option value="4">4 - Good ⭐</option>
                    <option value="3">3 - Okay ⭐</option>
                    <option value="2">2 - Bad ⭐</option>
                    <option value="1">1 - Terrible ⭐</option>
                </select><br>

                <label for="comment">Leave a comment:</label>
                <textarea name="comment" id="comment" placeholder="Write your thoughts..." required></textarea>

                <input type="submit" value="Submit Review">
            </form>
        <?php else: ?>
            <!-- Ask user to log in if not logged in -->
            <p><strong><a href="../public/login.php">Login</a></strong> to submit a review.</p>
        <?php endif; ?>
    </div>

    <hr>

    <!-- Shows Reviews -->
    <!-- If there are records in the reviews table, then the code will run and it will fetch each review -->
    <?php if ($result->num_rows > 0): ?> 
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="review-box">
                <div class="user"><?= htmlspecialchars($row['username']) ?></div>
                <div class="rating">Rating: <?= $row['rating'] ?> ⭐</div>
                <div class="comment"><?= nl2br(htmlspecialchars($row['commentLeft'])) ?></div>
                <div class="date"><?= $row['datePosted'] ?></div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <!-- If no reviews found -->
        <div class="no-reviews">No reviews yet. Be the first to write one!</div>
    <?php endif; ?>
</div>

<!-- Back button to restaurant page -->
<div style="margin-top: 30px; text-align: center;">
    <a href="restaurant.php?restaurantID=<?= $restaurantID ?>"
    style="padding: 10px 20px; background-color: rgb(76, 175, 80); color: white; text-decoration: none; border-radius: 6px;">
    ← Back to Restaurant Page
    </a>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>