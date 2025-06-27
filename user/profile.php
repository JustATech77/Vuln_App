<?php
require_once '../config.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Function to get profile image path
function getProfileImagePath($profile_image)
{
    if (empty($profile_image)) {
        return false;
    }

    // Check admin profile images first
    $adminPath = '../admin/Profile_image/' . $profile_image;
    if (file_exists($adminPath)) {
        return $adminPath;
    }

    // Check user profile images
    $userPath = 'Profile_image/' . $profile_image;
    if (file_exists($userPath)) {
        return $userPath;
    }

    return false;
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

// Get messages from session and clear them
$profile_success_message = isset($_SESSION['profile_success_message']) ? $_SESSION['profile_success_message'] : null;
$profile_error_message = isset($_SESSION['profile_error_message']) ? $_SESSION['profile_error_message'] : null;

// Clear messages after getting them
unset($_SESSION['profile_success_message'], $_SESSION['profile_error_message']);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/user.css">
    <title>profile</title>
</head>

<body>
    <div class="container">
        <h1>Welcome <?php echo $_SESSION['username']; ?></h1>
        <div class="prle-container">
            <div class="profile-info">
                <h2>Profile Information</h2>

                <!-- Display current profile image if exists -->
                <?php
                $profileImagePath = getProfileImagePath($current_profile_image);
                if ($profileImagePath): ?>
                    <div class="profile-image-container">
                        <img src="<?php echo $profileImagePath; ?>" alt="Profile Image" class="profile-image">
                        <br><br>
                        <form method="post">
                            <button type="submit" name="delete_profile_image" class="delete-profile-btn">Delete Profile Image</button>
                        </form>
                    </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" class="upload-form">
                    <input type="file" name="profile_image" id="profile_image" accept="jpg,jpeg,png,gif">
                    <button type="submit" name="upload_profile">Upload Profile Image</button>
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
                <div class="edit-profile-section">
                    <h3>Edit Profile</h3>
                    <form method="post">
                        <div>
                            <label for="username">Username:</label>
                            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                        </div>

                        <div>
                            <label for="email">Email:</label>
                            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                        </div>

                        <div>
                            <label for="current_password">Current Password:</label>
                            <input type="password" name="current_password" id="current_password" required>
                        </div>

                        <div>
                            <label for="new_password">New Password:</label>
                            <input type="password" name="new_password" id="new_password">
                        </div>

                        <div>
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" name="confirm_password" id="confirm_password">
                        </div>

                        <button type="submit" name="update_profile">Update Profile</button>
                    </form>
                </div>
                <!-- Add post -->
                <div class="add-post-section">
                    <h3>Add Post</h3>
                    <form method="post" enctype="multipart/form-data" name="add_post_form" action="POSTS/Handel_Posts.php">
                        <textarea rows="4" name="content" placeholder="Write your post here..." required></textarea>
                        <button class="add-post-btn" type="submit" name="add_post" value="add_post">Add Post</button>
                    </form>
                </div>

                <!-- view my posts -->
                <div class="view-post-section">
                    <h3>View Posts</h3>
                    <form method="post" enctype="multipart/form-data" name="view_post_form" action="user_posts.php?id=<?php echo $_SESSION['user_id']; ?>">
                        <button class="view-post-btn" type="submit" name="view_post" value="view_post">View My Posts</button>
                    </form>
                    <form method="post" enctype="multipart/form-data" name="home_form" action="POSTS/Home.php">
                        <button class="home-btn" type="submit" name="home" value="home">Home</button>
                    </form>
                </div>

            </div>
            <div class="profile-actions">
                <a class="logout-btn" href="../logout.php">Logout</a>
                <a class="user-list-btn" href="users_list.php">User List</a>
            </div>
            <?php
            if ($_SESSION['is_admin'] == 1) {
                echo "<div class='admin-panel-link'>";
                echo "<a href='../admin/admin_panal.php'>Admin Panel</a>";
                echo "</div>";
            }
            ?>
        </div>

    </div>
</body>

</html>