<?php
session_start();
include '../includes/dbConn.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION['userID'];
$restaurantID = isset($_GET['restaurantID']) ? (int)$_GET['restaurantID'] : null;
$filterDietID = isset($_GET['diet']) ? (int)$_GET['diet'] : null;

if (!$restaurantID) {
    echo "No restaurant selected.";
    exit;
}

// Get dietary preferences
$dietQuery = "SELECT dietID, name FROM DietaryPreference";
$dietResult = mysqli_query($conn, $dietQuery);

// Get dishes for restaurant with optional filter
$dishQuery = "SELECT d.*, dp.name AS dietName 
              FROM Dish d 
              LEFT JOIN DietaryPreference dp ON d.DietID = dp.dietID 
              WHERE d.restaurantID = ?";

$params = [$restaurantID];
$types = "i";

if ($filterDietID) {
    $dishQuery .= " AND d.DietID = ?";
    $params[] = $filterDietID;
    $types .= "i";
}

$stmt = mysqli_prepare($conn, $dishQuery);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$dishResult = mysqli_stmt_get_result($stmt);

// Get liked dishes by user
$liked = [];
if ($userID) {
    $likeQuery = "SELECT dishID FROM FavouriteDish WHERE userID = ?";
    $likeStmt = mysqli_prepare($conn, $likeQuery);
    mysqli_stmt_bind_param($likeStmt, "i", $userID);
    mysqli_stmt_execute($likeStmt);
    $likeResult = mysqli_stmt_get_result($likeStmt);
    while ($row = mysqli_fetch_assoc($likeResult)) {
        $liked[] = $row['dishID'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dishes</title>
    <style>
        .dish-card {
            border: 1px solid #ccc;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
        }
        .heart {
            cursor: pointer;
            font-size: 20px;
            user-select: none;
        }
        .liked {
            color: red;
        }
    </style>
</head>
<body>

<h2>Dishes</h2>

<!-- Dietary Preference Filter -->
<form method="GET">
    <input type="hidden" name="restaurantID" value="<?= htmlspecialchars($restaurantID) ?>">
    <label>Filter by Dietary Preference:</label>
    <select name="diet" onchange="this.form.submit()">
        <option value="">All</option>
        <?php while ($diet = mysqli_fetch_assoc($dietResult)) : ?>
            <option value="<?= $diet['dietID'] ?>" <?= ($filterDietID == $diet['dietID']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($diet['name']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<!-- Dishes Display -->
<div id="dishes">
    <?php while ($dish = mysqli_fetch_assoc($dishResult)) : ?>
        <div class="dish-card">
            <h3><?= htmlspecialchars($dish['name']) ?> - $<?= number_format($dish['price'], 2) ?></h3>
            <p><?= htmlspecialchars($dish['description']) ?></p>
            <p>Diet: <?= htmlspecialchars($dish['dietName'] ?? 'None') ?></p>
            <span class="heart <?= in_array($dish['dishID'], $liked) ? 'liked' : '' ?>"
                  onclick="toggleLike(<?= $dish['dishID'] ?>, this)">❤️</span>
        </div>
    <?php endwhile; ?>
</div>

<script>
function toggleLike(dishID, element) {
    // Disable button while request is in progress
    element.disabled = true;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "dishes.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("toggleLike=1&dishID=" + dishID);

    xhr.onload = function() {
        if (xhr.status === 200) {
            element.classList.toggle("liked");
        } else {
            alert("Something went wrong!");
        }
        // Re-enable button after request completes
        element.disabled = false;
    };
}
</script>

</body>
</html>