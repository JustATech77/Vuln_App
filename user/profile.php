<?php
require_once '../config.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Get user data from database
$query = "SELECT * FROM users WHERE username = '$_SESSION[username]'";
$result = mysqli_query($conn, $query);
$user_data = mysqli_fetch_assoc($result);

// get profile image from database
$query = "SELECT profile_image FROM users WHERE username = '$_SESSION[username]'";
$result = mysqli_query($conn, $query);
$profile_image = mysqli_fetch_assoc($result);
if ($profile_image) {
    $current_profile_image = $profile_image['profile_image'];
} else {
    $current_profile_image = null;
}

// Handle profile data update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if username or email already exists
    $check_query = "SELECT * FROM users WHERE (username = '$new_username' OR email = '$new_email') AND username != '$_SESSION[username]'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['profile_error_message'] = "Username or email already exists";
    } else {
        // Verify current password
        $verify_query = "SELECT password FROM users WHERE username = '$_SESSION[username]'";
        $verify_result = mysqli_query($conn, $verify_query);
        $user = mysqli_fetch_assoc($verify_result);
        if ($user['password'] != $current_password) {
            $_SESSION['profile_error_message'] = "Current password is incorrect";
        } else {
            // Update profile data
            $update_query = "UPDATE users SET username = '$new_username', email = '$new_email', password = '$new_password' WHERE username = '$_SESSION[username]'";
            if (mysqli_query($conn, $update_query)) {
                $_SESSION['profile_success_message'] = "Profile updated successfully";
            } else {
                $_SESSION['profile_error_message'] = "Error updating profile";
            }
            // update session username and email
            $_SESSION['username'] = $new_username;
            $_SESSION['email'] = $new_email;
            // Redirect to refresh the page
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}

// Handle profile image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $uploadDir = "Profile_image/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $profile_image = $_FILES['profile_image'];
    $profile_image_name = $profile_image['name'];
    $profile_image_tmp_name = $profile_image['tmp_name'];
    $profile_image_size = $profile_image['size'];
    $profile_image_error = $profile_image['error'];
    $profile_image_type = $profile_image['type'];
    $profile_image_ext = strtolower(pathinfo($profile_image_name, PATHINFO_EXTENSION));
    $allowedTypes = array("jpg", "jpeg", "png", "gif");
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    // Check file type first
    if (!in_array($profile_image_ext, $allowedTypes)) {
        $_SESSION['profile_error_message'] = "File type not allowed";
    } elseif ($profile_image_size > $maxFileSize) {
        $_SESSION['profile_error_message'] = "File size exceeds the maximum allowed size of 2MB";
    } else {
        // Generate unique filename
        $uniqueFileName = 'profile_' . $_SESSION['username'] . '_' . uniqid() . '.' . $profile_image_ext;
        $targetFilePath = $uploadDir . $uniqueFileName;

        // Upload the file
        if (move_uploaded_file($profile_image_tmp_name, $targetFilePath)) {
            $_SESSION['profile_success_message'] = "Profile image uploaded successfully";
            // Store the filename in database
            $query = "UPDATE users SET profile_image = '$uniqueFileName' WHERE username = '$_SESSION[username]'";
            mysqli_query($conn, $query);
            $current_profile_image = $uniqueFileName;
            echo "<pre>";
            print_r($current_profile_image);
            echo "</pre>";
        } else {
            $_SESSION['profile_error_message'] = "Profile image upload failed";
        }
    }
    // Redirect to prevent form resubmission on refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
// handle delete profile image
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_profile_image'])) {
    $query = "UPDATE users SET profile_image = NULL WHERE username = '$_SESSION[username]'";
    mysqli_query($conn, $query);
    $current_profile_image = null;
    $_SESSION['profile_success_message'] = "Profile image deleted successfully";
}

// Handle file uploads
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['files'])) {
    $uploadDir = "Uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $files = $_FILES['files'];
    $file_name = $files['name'];
    $file_tmp_name = $files['tmp_name'];
    $file_size = $files['size'];
    $file_error = $files['error'];
    $file_type = $files['type'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowedTypes = array("jpg", "jpeg", "png", "gif", "pdf", "doc", "docx", "txt");
    $maxFileSize = 1 * 1024 * 1024; // 1MB

    // Check file type first
    if (!in_array($file_ext, $allowedTypes)) {
        $_SESSION['error_message'] = "File type not allowed";
    }
    // Check file size
    elseif ($file_size > $maxFileSize) {
        $_SESSION['error_message'] = "File size exceeds the maximum allowed size of 1MB";
    }
    // If all checks pass, proceed with upload
    else {
        $fileName = uniqid() . '.' . $file_ext;
        $targetFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($file_tmp_name, $targetFilePath)) {
            $_SESSION['success_message'] = "File uploaded successfully";
        } else {
            $_SESSION['error_message'] = "File upload failed";
        }
    }
    // Redirect to prevent form resubmission on refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get messages from session and clear them
$profile_success_message = isset($_SESSION['profile_success_message']) ? $_SESSION['profile_success_message'] : null;
$profile_error_message = isset($_SESSION['profile_error_message']) ? $_SESSION['profile_error_message'] : null;
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;

// Clear messages after getting them
unset($_SESSION['profile_success_message'], $_SESSION['profile_error_message'], $_SESSION['success_message'], $_SESSION['error_message']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="users.css">
    <title>profile</title>
</head>

<body>
    <div class="container">
        <h1>Welcome <?php echo $_SESSION['username']; ?></h1>
        <div class="profile-container">
            <div class="profile-info">
                <h2>Profile Information</h2>

                <!-- Display current profile image if exists -->
                <?php if (isset($current_profile_image) && file_exists("Profile_image/" . $current_profile_image)) { ?>
                    <div style="text-align: center; margin: 20px 0;">
                        <img src="Profile_image/<?php echo $current_profile_image; ?>" alt="Profile Image" style="width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 3px solidrgb(255, 0, 43);">
                        <br><br>
                        <form method="post" style="display: inline;">
                            <button type="submit" name="delete_profile_image" class="delete-profile-btn" style="display: inline-block; padding: 15px 40px; background-color: rgb(255, 0, 43); color: white; text-decoration: none; border-radius: 10px; font-weight: 500; border: 1px solid rgba(255, 255, 255, 0.1); cursor: pointer;">Delete Profile Image</button>
                        </form>
                    </div>
                <?php } ?>

                <form method="post" enctype="multipart/form-data" style="margin-top: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <input type="file" name="profile_image" id="profile_image" accept="jpg,jpeg,png,gif">
                    <button type="submit" name="upload_profile" style="margin-top: 10px;">Upload Profile Image</button>
                    <?php if (isset($profile_success_message)) { ?>
                        <p class="upload-message success"><?php echo $profile_success_message; ?></p>
                    <?php } ?>
                    <?php if (isset($profile_error_message)) { ?>
                        <p class="upload-message error"><?php echo $profile_error_message; ?></p>
                    <?php } ?>
                </form>

                <p>Username: <?php echo $_SESSION['username']; ?></p>
                <p>Email: <?php echo $_SESSION['email']; ?></p>

                <!-- Edit Profile Form -->
                <div class="edit-profile-section" style="margin-top: 30px; padding: 20px; background-color: rgba(255, 255, 255, 0.1); border-radius: 10px;">
                    <h3 style="color: white; margin-bottom: 20px;">Edit Profile</h3>
                    <form method="post" style="display: flex; flex-direction: column; gap: 15px;">
                        <div style="display: flex; flex-direction: column;">
                            <label for="username" style="color: white; margin-bottom: 5px;">Username:</label>
                            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required style="padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label for="email" style="color: white; margin-bottom: 5px;">Email:</label>
                            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required style="padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label for="current_password" style="color: white; margin-bottom: 5px;">Current Password:</label>
                            <input type="password" name="current_password" id="current_password" required style="padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label for="new_password" style="color: white; margin-bottom: 5px;">New Password:</label>
                            <input type="password" name="new_password" id="new_password" style="padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label for="confirm_password" style="color: white; margin-bottom: 5px;">Confirm New Password:</label>
                            <input type="password" name="confirm_password" id="confirm_password" style="padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                        </div>

                        <button type="submit" name="update_profile" style="padding: 12px 30px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-top: 10px;">Update Profile</button>
                    </form>
                </div>

                <form method="post" enctype="multipart/form-data" style="margin-top: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <input type="file" name="files" id="files">
                    <button type="submit" name="upload" style="margin-top: 10px;">Upload</button>
                    <?php if (isset($success_message)) { ?>
                        <p class="upload-message success"><?php echo $success_message; ?></p>
                    <?php } ?>
                    <?php if (isset($error_message)) { ?>
                        <p class="upload-message error"><?php echo $error_message; ?></p>
                    <?php } ?>

                </form>
            </div>
            <div class="profile-actions">
                <a class="logout-btn" href="../logout.php">Logout</a>
                <a class="user-list-btn" href="users_list.php" style="display: inline-block; padding: 15px 40px; background-color: rgba(52, 152, 219, 0.9); color: white; text-decoration: none; border-radius: 10px; font-weight: 500; border: 1px solid rgba(255, 255, 255, 0.1); margin-left: 10px;">User List</a>
            </div>
            <?php
            if ($_SESSION['is_admin'] == 1) {
                echo "<div style='text-align: center; margin-top: 20px; '>";
                echo "<a style='text-decoration: none; color: white; background-color: #007bff; padding: 10px 20px; border-radius: 5px; ' href='../admin/admin_panal.php'>Admin Panel</a>";
                echo "</div>";
            }
            ?>
        </div>

    </div>
</body>

</html>