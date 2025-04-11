<?php 
// Connects php to the database
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$database = "FoodReview";

// Creates connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Checks connection, if none, alerts user through error message
if (!$conn) { 
    die("Connection failed: " . mysqli_connect_error());
} 
?>