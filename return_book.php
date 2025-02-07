<?php
session_start();
include('db_connect.php');

$message = "";

// Handle book return
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["return_book_id"])) {
    $return_book_id = $_POST["return_book_id"];

    // Get book details
    $book_query = "SELECT book_id, student_id, return_date FROM book_issues WHERE id = $return_book_id";
    $book_result = mysqli_query($conn, $book_query);
    $book_data = mysqli_fetch_assoc($book_result);

    if ($book_data) {
        $book_id = $book_data['book_id'];
        $student_id = $book_data['student_id'];
        $return_date = $book_data['return_date'];
        $today = date('Y-m-d');

        // Calculate fine if overdue
        $fine_per_day = 10; 
        $late_days = max(0, (strtotime($today) - strtotime($return_date)) / (60 * 60 * 24));
        $fine_amount = $late_days * $fine_per_day;

        if ($late_days > 0) {
            // Check if fine already exists
            $fine_query = "SELECT * FROM fines WHERE student_id = '$student_id' AND paid = 0";
            $fine_result = mysqli_query($conn, $fine_query);

            if (mysqli_num_rows($fine_result) > 0) {
                // Update fine amount
                $update_fine_query = "UPDATE fines SET amount = amount + '$fine_amount' WHERE student_id = '$student_id' AND paid = 0";
                mysqli_query($conn, $update_fine_query);
            } else {
                // Insert new fine record
                $insert_fine_query = "INSERT INTO fines (student_id, amount, paid) VALUES ('$student_id', '$fine_amount', 0)";
                mysqli_query($conn, $insert_fine_query);
            }
        }

        // Update book status as returned
        $update_query = "UPDATE book_issues SET remarks = 'Returned' WHERE id = $return_book_id";
        mysqli_query($conn, $update_query);

        // Update book availability
        $update_book_query = "UPDATE books SET availability = 'Available' WHERE id = '$book_id'";
        mysqli_query($conn, $update_book_query);

        $message = "Book returned successfully.";
    } else {
        $message = "Invalid book issue ID.";
    }
}

// Fetch all issued books (including pending and returned)
$query = "SELECT book_issues.id, students.name AS student_name, books.title AS book_title, 
                 book_issues.issue_date, book_issues.return_date, book_issues.remarks,
                 IF(CURDATE() > book_issues.return_date AND book_issues.remarks = 'Pending', DATEDIFF(CURDATE(), book_issues.return_date) * 10, 0) AS fine
          FROM book_issues
          JOIN students ON book_issues.student_id = students.id
          JOIN books ON book_issues.book_id = books.id";
$result = mysqli_query($conn, $query);
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
    <title>Return Book</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Return Book</h2>
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
        
        <form method="POST">
            <table>
                <tr>
                    <th>Issue ID</th>
                    <th>Student Name</th>
                    <th>Book Title</th>
                    <th>Issue Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Fine (₹)</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['student_name']; ?></td>
                    <td><?php echo $row['book_title']; ?></td>
                    <td><?php echo $row['issue_date']; ?></td>
                    <td><?php echo $row['return_date']; ?></td>
                    <td><?php echo $row['remarks']; ?></td>
                    <td><?php echo $row['fine']; ?></td>
                    <td>
                        <?php if ($row['remarks'] == 'Pending') { ?>
                            <button type="submit" name="return_book_id" value="<?php echo $row['id']; ?>" class="submit-btn">
                                Return
                            </button>
                        <?php } else { ?>
                            ✅ Returned
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </form>
        <a class="home-link" href="index.html">Back to Home</a>
    </div>
</body>
</html>
