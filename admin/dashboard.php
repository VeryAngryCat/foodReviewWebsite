<?php
// Database connection
include '../includes/dbConn.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../assets/foodRev3.css">
</head>
<body>
    <div class="container">
        <h1>Welcome Administrator!</h1>
        <ul>
            <li><a href="restaurants.php">Manage Restaurants</a></li>
            <li><a href="reviews.php">Manage Reviews</a></li>
            <li><a href="users.php">Manage Users</a></li>
            <li><a href="settings.php">Settings</a></li>
        </ul>
    </div>
</body>

</html>
