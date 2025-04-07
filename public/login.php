<?php
session_start(); 

include '../includes/dbConn.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uname = $_POST['username'];
    $pword = $_POST['userPassword'];

    if (empty($uname) || empty($pword)) {
        $error_message = "All fields are required!";
    } else {
        
        $sql = "SELECT userID, username, userPassword FROM Users WHERE username = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $uname);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            if ($pword == $row['userPassword']) { 
                // ✅ Check if userID is actually retrieved
                if (!empty($row['userID'])) {
                    $_SESSION['userID'] = $row['userID']; // ✅ Save userID in session
                    header("Location: ../public/browse.php");
                    exit();
                } else {
                    $error_message = "Login worked but userID is missing from database.";
                }
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