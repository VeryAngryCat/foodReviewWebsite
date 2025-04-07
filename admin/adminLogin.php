<?php
// Database connection
include '../includes/dbConn.php';

$error_message = '';
$success_message = '';

// Processes data from the user, linked to form in HTML
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uname = $_POST['username'];
    $pword = $_POST['adminPassword'];

    if (empty($uname) || empty($pword)) {
        $error_message = "All fields are required!";
    }

    $sql = "SELECT username, adminPassword FROM Admins WHERE username = ?";

    // Prevents SQL inection
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $uname);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $storedPassword = $row["adminPassword"]; // This is solely for the prepopulated passwords, for demonstration (as they are not hashed). Otherwise, there would  be no need for this if else loop and it would  solely start from password_verify
        if (strlen($storedPassword) == 60 && (substr($storedPassword, 0, 3) == '$2y$' || substr($storedPassword, 0, 3) == '$2a$' || substr($storedPassword, 0, 3) == '$2b$')) {
            if (password_verify($pword, $row["adminPassword"])) {
                $success_message = "Login successful!";
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            if ($pword == $storedPassword) {
                $success_message = "Login successful!";
            } else {
                $error_message = "Incorrect password.";
            }
        }
    } else {
        $error_message = "Username does not exist.";
    }
    echo "Database hashed password: " . $row['adminPassword']; // Check the password hash stored in the database
    echo "Password entered: " . $pword; // Check the entered password
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../assets/foodRev3.css">
</head>
<body>
    <div class="container">
        <h2>Administrator Login</h2>
        <form method="POST" action="adminLogin.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <label for="adminPassword">Password</label>
            <input type="password" id="adminPassword" name="adminPassword" required>

            <input type="submit" value="Log in">
        </form>
        <?php
            if ($error_message) {
                echo "<p class='error'>$error_message</p>";
            }
            if ($success_message) {
                echo "<p class='success'>$success_message</p>";
                header("Location: ../admin/dashboard.php");
                exit();
            }
        ?>
    </div>
</body>
</html>