<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Initialize error message variable
$error_message = "";

// Handle adding a new client
if (isset($_POST['add_client'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']); 
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']); 

    // Ensure 'name' is the correct column in your database
    $query = "INSERT INTO clients (name, email, address) VALUES ('$name', '$email', '$address')";

    if (mysqli_query($conn, $query)) {
        header('Location: clients.php');
        exit();
    } else {
        $error_message = "Error adding client: " . mysqli_error($conn);
    }
}

// Handle deleting a client
if (isset($_GET['delete_client'])) {
    $client_id = intval($_GET['delete_client']);
    $query = "DELETE FROM clients WHERE id = $client_id";
    if (mysqli_query($conn, $query)) {
        header('Location: clients.php');
        exit();
    } else {
        $error_message = "Error deleting client: " . mysqli_error($conn);
    }
}

// Handle updating a client
if (isset($_POST['update_client'])) {
    $client_id = intval($_POST['client_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']); 

    // Ensure 'name' matches the actual column in your database
    $query = "UPDATE clients SET name='$name', email='$email', address='$address' WHERE id=$client_id";
    if (mysqli_query($conn, $query)) {
        header('Location: clients.php');
        exit();
    } else {
        $error_message = "Error updating client: " . mysqli_error($conn);
    }
}

// Search functionality
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $clients = mysqli_query($conn, "SELECT * FROM clients WHERE name LIKE '%$search_query%' OR email LIKE '%$search_query%'");
} else {
    $clients = mysqli_query($conn, "SELECT * FROM clients");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="icon" type="image" href="images/logo.png">
    <title>Clients</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <style>
        body {
            background-color: #a4d7e1; 
        }
        .nav-link {
            color: black;
        }
        .nav-link:hover {
            color: #fff;
        }
        .table th {
            background-color: #84c7e5; 
            color: black; 
        }
        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #40E0D0;">
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Clients Management</h1>

        <?php if ($error_message): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="clients.php" method="GET" class="form-inline mb-3">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search" value="<?php echo htmlspecialchars($search_query); ?>" oninput="this.form.submit()">
        </form>

        <form action="clients.php" method="POST" class="mb-4">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="name" class="form-control" placeholder="Client Name" required>
                </div>
                <div class="col">
                    <input type="email" name="email" class="form-control" placeholder="Client Email" required>
                </div>
                <div class="col">
                    <input type="text" name="address" class="form-control" placeholder="Address" required>
                </div>
                <div class="col">
                    <input type="submit" name="add_client" value="Add Client" class="btn btn-dark">
                </div>
            </div>
        </form>

        <h2>Client List</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th> 
                    <th>Name</th> 
                    <th>Email</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $client_number = 1; 
                while ($client = mysqli_fetch_assoc($clients)): ?>
                <tr>
                    <td><?php echo $client_number++; ?></td> 
                    <td><?php echo $client['name']; ?></td>
                    <td><?php echo $client['email']; ?></td>
                    <td><?php echo $client['address']; ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="showEditForm(<?php echo $client['id']; ?>, '<?php echo htmlspecialchars($client['name']); ?>', '<?php echo htmlspecialchars($client['email']); ?>', '<?php echo htmlspecialchars($client['address']); ?>');">Edit</button> 
                        <a href="clients.php?delete_client=<?php echo $client['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this client?');">Delete</a>
                    </td>
                </tr>
                <tr id="edit_form_<?php echo $client['id']; ?>" class="edit-form" style="display: none;">
                    <td colspan="5">
                        <form action="clients.php" method="POST">
                            <input type="hidden" name="client_id" id="edit_client_id_<?php echo $client['id']; ?>">
                            <div class="form-row">
                                <div class="col">
                                    <input type="text" name="name" id="edit_name_<?php echo $client['id']; ?>" class="form-control" placeholder="Client Name" required>
                                </div>
                                <div class="col">
                                    <input type="email" name="email" id="edit_email_<?php echo $client['id']; ?>" class="form-control" placeholder="Client Email" required>
                                </div>
                                <div class="col">
                                    <input type="text" name="address" id="edit_address_<?php echo $client['id']; ?>" class="form-control" placeholder="Address" required>
                                </div>
                                <div class="col">
                                    <input type="submit" name="update_client" value="Update" class="btn btn-primary btn-sm">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="hideEditForm(<?php echo $client['id']; ?>);">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function showEditForm(id, name, email, address) {
            document.getElementById('edit_client_id_' + id).value = id;
            document.getElementById('edit_name_' + id).value = name;
            document.getElementById('edit_email_' + id).value = email;
            document.getElementById('edit_address_' + id).value = address;

            const editForm = document.getElementById('edit_form_' + id);
            editForm.style.display = 'table-row';
        }

        function hideEditForm(id) {
            const editForm = document.getElementById('edit_form_' + id);
            editForm.style.display = 'none';
        }
    </script>
</body>
</html>
