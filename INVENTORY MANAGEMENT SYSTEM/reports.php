<?php
session_start();
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

// Fetch total sales
$totalSalesQuery = "
    SELECT SUM(stock * price) AS total_sales
    FROM inventory";
$totalSalesResult = mysqli_query($conn, $totalSalesQuery);
$totalSales = mysqli_fetch_assoc($totalSalesResult)['total_sales'] ?? 0;

// Fetch top-selling products
$topSellingProductsQuery = "
    SELECT product_name, category, stock, price, (stock * price) AS total_revenue
    FROM inventory
    ORDER BY total_revenue DESC
    LIMIT 5";
$topSellingProductsResult = mysqli_query($conn, $topSellingProductsQuery);

// Fetch sales by category
$salesByCategoryQuery = "
    SELECT category, SUM(stock) AS total_quantity, SUM(stock * price) AS total_revenue
    FROM inventory
    GROUP BY category
    ORDER BY total_revenue DESC";
$salesByCategoryResult = mysqli_query($conn, $salesByCategoryQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="main-content">
        <div class="top-bar d-flex justify-content-between align-items-center">
        <h4>📊 Sales Reports</h4>
        </div>
        <!-- Total Sales -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Total Sales</div>
            <div class="card-body">
                <h5>Total Sales: $<?= number_format($totalSales, 2) ?></h5>
            </div>
        </div>

        <!-- Top-Selling Products -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">Top-Selling Products</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($topSellingProductsResult)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= htmlspecialchars($row['category']) ?></td>
                            <td><?= $row['stock'] ?></td>
                            <td>$<?= number_format($row['price'], 2) ?></td>
                            <td>$<?= number_format($row['total_revenue'], 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sales by Category -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-white">Sales by Category</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Total Quantity</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($salesByCategoryResult)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['category']) ?></td>
                            <td><?= $row['total_quantity'] ?></td>
                            <td>$<?= number_format($row['total_revenue'], 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>