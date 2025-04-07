<?php
// Database connection
include '../includes/dbConn.php';

$sql = "SELECT * FROM Users";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo "User: " . $row["username"] . " – Email: " . $row["email"] . "<br>";
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="publicReviewStyle1.css">
</head>
<body>
</body>
</html>