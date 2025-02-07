<?php
session_start();
include('db_connect.php');

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['selected_book'])) {
        $_SESSION['selected_book'] = $_POST['selected_book'];
        header("Location: issue_book.php"); // Ensure the correct file name
        exit();
    } else {
        $error = "Please select a book before proceeding.";
    }
}

// Fetch available books
$query = "SELECT * FROM books WHERE availability = 'Available'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Availability - Library Management</title>
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
            text-align: left;
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
        <h2>Book Availability</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <?php if (mysqli_num_rows($result) > 0) { ?>
            <form method="POST">
                <table>
                    <tr>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Select</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                        <td><input type="radio" name="selected_book" value="<?php echo htmlspecialchars($row['id']); ?>"></td>
                    </tr>
                    <?php } ?>
                </table>
                <button type="submit" class="submit-btn">Proceed</button>
            </form>
        <?php } else { ?>
            <p>No books available at the moment.</p>
        <?php } ?>
        
        <a class="home-link" href="index.php">Back to Home</a>
    </div>
</body>
</html>
