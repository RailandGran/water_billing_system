<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Include database connection
include 'db.php';

// Fetch admin details
$username = $_SESSION['username'];
$result = $conn->query("SELECT * FROM users WHERE username='$username'");
$admin = $result->fetch_assoc();

// Update profile logic
if (isset($_POST['updateProfile'])) {
    $email = $_POST['email'];

    // Update email
    $sql = "UPDATE users SET email='$email' WHERE username='$username'";
    if ($conn->query($sql) === TRUE) {
        $success = "Profile updated successfully.";
    } else {
        $error = "Error updating profile: " . $conn->error;
    }

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        
        if ($check !== false) {
            // Delete the old profile picture if it exists
            if (!empty($admin['profile_picture']) && file_exists($admin['profile_picture'])) {
                unlink($admin['profile_picture']);
            }

            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $update_picture_query = "UPDATE users SET profile_picture='$target_file' WHERE username='$username'";
                $conn->query($update_picture_query);
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        } else {
            $error = "File is not an image.";
        }
    }
}

// Fetch updated admin details to get the new profile picture
$result = $conn->query("SELECT * FROM users WHERE username='$username'");
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="icon" type="image" href="images/logo.png">
    <title>Profile </title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #e0f7fa;
        }
        .sidebar {
            background-color: #007bff;
            min-height: 100vh;
            padding-top: 20px;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 15px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #0056b3;
        }
        .main-content {
            margin-left: 200px;
            padding: 20px;
        }
        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        nav {
            background-color: #40E0D0;
            padding: 10px;
        }
        h1 {
            text-align: center;
        }
    </style>
</head>
<body>
    <nav>
        <a href="dashboard.php">Dashboard</a>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div style="text-align: center;">
            <h1><?php echo $username === 'admin' ? 'Admin Profile' : 'User Profile'; ?></h1>
        </div>

        <!-- Success or Error Messages -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Profile Form -->
        <form method="POST" enctype="multipart/form-data">
            <!-- Display Profile Picture -->
            <?php if (!empty($admin['profile_picture'])): ?>
                <img src="<?php echo $admin['profile_picture']; ?>" alt="Profile Picture" class="profile-picture">
            <?php endif; ?>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" class="form-control" value="<?php echo $admin['username']; ?>" readonly>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" class="form-control" value="<?php echo $admin['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" class="form-control">
            </div>
            <button type="submit" name="updateProfile" class="btn btn-primary">Update Profile</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
