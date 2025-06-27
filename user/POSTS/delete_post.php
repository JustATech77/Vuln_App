<?php 
session_start();
require_once '../../config.php';
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
// get the post id
$post_id = $_GET['id'];
// delete the post from the database
$sql = "DELETE FROM posts WHERE post_id = '$post_id'";
$result = mysqli_query($conn, $sql);
if ($result) {
    $success_message = "Post deleted successfully";
    header("Location: ../user_posts.php?id=" . $_SESSION['user_id']);
} else {
    $error_message = "Post not deleted";
    header("Location: ../user_posts.php?id=" . $_SESSION['user_id']);
}
?>