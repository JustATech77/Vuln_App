<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylee.css">
    <title>Register Page</title>
</head>

<body class="register-page">
    <div class="register-container">
        <h1>Register a new account </h1>
        <?php if (isset($_SESSION['error'])){ ?>
            <div class="error-message">
                <?php echo $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php  }?>
        <?php if (isset($_SESSION['success'])){ ?>
            <div class="success-message">
                <?php echo $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php  }?>
        <div class="register-form">
            <form action="confirm_register.php" method="POST">
                <div class="form-group-register">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="form-group-register">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </div>
                <div class="form-group-register">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm your password" required>
                </div>
                <div class="form-group-register">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group-button">
                    <button type="submit">Sign Up</button>
                </div>
                <div class="form-group-button">
                    <a  class="form-group-button-link" href="login.php">Already have an account? Login here</a>
                </div>
            </form>
        </div>
    </div>
    <script src="js/register.js"></script>
</body>
</html>
