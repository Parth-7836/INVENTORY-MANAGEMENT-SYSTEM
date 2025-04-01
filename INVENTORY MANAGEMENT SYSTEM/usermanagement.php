<?php
session_start();
// Check if user is logged in and has the right role
if (!isset($_SESSION['user']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff')) {
    header("Location: index.php");
    exit();
}

include 'partials/_dbconnect.php';
if ($_SESSION['role'] == 'admin') {
    include 'partials/_sidebaradmin.php';
} else {
    include 'partials/_sidebar.php';
}

// Fetch all staff members
$staffQuery = "SELECT * FROM staff WHERE role = 'staff'";
$staffResult = mysqli_query($conn, $staffQuery);

// Fetch all admins
$adminQuery = "SELECT id, username, email, phone, role, profile_picture FROM staff WHERE role = 'admin'";
$adminResult = mysqli_query($conn, $adminQuery);

// Count staff members
$staffCountQuery = "SELECT COUNT(*) AS staff_count FROM staff WHERE role = 'staff'";
$staffCountResult = mysqli_query($conn, $staffCountQuery);
$staffCount = mysqli_fetch_assoc($staffCountResult)['staff_count'];

// Count admin members
$adminCountQuery = "SELECT COUNT(*) AS admin_count FROM staff WHERE role = 'admin'";
$adminCountResult = mysqli_query($conn, $adminCountQuery);
$adminCount = mysqli_fetch_assoc($adminCountResult)['admin_count'];

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $sql = "DELETE FROM staff WHERE id = '$deleteId'";
    mysqli_query($conn, $sql);
    header("Location: usermanagement.php?success=User Deleted");
    exit();
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    // Handle profile picture upload
    $profilePicture = $_FILES['profile_picture']['name'];
    if ($profilePicture) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($profilePicture);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile);

        $sql = "UPDATE staff SET username = '$username', email = '$email', phone = '$phone', role = '$role', profile_picture = '$targetFile' WHERE id = '$id'";
    } else {
        $sql = "UPDATE staff SET username = '$username', email = '$email', phone = '$phone', role = '$role' WHERE id = '$id'";
    }

    mysqli_query($conn, $sql);
    header("Location: usermanagement.php?success=User Updated");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="main-content">
    <div class="top-bar d-flex justify-content-between align-items-center">
    <h4>👥 User Management</h4>
        <div class="d-flex align-items-center">
                <img src="<?= htmlspecialchars($_SESSION['profile_picture']) ?>" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                <span class="ms-2">Welcome, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong></span>
                <a href="logout.php" class="btn btn-danger btn-sm ms-3">Logout</a>
            </div>
    </div>
        

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
            <div id="success" class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <!-- Admin Table -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">Admin Members</div>
            <div class="card-body">
                <h5>Total Admin Members: <?= $adminCount ?></h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SrNo</th>
                            <th>Profile Picture</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    $adminSequence = 1; // Initialize sequence number for Admin table
    while ($row = mysqli_fetch_assoc($adminResult)): ?>
    <tr>
        <td><?= $adminSequence++ ?></td> <!-- Display sequence number -->
        <td>
            <?php if ($row['profile_picture']): ?>
                <img src="<?= $row['profile_picture'] ?>" alt="Profile Picture" style="width: 50px; height: 50px; border-radius: 50%;">
            <?php else: ?>
                <img src="uploads/default.png" alt="Default Picture" style="width: 50px; height: 50px; border-radius: 50%;">
            <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($row['username']) ?></td> <!-- Display username -->
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['phone']) ?></td> <!-- Display phone -->
        <td>
            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                data-id="<?= $row['id'] ?>"
                data-username="<?= htmlspecialchars($row['username']) ?>"
                data-email="<?= htmlspecialchars($row['email']) ?>"
                data-mobile="<?= htmlspecialchars($row['phone']) ?>"
                data-role="admin"
                data-profile-picture="<?= htmlspecialchars($row['profile_picture']) ?>">
                Edit
            </button>
            <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal"
            data-id="<?= $row['id'] ?>">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</tbody>
                </table>
            </div>
        </div>

        <!-- Staff Table -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Staff Members</div>
            <div class="card-body">
                <h5>Total Staff Members: <?= $staffCount ?></h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SrNo</th>
                            <th>Profile Picture</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    $staffSequence = 1; // Initialize sequence number for Staff table
    while ($row = mysqli_fetch_assoc($staffResult)): ?>
    <tr>
        <td><?= $staffSequence++ ?></td> <!-- Display sequence number -->
        <td>
            <?php if ($row['profile_picture']): ?>
                <img src="<?= $row['profile_picture'] ?>" alt="Profile Picture" style="width: 50px; height: 50px; border-radius: 50%;">
            <?php else: ?>
                <img src="uploads/default.png" alt="Default Picture" style="width: 50px; height: 50px; border-radius: 50%;">
            <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['phone']) ?></td>
        <td>
            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                data-id="<?= $row['id'] ?>"
                data-username="<?= htmlspecialchars($row['username']) ?>"
                data-email="<?= htmlspecialchars($row['email']) ?>"
                data-mobile="<?= htmlspecialchars($row['phone']) ?>"
                data-role="staff"
                data-profile-picture="<?= htmlspecialchars($row['profile_picture']) ?>">
                Edit
            </button>
            <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal"
            data-id="<?= $row['id'] ?>">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="usermanagement.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label for="edit-username" class="form-label">Username</label>
                        <input type="text" name="username" id="edit-username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-email" class="form-label">Email</label>
                        <input type="email" name="email" id="edit-email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-mobile" class="form-label">Mobile</label>
                        <input type="text" name="phone" id="edit-mobile" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-role" class="form-label">Role</label>
                        <select name="role" id="edit-role" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-profile-picture" class="form-label">Profile Picture</label>
                        <input type="file" name="profile_picture" id="edit-profile-picture" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_user" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Delete User Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="usermanagement.php">
                <div class="modal-body">
                    <p>Are you sure you want to delete this user?</p>
                    <input type="hidden" name="delete_id" id="delete-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Populate Edit Modal
    const editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const username = button.getAttribute('data-username');
        const email = button.getAttribute('data-email');
        const mobile = button.getAttribute('data-mobile');
        const role = button.getAttribute('data-role');
        const profilePicture = button.getAttribute('data-profile-picture');

        document.getElementById('edit-id').value = id;
        document.getElementById('edit-username').value = username;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-mobile').value = mobile;
        document.getElementById('edit-role').value = role;
        document.getElementById('edit-profile-picture').value = profilePicture;
    });
    // Populate Delete Modal
const deleteModal = document.getElementById('deleteModal');
deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget; // Button that triggered the modal
    const id = button.getAttribute('data-id'); // Extract user ID

    // Populate the hidden input field in the modal
    document.getElementById('delete-id').value = id;
});
    // Hide success message after 3 seconds
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            let alertBox = document.getElementById("success");
            if (alertBox) {
                alertBox.style.transition = "opacity 0.5s";
                alertBox.style.opacity = "0";
                setTimeout(() => alertBox.remove(), 500); // Remove element after fade out
            }
        }, 2000); // 3 seconds delay
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>