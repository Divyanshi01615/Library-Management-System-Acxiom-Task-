<?php
include('db_connect.php');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if the student is not logged in
if (!isset($_SESSION['student'])) {
    header("Location: student_login.php");
    exit();
}

$student_email = $_SESSION['student']; // Get logged-in student's email

// Fetch student details
$query = "SELECT name, profile_pic FROM students WHERE email=?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $student_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

// Default profile picture if none is set
$profile_pic = !empty($student['profile_pic']) ? htmlspecialchars($student['profile_pic']) : 'uploads/default_profile.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: black; /* Navbar color */
            padding: 15px;
            text-align: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: inline-block;
        }
        .navbar a:hover {
            background-color: black;
            border-radius: 5px;
        }
        .container {
            width: 60%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            text-align: center;
        }
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #fff;
        }
        .btn {
            background-color: black; /* Button color same as navbar */
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 10px;
        }
        .btn:hover {
            background-color: black;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="student_dashboard.php">Home</a>
        <a href="issue_book.php">Issue Book</a>
        <a href="return_book.php">Return Book</a>
        <a href="pay_fine.php">Pay Fine</a>
        <a href="user_profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($student['name']); ?></h2>
        <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="profile-pic">
        
        <h3>Library Actions</h3>
        <a href="issue_book.php" class="btn">Issue a Book</a>
        <a href="return_book.php" class="btn">Return a Book</a>
        <a href="pay_fine.php" class="btn">Pay Fine</a>
        <a href="user_profile.php" class="btn">Edit Profile</a>
    </div>

</body>
</html>
