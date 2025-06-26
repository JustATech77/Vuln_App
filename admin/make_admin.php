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
$user_id = $_GET['id'];
$query = "UPDATE users SET is_admin = 1 WHERE id = $user_id";
$result = mysqli_query($conn, $query);
if ($result) {
   header('Location: admin_panal.php');
   exit();
} else {
   echo "Error: " . mysqli_error($conn);
}
exit();
