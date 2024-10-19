<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Supply and Sanitation Management System</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    
    <style>
        body {
            background: url('images/water.jpg') no-repeat center center fixed; /* Background image */
            background-size: cover; /* Cover the entire area */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensure footer stays at the bottom */
        }
        .navbar {
            background-color: rgba(0, 123, 255, 0.8); /* Semi-transparent navbar */
            display: flex;
            justify-content: center; /* Center navbar items */
            padding: 10px 0;
        }
        .navbar-brand {
            font-weight: bold;
            color: white;
            text-align: center;
        }
        .auth-container {
            position: absolute;
            top: 30px;
            right: 30px;
            display: flex;
        }
        .auth-container a {
            margin-left: 20px;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            border-radius: 10px;
            transition: background-color 0.3s; /* Transition effect */
        }
        .auth-container a:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        .welcome-section {
            flex: 1; /* Allow this section to grow */
            display: flex;
            justify-content: center; /* Center content */
            align-items: center; /* Center vertically */
            text-align: center;
            color: white; /* Change text color for better visibility */
            padding: 20px; /* Add padding for spacing */
            background: rgba(0, 0, 0, 0.5); /* Dark overlay for readability */
        }
        .welcome-box {
            max-width: 600px; /* Limit width for better readability */
        }
        footer {
            background-color: rgba(0, 123, 255, 0.8); /* Semi-transparent footer */
            color: white;
            padding: 20px 0;
            text-align: center;
        }

    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-brand">Water Supply and Sanitation Management System</div>
        <div class="auth-container">
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn">Register</a>
        </div>
    </div>

    <div class="welcome-section">
        <div class="welcome-box">
            <h1>Welcome to the Water Supply and Sanitation Management System</h1>
            <p>Manage and track your water consumption and billing with ease.</p>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Water Billing System. All Rights Reserved.</p>
    </footer>
</body>
</html>
