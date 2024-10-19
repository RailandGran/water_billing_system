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

// Fetch all users from the database
$users = $conn->query("SELECT * FROM users");

// Add a new user
if (isset($_POST['addUser'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password
    $email = $_POST['email'];

    $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
    if ($conn->query($sql) === TRUE) {
        header("Location: users.php");
        exit;
    } else {
        $error = "Error adding user: " . $conn->error;
    }
}

// Edit user logic
if (isset($_POST['editUser'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['edit_username'];
    $email = $_POST['edit_email'];

    $sql = "UPDATE users SET username='$username', email='$email' WHERE id='$user_id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: users.php");
        exit;
    } else {
        $error = "Error updating user: " . $conn->error;
    }
}

// Delete user logic
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id='$user_id'");
    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image" href="images/logo.png">
    <title>Users</title>
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
        .table-responsive {
            margin-top: 20px;
        }
        nav {
            background-color: #40E0D0;
            padding: 10px;
        }
    </style>
</head>
<body>

<nav>
    <a href="dashboard.php">Dashboard</a>
</nav>

<!-- Main Content -->
<div class="main-content">
    <h1>User Accounts</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>

    <!-- Table to display user accounts -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $row['id']; ?>">Edit</button>
                            <a href="users.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editUserLabel">Edit User Account</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="">
                                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                        <div class="form-group">
                                            <label for="edit_username">Username</label>
                                            <input type="text" name="edit_username" class="form-control" value="<?php echo $row['username']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_email">Email</label>
                                            <input type="email" name="edit_email" class="form-control" value="<?php echo $row['email']; ?>" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="editUser" class="btn btn-success">Save Changes</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="addUser" class="btn btn-primary">Add User</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>