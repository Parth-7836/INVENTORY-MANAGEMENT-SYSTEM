<?php
session_start();
// Check if user is logged in and is a staff member
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'staff') {
    header("Location: index.php");
    exit();
}
include 'partials/_dbconnect.php';
include 'partials/_sidebar.php';
?>
<body>
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h4>📊 Dashboard Overview</h4>
        </div>

        <div class="container mt-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Total Products</h5>
                            <p class="card-text">1,250</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Low Stock Alerts</h5>
                            <p class="card-text">24 Items</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Pending Orders</h5>
                            <p class="card-text">5 Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Expiring Soon</h5>
                            <p class="card-text">10 Products</p>
                        </div>
                    </div>
                </div>
            </div>
</body>
</html>