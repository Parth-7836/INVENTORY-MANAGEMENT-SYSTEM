<?php
session_start();
include 'partials/_dbconnect.php';

// Function to check if a user exists
function userExists($conn, $username) {
    $sql = "SELECT id FROM staff WHERE username = '$username'";
    $result = $conn->query($sql);
    // Return true if user exists, false otherwise
    return $result->num_rows > 0;
}

// Auto-create Admin if not exists
if (!userExists($conn, "admin")) {
    $admin_pass = password_hash("admin123", PASSWORD_BCRYPT);
    $conn->query("INSERT INTO staff (username, password, role) VALUES ('admin', '$admin_pass', 'admin')");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    // Check if the user exists
    $sql = "SELECT * FROM staff WHERE username = '$username'";
    $result = $conn->query($sql);
    // If user exists, verify the password
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == "admin") {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: staff_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 350px;">
        <h4 class="text-center mb-3">Login</h4>
        
        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php } ?>
        
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
            <div class="text-center mt-3">
                Don't have an account? <a href="register.php">Register</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>