<?php
// Database connection
include '../includes/dbConn.php';
include '../includes/authAdmin.php';

// This is to add or remove other admin accounts

// Search box for admins
$searchTerm = $_POST['search'] ?? '';
$searchSql = $searchTerm ? "WHERE username LIKE '%$searchTerm%'" : '';

// Gets all admins
$allAdmins = $conn->query("SELECT adminID, username, adminPassword FROM Admins $searchSql");

// Handles admin account delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['deleteAdminID'])) {
        $deleteID = $_POST['deleteAdminID'];
        $deleteStmt = $conn->prepare("DELETE FROM Admins WHERE adminID=?");
        $deleteStmt->bind_param("i", $deleteID);
        $deleteStmt->execute();
        header("Location: ../admin/settings.php");
        exit;
    }

    // Handles adding admin account
    if (isset($_POST['add-button'])) {
        $username = $_POST['username'];
        $password = $_POST['adminPassword'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hashed password generated herre
        $insertStmt = $conn->prepare("INSERT INTO Admins (username, adminPassword) VALUES (?, ?)");
        $insertStmt->bind_param("ss", $username, $hashedPassword);
        $insertStmt->execute();
        header("Location: ../admin/settings.php");
        exit;
    }
}
?>

<!DOCTYPE  html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
    <link rel="stylesheet" type="text/css" href="../assets/foodRev3.css">
    <style>
        .admin-box {
            border: 1px solid black;
            padding: 20px;
            margin-bottom: 10px;
            position: relative;
        }
        .delete-button {
            display: none;
            position: absolute;
            top: 10px;
            right: 10px;
            background: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .admin-box:hover .delete-button {
            display: block;
        }

        .add-admin-btn {
            font-size: 40px;
            cursor: pointer;
            width: 50px;
            height: 50px;
            border: 1px solid black;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            margin: 20px auto;
            transition: background-color 0.3s;
        }
        .add-admin-btn:hover {
            background-color: lightgrey;
        }

        .add-admin-form {
            display: none;
            margin-top: 20px;
            padding: 10px;
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <h1>Set Admin Privileges</h1>
    <!-- search-bar listens for click of button -->
    <form method="post" class="search-bar">
        <input type="text" name="search" placeholder="Search admin" value="<?= htmlspecialchars($searchTerm) ?>">
        <button type="submit">Search</button>
    </form>

    <div class="container">
        <!-- A vertical list of all the users in the db -->
        <div class="admins-list">
            <!-- Loops through allAdmins (all the admins, obviously) with $user1 being a temporary variable pointing at each row -->
            <?php while ($admin = $allAdmins->fetch_assoc()): ?>
                <!-- All the info per admin is dumped in each own box -->
                <div class="admin-box">
                    <div class="admin-info">
                        <strong>Username: </strong><?= htmlspecialchars($admin['username']); ?><br>
                        <strong>Password: </strong><?= htmlspecialchars($admin['adminPassword']); ?>
                    </div>
                    <form method="post" class="delete-form">
                        <input type="hidden" name="deleteAdminID" value="<?= $admin['adminID'] ?>">
                        <button type="submit" class="delete-button">Delete</button>
                    </form>
                </div>
            <?php endwhile; ?>
            
            <!-- a '+' button which toggles an admin form -->
            <div class="add-admin-btn" onclick="toggleAddAdminForm()">+</div>
        </div>
        <!-- has an id so can be called in the js -->
        <div class="add-admin-form" id="admin-form-id">
            <form method="post">
                <!-- Form for details -->
                <label>Username:</label><br>
                <input type="text" name="username" required><br><br>
                <label>Password:</label><br>
                <input type="password" name="adminPassword" required><br><br>
                <button type="submit" name="add-button">Add Admin</button>
            </form>
        </div>

        <script>
            function toggleAddAdminForm() {
                // Gets details from the form with this id
                var form = document.getElementById('admin-form-id');
                form.style.display = form.style.display === 'none' ? 'block' : 'none'; // Toggles visibility of form
            }
        </script>
    </div>
    <div class="back-to-dashboard">
        <a href="../admin/dashboard.php">← Back to Dashboard</a> <!-- Return button -->
    </div>
    <?php
    // Closes the database connection
    mysqli_close($conn);
    ?>
</body>