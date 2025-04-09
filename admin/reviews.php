<?php
// Database connection
include '../includes/dbConn.php';
include '../includes/authAdmin.php';

// Search box
$searchTerm = $_POST['search'] ?? '';
$searchSql = $searchTerm ? "WHERE commentLeft LIKE '%$searchTerm%'" : '';
// Moderate Reviews
// Delete Review

$allReviews = $conn->query("SELECT reviewID, datePosted, rating, commentLeft FROM Reviews $searchSql");
$selectedReview = null;
$reviewID = $_GET['reviewID'] ?? null;

if($reviewID) {
    $stmt = $conn->prepare("SELECT * FROM Reviews WHERE reviewID = ?");
    $stmt->bind_param("i", $reviewID);
    $stmt->execute();
    $result = $stmt->get_result();
    $review = $result->fetch_assoc();
    $review1 = $result->fetch_assoc();
    // Edits name of review

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['edit'])) {
            $newComment = $_POST['comment'];
            $updateStmt = $conn->prepare("UPDATE Reviews SET commentLeft=? WHERE reviewID=?");
            $updateStmt->bind_param("si", $newComment, $reviewID);
            $updateStmt->execute();
            header("Location: ../admin/reviews.php?reviewID=$reviewID");
            exit;
        }
    // Deletes review
        if (isset($_POST['delete'])) {
            $deleteStmt = $conn->prepare("DELETE FROM Reviews WHERE reviewID=?");
            $deleteStmt->bind_param("i", $reviewID);
            $deleteStmt->execute();
            header("Location: ../admin/reviews.php"); // Goes back to list
            exit;
        }
    }
}
?>

<!DOCTYPE  html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Comments</title>
    <link rel="stylesheet" type="text/css" href="../assets/foodRev3.css">
    <style>
        .container { display: flex; gap: 40px; padding: 40px; }
        .comments-list {flex:1;}
        .comment { border: 1px solid black; padding: 10px; margin-bottom: 10px; cursor: pointer; }
        .edit-box { flex: 1; border: 1px solid black; padding: 20px;}
        .search-bar { margin-bottom: 20px; padding: 10px; border-radius: 4px; border-color: grey; }
    </style>
</head>
    <body>
        <h1>Manage Comments</h1>

        <form method="post" class="search-bar">
            <input type="text" name="search" placeholder="Search comment" value="<?= htmlspecialchars($searchTerm) ?>">
            <button type="submit">Search</button>
        </form>

        <div class="container">
            <div class="comments-list">
                <?php while ($review1 = mysqli_fetch_assoc($allReviews)): ?>
                    <div class="comment">
                        <a href="?reviewID=<?= $review1['reviewID'] ?>" style="text-decoration:none; color:inherit;">
                            <?= htmlspecialchars($review1['datePosted']) ?><br>
                            Rating: <?= htmlspecialchars($review1['rating']) ?>/5<br>
                            <?= htmlspecialchars($review1['commentLeft']) ?>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php if (isset($review) && $review):?>
                <div class="edit-box">
                    <h2>Edit Comment</h2>
                    <form method="post">
                        <textarea name="comment" rows="5" cols="40"><?= htmlspecialchars($review['commentLeft'] ?? '') ?></textarea>
                        <br><br>
                        <button type="submit" name="edit">Save</button>
                        <button type="submit" name="delete" onclick="return confirm('Delete this comment?')">Delete</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <?php
        // Closes the database connection
        mysqli_close($conn);
        ?>
    </body>
</html>