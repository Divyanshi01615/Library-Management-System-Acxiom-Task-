<?php
include('db_connect.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $query = "UPDATE books SET title='$title', author='$author' WHERE id='$book_id'";
    
    if (mysqli_query($conn, $query)) {
        $message = "Book updated successfully.";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
        }
        input, button {
            margin-top: 10px;
            padding: 10px;
            width: 100%;
        }
        button {
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Book</h2>
        <?php if (isset($message)) echo "<p>$message</p>"; ?>
        <form method="POST">
            <input type="text" name="book_id" placeholder="Enter Book ID" required>
            <input type="text" name="title" placeholder="Enter Title" required>
            <input type="text" name="author" placeholder="Enter Author" required>
            <button type="submit">Update Book</button>
        </form>
        <a href="index.html">Back to Home</a>
    </div>
</body>
</html>