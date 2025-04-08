<?php
include '../includes/dbConn.php';
include '../includes/authUser.php';
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dishes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
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
        .fav-msg {
            display: none;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Dishes</h2>

<!-- Back Button -->
<div style="margin-top: 30px; text-align: center;">
    <a href="restaurant.php?restaurantID=<?= htmlspecialchars($restaurantID) ?>" class="back-btn">
        ← Back to Restaurant Page
    </a>
</div>

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
        <?php
        $isLiked = in_array($dish['dishID'], $liked);
        $heartClass = $isLiked ? 'heart liked' : 'heart';
        ?>
        <div class="dish-card">
            <h3><?= htmlspecialchars($dish['name']) ?> - $<?= number_format($dish['price'], 2) ?></h3>
            <p><?= htmlspecialchars($dish['description']) ?></p>
            <p>Diet: <?= htmlspecialchars($dish['dietName'] ?? 'None') ?></p>
            <span class="<?= $heartClass ?>"
                  onclick="toggleLike(this, <?= $dish['dishID'] ?>)">❤️</span>
            <span class="fav-msg">✅ Added to Favorites</span>
        </div>
    <?php endwhile; ?>
</div>

<script>
function toggleLike(element, dishID) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "dishes.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("toggleLike=1&dishID=" + dishID);

    xhr.onload = function() {
        if (xhr.status === 200) {
            // Toggle the heart color
            element.classList.toggle("liked");

            // Show the "Added to Favorites" message
            const msg = element.nextElementSibling;
            msg.style.display = "inline-block";

            // Hide the message after 2 seconds
            setTimeout(() => {
                msg.style.display = "none";
            }, 2000);
        } else {
            alert("Something went wrong!");
        }
    };
}
</script>

</body>
</html>

<?php
// Handle the AJAX request to like/unlike a dish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggleLike']) && isset($_POST['dishID'])) {
    $dishID = (int)$_POST['dishID'];
    if (in_array($dishID, $liked)) {
        // Remove from favorites
        $deleteQuery = "DELETE FROM FavouriteDish WHERE userID = ? AND dishID = ?";
        $deleteStmt = mysqli_prepare($conn, $deleteQuery);
        mysqli_stmt_bind_param($deleteStmt, "ii", $userID, $dishID);
        mysqli_stmt_execute($deleteStmt);
        echo json_encode(["status" => "removed"]);
    } else {
        // Add to favorites
        $insertQuery = "INSERT INTO FavouriteDish (userID, dishID) VALUES (?, ?)";
        $insertStmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "ii", $userID, $dishID);
        mysqli_stmt_execute($insertStmt);
        echo json_encode(["status" => "added"]);
    }
    exit;
}
?>