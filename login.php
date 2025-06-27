<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylee.css">
    <title>Login</title>
</head>
<body class="login-page">
    <div class="login-container">
        <h1>Login</h1>
        <?php if(isset($_SESSION['success'])) {?>
            <div class="success-message">
                <?php echo $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php }?>
        <?php if(isset($_SESSION['error'])) {?>
            <div class="error-message">
                <?php echo $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php }?>
        <form class="login-form" action="confirm_login.php" method="post">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
        </div>
        <div class="form-group">
            
            <button type="submit" name="login" >Login</button>
        </div>
        <div class="form-group-button-link">
            <a class="form-group-button-link" href="register.php">Don't have an account? Register here</a>
        </div>
        </form>     
    </div>
</body>
</html>
<?php

?>
