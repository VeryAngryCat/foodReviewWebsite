<?php
// Database connection
include '../includes/dbConn.php';
include '../includes/authUser.php';

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

$userID = $_SESSION['userID'] ?? null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $userID) {
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    if ($rating && $comment) {
        $insertQuery = "INSERT INTO Reviews (userID, restaurantID, rating, commentLeft, datePosted) 
                        VALUES (?, ?, ?, ?, CURDATE())";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("iiis", $userID, $restaurantID, $rating, $comment);
        $stmt->execute();
        $stmt->close();

        header("Location: review.php?restaurantID=$restaurantID");
        exit();
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
            background-color: rgb(89, 169, 255); /* Matching your earlier theme */
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: rgb(253, 204, 211);
            padding: 30px;
            text-align: center;
            color: black;
            font-size: 28px;
            font-weight: bold;
            
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .review-form {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            height: 100px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        select, input[type="submit"] {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

        input[type="submit"] {
            background: rgb(244, 119, 182);
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background: rgb(237, 102, 169);
        }

        .review-box {
            background-color: rgb(248, 222, 235);
            padding: 15px;
            margin: 15px 0;
            border-left: 5px solid rgb(245, 174, 209);
            border-radius: 8px;
        }

        .review-box .user {
            font-weight: bold;
            font-size: 16px;
            color: #333;
        }

        .review-box .rating {
            color: rgb(244, 180, 0);
            font-weight: bold;
            margin-bottom: 5px;
        }

        .review-box .date {
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

<div class="header">
    Reviews for <?= htmlspecialchars($restaurantName) ?>
</div>


    <!-- Review Form -->
    <div class="review-form">
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
                </select><br>

                <label for="comment">Leave a comment:</label>
                <textarea name="comment" id="comment" placeholder="Write your thoughts..." required></textarea>

                <input type="submit" value="Submit Review">
            </form>
        <?php else: ?>
            <p><strong><a href="login.php">Login</a></strong> to submit a review.</p>
        <?php endif; ?>
    </div>

    <hr>

    <!-- Reviews -->
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
        <div class="no-reviews">No reviews yet. Be the first to write one!</div>
    <?php endif; ?>
</div>

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