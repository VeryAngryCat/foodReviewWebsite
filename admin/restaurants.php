<?php
// Database connection
include '../includes/dbConn.php';
include '../includes/authAdmin.php';

$allRestaurants = $conn->query("SELECT restaurantID, name FROM Restaurant");
$restaurantID = $_GET['restaurantID'] ?? null;
// Views info on a restaurant

if($restaurantID) {
    $stmt = $conn->prepare("SELECT * FROM Restaurant WHERE restaurantID = ?");
    $stmt->bind_param("i", $restaurantID);
    $stmt->execute();
    $result = $stmt->get_result();
    $restaurant = $result->fetch_assoc();

    if (!$restaurant) {
        echo "Restaurant not found.";
        exit;
    }

    // Gets no of reviews
    $reviewQuery = "SELECT COUNT(*) AS totalReviews, AVG(rating) AS avgRating FROM Reviews WHERE restaurantID = ?";
    $reviewStmt = $conn->prepare($reviewQuery);
    $reviewStmt->bind_param("i", $restaurantID);
    $reviewStmt->execute();
    $resultReview = $reviewStmt->get_result();
    $reviewStats = $resultReview->fetch_assoc();

    // Gets average rating from Review table
    $ratingQuery = "SELECT AVG(rating) AS average_rating FROM Reviews WHERE restaurantID = ?";
    $ratingStmt = $conn->prepare($ratingQuery);
    $ratingStmt->bind_param("i", $restaurantID);
    $ratingStmt->execute();
    $resultRating = $ratingStmt->get_result();
    $ratingData = $resultRating->fetch_assoc();
    $averageRating = $ratingData['average_rating'];

    // Gets no of dishes
    $dishQuery = "SELECT COUNT(*) AS totalDishes FROM Dish WHERE restaurantID = ?";
    $dishStmt = $conn->prepare($dishQuery);
    $dishStmt->bind_param("i", $restaurantID);
    $dishStmt->execute();
    $resultDish = $dishStmt->get_result();
    $dishStats = $resultDish->fetch_assoc();

    // Gets status
    $statusQuery = "SELECT operationStatus FROM Restaurant WHERE restaurantID = ?";
    $statusStmt = $conn->prepare($statusQuery);
    $statusStmt->bind_param("s", $restaurantID);
    $statusStmt->execute();
    $resultStatus = $statusStmt->get_result();
    $statusStats = $resultStatus->fetch_assoc();

    $action = $_GET['action'] ?? '';
    // Edits name of restaurant
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['edit'])) {
            $newName = $_POST['name'];
            $updateStmt = $conn->prepare("UPDATE Restaurant SET name=? WHERE restaurantID=?");
            $updateStmt->bind_param("si", $newName, $restaurantID);
            $updateStmt->execute();
            header("Location: ../admin/restaurants.php?restaurantID=$restaurantID");
            exit;
        }
    // Deletes restaurant
        if (isset($_POST['delete'])) {
            $deleteStmt = $conn->prepare("DELETE FROM Restaurant WHERE restaurantID=?");
            $deleteStmt->bind_param("i", $restaurantID);
            $deleteStmt->execute();
            header("Location: ../admin/restaurants.php"); // Go back to list
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
        <title>Manage Restaurants</title>
        <link rel="stylesheet" type="text/css" href="../assets/foodRev3.css">
    </head>
    <body>
        <form method="get" action="../admin/restaurants.php" class="choice">
            <label>Select a Restaurant:</label>
            <select name="restaurantID" onchange="this.form.submit()">
                <option disabled selected>Select one</option>
                <?php while ($row = $allRestaurants->fetch_assoc()): ?>
                    <option value="<?= $row['restaurantID'] ?>" <?= isset($restaurantID) && $restaurantID == $row['restaurantID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
        <?php if (isset($restaurantID)): ?>
            <div class="container" style="margin-top: 200px; width: 75vw;">
                <h1 style="padding-top: 20px; padding-bottom: 100px;"><?= htmlspecialchars($restaurant['name']) ?></h1>
                <div class="stats">
                    <div class="stat-card">Total Reviews: <?= $reviewStats['totalReviews'] ?></div>
                    <div class="stat-card">Avg Rating: <?= number_format($reviewStats['avgRating'], 1) ?>/5</div>
                    <div class="stat-card">Total Dishes: <?= $dishStats['totalDishes'] ?></div>
                    <div class="stat-card">Operational Status: <?= $statusStats['operationStatus'] ?></div>
                </div>
                <form method="post" class="edit-delete-form">
                    <label for="name">Edit Restaurant Name:</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($restaurant['name']) ?>">
                    <div class="form-buttons">
                        <button type="submit" name="edit" class="edit-button">Save Changes</button>
                        <button type="submit" name="delete" class="delete-button" onclick="return confirm('Delete Restaurant?')">Delete Restaurant</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        <?php // Closes the database connection
        mysqli_close($conn);
        ?>
    </body>
</html>