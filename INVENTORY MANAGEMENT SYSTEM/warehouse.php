<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'staff') {
    header("Location: index.php");
    exit();
}
include 'partials/_dbconnect.php';
include 'partials/_sidebar.php';

// Handle new warehouse creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_warehouse'])) {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $total_products = $_POST['total_products'];
    $total_quantity = $_POST['total_quantity'];
    $expiry_date = $_POST['expiry_date'];

    $sql = "INSERT INTO warehouses (name, location, total_products, total_quantity, expiry_date) 
            VALUES ('$name', '$location', '$total_products', '$total_quantity', '$expiry_date')";
    mysqli_query($conn, $sql);
    header("Location: warehouse.php?success=Warehouse Added");
    exit();
}

// Handle warehouse update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_warehouse'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $total_products = $_POST['total_products'];
    $total_quantity = $_POST['total_quantity'];
    $expiry_date = $_POST['expiry_date'];

    $sql = "UPDATE warehouses SET name='$name', location='$location', total_products='$total_products', 
            total_quantity='$total_quantity', expiry_date='$expiry_date' WHERE id='$id'";
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
    </div>

    <!-- Success Message -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
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
                    <label for="total_products" class="form-label">Total Products</label>
                    <input type="number" name="total_products" id="total_products" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="total_quantity" class="form-label">Total Quantity</label>
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
                        <th>Total Products</th>
                        <th>Total Quantity</th>
                        <th>Expiry</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($warehouseResult)): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['location'] ?></td>
                        <td><?= $row['total_products'] ?></td>
                        <td><?= $row['total_quantity'] ?></td>
                        <td><?= $row['expiry_date'] ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="<?= $row['id'] ?>"
                                data-name="<?= $row['name'] ?>"
                                data-location="<?= $row['location'] ?>"
                                data-total-products="<?= $row['total_products'] ?>"
                                data-total-quantity="<?= $row['total_quantity'] ?>"
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
                        <label for="edit-total-products" class="form-label">Total Products</label>
                        <input type="number" name="total_products" id="edit-total-products" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-total-quantity" class="form-label">Total Quantity</label>
                        <input type="number" name="total_quantity" id="edit-total-quantity" class="form-control" required>
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
</script>
</body>
</html>