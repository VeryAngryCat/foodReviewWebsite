<?php
// Database connection
include '../includes/dbConn.php';
include '../includes/authAdmin.php';

$searchTerm = $_POST['search'] ?? ''; // Search box for users to enter part of username/name/email
$searchSql = $searchTerm ? "WHERE firstName LIKE '%$searchTerm%' OR lastName LIKE '%$searchTerm%' OR email LIKE '%$searchTerm%' OR username LIKE '%$searchTerm%'" : '';

// Gets all users
$allUsers = $conn->query("SELECT userID, firstName, lastName, email, username FROM Users $searchSql");

// Views selected user
$userID = $_GET['userID'] ?? null; //If is not selected for the time being is defined as null
$user = null; // Set as null for the time being

if ($userID) {

    // Gets user info
    $stmt = $conn->prepare("SELECT firstName, lastName, email, username FROM `Users` WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

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

    // Handles deletion of user
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['delete-user'])) {
            $deleteStmt = $conn->prepare("DELETE FROM Users WHERE userID=?");
            $deleteStmt->bind_param("i", $userID);
            $deleteStmt->execute();
            header("Location: ../admin/users.php"); // When finished refreshes
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
        .container {
            display: flex;
            gap: 40px;
            padding: 40px;
            height: 75vh
        }

        .users-list {
            flex: 1; /* List expands to fill extra space */
            overflow-y: auto;  /* User can scroll using scroll bar if theres alot of info */
            border: 1px solid black;
            padding: 5px;
        }

        .user-box {
            border: 1px solid black;
            padding: 20px;
            margin-bottom: 10px;
            cursor: pointer;
            word-wrap: break-word; /* So the words don't overflow over edge of box */
        }

        .edit-box {
            flex: 1;
            border: 1px solid black;
            padding: 20px; /* Alot of space so that user can edit and not feel overwhelmed */
            overflow-y: auto; 
        }

        .search-bar {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 4px;
            border-color: grey;
        }
    </style>
</head>
<body>
    <h1>Manage Users</h1> <!-- Heading -->
    <!-- Search bar so that admin can find users - links to the php code above ($searchTerm) -->
    <form method="post" class="search-bar">
        <input type="text" name="search" placeholder="Search user" value="<?= htmlspecialchars($searchTerm) ?>">
        <button type="submit">Search</button>
    </form>

    <!-- Container with list of users and an editing menu which pops up -->
    <div class="container">

        <!-- A vertical list of all the users in the db -->
        <div class="users-list">

            <!-- Loops through allUsers (all the users, obviously) with $user1 being a temporary variable pointing at each row -->
            <?php while ($user1 = mysqli_fetch_assoc($allUsers)): ?>

                <!-- All the info per user is dumped in each own box -->
                <div class="user-box">
                    <!-- Link to a particular userID so the side editing menu can be activated -->
                    <a href="?userID=<?= $user1['userID'] ?>" style="text-decoration:none; color:inherit;">
                        <strong>Full Name: </strong><?php echo htmlspecialchars($user1['firstName'] . " " . $user1['lastName']); ?><br>
                        <strong>Email: </strong><?php echo htmlspecialchars($user1['email']); ?><br>
                        <strong>Username: </strong><?php echo htmlspecialchars($user1['username']); ?>
                    </a>
                </div>
            <?php endwhile;?>
        </div>

        <!-- When a particular user is selected and user is not null, the code below activates the side  menu -->
        <?php if (isset($user) && $user): ?>

            <!-- edit-box aka the side menu (to see more info on a user and delete them if needed) -->
            <div class="edit-box">
        
                <!-- An 'action' that the user could take is deleting a user through clicking delete-button, which links to an if POST code in the php at the top -->
                <form method="post">
                    <button type="submit" class="delete-button" name="delete-user" onclick="return confirm('Delete this user?')">Delete User</button> <!-- Admin has to confirm deletion -->
                </form>

                <!-- This section shows a selected user's reviews -->
                <h2>Reviews by user <?=htmlspecialchars($user['firstName']);?> <?=htmlspecialchars($user['lastName']);?></h2>
                
                <!-- If there are reviews present for the user and they are not NULL, they are shown -->
                <?php if ($reviewResult && $reviewResult->num_rows > 0): ?>
                    <?php while ($rev = $reviewResult->fetch_assoc()): ?>
                        <br><br><strong>Restaurant:</strong> <?= htmlspecialchars($rev['restaurantName']) ?><br>
                        <strong>Rating:</strong> <?= htmlspecialchars($rev['rating']) ?>/5<br>
                        <strong>Date:</strong> <?= htmlspecialchars($rev['datePosted']) ?><br>
                        <strong>Comment:</strong><i> <?= nl2br(htmlspecialchars($rev['commentLeft'])) ?></i>
                    <?php endwhile; ?>

                <!-- If there are no reviews, default message is output -->
                <?php else: ?>
                    <p>This user hasn't written any reviews.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <!-- A return button -->
    <div class="back-to-dashboard">
        <a href="../admin/dashboard.php">← Back to Dashboard</a>
    </div>
    <?php
    // Closes the database connection
    mysqli_close($conn);
    ?>
</body>
</html>