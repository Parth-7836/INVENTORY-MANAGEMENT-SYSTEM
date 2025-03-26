<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'staff') {
    header("Location: index.php");
    exit();
}
include 'partials/_dbconnect.php';
include 'partials/_sidebar.php';
// Handle new supplier creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete_id']) && !isset($_POST['update_supplier'])) {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    
    $sql = "INSERT INTO suppliers (name, contact, email, address) VALUES ('$name', '$contact', '$email', '$address')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: suppliers.php?success=Supplier Added");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle supplier update
if (isset($_POST['update_supplier'])) {
    $id = $_POST['edit_id'];
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $sql = "UPDATE suppliers SET name='$name', contact='$contact', email='$email', address='$address' WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: suppliers.php?success=Supplier Updated");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle supplier deletion
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    $sql = "DELETE FROM suppliers WHERE id = $delete_id";

    if (mysqli_query($conn, $sql)) {
        header("Location: suppliers.php?success=Supplier Deleted");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch suppliers
$suppliers = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supplier Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h4>🏭 Suppliers</h4>
            <div class="d-flex align-items-center">
                <img src="<?= htmlspecialchars($_SESSION['profile_picture']) ?>" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                <span class="ms-2">Welcome, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong></span>
                <a href="logout.php" class="btn btn-danger btn-sm ms-3">Logout</a>
            </div>
        </div>

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
            <div id="success" class="alert alert-success"><?= $_GET['success'] ?></div>
        <?php endif; ?>

        <!-- Add Supplier Form -->
        <div class="card mb-4">
            <div class="card-header">Add New Supplier</div>
            <div class="card-body">
                <form method="POST" action="suppliers.php">
                    <div class="mb-3">
                        <label for="name" class="form-label">Supplier Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact</label>
                        <input type="text" name="contact" id="contact" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" id="address" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Supplier</button>
                </form>
            </div>
        </div>

        <!-- Supplier Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($suppliers)): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['contact'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['address'] ?></td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                            data-id="<?= $row['id'] ?>"
                            data-name="<?= $row['name'] ?>"
                            data-contact="<?= $row['contact'] ?>"
                            data-email="<?= $row['email'] ?>"
                            data-address="<?= $row['address'] ?>">
                            Edit
                        </button>
                        <form method="POST" action="suppliers.php" class="d-inline">
                            <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this supplier?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Supplier Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="suppliers.php">
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit-id">
                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Supplier Name</label>
                            <input type="text" name="name" id="edit-name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-contact" class="form-label">Contact</label>
                            <input type="text" name="contact" id="edit-contact" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-email" class="form-label">Email</label>
                            <input type="email" name="email" id="edit-email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-address" class="form-label">Address</label>
                            <textarea name="address" id="edit-address" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update_supplier" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        var editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var contact = button.getAttribute('data-contact');
            var email = button.getAttribute('data-email');
            var address = button.getAttribute('data-address');

            var modalId = editModal.querySelector('#edit-id');
            var modalName = editModal.querySelector('#edit-name');
            var modalContact = editModal.querySelector('#edit-contact');
            var modalEmail = editModal.querySelector('#edit-email');
            var modalAddress = editModal.querySelector('#edit-address');

            modalId.value = id;
            modalName.value = name;
            modalContact.value = contact;
            modalEmail.value = email;
            modalAddress.value = address;
        });
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
</body>
</html>