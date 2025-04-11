<?php
// Authenticates admin by their id and if isn't set then redirects them to login
session_start();

if (!isset($_SESSION['adminID'])) {
    header("Location: ../admin/adminLogin.php");
    exit();
}
?>