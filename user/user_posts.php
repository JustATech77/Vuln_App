<?php
session_start();
require_once '../config.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
// get the user id
$user_id = $_GET['id'];
// get the posts from the database
$sql = "SELECT * FROM posts WHERE id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts</title>
    <link rel="stylesheet" href="css/posts.css">

</head>

<body class="user-posts-body">
    <div class="container">
        <?php if (isset($_SESSION['success_message'])) { ?>
            <div class="message success">
                <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php } ?>

        <?php if (isset($_SESSION['error_message'])) { ?>
            <div class="message error">
                <?php echo $_SESSION['error_message']; ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php } ?>

        <div class="posts">
            <?php if (empty($posts)) { ?>
                <div class="post">
                    <p>There is no posts for this user</p>
                </div>
            <?php } else { ?>
                <?php foreach ($posts as $post) { ?>
                    <div class="post">
                        <p><?php echo htmlspecialchars($post['content']); ?></p>

                        <p><?php echo $post['created_at']; ?></p>

                        <a href="POSTS/edit_post.php?id=<?php echo $post['post_id']; ?>">Edit</a>

                        <a href="POSTS/delete_post.php?id=<?php echo $post['post_id']; ?>">Delete</a>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <a href="profile.php" class="back-to-profile">Back to profile</a>
    </div>
</body>

</html>