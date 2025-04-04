<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Review - Home</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e8f5e9;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #2e7d32;
        }
        .container {
            text-align: center;
            padding: 40px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-size: 3em;
            margin-bottom: 30px;
            color: #388e3c;
        }
        .btn {
            padding: 15px 30px;
            font-size: 18px;
            margin: 15px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #388e3c;
            color: white;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #2c6e2f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome to Food Review</h2>
        <button class="btn" onclick="window.location.href='login.php'">Login</button>
        <button class="btn" onclick="window.location.href='register.php'">Signup</button>
    </div>
</body>
</html>
