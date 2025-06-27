<?php
require_once '../config.php';
session_start();

// query to get all users
$query = "SELECT * FROM users ORDER BY username ASC";
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

// Function to get profile image path
function getProfileImagePath($user)
{
    if (empty($user['profile_image'])) {
        return false;
    }

    // Check admin profile images first
    $adminPath = '../admin/Profile_image/' . $user['profile_image'];
    if (file_exists($adminPath)) {
        return $adminPath;
    }

    // Check user profile images
    $userPath = 'Profile_image/' . $user['profile_image'];
    if (file_exists($userPath)) {
        return $userPath;
    }

    return false;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <link rel="stylesheet" href="css/users_list.css">
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
                <a class="home-link" href="POSTS/home.php">Home</a>
                <a class="profile-link" href="profile.php">Profile</a>
                <a class="logout-link" href="../logout.php">Logout</a>
            </div>
        </div>

        <!-- Users Table -->
        <div class="users-table-container">
            <?php if (count($users) > 0): ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Avatar</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Join Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr class="user-row">
                                <td class="avatar-cell">
                                    <?php
                                    $profileImagePath = getProfileImagePath($user);
                                    if ($profileImagePath): ?>
                                        <img src="<?php echo $profileImagePath; ?>" alt="Profile Image" class="user-avatar-img" />
                                    <?php else: ?>
                                        <div class="default-avatar-small">
                                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="username-cell">
                                    <span class="username-text"><?php echo htmlspecialchars($user['username']); ?></span>
                                </td>
                                <td class="email-cell">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </td>
                                <td class="role-cell">
                                    <?php if ($user['is_admin'] == 1): ?>
                                        <span class="admin-badge">Admin</span>
                                    <?php else: ?>
                                        <span class="user-badge">User</span>
                                    <?php endif; ?>
                                </td>
                                <td class="date-cell">
                                    <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                </td>
                                <td class="actions-cell">
                                    <a class="action-btn admin-btn" href="../admin/edit_user.php?id=<?php echo $user['id']; ?>">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-users">
                    <p>No users found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>