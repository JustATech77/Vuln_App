<?php
require_once '../config.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_user.css">
    <title>profile</title>
</head>
<body>
    <div class="container">
        <h1>Welcome <?php echo $_SESSION['username']; ?></h1>
        <div class="profile-container">
            <div class="profile-info">
                <h2>Profile Information</h2>
                <p>Username: <?php echo $_SESSION['username']; ?></p>
                <p>Email: <?php echo $_SESSION['email']; ?></p>
            </div>
            <div class="profile-actions">
                <a class="logout-btn" href="../logout.php">Logout</a>
            </div>
            <?php
            if ($_SESSION['is_admin'] == 1) {
                echo "<div style='text-align: center; margin-top: 20px; '>";
                echo "<a style='text-decoration: none; color: white; background-color: #007bff; padding: 10px 20px; border-radius: 5px; ' href='../admin/admin_panal.php'>Admin Panel</a>";
                echo "</div>";
            }
            ?>
        </div>

    </div>
</body>

</html>