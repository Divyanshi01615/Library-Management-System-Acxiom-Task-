<?php
include('db_connect.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['student'])) {
    header('Location: student_login.php');
    exit();
}

$student_id = $_SESSION['student'];

// Fetch the outstanding fine amount
$query = "SELECT id, amount FROM fines WHERE student_id='$student_id' AND paid=0";
$result = mysqli_query($conn, $query);
$fine = mysqli_fetch_assoc($result);
$fine_id = $fine['id'] ?? null;
$fine_amount = $fine['amount'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $fine_id) {
    // Mark the fine as paid
    $update_query = "UPDATE fines SET paid=1 WHERE id='$fine_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $message = "Fine paid successfully.";
        $fine_amount = 0; // Reset displayed fine amount
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>
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

/* Container */
.container {
    width: 60%;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

/* Heading */
h2 {
    color: #333;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
}

th {
    background: #333;
    color: #fff;
}

td {
    background: #f9f9f9;
}

/* Messages */
.message {
    color: green;
    font-weight: bold;
    margin: 10px 0;
}

.error {
    color: red;
    font-weight: bold;
    margin: 10px 0;
}

/* Buttons */
.submit-btn, .btn {
    background: #333;
    color: #fff;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}

.submit-btn:hover, .btn:hover {
    background: #555;
}

/* Disabled Button */
.disabled-btn {
    background: #ccc;
    color: #666;
    cursor: not-allowed;
}

/* Home Link */
.home-link {
    display: block;
    margin-top: 15px;
    text-decoration: none;
    color: #333;
    font-weight: bold;
}

.home-link:hover {
    text-decoration: underline;
}
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Fine</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Pay Fine</h2>
        <p>Fine Amount: ₹<?php echo $fine_amount; ?></p>
        <?php if (isset($message)) { ?>
            <p class="<?php echo ($fine_amount > 0) ? 'message' : 'error'; ?>"><?php echo $message; ?></p>
        <?php } ?>
        
        <?php if ($fine_amount > 0) { ?>
            <form method="POST">
                <button type="submit" class="btn">Pay Fine</button>
            </form>
        <?php } else { ?>
            <p>No fine due.</p>
        <?php } ?>

        <a href="student_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
