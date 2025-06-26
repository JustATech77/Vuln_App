<?php
require_once '../config.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $uploadDir = "Uploads/";
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $file = $_FILES["image"];
    $fileName = basename($file["name"]);
    $targetFilePath = $uploadDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    
    // Check if file is actually uploaded
    if ($file["error"] == 0) {
        // Check file size (limit to 5MB)
        if ($file["size"] <= 5000000) {
            // Allow certain file formats
            $allowedTypes = array("jpg", "jpeg", "png", "gif", "pdf", "doc", "docx", "txt");
            if (in_array($fileType, $allowedTypes)) {
                // Generate unique filename to avoid conflicts
                $uniqueFileName = time() . '_' . $fileName;
                $targetFilePath = $uploadDir . $uniqueFileName;
                
                if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                    $uploadMessage = "File uploaded successfully: " . $uniqueFileName;
                    $uploadStatus = "success";
                } else {
                    $uploadMessage = "Sorry, there was an error uploading your file.";
                    $uploadStatus = "error";
                }
            } else {
                $uploadMessage = "Sorry, only JPG, JPEG, PNG, GIF, PDF, DOC, DOCX & TXT files are allowed.";
                $uploadStatus = "error";
            }
        } else {
            $uploadMessage = "Sorry, your file is too large. Maximum size is 5MB.";
            $uploadStatus = "error";
        }
    } else {
        $uploadMessage = "Please select a file to upload.";
        $uploadStatus = "error";
    }
}
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
                <p>Username: <?php echo $_SESSION['username']; ?></p>
                <p>Email: <?php echo $_SESSION['email']; ?></p>
                
                <!-- Upload Message Display -->
                <?php if (isset($uploadMessage)): ?>
                    <div style="margin: 10px 0; padding: 10px; border-radius: 5px; <?php echo $uploadStatus == 'success' ? 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;' : 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'; ?>">
                        <?php echo $uploadMessage; ?>
                    </div>
                <?php endif; ?>
                
                <!-- File Upload Form -->
                <form method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
                    <div style="margin-bottom: 10px;">
                        <label for="image" style="display: block; margin-bottom: 5px; font-weight: bold;">Upload File:</label>
                        <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt" required>
                    </div>
                    <button type="submit" style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Upload</button>
                </form>
            </div>
            <div class="profile-actions">
                <a class="logout-btn" href="../logout.php">Logout</a>
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