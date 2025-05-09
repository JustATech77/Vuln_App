<?php
require_once '../config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
// check if user is admin
if ($_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit();
}
$query = "SELECT * FROM users";
$result_of_users = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="admin-container">
        <div class="header">
            <h1>Admin Panel</h1>
        </div>
        <div class="users-table">
            <a href='add_user.php' class='add-user-btn'>Add User</a>
            <table class="users-table-content">
                <tr>
                    <th class="users-table-content-header">ID</th>
                    <th class="users-table-content-header">Username</th>
                    <th class="users-table-content-header">Email</th>
                    <th class="users-table-content-header">Role</th>
                    <th class="users-table-content-header">Action</th>
                </tr>
                <?php
                while ($row = mysqli_fetch_array($result_of_users)) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . ($row['is_admin'] == 1 ? 'Admin' : 'User') . "</td>";
                    echo "<td>
                                <a href='delete_user.php?id=" . $row['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>";
                    if ($row['is_admin'] == 0) {
                        echo "<a href='make_admin.php?id=" . $row['id'] . "' class='make-admin-btn' onclick='return confirm(\"Are you sure you want to make this user an admin?\")'>Make Admin</a>";
                    } else {
                        echo "<a href='remove_admin.php?id=" . $row['id'] . "' class='remove-admin-btn' onclick='return confirm(\"Are you sure you want to remove admin privileges?\")'>Remove Admin</a>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
            <!-- head to profil page -->
            <a href="../user/profile.php" class="profile-link">Profile</a>
        </div>
    </div>
</body>

</html>