<?php
include('db_connect.php');

// Check if a session is already active before starting one
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = "";

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure Password Hashing
    $membership_type = mysqli_real_escape_string($conn, $_POST['membership_type']);

    // Handle Profile Picture Upload
    $profile_pic = "default_profile.jpg"; // Default profile picture
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/";
        $profile_pic = $target_dir . basename($_FILES["profile_pic"]["name"]);
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $profile_pic);
    }

    // Check if student already exists
    $check_query = "SELECT * FROM students WHERE email=?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $message = "Student already exists. Please <a href='student_login.php'>Login</a>.";
    } else {
        // Insert new student
        $query = "INSERT INTO students (name, email, password, membership_type, profile_pic) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $password, $membership_type, $profile_pic);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Student registered successfully! <a href='student_login.php'>Login here</a>.";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 40%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        input, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }
        .submit-btn {
            background: #333;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        .submit-btn:hover {
            background: #333;
        }
        .message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Student Registration</h2>
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="membership_type" required>
                <option value="">Select Membership Type</option>
                <option value="Regular">Regular</option>
                <option value="Premium">Premium</option>
            </select>
            <button type="submit" class="submit-btn">Register</button>
        </form>

        <p>Already registered? <a href="student_login.php">Login here</a></p>
    </div>

</body>
</html>
