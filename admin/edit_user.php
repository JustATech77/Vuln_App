<?php
require_once '../config.php';
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../user/users_list.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ../user/users_list.php');
    exit();
}

$user_id = intval($_GET['id']);

// Function to get profile image path
function getProfileImagePath($profile_image)
{
    if (empty($profile_image)) {
        return false;
    }

    // Check admin profile images first
    $adminPath = 'Profile_image/' . $profile_image;
    if (file_exists($adminPath)) {
        return $adminPath;
    }

    // Check user profile images
    $userPath = '../user/Profile_image/' . $profile_image;
    if (file_exists($userPath)) {
        return $userPath;
    }

    return false;
}

// Fetch user data
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
if (!$user) {
    header('Location: ../user/users_list.php');
    exit();
}

// Handle delete profile image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_profile_image'])) {
    $profile_image = $user['profile_image'];

    if (!empty($profile_image)) {
        // Try to delete from admin folder
        $adminPath = 'Profile_image/' . $profile_image;
        if (file_exists($adminPath)) {
            unlink($adminPath);
        }

        // Try to delete from user folder
        $userPath = '../user/Profile_image/' . $profile_image;
        if (file_exists($userPath)) {
            unlink($userPath);
        }

        // Update database to remove profile image
        $update = "UPDATE users SET profile_image = NULL WHERE id = $user_id";
        if (mysqli_query($conn, $update)) {
            $_SESSION['success_message'] = 'Profile image deleted successfully';
            header('Location: ../user/users_list.php');
            exit();
        } else {
            $error = 'An error occurred while deleting the image.';
        }
    }
}

// Update data on save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_profile_image'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $profile_image = $user['profile_image'];

    // Handle new image upload if provided
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $uploadDir = 'Profile_image/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $file = $_FILES['profile_image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $newFileName = 'profile_' . $username . '_' . uniqid() . '.' . $ext;
            $targetPath = $uploadDir . $newFileName;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // Delete old image if exists
                if (!empty($profile_image)) {
                    // Try to delete from admin folder
                    $adminPath = 'Profile_image/' . $profile_image;
                    if (file_exists($adminPath)) {
                        unlink($adminPath);
                    }

                    // Try to delete from user folder
                    $userPath = '../user/Profile_image/' . $profile_image;
                    if (file_exists($userPath)) {
                        unlink($userPath);
                    }
                }
                $profile_image = $newFileName;
            }
        }
    }

    $update = "UPDATE users SET username='$username', email='$email', profile_image='$profile_image' WHERE id=$user_id";
    if (mysqli_query($conn, $update)) {
        $_SESSION['success_message'] = 'User data updated successfully';
        header('Location: ../user/users_list.php');
        exit();
    } else {
        $error = 'An error occurred while updating.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="edit_user.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Edit User</h1>
        </div>
        <form class="edit-form" method="post" enctype="multipart/form-data">
            <?php
            $profileImagePath = getProfileImagePath($user['profile_image']);
            if ($profileImagePath): ?>
                <img src="<?php echo $profileImagePath; ?>" alt="Profile Image" />
                <br><br>
                <button type="submit" name="delete_profile_image" class="delete-profile-btn" onclick="return confirm('Are you sure you want to delete this profile image?')">Delete Profile Image</button>
                <br><br>
            <?php else: ?>
                <img class="default-avatar" src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['username']); ?>&background=007bff&color=fff&size=200" alt="No Image" />
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo $user['username']; ?>" required>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $user['email']; ?>" required>
            <label for="profile_image">Change Profile Image</label>
            <input type="file" name="profile_image" id="profile_image" accept=".jpg,.jpeg,.png,.gif">
            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>

</html>