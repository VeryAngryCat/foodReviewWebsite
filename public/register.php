<?php
// Database connection
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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if form inputs are set (avoiding null issues)
    if (empty($firstName) || empty($lastName) || empty($username) || empty($email) || empty($password)) {
        echo "All fields are required!";
        exit;
    }

    // Validate if username or email already exists
    $sql = "SELECT COUNT(*) AS count FROM Users WHERE username = ? OR email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($count > 0) {
        echo "Username or email already exists.";
    } else {
        // Validate password (must contain @)
        if (strpos($password, '@') === false) {
            echo "Password must contain '@'.";
            exit;
        }

        // Validate username (must contain at least one uppercase letter, one lowercase letter, and one number)
        if (!preg_match('/[A-Z]/', $username) || !preg_match('/[a-z]/', $username) || !preg_match('/[0-9]/', $username)) {
            echo "Username must contain at least one uppercase letter, one lowercase letter, and one number.";
            exit;
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // SQL to insert user
        $sql = "INSERT INTO Users (firstName, lastName, email, username, userPassword) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $firstName, $lastName, $email, $username, $hashed_password);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            echo "User registered successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
}

// Close the database connection
mysqli_close($conn);
?>
