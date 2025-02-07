<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('db_connect.php');

$message = "";

// Ensure the user is logged in
if (!isset($_SESSION['student'])) {
    header('Location: student_login.php');
    exit();
}

$student_email = $_SESSION['student'];

// Fetch student ID from the database
$student_query = "SELECT id FROM students WHERE email = ?";
$stmt = mysqli_prepare($conn, $student_query);
mysqli_stmt_bind_param($stmt, "s", $student_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
$student_id = $student['id'];

// Fetch available books
$book_query = "SELECT * FROM books WHERE availability = 'Available'";
$book_result = mysqli_query($conn, $book_query);

// Handle book issue
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['book_id'])) {
        $book_id = mysqli_real_escape_string($conn, $_POST['book_id']);

        // Check if the student has already issued a book
        $check_query = "SELECT * FROM book_issues WHERE student_id = ? AND return_date IS NULL";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "i", $student_id);
        mysqli_stmt_execute($stmt);
        $check_result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "You have already issued a book. Please return it before issuing a new one.";
        } else {
            // Issue book
            $issue_date = date("Y-m-d");
            $return_date = date("Y-m-d", strtotime("+14 days")); // 2-week return policy

            $issue_query = "INSERT INTO book_issues (student_id, book_id, issue_date, return_date) 
                            VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $issue_query);
            mysqli_stmt_bind_param($stmt, "iiss", $student_id, $book_id, $issue_date, $return_date);

            if (mysqli_stmt_execute($stmt)) {
                // Update book availability
                $update_book_query = "UPDATE books SET availability = 'Issued' WHERE id = ?";
                $stmt = mysqli_prepare($conn, $update_book_query);
                mysqli_stmt_bind_param($stmt, "i", $book_id);
                mysqli_stmt_execute($stmt);

                $message = "Book issued successfully! Return it by $return_date.";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    } else {
        $message = "Please select a book to issue.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Book - Library Management</title>
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
            width: 60%;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background: #333;
            color: #fff;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .submit-btn {
            background: #333;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }
        .submit-btn:hover {
            background: #555;
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
        <h2>Issue a Book</h2>
        <?php if ($message) echo "<p class='error'>$message</p>"; ?>
        
        <form method="POST">
            <table>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Select</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($book_result)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['author']; ?></td>
                    <td><input type="radio" name="book_id" value="<?php echo $row['id']; ?>"></td>
                </tr>
                <?php } ?>
            </table>
            <button type="submit" class="submit-btn">Issue Book</button>
        </form>
        <a class="home-link" href="student_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
