<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'staff') {
    header("Location: index.php");
    exit();
}
include 'partials/_dbconnect.php';
// Include the appropriate sidebar based on the user's role
if ($_SESSION['role'] == 'admin') {
    include 'partials/_sidebaradmin.php';
} else {
    include 'partials/_sidebar.php';
}

// Fetch staff profile data
$user_id = $_SESSION['user_id']; // Assuming `user_id` is stored in the session
$profileQuery = "SELECT * FROM staff WHERE id = '$user_id'";
$profileResult = mysqli_query($conn, $profileQuery);

if (!$profileResult) {
    die("Error fetching profile: " . mysqli_error($conn));
}

$profile = mysqli_fetch_assoc($profileResult);

// Provide default values if no profile is found
if (!$profile) {
    $profile = [
        'username' => '',
        'email' => '',
        'phone' => '',
        'profile_picture' => 'uploads/default.png', // Default profile picture
        'password' => '', // Default password (hashed)
    ];
}

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password']; // Raw password
    $profile_picture = $_FILES['profile_picture'];

    // Handle profile picture upload
    if ($profile_picture['size'] > 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($profile_picture['name']);
        if (!move_uploaded_file($profile_picture['tmp_name'], $target_file)) {
            die("Error uploading profile picture.");
        }
    } else {
        $target_file = $profile['profile_picture']; // Keep the existing profile picture
    }

    // Hash the password if it is provided, otherwise keep the existing password
    $hashed_password = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : $profile['password'];

    // Update query to modify the user's profile
    $updateProfileQuery = "UPDATE staff SET 
        username = '$username',
        email = '$email',
        phone = '$phone',
        password = '$hashed_password',
        profile_picture = '$target_file'
        WHERE id = '$user_id'";

    // Execute the query and check for errors
    if (mysqli_query($conn, $updateProfileQuery)) {
        // Update the session with the new username and profile picture
        $_SESSION['user'] = $username; // Store updated username in session
        $_SESSION['profile_picture'] = $target_file;

        // Redirect with a success message
        header("Location: settings.php?success=Profile Updated");
        exit();
    } else {
        die("Error updating profile: " . mysqli_error($conn) . "<br>Query: " . $updateProfileQuery);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f4f7fa;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h4>👤 Profile Settings</h4>
            <div class="d-flex align-items-center">
                <img src="<?= htmlspecialchars($_SESSION['profile_picture']) ?>" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                <span class="ms-2">Welcome, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong></span>
                <a href="logout.php" class="btn btn-danger btn-sm ms-3">Logout</a>
            </div>
        </div>

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
            <div id="success" class="alert alert-success mt-3"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <!-- Profile Settings -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Edit Profile</div>
            <div class="card-body">
                <form method="POST" action="settings.php" enctype="multipart/form-data">
                    <div class="mb-4 text-center">
                        <img src="<?= htmlspecialchars($profile['profile_picture']) ?>" alt="Profile Picture" class="profile-picture mb-3">
                        <input type="file" name="profile_picture" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($profile['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($profile['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($profile['phone']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password (Leave blank to keep current password)</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>