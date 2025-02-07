<?php
include('db_connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['student'])) {
    header('Location: student_login.php');
    exit();
}

$email = $_SESSION['student'];
$query = "SELECT * FROM students WHERE email='$email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $membership_type = mysqli_real_escape_string($conn, $_POST['membership_type']);

    // Profile picture upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file);
        $profile_pic = $target_file;

        $update_query = "UPDATE students SET name='$name', membership_type='$membership_type', profile_pic='$profile_pic' WHERE email='$email'";
    } else {
        $update_query = "UPDATE students SET name='$name', membership_type='$membership_type' WHERE email='$email'";
    }

    if (mysqli_query($conn, $update_query)) {
        $message = "Profile updated successfully!";
        header("Refresh:0"); // Refresh page to show updated info
    } else {
        $message = "Error updating profile: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 50%;
        }
        img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .form-group {
            margin: 10px 0;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .submit-btn {
            background: #333;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .submit-btn:hover {
            background: #555;
        }
        .message {
            color: green;
            margin-top: 10px;
        }
        .logout-link {
            display: block;
            margin-top: 15px;
            text-decoration: none;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Profile</h2>

        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>

        <img src="<?php echo !empty($user['profile_pic']) ? $user['profile_pic'] : 'uploads/default.png'; ?>" alt="Profile Picture">

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>

            <div class="form-group">
                <label>Membership Type:</label>
                <select name="membership_type">
                    <option value="Basic" <?php if ($user['membership_type'] == 'Basic') echo 'selected'; ?>>Basic</option>
                    <option value="Premium" <?php if ($user['membership_type'] == 'Premium') echo 'selected'; ?>>Premium</option>
                </select>
            </div>

            <div class="form-group">
                <label>Profile Picture:</label>
                <input type="file" name="profile_pic">
            </div>

            <button type="submit" class="submit-btn">Update Profile</button>
        </form>

        <a href="logout.php" class="logout-link">Logout</a>
    </div>
</body>
</html>
