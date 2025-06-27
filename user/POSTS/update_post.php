<?php
session_start();
require_once '../../config.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../user_posts.php?id=" . $_SESSION['user_id']);
    exit();
}

$post_id = $_POST['post_id'];
$content = trim($_POST['content']);

// Validate input
if (empty($content)) {
    $_SESSION['error_message'] = "post content cannot be empty";
    header("Location: edit_post.php?id=" . $post_id);
    exit();
}

// Check if post exists and user owns it
$check_sql = "SELECT * FROM posts WHERE post_id = '$post_id' AND id = '" . $_SESSION['user_id'] . "'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) == 0) {
    $_SESSION['error_message'] = "you cannot edit this post";
    header("Location: ../user_posts.php?id=" . $_SESSION['user_id']);
    exit();
}

// Update the post
$update_sql = "UPDATE posts SET content = '$content' WHERE post_id = '$post_id' AND id = '" . $_SESSION['user_id'] . "'";
$update_result = mysqli_query($conn, $update_sql);

if ($update_result) {
    $_SESSION['success_message'] = "post updated successfully";
} else {
    $_SESSION['error_message'] = "error updating post";
}

header("Location: ../user_posts.php?id=" . $_SESSION['user_id']);
exit();
