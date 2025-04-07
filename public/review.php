<?php
session_start();
// Database connection
include '../includes/dbConn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$restaurantID = $_GET['restaurantID'] ?? null;

if (!$restaurantID) {
    echo "No restaurant selected.";
    exit();
}

// Get restaurant name
$nameQuery = "SELECT name FROM Restaurant WHERE restaurantID = ?";
$stmt = $conn->prepare($nameQuery);
$stmt->bind_param("i", $restaurantID);
$stmt->execute();
$stmt->bind_result($restaurantName);
$stmt->fetch();
$stmt->close();

if (!$restaurantName) {
    echo "Restaurant not found.";
    exit();
}

// Get the restaurant ID from GET parameter
if (!isset($_GET['restaurantID'])) {
    echo "Restaurant ID not provided.";
    exit();
}
$restaurantID = $_GET['restaurantID'];

// Store logged-in userID if available
$userID = $_SESSION['userID'] ?? null;

// Handle form submission (only if user is logged in)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($userID) {
        $rating = $_POST['rating'];
        $comment = trim($_POST['comment']);

        if ($rating && $comment) {
            $insertQuery = "INSERT INTO Reviews (userID, restaurantID, rating, commentLeft, datePosted) VALUES (?, ?, ?, ?, CURDATE())";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("iiis", $userID, $restaurantID, $rating, $comment);
            $stmt->execute();
            $stmt->close();

            header("Location: review.php?restaurantID=$restaurantID");
            exit();
        }
    } else {
        echo "You must be logged in to submit a review.";
    }
}

// Fetch reviews
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            padding: 30px;
        }

        .container {
            width: 80%;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #2d6a4f;
        }

        .review-form {
            margin-top: 30px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            height: 100px;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        select, input[type="submit"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background: #2d6a4f;
            color: white;
            cursor: pointer;
        }

        .review {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }

        .review .rating {
            color: #f4b400;
            font-weight: bold;
        }

        .review .user {
            font-weight: bold;
            color: #333;
        }

        .review .date {
            font-size: 0.9em;
            color: #777;
        }

        .no-reviews {
            text-align: center;
            color: #999;
            padding: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Reviews for <?php echo htmlspecialchars($restaurantName); ?></h1>

    <!-- Review Form -->
    <div class="review-form">
        <form action="" method="POST">
        <?php if ($userID): ?>
        <form action="" method="POST">
            <label for="rating">Rating:</label>
            <select name="rating" id="rating" required>
                <option value="">Select</option>
                <option value="5">5 - Excellent ⭐</option>
                <option value="4">4 - Good ⭐</option>
                <option value="3">3 - Okay ⭐</option>
                <option value="2">2 - Bad ⭐</option>
                <option value="1">1 - Terrible ⭐</option>
            </select><br><br>

            <label for="comment">Leave a comment:</label>
            <textarea name="comment" id="comment" placeholder="Write your thoughts..." required></textarea><br>

            <input type="submit" value="Submit Review">
        </form>
    <?php else: ?>
        <p><strong><a href="login.php">Login</a></strong> to submit a review.</p>
    <?php endif; ?>

    <hr>

    <!-- Review List -->
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="review">
                <div class="user"><?php echo htmlspecialchars($row['username']); ?></div>
                <div class="rating">Rating: <?php echo $row['rating']; ?> ⭐</div>
                <div class="comment"><?php echo nl2br(htmlspecialchars($row['commentLeft'])); ?></div>
                <div class="date"><?php echo $row['datePosted']; ?></div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-reviews">No reviews yet. Be the first to write one!</div>
    <?php endif; ?>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>