<?php
require_once 'config.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $email = $_POST['email'];
    $query = "SELECT * FROM users WHERE email = '$email' AND username = '$username'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error'] = "Email or username already  exists.";
        header("Location: register.php");
        exit();
    }
    if ($password != $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: register.php");
        exit();
    }
    $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $_SESSION['success'] = "Account created successfully";
        header("Location: login.php");
        exit();
    }
    else
    {
        $_SESSION['error'] = "Error creating account";
        header("Location: register.php");
        exit();
    }
}
else
{
    header("Location: register.php");
    exit();
}
?>