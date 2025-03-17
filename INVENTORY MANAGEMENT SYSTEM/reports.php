<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'staff') {
    header("Location: index.php");
    exit();
}
include 'partials/_dbconnect.php';
include 'partials/_sidebar.php';

// Fetch total sales and revenue
$salesQuery = "SELECT SUM(quantity_sold) AS total_sales, SUM(total_revenue) AS total_revenue FROM sales";
$salesResult = mysqli_query($conn, $salesQuery);
$salesData = mysqli_fetch_assoc($salesResult);

// Fetch fast-moving & slow-moving products
$fastMovingQuery = "SELECT product_name, SUM(sold) AS total_sold FROM inventory GROUP BY product_name ORDER BY total_sold DESC LIMIT 5";
$slowMovingQuery = "SELECT product_name, SUM(sold) AS total_sold FROM inventory GROUP BY product_name ORDER BY total_sold ASC LIMIT 5";
$fastMovingResult = mysqli_query($conn, $fastMovingQuery);
$slowMovingResult = mysqli_query($conn, $slowMovingQuery);

// Fetch financial reports
$financeQuery = "SELECT * FROM financial_reports ORDER BY report_date DESC LIMIT 1";
$financeResult = mysqli_query($conn, $financeQuery);
$financeData = mysqli_fetch_assoc($financeResult);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reports & Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h4>📊 Reports and Analytics</h4>
        </div>
            <!-- Sales Report -->
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Sales</h5>
                        <p class="card-text"><?= $salesData['total_sales'] ?? 0 ?> Items</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <p class="card-text">$<?= number_format($salesData['total_revenue'] ?? 0, 2) ?></p>
                    </div>
                </div>
            </div>

            <!-- Fast-Moving Products -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">🔥 Fast-Moving Products</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php while ($row = mysqli_fetch_assoc($fastMovingResult)): ?>
                                <li class="list-group-item"><?= $row['product_name'] ?> - Sold: <?= $row['total_sold'] ?></li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Slow-Moving Products -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header bg-warning text-white">🐌 Slow-Moving Products</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php while ($row = mysqli_fetch_assoc($slowMovingResult)): ?>
                                <li class="list-group-item"><?= $row['product_name'] ?> - Sold: <?= $row['total_sold'] ?></li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Financial Reports -->
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header bg-dark text-white">💰 Financial Report (Latest)</div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Total Income</th>
                                    <th>Total Expense</th>
                                    <th>Net Profit</th>
                                    <th>Report Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>$<?= number_format($financeData['total_income'] ?? 0, 2) ?></td>
                                    <td>$<?= number_format($financeData['total_expense'] ?? 0, 2) ?></td>
                                    <td class="<?= ($financeData['net_profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                        $<?= number_format($financeData['net_profit'] ?? 0, 2) ?>
                                    </td>
                                    <td><?= $financeData['report_date'] ?? 'N/A' ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
