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
if ($_POST['username'] == '' || $_POST['email'] == '' || $_POST['password'] == '') {
    $_SESSION['error'] = "Please fill all fields";
    header('Location: add_user.php');
    exit();
}
// check if email is already used
$email = $_POST['email'];
$query = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    $_SESSION['error'] = "Email already used";
    header('Location: add_user.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
}
$query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
$result = mysqli_query($conn, $query);
if ($result) {
    $_SESSION['success'] = "User added successfully";
    header('Location: admin_panal.php');
    exit();
}
else {
    $_SESSION['error'] = "Error adding user";
    header('Location: add_user.php');
    exit();
}
?>  
