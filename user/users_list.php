<?php
require_once '../config.php';
session_start();
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
// Count users and roles
$total_users = count($users);
$admin_count = 0;
$user_count = 0;
foreach ($users as $user) {
    if ($user['is_admin'] == 1) {
        $admin_count++;
    } else {
        $user_count++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <link rel="stylesheet" href="../css/user_list.css">

</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Users List</h1>
            <?php if (isset($_SESSION['username']) && $_SESSION['is_admin'] == 1): ?>
                <div class="stats-container" style="margin-bottom: 20px;">
                    <div class="stat-card">

                        <h3>Total Users</h3>
                        <div class="stat-number"><?php echo $total_users; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Admins</h3>
                        <div class="stat-number"><?php echo $admin_count; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Normal Users</h3>
                        <div class="stat-number"><?php echo $user_count; ?></div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                <a class="profile-link" href="profile.php">Profile</a>
                <a class="logout-link" href="../logout.php">Logout</a>
            </div>
        </div>
        <div class="users-grid">
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <div class="user-card">
                        <div class="user-avatar">
                            <?php if (!empty($user['profile_image']) && file_exists('Profile_image/' . $user['profile_image'])): ?>
                                <img src="Profile_image/<?php echo $user['profile_image']; ?>" alt="Profile Image" />
                            <?php else: ?>
                                <img class="default-avatar" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['username']); ?>&background=007bff&color=fff&size=200" alt="No Image" />
                            <?php endif; ?>
                        </div>
                        <div class="user-info">
                            <div class="username">
                                <?php echo $user['username']; ?>
                                <?php if ($user['is_admin'] == 1): ?>
                                    <span class="admin-badge">Admin</span>
                                <?php endif; ?>
                            </div>
                            <div class="email">Email: <?php echo $user['email']; ?></div>
                        </div>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                            <div class="user-actions">
                                <a class="action-btn admin-btn" href="../admin/edit_user.php?id=<?php echo $user['id']; ?>">Edit</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-users">
                    <p>No users found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>