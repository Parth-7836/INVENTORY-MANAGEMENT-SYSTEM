<?php
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff')) {
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

// Handle new warehouse creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_warehouse'])) {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $expiry_date = $_POST['expiry_date'];

    $sql = "INSERT INTO warehouses (name, location, category, stock, expiry_date) 
            VALUES ('$name', '$location', '$category', '$stock', '$expiry_date')";
    mysqli_query($conn, $sql);
    header("Location: warehouse.php?success=Warehouse Added");
    exit();
}

// Handle warehouse update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_warehouse'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $expiry_date = $_POST['expiry_date'];
    $sql = "UPDATE warehouses SET name='$name', location='$location', category='$category', stock='$stock', expiry_date='$expiry_date' WHERE id='$id'";
    mysqli_query($conn, $sql);
    header("Location: warehouse.php?success=Warehouse Updated");
    exit();
}

// Handle warehouse deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_warehouse'])) {
    $id = $_POST['delete_id'];

    $sql = "DELETE FROM warehouses WHERE id='$id'";
    mysqli_query($conn, $sql);
    header("Location: warehouse.php?success=Warehouse Deleted");
    exit();
}

// Fetch warehouse data
$warehouseQuery = "SELECT * FROM warehouses";
$warehouseResult = mysqli_query($conn, $warehouseQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Warehouse Management</title>
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
        .table thead {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
<div class="main-content">
    <div class="top-bar d-flex justify-content-between align-items-center">
        <h4>🏬 Warehouse</h4>
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

    <!-- Add New Warehouse -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">➕ Add New Warehouse</div>
        <div class="card-body">
            <form method="POST" action="warehouse.php">
                <div class="mb-3">
                    <label for="name" class="form-label">Warehouse Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" name="location" id="location" class="form-control" required>
                </div>
                <div class="mb-3">
                        <label for="add-category" class="form-label">Category</label>
                        <select class="form-control" id="add-category" name="category" required>
                        <option>Select Category</option>
                        <option value="Antibiotics">Antibiotics</option>
                            <option value="Antivirals">Antivirals</option>
                            <option value="Pain Relievers (Analgesics)">Pain Relievers (Analgesics)</option>
                            <option value="Anti-inflammatory Drugs">Anti-inflammatory Drugs</option>
                            <option value="Cardiovascular Drugs">Cardiovascular Drugs</option>
                            <option value="Diabetes Medications">Diabetes Medications</option>
                            <option value="Neurological & Psychiatric Drugs">Neurological & Psychiatric Drugs</option>
                        </select>
                    </div>
                <div class="mb-3">
                    <label for="total_quantity" class="form-label">Stock</label>
                    <input type="number" name="total_quantity" id="total_quantity" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="expiry_date" class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" id="expiry_date" class="form-control" required>
                </div>
                <button type="submit" name="add_warehouse" class="btn btn-primary">Add Warehouse</button>
            </form>
        </div>
    </div>

    <!-- Stock Distribution Across Warehouses -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">📦 Stock Distribution</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Warehouse Name</th>
                        <th>Location</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Expiry</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($warehouseResult)): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['location'] ?></td>
                        <td><?= $row['category'] ?></td>
                        <td><?= $row['stock'] ?></td>
                        <td><?= $row['expiry_date'] ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="<?= $row['id'] ?>"
                                data-name="<?= $row['name'] ?>"
                                data-location="<?= $row['location'] ?>"
                                data-total-products="<?= $row['category'] ?>"
                                data-total-quantity="<?= $row['stock'] ?>"
                                data-expiry-date="<?= $row['expiry_date'] ?>">
                                Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                data-id="<?= $row['id'] ?>">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Warehouse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="warehouse.php">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Warehouse Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-location" class="form-label">Location</label>
                        <input type="text" name="location" id="edit-location" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="add-category" class="form-label">Category</label>
                        <select class="form-control" id="add-category" name="category" required>
                        <option>Select Category</option>
                        <option value="Antibiotics">Antibiotics</option>
                            <option value="Antivirals">Antivirals</option>
                            <option value="Pain Relievers (Analgesics)">Pain Relievers (Analgesics)</option>
                            <option value="Anti-inflammatory Drugs">Anti-inflammatory Drugs</option>
                            <option value="Cardiovascular Drugs">Cardiovascular Drugs</option>
                            <option value="Diabetes Medications">Diabetes Medications</option>
                            <option value="Neurological & Psychiatric Drugs">Neurological & Psychiatric Drugs</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-total-quantity" class="form-label">Stock</label>
                        <input type="number" name="stock" id="edit-total-quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-expiry-date" class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" id="edit-expiry-date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_warehouse" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this warehouse?
            </div>
            <div class="modal-footer">
                <form method="POST" action="warehouse.php">
                    <input type="hidden" name="delete_id" id="delete-id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_warehouse" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Populate Edit Modal
    var editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var name = button.getAttribute('data-name');
        var location = button.getAttribute('data-location');
        var totalProducts = button.getAttribute('data-total-products');
        var totalQuantity = button.getAttribute('data-total-quantity');
        var expiryDate = button.getAttribute('data-expiry-date');

        document.getElementById('edit-id').value = id;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-location').value = location;
        document.getElementById('edit-total-products').value = totalProducts;
        document.getElementById('edit-total-quantity').value = totalQuantity;
        document.getElementById('edit-expiry-date').value = expiryDate;
    });

    // Populate Delete Modal
    var deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
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
</body>
</html>