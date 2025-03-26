<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Dashboard - Inventory Management</title>
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
    <h4 class="text-white">📦 Staff Dashboard</h4>
    <hr class="text-white">
    <a href="staff_dashboard.php">🏠 Dashboard</a>
    <a href="inventory.php">📦 Inventory</a>
    <a href="#" class="text-white" data-bs-toggle="collapse" data-bs-target="#purchaseMenu" aria-expanded="false">
        📑 Purchase Management
    </a>
    <div id="purchaseMenu" class="collapse">
        <a href="purchase.php" class="ms-3">📋 View Purchases</a>
        <a href="suppliers.php" class="ms-3">🏭 Suppliers</a>
    </div>
    <a href="warehouse.php">🏬 Warehouse</a>
    <a href="settings.php">⚙️ Settings</a>
</div>

</body>
</html>
