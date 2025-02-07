<?php
session_start();
include('db_connect.php');

$message = "";

if (isset($_SESSION['student'])) {
    header('Location: student_dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $query = "SELECT * FROM students WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['password'] === $password) {
            $_SESSION['student'] = $email;
            header('Location: student_dashboard.php');
            exit();
        } else {
            $message = "Invalid email or password.";
        }
    } else {
        if (isset($_POST['name']) && isset($_POST['membership_type'])) {
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $membership_type = mysqli_real_escape_string($conn, $_POST['membership_type']);
            
            $query = "INSERT INTO students (name, email, password, membership_type) VALUES ('$name', '$email', '$password', '$membership_type')";
            if (mysqli_query($conn, $query)) {
                $message = "Student registered successfully. Please login.";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        } else {
            $message = "Please provide complete registration details.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Library Management</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 350px;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .container h2 {
            margin-bottom: 20px;
        }
        .container input, .container select, .container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .container button {
            background: #333;
            color: #fff;
            cursor: pointer;
        }
        .container button:hover {
            background: #555;
        }
        .message {
            color: green;
            margin-top: 10px;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .home-link {
            display: block;
            margin-top: 15px;
            text-decoration: none;
            color: #333;
        }
        .home-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Login</h2>
        <?php if ($message) echo "<p class='message'>$message</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>New User? <a href="student_register.php">Register Here</a></p>
        <a class="home-link" href="index.php">Back to Home</a>
    </div>
</body>
</html>
