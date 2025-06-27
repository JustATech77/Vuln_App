<?php
session_start();
require_once '../../config.php';
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

// get the post id
$post_id = $_GET['id'];

// get the post from the database
$sql = "SELECT * FROM posts WHERE post_id = '$post_id'";
$result = mysqli_query($conn, $sql);
$post = mysqli_fetch_assoc($result);

// Check if post exists
if (!$post) {
    header("Location: ../user_posts.php?id=" . $_SESSION['user_id']);
    exit();
}

// Check if user owns this post
if ($post['id'] != $_SESSION['user_id']) {
    header("Location: ../user_posts.php?id=" . $_SESSION['user_id']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="../css/posts.css">
</head>

<body class="edit-post-body">
    <div class="edit-post-container">
        <h1>Edit Post</h1>
        <form action="update_post.php" method="POST">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <textarea name="content" placeholder="write your post here..." required><?php echo htmlspecialchars($post['content']); ?></textarea>
            <div class="buttons">
                <button type="submit" class="btn btn-primary">save changes</button>
                <a href="../user_posts.php?id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-secondary">cancel</a>
            </div>
        </form>
                
    </div>
</body>

</html>