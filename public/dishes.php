<?php
include '../includes/dbConn.php';
include '../includes/authUser.php';

$userID = $_SESSION['userID'];

// Handle heart icon toggle (like/unlike)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggleLike'], $_POST['dishID'])) {
    $dishID = (int)$_POST['dishID'];

    // Check if already liked
    $checkSql = "SELECT * FROM FavouriteDish WHERE userID = ? AND dishID = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ii", $userID, $dishID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Unlike (delete from favourites)
        $delStmt = $conn->prepare("DELETE FROM FavouriteDish WHERE userID = ? AND dishID = ?");
        $delStmt->bind_param("ii", $userID, $dishID);
        $delStmt->execute();
    } else {
        // Like (insert into favourites)
        $insStmt = $conn->prepare("INSERT INTO FavouriteDish (userID, dishID) VALUES (?, ?)");
        $insStmt->bind_param("ii", $userID, $dishID);
        $insStmt->execute();
    }

    exit();  // Stop further output for AJAX call
}

// Page rendering starts here
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
$likeQuery = "SELECT dishID FROM FavouriteDish WHERE userID = ?";
$likeStmt = mysqli_prepare($conn, $likeQuery);
mysqli_stmt_bind_param($likeStmt, "i", $userID);
mysqli_stmt_execute($likeStmt);
$likeResult = mysqli_stmt_get_result($likeStmt);
while ($row = mysqli_fetch_assoc($likeResult)) {
    $liked[] = $row['dishID'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: rgb(89, 169, 255);
            color: #333;
        }

        header {
            background-color: rgb(253, 204, 211);
            color: black;
            padding: 30px 20px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            border-bottom: 4px solid #fff;
        }

        form {
            margin: 20px auto;
            max-width: 400px;
            text-align: center;
        }

        .dish-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .dish-card {
            background-color: rgb(190, 242, 255);
            border-left: 4px solid rgb(245, 174, 209);
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            position: relative;
        }

        .dish-card h3 {
            margin-top: 0;
            font-weight: bold;
        }

        .dish-card p {
            margin: 5px 0;
        }

        .heart {
            cursor: pointer;
            font-size: 20px;
            user-select: none;
            position: absolute;
            top: 15px;
            right: 15px;
        }

        .liked {
            color: red;
        }
    </style>
</head>
<body>

<header>
    Dishes Available
</header>

<!-- Dietary Preference Filter -->
<form method="GET">
    <input type="hidden" name="restaurantID" value="<?= htmlspecialchars($restaurantID) ?>">
    <label><strong>Filter by Dietary Preference:</strong></label>
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
<div class="dish-grid">
    <?php while ($dish = mysqli_fetch_assoc($dishResult)) : ?>
        <div class="dish-card">
            <h3><?= htmlspecialchars($dish['name']) ?> - $<?= number_format($dish['price'], 2) ?></h3>
            <p><?= htmlspecialchars($dish['description']) ?></p>
            <p><strong>Diet:</strong> <?= htmlspecialchars($dish['dietName'] ?? 'None') ?></p>
            <span class="heart <?= in_array($dish['dishID'], $liked) ? 'liked' : '' ?>"
                  title="Click to like/unlike"
                  onclick="toggleLike(<?= $dish['dishID'] ?>, this)">❤️</span>
        </div>
    <?php endwhile; ?>
</div>
<div style="margin-top: 30px; text-align: center;">
    <a href="restaurant.php?restaurantID=<?= $restaurantID ?>"
    style="padding: 10px 20px; background-color: rgb(76, 175, 80); color: white; text-decoration: none; border-radius: 6px;">
    ← Back to Restaurant Page
    </a>
</div>
<script>
function toggleLike(dishID, element) {
    element.style.pointerEvents = "none"; // prevent double clicks

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "dishes.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.status === 200) {
            element.classList.toggle("liked");
        } else {
            alert("Something went wrong!");
        }
        element.style.pointerEvents = "auto";
    };
    xhr.send("toggleLike=1&dishID=" + dishID);
}
</script>

</body>
</html>
