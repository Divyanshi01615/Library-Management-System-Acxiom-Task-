<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Library Management System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
        }
        .container {
            width: 600px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #333;
        }
        .logout-btn {
            text-decoration: none;
            background: red;
            padding: 10px;
            color: white;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome, <?php echo $_SESSION['admin']; ?>!</h2>
    <p>Manage your library system below:</p>
    <button onclick="window.location.href='add_book.php'">Add Book</button>
    <button onclick="window.location.href='update_book.php'">Update Book</button>
    <br><br>
    <a class="logout-btn" href="admin_dashboard.php?logout=true">Logout</a>
</div>

</body>
</html>
