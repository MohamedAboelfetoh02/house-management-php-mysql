<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: white;
            text-align: center;
            overflow-x: hidden;
        }
        .welcome {
            margin-bottom: 20px;
            font-size: 28px;
            color: #333333;
            background-color: #ffffff;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-transform: capitalize;
            max-width: 90%; /* Ensures it fits on small screens */
        }
        .box {
            background: #ffffff;
            color: #333;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 90%; /* Adjusts for smaller screens */
            max-width: 300px;
            text-align: center;
        }
        .box a {
            display: block;
            margin: 15px 0;
            padding: 10px;
            background-color: #6a11cb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .box a:hover {
            background-color: #2575fc;
        }
        .logout {
            margin-top: 20px;
            padding: 10px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .logout:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</div>
    <div class="box">
        <a href="owners.php">Manage Owners</a>
        <a href="houses.php">Manage Houses</a>
        <a href="change_password.php">Change Password</a> <!-- New option added -->
        <form method="POST" action="logout.php">
            <button class="logout" type="submit">Logout</button>
        </form>
    </div>
</body>
</html>
