<?php session_start();
require_once '../../config.php';
// check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
// handle the post
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
$content = $_POST['content'];

// Validate that content is not empty
if (empty(trim($content))) {
    $error_message = "Post content cannot be empty";
    header("Location: ../profile.php");
    exit();
}

// insert the post into the database using prepared statement
$sql = "INSERT INTO posts (id, content, created_at) VALUES ('$user_id', '$content', NOW())";
$result = mysqli_query($conn, $sql);

if ($result) {
    $success_message = "Post added successfully";
    header("Location: ../profile.php");
} else {
    $error_message = "Post not added";
    header("Location: ../profile.php");
}
?>