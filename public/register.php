<?php
// Database connection
include '../includes/dbConn.php';

// Sets up error and success messages
$error_message = '';
$success_message = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if any field is empty
    if (empty($firstName) || empty($lastName) || empty($username) || empty($email) || empty($password)) {
        $error_message = "All fields are required!";
    } else {
        // Check if username/email already exists in the database
        $sql = "SELECT COUNT(*) AS count FROM Users WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt); // Runs the query
        mysqli_stmt_bind_result($stmt, $count); // Stores the result (0 or 1+)
        mysqli_stmt_fetch($stmt); // Fetches the result
        mysqli_stmt_close($stmt); // Closes the query

        if ($count > 0) {
            $error_message = "Username or email already exists.";
        } else {
            // // Check if email has '@' (basic validation)
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
                    $result = mysqli_stmt_execute($stmt); // Runs the INSERT query

                    if ($result) {
                        $success_message = "User registered successfully!";
                    } else {
                        $error_message = "Error: " . mysqli_error($conn);
                    }

                    mysqli_stmt_close($stmt);  // Closes the query
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
    <link rel="stylesheet" type="text/css" href="../assets/foodRev1.css"> <!-- Links CSS file for styling -->
</head>
<body>

    <div class="container">
        <h2>User Registration</h2>

        <!-- Form to capture user input -->
        <form action="register.php" method="POST">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" required> <!-- 'required' means field can't be empty -->

            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required> <!-- 'type=email' checks for basic email format -->

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required> <!-- 'type=password' hides typed characters -->
            <input type="submit" value="Register"> <!-- Submit button -->
        </form>

        <!-- Displays error or success messages -->
        <?php
            if ($error_message) {
                echo "<p class='error'>$error_message</p>";  // Shows error in red
            }
            if ($success_message) {
                echo "<p class='success'>$success_message</p>";   // Shows success in green
                echo "<a href='login.php' class='login-button'>Continue to Login</a>";  // Link to login page
            }

            
        ?>
    </div>

</body>
</html>
