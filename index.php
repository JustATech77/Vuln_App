<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" type="image/png" href="image/fav/images.png">
    <title>Welcome - Home Page</title>
</head>

<body class="index-page">
    <div class="index-container">
        <h1>
            <span>Welcome In My Cave</span>
        </h1>
        <div class="index-buttons">
            <div class="button-group">
                <a href="login.php" class="index-button login-btn">
                    <span>Login</span>
                </a>
            </div>

            <div class="button-group">
                <a href="register.php" class="index-button register-btn">
                    <span>Register</span>
                </a>
            </div>
        </div>
    </div>
</body>

</html>