<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
include 'partials/_dbconnect.php';
include 'partials/_sidebar.php';

// Fetch total products and their categories
$productsQuery = "SELECT product_name, category_name FROM products";
$productsResult = mysqli_query($conn, $productsQuery);

// Fetch total products
$totalProductsQuery = "SELECT COUNT(*) AS total_products FROM products";
$totalProductsResult = mysqli_query($conn, $totalProductsQuery);
$totalProducts = mysqli_fetch_assoc($totalProductsResult)['total_products'];

// Fetch low stock products (JOIN products and inventory tables)
$lowStockQuery = "
     SELECT p.product_name, i.stock
    FROM products p
    JOIN inventory i ON p.id = i.product_id
    WHERE i.stock < 15";
$lowStockResult = mysqli_query($conn, $lowStockQuery);
$lowStockData = [];
while ($row = mysqli_fetch_assoc($lowStockResult)) {
    $lowStockData[] = $row;
}

// Fetch products nearing expiration (JOIN products and inventory tables)
$nearExpiryQuery = "
    SELECT p.product_name, i.expiry_date
    FROM products p
    JOIN inventory i ON p.id = i.product_id
    WHERE i.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
$nearExpiryResult = mysqli_query($conn, $nearExpiryQuery);
$nearExpiryData = [];
while ($row = mysqli_fetch_assoc($nearExpiryResult)) {
    $nearExpiryData[] = $row;
}
// Debugging: Check if data is being fetched correctly
// Uncomment the following lines to debug the data
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .chart-container {
            width: 100%;
            height: 400px;
        }
        .table-container {
            margin-top: 20px;
            overflow-x: auto;
        }
        .table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar d-flex justify-content-between align-items-center p-3 bg-light shadow-sm">
            <h4>📊 Dashboard Overview</h4>
            <div class="d-flex align-items-center">
                <img src="<?= htmlspecialchars($_SESSION['profile_picture']) ?>" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                <span class="ms-2">Welcome, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong></span>
                <a href="logout.php" class="btn btn-danger btn-sm ms-3">Logout</a>
            </div>
        </div>

        <div class="container mt-4">
            <!-- Summary Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Total Products</h5>
                            <p class="card-text display-4"><?= $totalProducts ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Low Stock Alerts</h5>
                            <p class="card-text display-4"><?= count($lowStockData) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Expiring Soon</h5>
                            <p class="card-text display-4"><?= count($nearExpiryData) ?></p>
                        </div>
                    </div>
                </div>
            </div>

           <!-- Charts -->
           <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Low Stock Products</h5>
                            <div class="chart-container">
                                <canvas id="lowStockChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Products Nearing Expiration</h5>
                            <div class="chart-container">
                                <canvas id="nearExpiryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Products and Categories Table -->
            <div class="table-container">
                <h5 class="mb-3">📋 Total Products and Categories</h5>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $counter = 1;
                        while ($row = mysqli_fetch_assoc($productsResult)) {
                            echo "<tr>";
                            echo "<td>" . $counter++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Low Stock Chart Data
        const lowStockLabels = <?= json_encode(array_column($lowStockData, 'product_name')) ?>;
        const lowStockValues = <?= json_encode(array_column($lowStockData, 'stock')) ?>;

        const lowStockChart = new Chart(document.getElementById('lowStockChart'), {
            type: 'bar',
            data: {
                labels: lowStockLabels,
                datasets: [{
                    label: 'Stock Quantity',
                    data: lowStockValues,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    borderRadius: 5,
                    hoverBackgroundColor: 'rgba(75, 192, 192, 0.4)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        // Near Expiry Chart Data
        const nearExpiryLabels = <?= json_encode(array_column($nearExpiryData, 'product_name')) ?>;
        const nearExpiryValues = <?= json_encode(array_map(function($row) {
            return (new DateTime($row['expiry_date']))->diff(new DateTime())->days;
        }, $nearExpiryData)) ?>;

        const nearExpiryChart = new Chart(document.getElementById('nearExpiryChart'), {
            type: 'line',
            data: {
                labels: nearExpiryLabels,
                datasets: [{
                    label: 'Days to Expiry',
                    data: nearExpiryValues,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>