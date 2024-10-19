<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Initialize error message variable
$error_message = "";

// Handle adding a new billing
if (isset($_POST['add_billing'])) {
    $client_name = mysqli_real_escape_string($conn, $_POST['client_name']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Fetch client ID based on the client name
    $client_query = "SELECT id FROM clients WHERE name = '$client_name'";
    $client_result = mysqli_query($conn, $client_query);
    
    if (!$client_result) {
        die("Client Query Failed: " . mysqli_error($conn)); // Debugging line
    }
    
    if ($client = mysqli_fetch_assoc($client_result)) {
        $client_id = $client['id'];

        $query = "INSERT INTO billing (client_id, amount, due_date, status) VALUES ('$client_id', '$amount', '$due_date', '$status')";
        if (mysqli_query($conn, $query)) {
            header('Location: billing.php');
            exit();
        } else {
            $error_message = "Error adding billing: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Client not found.";
    }
}

// Handle deleting a billing
if (isset($_GET['delete_billing'])) {
    $billing_id = intval($_GET['delete_billing']);
    $query = "DELETE FROM billing WHERE id = $billing_id";
    if (mysqli_query($conn, $query)) {
        header('Location: billing.php');
        exit();
    } else {
        $error_message = "Error deleting billing: " . mysqli_error($conn);
    }
}

// Handle updating a billing
if (isset($_POST['update_billing'])) {
    $billing_id = intval($_POST['billing_id']);
    $client_name = mysqli_real_escape_string($conn, $_POST['client_name']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Fetch client ID based on the client name
    $client_query = "SELECT id FROM clients WHERE name = '$client_name'";
    $client_result = mysqli_query($conn, $client_query);

    if (!$client_result) {
        die("Client Query Failed: " . mysqli_error($conn)); // Debugging line
    }

    if ($client = mysqli_fetch_assoc($client_result)) {
        $client_id = $client['id'];

        // Update billing record
        $query = "UPDATE billing SET client_id='$client_id', amount='$amount', due_date='$due_date', status='$status' WHERE id=$billing_id";
        if (mysqli_query($conn, $query)) {
            header('Location: billing.php');
            exit();
        } else {
            $error_message = "Error updating billing: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Client not found.";
    }
}

// Fetch all billing records
$billings = mysqli_query($conn, "SELECT b.*, c.name AS client_name FROM billing b JOIN clients c ON b.client_id = c.id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="icon" type="image" href="images/logo.png">
    <title>Billing Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #a4d7e1; 
        }
        nav {
            background-color: #40E0D0;
        }
        h1, h2, form, table {
            color: black;
        }
        .edit-button, .update-button, .cancel-button {
            color: red;
            cursor: pointer;
            text-decoration: underline;
        }
        .edit-form {
            display: none; /* Hide the edit form by default */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="dashboard.php">Dashboard</a>
    </nav>

    <div class="container mt-4">
        <h1>Billing Management</h1>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="billing.php" method="POST" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <input type="text" name="client_name" class="form-control" placeholder="Client Name" required>
                </div>
                <div class="form-group col-md-4">
                    <input type="number" name="amount" class="form-control" placeholder="Amount" required>
                </div>
                <div class="form-group col-md-4">
                    <input type="date" name="due_date" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <select name="status" class="form-control" required>
                    <option value="PAID">PAID</option>
                    <option value="UNPAID">UNPAID</option>
                </select>
            </div>
            <button type="submit" name="add_billing" class="btn btn-primary">Add Billing</button>
        </form>

        <h2>Billing List</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client Name</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $billing_number = 1; 
            while ($billing = mysqli_fetch_assoc($billings)): ?>
                <tr>
                    <td><?php echo $billing_number++; ?></td>
                    <td><?php echo $billing['client_name']; ?></td>
                    <td><?php echo $billing['amount']; ?></td>
                    <td><?php echo $billing['due_date']; ?></td>
                    <td><?php echo $billing['status']; ?></td>
                    <td>
                        <button class="edit-button" onclick="showEditForm(<?php echo $billing['id']; ?>, '<?php echo $billing['client_name']; ?>', '<?php echo $billing['amount']; ?>', '<?php echo $billing['due_date']; ?>', '<?php echo $billing['status']; ?>');">Edit</button> | 
                        <a href="billing.php?delete_billing=<?php echo $billing['id']; ?>" class="text-danger" onclick="return confirm('Are you sure you want to delete this billing?');">Delete</a>
                    </td>
                </tr>
                <tr id="edit_form_<?php echo $billing['id']; ?>" class="edit-form">
                    <td colspan="6">
                        <form action="billing.php" method="POST">
                            <input type="hidden" name="billing_id" id="edit_billing_id_<?php echo $billing['id']; ?>" value="<?php echo $billing['id']; ?>">
                            <input type="text" name="client_name" id="edit_client_name_<?php echo $billing['id']; ?>" value="<?php echo $billing['client_name']; ?>" required>
                            <input type="number" name="amount" id="edit_amount_<?php echo $billing['id']; ?>" value="<?php echo $billing['amount']; ?>" required>
                            <input type="date" name="due_date" id="edit_due_date_<?php echo $billing['id']; ?>" value="<?php echo $billing['due_date']; ?>" required>
                            <select name="status" id="edit_status_<?php echo $billing['id']; ?>" required>
                                <option value="PAID" <?php if($billing['status'] == 'PAID') echo 'selected'; ?>>PAID</option>
                                <option value="UNPAID" <?php if($billing['status'] == 'UNPAID') echo 'selected'; ?>>UNPAID</option>
                            </select>
                            <button type="submit" name="update_billing" class="btn btn-warning">Update</button>
                            <button type="button" class="btn btn-secondary cancel-button" onclick="hideEditForm(<?php echo $billing['id']; ?>);">Cancel</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function showEditForm(id, clientName, amount, dueDate, status) {
            document.getElementById('edit_form_' + id).style.display = '';
            document.getElementById('edit_client_name_' + id).value = clientName;
            document.getElementById('edit_amount_' + id).value = amount;
            document.getElementById('edit_due_date_' + id).value = dueDate;
            document.getElementById('edit_status_' + id).value = status;
        }

        function hideEditForm(id) {
            document.getElementById('edit_form_' + id).style.display = 'none';
        }
    </script>
</body>
</html>
