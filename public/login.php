<?php
// Database connection
include '../includes/dbConn.php';

$error_message = '';
$success_message = '';

// Processes data from the user, linked to form in HTML
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uname = $_POST['username'];
    $pword = $_POST['userPassword'];

    if (empty($uname) || empty($pword)) {
        $error_message = "All fields are required!";
    }

    $sql = "SELECT username, userPassword FROM Users WHERE username = ?";

    // Prevents SQL inection
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $uname);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($pword, $row["userPassword"])) {
            $success_message = "Login successful!";
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "Username does not exist.";
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../assets/publicReviewStyle1.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <label for="userPassword">Password</label>
            <input type="text" id="userPassword" name="userPassword" required>

            <input type="submit" value="Log in">
        </form>
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