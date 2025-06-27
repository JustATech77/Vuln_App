<?php
require_once '../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
if ($_SESSION['is_admin'] != 1) {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="add_user.css">
</head>

<body class="add-user-body">
    <div class="admin-container">
        <h1>Add User</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo "<p style='color: red;'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo "<p style='color: green;'>" . $_SESSION['success'] . "</p>";
            unset($_SESSION['success']);
        }
        ?>
        <div class="add-user-form-container">
            <form class="add-user-form" method="POST" action="confirm_adding_users.php">
                <label class="add-user-form-label" for="username">Username</label>
                <input class="add-user-form-input" type="text" name="username" placeholder="Username">
                <label class="add-user-form-label" for="email">Email</label>
                <input class="add-user-form-input" type="email" name="email" placeholder="Email">
                <label class="add-user-form-label" for="password">Password</label>
                <input class="add-user-form-input" type="password" name="password" placeholder="Password">
                <button class="add-user-form-button" type="submit">Add User</button>
            </form>

            <div style="text-align: center; margin-top: 20px;">
                <a href="admin_panal.php" class="add-user-form-button back-button" style="text-decoration: none; display: inline-block;">Back to Admin Panel</a>
            </div>
        </div>
    </div>
</body>

</html>