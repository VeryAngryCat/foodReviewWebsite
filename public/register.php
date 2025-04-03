<?php

// Database connection (make sure it's included in your file)
$host = 'localhost'; 
$dbname = 'your_database_name';  
$username = 'your_username';  
$password = 'your_password';  

try {
    // Establish the PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get user input
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// SQL to insert user
$sql = "INSERT INTO Users (firstName, lastName, email, username, userPassword) 
        VALUES (:firstName, :lastName, :email, :username, :userPassword)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'firstName' => $firstName,
    'lastName' => $lastName,
    'email' => $email,
    'username' => $username,
    'userPassword' => $hashed_password
]);

echo "User registered successfully!";
?>

<?php
$sql = "SELECT COUNT(*) AS count 
        FROM Users 
        WHERE username = :username OR email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute(['username' => $username, 'email' => $email]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['count'] > 0) {
    echo "Username or email already exists.";
} else {
    echo "Username and email are available.";
}
?>


<?php
if (strpos($password, '@') !== false) {
    echo "Password contains '@'.";
} else {
    echo "Password must contain '@'.";
}
?>


<?php
if (preg_match('/[A-Z]/', $username) && preg_match('/[a-z]/', $username) && preg_match('/[0-9]/', $username)) {
    echo "Username is valid.";
} else {
    echo "Username must contain at least one uppercase letter, one lowercase letter, and one number.";
}
?>


