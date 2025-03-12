<?php
include 'partials/_dbconnect.php';
// Handle update stock form submission
if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $sql = "UPDATE inventory SET product_name = '$product_name', category = '$category', stock = '$stock', price = '$price' WHERE id = '$id'";
    $conn->query($sql);
}

// Handle delete stock form submission
if (isset($_POST['delete_stock'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM inventory WHERE id = '$id'";
    $conn->query($sql);
}

// Fetch inventory data
$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
            transition: all 0.3s;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .main-content {
            margin-left: 260px;
            padding: 30px; /* Increased padding */
        }
        .top-bar {
            background-color: white;
            padding: 20px; /* Increased padding */
            box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px; /* Added margin */
        }
        .btn-edit {
            background-color: #007bff;
            color: white;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center text-white">📦 Staff Dashboard</h4>
        <hr class="text-white">
        <a href="staff_dashboard.php">🏠 Dashboard</a>
        <a href="inventory.php">📦 Inventory</a>
        <a href="orders.php">🛒 Orders</a>
        <a href="#">📊 Reports</a>
        <a href="#">⚙️ Settings</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h4>📦 Inventory Management</h4>
            <a href="logout.php" class="btn btn-dark">Logout</a>
        </div>
        <?php
        include 'partials/_dbconnect.php';
// Database connection

// Filter orders by status
$where_clause = "";
if (isset($_GET['status']) && $_GET['status'] != "") {
    $status = $_GET['status'];
    $where_clause = "WHERE order_status = '$status'";
}

// Fetch orders
$sql = "SELECT * FROM orders $where_clause ORDER BY id DESC";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h3>📦 View Orders</h3>

    <!-- Filter Orders -->
    <form method="GET" class="mb-3">
        <select name="status" class="form-control d-inline-block w-25">
            <option value="">📂 All Orders</option>
            <option value="Pending">🟡 Pending</option>
            <option value="Completed">✅ Completed</option>
            <option value="Canceled">❌ Canceled</option>
        </select>
        <button type="submit" class="btn btn-primary">🔍 Filter</button>
    </form>

    <!-- Orders Table -->
    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>👤 Customer</th>
                <th>📦 Product</th>
                <th>🔢 Quantity</th>
                <th>💲 Total Price</th>
                <th>📌 Status</th>
                <th>✏️ Update</th>
                <th>🗑️ Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['customer_name']; ?></td>
                    <td><?= $row['product_name']; ?></td>
                    <td><?= $row['quantity']; ?></td>
                    <td>$<?= $row['total_price']; ?></td>
                    <td><span class="badge badge-<?= ($row['order_status'] == 'Pending') ? 'warning' : (($row['order_status'] == 'Completed') ? 'success' : 'danger'); ?>">
                        <?= $row['order_status']; ?>
                    </span></td>
                    
                    <!-- Update Order Status -->
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <select name="order_status" class="form-control d-inline-block w-50">
                                <option value="Pending" <?= ($row['order_status'] == 'Pending') ? 'selected' : ''; ?>>🟡 Pending</option>
                                <option value="Completed" <?= ($row['order_status'] == 'Completed') ? 'selected' : ''; ?>>✅ Completed</option>
                                <option value="Canceled" <?= ($row['order_status'] == 'Canceled') ? 'selected' : ''; ?>>❌ Canceled</option>
                            </select>
                            <button type="submit" name="update_order" class="btn btn-warning btn-sm">✔️ Update</button>
                        </form>
                    </td>

                    <!-- Delete Order -->
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <button type="submit" name="delete_order" class="btn btn-danger btn-sm">🗑️ Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
