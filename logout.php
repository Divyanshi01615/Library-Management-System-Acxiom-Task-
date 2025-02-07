<?php
session_start();
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session
header("Location: student_login.php"); // Redirect to login page
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out...</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 50%;
        }
        h2 {
            color: #333;
        }
        p {
            margin: 10px 0;
            color: #666;
        }
        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: white;
            background: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
        }
        a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>You have been logged out</h2>
        <p>Redirecting to login page...</p>
        <p>If you are not redirected automatically, <a href="student_login.php">click here</a>.</p>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = "student_login.php";
        }, 3000); // Redirect after 3 seconds
    </script>
</body>
</html>
