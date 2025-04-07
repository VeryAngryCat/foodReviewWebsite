<?php
// Database connection
include '../includes/dbConn.php';

// Initialises error and success messages
$error_message = '';
$success_message = '';

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
        $error_message = "All fields are required!";
    } else {
        // Validate if username or email already exists
        $sql = "SELECT COUNT(*) AS count FROM Users WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($count > 0) {
            $error_message = "Username or email already exists.";
        } else {
            // Validates email (must contain @)
            if (strpos($email, '@') === false) {
                $error_message = "Email must contain '@'.";
            } else {
                // Validates password (must contain at least one uppercase letter, one lowercase letter, and one number)
                if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
                    $error_message = "Password must contain at least one uppercase letter, one lowercase letter, and one number.";
                } else {
                    // Hashes the password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // SQL to insert user
                    $sql = "INSERT INTO Users (firstName, lastName, email, username, userPassword) 
                            VALUES (?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "sssss", $firstName, $lastName, $email, $username, $hashed_password);
                    $result = mysqli_stmt_execute($stmt);

                    if ($result) {
                        $success_message = "User registered successfully!";
                    } else {
                        $error_message = "Error: " . mysqli_error($conn);
                    }

                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
}  

// Closes the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" type="text/css" href="../assets/publicReviewStyle1.css">
</head>
<body>

    <div class="container">
        <h2>User Registration</h2>

        <!-- Form to capture user input -->
        <form action="register.php" method="POST">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" required>

            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Register">
        </form>

        <!-- Displays error or success messages -->
        <?php
            if ($error_message) {
                echo "<p class='error'>$error_message</p>";
            }
            if ($success_message) {
                echo "<p class='success'>$success_message</p>";
            }
        ?>
    </div>

</body>
</html>
