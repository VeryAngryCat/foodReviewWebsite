<?php
// Database connection
include '../includes/dbConn.php';
include '../includes/authAdmin.php';

// Counts total users
$totalUsers = "SELECT COUNT(*) AS usTotal FROM users";
$resultUsers = mysqli_query($conn, $totalUsers);
$dataUsers = mysqli_fetch_assoc($resultUsers);

// Counts total restaurants
$totalRest = "SELECT COUNT(*) AS restTotal FROM restaurant";
$resultRest = mysqli_query($conn, $totalRest);
$dataRest = mysqli_fetch_assoc($resultRest);

//Counts total reviews
$totalRev = "SELECT COUNT(*) AS revTotal FROM Reviews";
$resultRev = mysqli_query($conn, $totalRev);
$dataRev = mysqli_fetch_assoc($resultRev);

//Counts total admins
$totalAdm = "SELECT COUNT(*) AS admTotal FROM Admins";
$resultAdm = mysqli_query($conn, $totalAdm);
$dataAdm = mysqli_fetch_assoc($resultAdm);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../assets/foodRev3.css">
</head>
<body>
    <div class="container" style="margin-top: 100px;">
        <h1>Welcome Administrator!</h1>
        <!-- An option menu for the admin -->
        <div class="class-menu">
            <a href="restaurants.php">Manage Restaurants</a>
            <a href="reviews.php">Manage Reviews</a>
            <a href="users.php">Manage Users</a>
            <a href="settings.php">Settings</a>
        </div>
    </div>
    <div class="stats" style="margin-top: 10vh;">
        <!-- Displays total users, restaurants, and reviews (kind of like an infograph)-->
        <div class="stat-card">
            <h2>
                <?php echo $dataUsers['usTotal'];?>
                <p>Total Users</p>
            </h2>
        </div>
        <div class="stat-card">
            <h2>
                <?php echo $dataRest['restTotal'];?>
                <p>Total Restaurants</p>
            </h2>
        </div>
        <div class="stat-card">
            <h2>
                <?php echo $dataRev['revTotal'];?>
                <p>Total Reviews</p>
            </h2>
        </div>
        <div class="stat-card">
            <h2>
                <?php echo $dataAdm['admTotal'];?>
                <p>Total Admins</p>
            </h2>
        </div>
    </div>
    <!-- Log out button which takes admin back to login page -->
    <a href="../admin/adminLogin.php" class="logout-button">Logout</a>
</body>
</html>
