<?php
include('db_connect.php');

$username = "admin";  // Change this if your username is different
$new_password = "admin123"; // Change this to a new password
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

// Update password in database
$update_query = "UPDATE admins SET password = ? WHERE username = ?";
$stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $username);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo "Password updated successfully!";
} else {
    echo "Error updating password!";
}

mysqli_close($conn);
?>
