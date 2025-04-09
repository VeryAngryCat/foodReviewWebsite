<?php
// Database connection
include '../includes/dbConn.php';
include '../includes/authAdmin.php';

// Views info on a user
// Deletes user
// User info

// Search box for users
$searchTerm = $_POST['search'] ?? '';
$searchSql = $searchTerm ? "WHERE firstName LIKE '%$searchTerm%' OR lastName LIKE '%$searchTerm%' OR email LIKE '%$searchTerm%' OR username LIKE '%$searchTerm%'" : '';

// Gets all users
$allUsers = $conn->query("SELECT userID, firstName, lastName, email, username FROM Users $searchSql");

// Views selected user
$userID = $_GET['userID'] ?? null;
$user = null;

if ($userID) {
    // Gets user info
    $stmt = $conn->prepare("SELECT firstName, lastName, email, username FROM `Users` WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $user1 = $result->fetch_assoc(); // Temporaryy variable to use in the while loop

    // Gets user reviews
    $reviewSql = "
        SELECT r.commentLeft, r.rating, r.datePosted, res.name AS restaurantName
        FROM Reviews r 
        JOIN Restaurant res ON r.restaurantID = res.restaurantID 
        WHERE r.userID = ?";
    $reviewStmt = $conn->prepare($reviewSql);
    $reviewStmt->bind_param("i", $userID);
    $reviewStmt->execute();
    $reviewResult = $reviewStmt->get_result();

    // Handles deletion of
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['deleteUser'])) {
            $deleteStmt = $conn->prepare("DELETE FROM Users WHERE userID=?");
            $deleteStmt->bind_param("i", $userID);
            $deleteStmt->execute();
            header("Location: ../admin/users.php");
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
    <title>Manage Users</title>
    <link rel="stylesheet" type="text/css" href="../assets/foodRev3.css">
    <style>
        .container { display: flex; gap: 40px; padding: 40px; height: 75vh}
        .users-list {flex:1; overflow-y: auto; border: 1px solid black; padding: 5px;}
        .user-box { border: 1px solid black; padding: 20px; margin-bottom: 10px; cursor: pointer; word-wrap: break-word;}
        .edit-box { flex: 1; border: 1px solid black; padding: 20px; overflow-y: auto;}
        .search-bar { margin-bottom: 20px; padding: 10px; border-radius: 4px; border-color: grey; }
    </style>
</head>
<body>
    <h1>Manage Users</h1>
    <form method="post" class="search-bar">
        <input type="text" name="search" placeholder="Search user" value="<?= htmlspecialchars($searchTerm) ?>">
        <button type="submit">Search</button>
    </form>

    <div class="container">
        <div class="users-list">
            <?php while ($user1 = mysqli_fetch_assoc($allUsers)): ?>
                <div class="user-box">
                    <a href="?userID=<?= $user1['userID'] ?>" style="text-decoration:none; color:inherit;">
                        <strong>Full Name: </strong><?php echo htmlspecialchars($user1['firstName'] . " " . $user1['lastName']); ?><br>
                        <strong>Email: </strong><?php echo htmlspecialchars($user1['email']); ?><br>
                        <strong>Username: </strong><?php echo htmlspecialchars($user1['username']); ?>
                    </a>
                </div>
            <?php endwhile;?>
        </div>

        <?php if (isset($user) && $user): ?>
            <div class="edit-box">
                <form method="post">
                    <button type="submit" class="delete-button" name="deleteUser" onclick="return confirm('Delete this user?')">Delete User</button>
                </form>
                <h2>Reviews by user <?=htmlspecialchars($user['firstName']);?> <?=htmlspecialchars($user['lastName']);?></h2>
                <?php if ($reviewResult && $reviewResult->num_rows > 0): ?>
                    <?php while ($rev = $reviewResult->fetch_assoc()): ?>
                        <br><br><strong>Restaurant:</strong> <?= htmlspecialchars($rev['restaurantName']) ?><br>
                        <strong>Rating:</strong> <?= htmlspecialchars($rev['rating']) ?>/5<br>
                        <strong>Date:</strong> <?= htmlspecialchars($rev['datePosted']) ?><br>
                        <strong>Comment:</strong><i> <?= nl2br(htmlspecialchars($rev['commentLeft'])) ?></i>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>This user hasn't written any reviews.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    // Closes the database connection
    mysqli_close($conn);
    ?>
</body>
</html>