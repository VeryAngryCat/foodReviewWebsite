<?php
session_start();

if (!isset($_SESSION['adminID'])) {
    header("Location: ../admin/adminLogin.php");
    exit();
}
?>