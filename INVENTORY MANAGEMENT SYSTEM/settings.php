<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'staff') {
    header("Location: index.php");
    exit();
}
include 'partials/_dbconnect.php';
include 'partials/_sidebar.php';

// Fetch settings data
$settingsQuery = "SELECT * FROM settings LIMIT 1";
$settingsResult = mysqli_query($conn, $settingsQuery);
$settings = mysqli_fetch_assoc($settingsResult);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings & Configuration</title>
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
    </style>
</head>
<body>
<div class="main-content">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h4>⚙️Settings and Configurations
            </h4>
        </div>
        
        <!-- Business Settings -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">🏢 Business Settings</div>
            <div class="card-body">
                <form method="POST" action="update_settings.php">
                    <div class="mb-3">
                        <label class="form-label">Business Name</label>
                        <input type="text" class="form-control" name="business_name" value="<?= $settings['business_name'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Business Email</label>
                        <input type="email" class="form-control" name="business_email" value="<?= $settings['business_email'] ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
        
        <!-- Notification Preferences -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">🔔 Notification Preferences</div>
            <div class="card-body">
                <form method="POST" action="update_settings.php">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="email_notifications" <?= $settings['email_notifications'] ? 'checked' : '' ?>>
                        <label class="form-check-label">Enable Email Notifications</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="sms_notifications" <?= $settings['sms_notifications'] ? 'checked' : '' ?>>
                        <label class="form-check-label">Enable SMS Notifications</label>
                    </div>
                    <button type="submit" class="btn btn-success mt-3">Save Preferences</button>
                </form>
            </div>
        </div>
</body>
</html>
 
 