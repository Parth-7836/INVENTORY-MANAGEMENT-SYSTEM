<?php
session_start();
include 'partials/_dbconnect.php';

// Function to check if an admin user exists
function userExists($conn, $email) {
    $sql = "SELECT id FROM staff WHERE email = '$email'";
    $result = $conn->query($sql);
    // Return true if user exists, false otherwise
    return $result->num_rows > 0;
}

// Auto-create Admin if not exists
if (!userExists($conn, "admin@example.com")) {
    $admin_pass = password_hash("admin123", PASSWORD_BCRYPT);
    $conn->query("INSERT INTO staff (username,email,phone, password, role) VALUES ('admin','admin@example.com','9999999999','$admin_pass', 'admin')");
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Query to fetch user details by email
    $loginQuery = "SELECT * FROM staff WHERE email = '$email'";
    $result = mysqli_query($conn, $loginQuery);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id']; // Store user ID in session
            $_SESSION['user'] = $user['username']; // Store username in session
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_picture'] = $user['profile_picture'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
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
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
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