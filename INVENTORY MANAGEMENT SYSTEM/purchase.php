<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'staff') {
    header("Location: index.php");
    exit();
}
include 'partials/_dbconnect.php';
include 'partials/_sidebar.php';
// Handle new purchase order creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_id = $_POST['supplier_id'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    
    $sql = "INSERT INTO purchase_orders (supplier_id, product_name, quantity, price) 
            VALUES ('$supplier_id', '$product_name', '$quantity', '$price')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: purchase.php?success=Purchase Order Created");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch purchase orders
$orders = mysqli_query($conn, "SELECT purchase_orders.*, suppliers.name AS supplier_name 
                                FROM purchase_orders 
                                JOIN suppliers ON purchase_orders.supplier_id = suppliers.id 
                                ORDER BY created_at DESC");

// Fetch suppliers for dropdown
$suppliers = mysqli_query($conn, "SELECT * FROM suppliers");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purchase Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h4>📋 View Purchases</h4>
        </div>

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= $_GET['success'] ?></div>
        <?php endif; ?>

        <!-- Create New Purchase Order -->
        <div class="card mb-4">
            <div class="card-header">Create New Purchase Order</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">Select Supplier</option>
                            <?php while ($row = mysqli_fetch_assoc($suppliers)): ?>
                                <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Purchase Order</button>
                </form>
            </div>
        </div>

        <!-- Purchase Orders Table -->
        <div class="card">
            <div class="card-header">Purchase Orders List</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($orders)): ?>
                        <tr>
                            <td><?= $row['supplier_name'] ?></td>
                            <td><?= $row['product_name'] ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td>$<?= $row['price'] ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $row['status'] == 'Pending' ? 'warning' : 
                                    ($row['status'] == 'Processing' ? 'primary' : 'success') ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td>
                                <!-- Update Status -->
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown">Update Status</button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="?order_id=<?= $row['id'] ?>&update_status=Processing">Processing</a></li>
                                        <li><a class="dropdown-item" href="?order_id=<?= $row['id'] ?>&update_status=Received">Received</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>
