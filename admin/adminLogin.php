<?php
// Database connection
include '../includes/dbConn.php';

$error_message = '';

// Processes data from the user, linked to form in HTML
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uname = $_POST['username'];
    $pword = $_POST['adminPassword'];

    if (empty($uname) || empty($pword)) {
        $error_message = "All fields are required!";
    } else {
        $sql = "SELECT username, adminPassword FROM Admins WHERE username = ?";

        // Prevents SQL inection
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $uname);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $storedPassword = $row["adminPassword"]; // This is solely for the prepopulated passwords, for demonstration (as they are not hashed). Otherwise, there would  be no need for this if else loop and it would  solely start from password_verify
            if (password_verify($pword, $storedPassword)) {
                header("Location: ../admin/dashboard.php");
                exit();
            } else if ($pword === $storedPassword) { // For legacy plain-text passwords
                header("Location: ../admin/dashboard.php");
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
    <title>Admin Login</title>
    <link rel="stylesheet" type="text/css" href="../assets/foodRev1.css">
    <style>
        input[type="submit"] { background-color: blue;}
        input[type="submit"]:hover {background-color: rgb(89, 89, 255);}
    </style>
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
        ?>
    </div>
</body>
</html>