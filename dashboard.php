<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$query = "SELECT role FROM users WHERE username='{$_SESSION['username']}'";
$result = mysqli_query($conn, $query);
$user_row = mysqli_fetch_assoc($result);

$total_customers_query = "SELECT COUNT(*) as total FROM clients"; 
$total_customers_result = mysqli_query($conn, $total_customers_query);
$total_customers_row = mysqli_fetch_assoc($total_customers_result);
$total_customers = $total_customers_row['total'];

$total_bills_query = "SELECT COUNT(*) as total FROM billing"; 
$total_bills_result = mysqli_query($conn, $total_bills_query);
$total_bills_row = mysqli_fetch_assoc($total_bills_result);
$total_bills = $total_bills_row['total'];

if ($user_row['role'] === 'admin') {
    $total_income_query = "SELECT SUM(amount) as total_income FROM billing WHERE status = 'paid'"; 
    $total_income_result = mysqli_query($conn, $total_income_query);
    $total_income_row = mysqli_fetch_assoc($total_income_result);
    $total_income_today = $total_income_row['total_income'] ?? 0; 
} else {
    $total_income_today = null; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="icon" type="image" href="images/logo.png">
    <title>Dashboard</title>
    <style>
        * {
            text-decoration: none;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            display: flex;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background-image: url('images/water.jpg'); 
            background-size: cover;
            background-position: center;
        }
        .sidebar {
            width: 250px;
            background-color: #40E0D0; 
            color: black; 
            padding-top: 20px;
            position: fixed;
            height: 100%;
            transition: width 0.3s;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 15px;
            text-align: center; 
            white-space: nowrap; 
        }
        .sidebar ul li a {
            color: black; 
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: background 0.3s;
        }
        .sidebar ul li a:hover {
            background-color: #87cefa; 
        }
        .sidebar ul li a i {
            margin-right: 10px; 
        }
        .sidebar.closed ul li a .link-text {
            display: none; /* Hide link text when sidebar is closed */
        }
        .sidebar.closed ul li a {
            justify-content: center; /* Center the icons */
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
            transition: margin-left 0.3s;
        }
        .header {
            background-color: #40E0D0;
            padding: 20px;
            display: flex;              
            justify-content: space-between;
            align-items: center;        
            color: black; 
        }
        .welcome-section {
            display: flex;
            align-items: center;
        }
        .header img {
            width: 70px;
            height: 70px;
            margin-right: 10px;
        }
        .user-menu {
            position: relative;
            display: inline-block;
        }
        .user-icon {
            background-color: black; 
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 24px;
            color: white;
        }
        .dropdown {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            color: black;
            min-width: 150px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        .dropdown a {
            color: black; 
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown a:hover {
            background-color: #ddd;
        }
        .dropdown.show {
            display: block;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        .stat-box {
            background-color: Turquoise; 
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            flex: 1;
            margin: 0 10px;
        }
        .stat-box h2 {
            margin: 10px 0;
            color: black; 
        }
        .stat-box i {
            font-size: 50px;
            color: black; 
        }
        .stat-label {
            color: black; 
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.1/css/boxicons.min.css">
</head>
<body>

    <div class="sidebar" id="sidebar">
        <ul>
            <li><a href="dashboard.php"><i class="bx bx-home"></i><span class="link-text">Dashboard</span></a></li>
            <li><a href="clients.php"><i class="bx bx-user"></i><span class="link-text">Clients</span></a></li>
            <li><a href="billing.php"><i class="bx bx-file"></i><span class="link-text">Billing</span></a></li>
            <li><a href="users.php"><i class="bx bx-user-circle"></i><span class="link-text">Users</span></a></li>
            <li><a href="profile.php"><i class="bx bx-cog"></i><span class="link-text">Profile</span></a></li>
            <li><a href="logout.php"><i class="bx bx-log-out"></i><span class="link-text">Logout</span></a></li>
        </ul>
    </div>

    <div class="main-content" id="main-content">
        <i class="bx bx-menu toggle-sidebar" id="toggle-btn" style="cursor: pointer; font-size: 24px;"></i>
        <div class="header"> 
            <div class="welcome-section">
                <img src="images/logo.png" alt="Logo"> 
                <h1>Welcome, <span><?php echo ucfirst($_SESSION['username']); ?></span></h1> 
            </div>

        </div>
        <hr>
        <div style="text-align: center;">
            <h2 style="color: black;">Dashboard</h2> 
            <p style="color: black;">This is your Water Supply And Sanitation Management System Dashboard.</p> 
        </div>

        <div class="stats">
            <div class="stat-box">
                <i class="bx bx-group"></i> 
                <h2><?php echo $total_customers; ?></h2> 
                <p class="stat-label">Total Clients</p>
            </div>
            <div class="stat-box">
                <i class="bx bx-file"></i> 
                <h2><?php echo $total_bills; ?></h2> 
                <p class="stat-label">Total Bills</p>
            </div>
            <?php if ($user_row['role'] === 'admin'): ?>
                <div class="stat-box">
                    <i class="bx bx-wallet"></i>
                    <h2>₱<?php echo number_format($total_income_today, 2); ?></h2>
                    <p class="stat-label">Total Income Today</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleBtn = document.getElementById('toggle-btn');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('closed');
            if (sidebar.classList.contains('closed')) {
                sidebar.style.width = '60px'; // Minimized sidebar width
                mainContent.style.marginLeft = '60px'; // Adjust main content margin
            } else {
                sidebar.style.width = '250px'; // Original sidebar width
                mainContent.style.marginLeft = '250px'; // Adjust main content margin
            }
        });
    </script>

</body>
</html>