<?php
// Database and authentication connection
include '../includes/dbConn.php';
include '../includes/authUser.php';

// Get the logged-in user's ID from session
$userID = $_SESSION['userID'];

//-----CODE TO HANDLE HEART ICON TOGGLE-----
// When icon is clicked for like/unlike
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggleLike'], $_POST['dishID'])) {
    $dishID = (int)$_POST['dishID']; // Ensures dishID is an integer

    // Checks if user already liked the dish
    $checkSql = "SELECT * FROM FavouriteDish WHERE userID = ? AND dishID = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ii", $userID, $dishID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Unlike it from Favourite Dish table
        $delStmt = $conn->prepare("DELETE FROM FavouriteDish WHERE userID = ? AND dishID = ?");
        $delStmt->bind_param("ii", $userID, $dishID);
        $delStmt->execute();
    } else {
        // If not liked, add it to Favourite Dish table
        $insStmt = $conn->prepare("INSERT INTO FavouriteDish (userID, dishID) VALUES (?, ?)");
        $insStmt->bind_param("ii", $userID, $dishID);
        $insStmt->execute();
    }

    exit();  // Stop further output
    
}

// Get restaurantID and optional diet filter from the URL
$restaurantID = isset($_GET['restaurantID']) ? (int)$_GET['restaurantID'] : null;
$filterDietID = isset($_GET['diet']) ? (int)$_GET['diet'] : null;
// If no restaurant, print error message and exits
if (!$restaurantID) {
    echo "No restaurant selected.";
    exit;
}

//-----CODE TO GET DIETARY PREFERENCES-----
$dietQuery = "SELECT dietID, name FROM DietaryPreference";
$dietResult = mysqli_query($conn, $dietQuery);

//-----CODE TO GET DISHES WITH FILTER-----
// SQL query to get dishes from specific restaurant with the diet filter
$dishQuery = "SELECT d.*, dp.name AS dietName 
              FROM Dish d 
              LEFT JOIN DietaryPreference dp ON d.DietID = dp.dietID 
              WHERE d.restaurantID = ?";

$params = [$restaurantID]; // Parameters for binding
$types = "i"; // Type string 

if ($filterDietID) {
    // Add dietary preference filter if selected
    $dishQuery .= " AND d.DietID = ?";
    $params[] = $filterDietID;
    $types .= "i";
}

// Prepare and execute the dish query
$stmt = mysqli_prepare($conn, $dishQuery);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$dishResult = mysqli_stmt_get_result($stmt);

// Get all the liked dishes by user
$liked = [];
$likeQuery = "SELECT dishID FROM FavouriteDish WHERE userID = ?";
$likeStmt = mysqli_prepare($conn, $likeQuery);
mysqli_stmt_bind_param($likeStmt, "i", $userID);
mysqli_stmt_execute($likeStmt);
$likeResult = mysqli_stmt_get_result($likeStmt);
// Stores all the liked dish IDs in an array for later use
while ($row = mysqli_fetch_assoc($likeResult)) {
    $liked[] = $row['dishID'];
}
?>

<!DOCTYPE html> <!--HTML-->
<html>
<head>
    <!--CSS STYLING-->
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

<!-- Filter Form for Dietary Preferences -->
<form method="GET">
    <!-- Keep the selected restaurant ID -->
    <input type="hidden" name="restaurantID" value="<?= htmlspecialchars($restaurantID) ?>">
    <!-- Dropdown filter for dietary preferences -->
    <label><strong>Filter by Dietary Preference:</strong></label>
    <select name="diet" onchange="this.form.submit()">
        <option value="">All</option>
        <!-- Goes through each diet from the database -->
        <?php while ($diet = mysqli_fetch_assoc($dietResult)) : ?>
            <!-- Shows diet name as an option -->
            <!-- If this diet is selected, keep it selected -->
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
            <!-- Heart icon for liking/unliking dishes -->
            <span class="heart <?= in_array($dish['dishID'], $liked) ? 'liked' : '' ?>"
                  title="Click to like/unlike"
                  onclick="toggleLike(<?= $dish['dishID'] ?>, this)">❤️</span>
        </div>
    <?php endwhile; ?>
</div>

<!-- Back to restaurant page button -->
<div style="margin-top: 30px; text-align: center;">
    <a href="restaurant.php?restaurantID=<?= $restaurantID ?>"
    style="padding: 10px 20px; background-color: rgb(76, 175, 80); color: white; text-decoration: none; border-radius: 6px;">
    ← Back to Restaurant Page
    </a>
</div>

<!-- JavaScript to handle heart icon toggle -->
<script>
function toggleLike(dishID, element) {
    element.style.pointerEvents = "none"; // prevents double clicks

    // Creates a new AJAX request
    const xhr = new XMLHttpRequest();
    // Sets up the request to send data to dishes.php using POST method
    xhr.open("POST", "dishes.php", true);
    // Tells the server we're sending form data
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    // When we get a response
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Toggles heart color (like/unlike)
            element.classList.toggle("liked");
        } else {
            // Error message
            alert("Something went wrong!");
        }
        // Allows clicking again
        element.style.pointerEvents = "auto";
    };
    // Sends dishID to PHP for toggling like
    xhr.send("toggleLike=1&dishID=" + dishID);
}
</script>

</body>
</html>
