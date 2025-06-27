<?php
session_start();
require_once '../../config.php';
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Function to get profile image path
function getProfileImagePath($profile_image)
{
    if (empty($profile_image)) {
        return false;
    }

    // Check admin profile images first
    $adminPath = '../../admin/Profile_image/' . $profile_image;
    if (file_exists($adminPath)) {
        return $adminPath;
    }

    // Check user profile images
    $userPath = '../Profile_image/' . $profile_image;
    if (file_exists($userPath)) {
        return $userPath;
    }

    return false;
}

// Get all posts with user information
$sql = "SELECT posts.*, users.username, users.profile_image 
        FROM posts 
        JOIN users ON posts.id = users.id 
        ORDER BY posts.created_at DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error in query: " . mysqli_error($conn));
}

$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - All Posts</title>
    <link rel="stylesheet" href="Home.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🏠 Home</h1>
            <p>View all posts from all users</p>
            <div class="nav-links">
                <a href="../profile.php">Profile</a>
                <a href="../user_posts.php?id=<?php echo $_SESSION['user_id']; ?>">My Posts</a>
                <a href="../users_list.php">Users List</a>
                <a href="../../logout.php">Logout</a>
            </div>
        </div>

        <div class="posts-container">
            <?php
            // Debug information
            echo "<!-- Number of posts: " . count($posts) . " -->";
            ?>
            <?php if (empty($posts)): ?>
                <div class="no-posts">
                    <h3>📝 No posts yet</h3>
                    <p>Be the first to post! 🚀</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <?php
                            $profileImagePath = getProfileImagePath($post['profile_image']);
                            if ($profileImagePath): ?>
                                <img src="<?php echo $profileImagePath; ?>"
                                    alt="Profile Image" class="user-avatar">
                            <?php else: ?>
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($post['username']); ?>&background=007bff&color=fff&size=100"
                                    alt="Default Avatar" class="user-avatar">
                            <?php endif; ?>

                            <div class="user-info">
                                <div class="username"><?php echo htmlspecialchars($post['username']); ?></div>
                                <div class="post-date">
                                    📅 <?php echo date('Y/m/d H:i', strtotime($post['created_at'])); ?>
                                </div>
                            </div>
                        </div>

                        <div class="post-content">
                            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>