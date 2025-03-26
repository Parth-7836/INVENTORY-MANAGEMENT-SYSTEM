<?php
session_start();
include 'partials/_dbconnect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $profile_picture = $_FILES['profile_picture'];

    // Check if the username or email already exists in the staff table
    $sql = "SELECT id FROM staff WHERE username = '$username' OR email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Staff user exists, set error message
        $error = "Username or Email already exists. Please login.";
    } else {
        // Handle profile picture upload
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($profile_picture['name']);
        if ($profile_picture['size'] > 0) {
            move_uploaded_file($profile_picture['tmp_name'], $target_file);
        } else {
            $target_file = "uploads/default.png"; // Default profile picture
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert the staff user into the staff table
        $sql = "INSERT INTO staff (username, password, email, phone, role, profile_picture) 
                VALUES ('$username', '$hashed_password', '$email', '$phone', 'staff', '$target_file')";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php?message=Registration successful. Please login.");
            exit();
        } else {
            $error = "Error registering user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 400px;">
        <h4 class="text-center mb-3">Register as Staff</h4>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php } ?>

        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" maxlength="10" name="phone" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Profile Picture</label>
                <input type="file" name="profile_picture" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
            <div class="text-center mt-3">
                Already have an account? <a href="index.php">Login</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>