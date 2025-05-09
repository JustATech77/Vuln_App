<?php
$host = 'localhost';
$dbname = 'null';
$username = 'root';
$password = '';
$conn = mysqli_connect($host, $username, $password, $dbname);
if(!$conn)
{
    die("Connection failed: " . mysqli_connect_error());
}
?>