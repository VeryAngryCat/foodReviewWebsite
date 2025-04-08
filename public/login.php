<?php
session_start();
// Database connection
include '../includes/dbConn.php';

$error_message = '';

// Processes data from the user, linked to form in HTML
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uname = $_POST['username'];
    $pword = $_POST['userPassword'];

    if (empty($uname) || empty($pword)) {
        $error_message = "All fields are required!";
    } else {
        $sql = "SELECT username, userPassword FROM Users WHERE username = ?";

        // Prevents SQL inection
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $uname);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $storedPassword = $row["userPassword"]; // This is solely for the prepopulated passwords, for demonstration (as they are not hashed). Otherwise, there would  be no need for this if else loop and it would  solely start from password_verify
            if (password_verify($pword, $storedPassword)) {
                header("Location: ../public/browse.php");
                exit();
            } else if ($pword === $storedPassword) { // For legacy plain-text passwords
                header("Location: ../public/browse.php");
                exit();
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "Username does not exist.";
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" type="text/css" href="../assets/foodRev1.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <label for="userPassword">Password</label>
            <input type="password" id="userPassword" name="userPassword" required>

            <input type="submit" value="Log in">
        </form>
        <?php
            if ($error_message) {
                echo "<p class='error'>$error_message</p>";
            }
        ?>
    </div>
</body>
</html>