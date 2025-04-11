<?php
// Authenticates user by their id and if isn't set then redirects them to login
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: ../public/login.php");
    exit();
}
?>