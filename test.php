<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = ""; // Leave blank if no password is set
$database = "FoodReview"; // Replace with your actual database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) { 
    die("Connection failed: " . mysqli_connect_error());
} 

echo "Connected successfully"; 

?>