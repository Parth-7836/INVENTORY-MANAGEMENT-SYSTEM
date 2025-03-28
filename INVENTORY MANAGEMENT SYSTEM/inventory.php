<?php
session_start();
include 'partials/_dbconnect.php';
include 'partials/_sidebar.php';
// Handle update stock form submission
if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $expiry_date = $_POST['expiry_date'];
    // Update the stock and price by incrementing the existing values
    $sql = "
        UPDATE inventory 
        SET 
            stock = stock + '$new_stock', 
            price = price + '$new_price' 
        WHERE id = '$id'
    ";
    $conn->query($sql);
}

// Handle delete stock form submission
if (isset($_POST['delete_stock'])) {
    $id = $_POST['id'];
    // Delete the product
    $sql = "DELETE FROM inventory WHERE id = '$id'";
    $conn->query($sql);
}

// Handle add new stock form submission
if (isset($_POST['add_stock'])) {
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $expiry_date = $_POST['expiry_date'];

    // Check if the product already exists
    $check_sql = "SELECT * FROM inventory WHERE product_name = '$product_name'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Update existing product
        $sql = "UPDATE inventory SET category = '$category', stock = '$stock', price = '$price', expiry_date = '$expiry_date' WHERE product_name = '$product_name'";
    } else {
        // Insert new product
        $sql = "INSERT INTO inventory (product_name, category, stock, price, expiry_date) VALUES ('$product_name', '$category', '$stock', '$price', '$expiry_date')";
    }
    $conn->query($sql);
}
// Fetch distinct categories for filter dropdown
$category_sql = "SELECT DISTINCT category FROM inventory";
$category_result = $conn->query($category_sql);

// Fetch inventory data with search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
// Calculate offset for pagination
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM inventory WHERE product_name LIKE '%$search%' AND category LIKE '%$filter%' LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Fetch total number of records for pagination
$total_sql = "SELECT COUNT(*) FROM inventory WHERE product_name LIKE '%$search%' AND category LIKE '%$filter%'";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);

// Fetch low-stock alerts
$low_stock_sql = "SELECT * FROM inventory WHERE stock < 15";
$low_stock_result = $conn->query($low_stock_sql);

// Fetch expiring soon products
$expiry_sql = "SELECT * FROM inventory WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
$expiry_result = $conn->query($expiry_sql);
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
        .btn-add {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
 <!-- Sidebar -->

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h4>📦 Inventory Management</h4>
            <div class="d-flex align-items-center">
                <img src="<?= htmlspecialchars($_SESSION['profile_picture']) ?>" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                <span class="ms-2">Welcome, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong></span>
                <a href="logout.php" class="btn btn-danger btn-sm ms-3">Logout</a>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by product name" value="<?php echo $search; ?>">
                </div>
                <div class="col-md-3">
                    <select name="filter" class="form-control">
                        <option>Select Category</option>
                    <option value="Antibiotics">Antibiotics</option>
                            <option value="Antivirals">Antivirals</option>
                            <option value="Pain Relievers (Analgesics)">Pain Relievers (Analgesics)</option>
                            <option value="Anti-inflammatory Drugs">Anti-inflammatory Drugs</option>
                            <option value="Cardiovascular Drugs">Cardiovascular Drugs</option>
                            <option value="Diabetes Medications">Diabetes Medications</option>
                            <option value="Neurological & Psychiatric Drugs">Neurological & Psychiatric Drugs</option>
                        <?php while ($category_row = $category_result->fetch_assoc()) { ?>
                            <option value="<?php echo $category_row['category']; ?>" <?php if ($filter == $category_row['category']) echo 'selected'; ?>><?php echo $category_row['category']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addModal">➕ Add New Stock</button>
                </div>
            </div>
        </form>

        <!-- Inventory Table -->
        <div class="card mt-3">
            <div class="card-header bg-dark text-white">
                Inventory List
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>📦 Product</th>
                            <th>🏷️ Category</th>
                            <th>📊 Stock</th>
                            <th>💲 Price</th>
                            <th>📅 Expiry Date</th>
                            <th>⚙️ Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['product_name']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['stock']; ?></td>
                                <td>$<?php echo $row['price']; ?></td>
                                <td><?php echo $row['expiry_date']; ?></td>
                                <td>
                                    <!-- EDIT BUTTON -->
                                    <button class="btn btn-edit btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" data-id="<?php echo $row['product_id']; ?>" data-product_name="<?php echo $row['product_name']; ?>" data-category="<?php echo $row['category']; ?>" data-stock="<?php echo $row['stock']; ?>" data-price="<?php echo $row['price']; ?>" data-expiry_date="<?php echo $row['expiry_date']; ?>">✏️</button>
                                    <!-- DELETE STOCK FORM -->
                                    <form method="POST" class="d-inline-block">
                                    <button type="button" class="btn btn-delete btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $row['product_id']; ?>">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center mt-4">
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&filter=<?php echo $filter; ?>"><?php echo $i; ?></a></li>
                <?php } ?>
            </ul>
        </nav>

        <!-- Low-Stock Alerts -->
        <div class="card mt-3">
            <div class="card-header bg-warning text-dark">
                Low-Stock Alerts
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>📦 Product</th>
                            <th>🏷️ Category</th>
                            <th>📊 Stock</th>
                            <th>💲 Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($low_stock_row = $low_stock_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $low_stock_row['product_name']; ?></td>
                                <td><?php echo $low_stock_row['category']; ?></td>
                                <td><?php echo $low_stock_row['stock']; ?></td>
                                <td>$<?php echo $low_stock_row['price']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Expiry Management -->
        <div class="card mt-3">
            <div class="card-header bg-danger text-white">
                Expiring Soon
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>📦 Product</th>
                            <th>🏷️ Category</th>
                            <th>📊 Stock</th>
                            <th>💲 Price</th>
                            <th>📅 Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($expiry_row = $expiry_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $expiry_row['product_name']; ?></td>
                                <td><?php echo $expiry_row['category']; ?></td>
                                <td><?php echo $expiry_row['stock']; ?></td>
                                <td>$<?php echo $expiry_row['price']; ?></td>
                                <td><?php echo $expiry_row['expiry_date']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Add New Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add-product_name" class="form-label">Product Name</label>
                        <select class="form-control" id="add-product_name" name="product_name" required>
                        <option value="Select Product">Select Product</option>
                        <option value="a">a</option>
                            <option value="b">b</option>
                            <option value="c">c</option>
                            <option value="d">d</option>
                            <option value="e">e</option>
                            <option value="f">f</option>
                            <option value="g">g</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="add-category" class="form-label">Category</label>
                        <select class="form-control" id="add-category" name="category" required>
                        <option value="Select Category">Select Category</option>
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
                        <label for="add-stock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="add-stock" name="stock" required>
                    </div>
                    <div class="mb-3">
                        <label for="add-price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="add-price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="add-expiry_date" class="form-label">Expiry Date</label>
                        <input type="date" class="form-control" id="add-expiry_date" name="expiry_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_stock" class="btn btn-primary">Add Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label for="edit-product_name" class="form-label">Product Name</label>
                        <select class="form-control" id="edit-product_name" name="product_name" required>
                        <option value="Select Product">Select Product</option>
                        <option value="a">a</option>
                            <option value="b">b</option>
                            <option value="c">c</option>
                            <option value="d">d</option>
                            <option value="e">e</option>
                            <option value="f">f</option>
                            <option value="g">g</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-category" class="form-label">Category</label>
                        <select class="form-control" id="add-category" name="category" required>
                        <option value="Select Category">Select Category</option>
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
                        <label for="edit-stock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="edit-stock" name="stock" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="edit-price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-expiry_date" class="form-label">Expiry Date</label>
                        <input type="date" class="form-control" id="edit-expiry_date" name="expiry_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_product" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this product?
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteForm">
                    <input type="hidden" name="id" id="delete-id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_stock" class="btn btn-danger">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Set the selected category in the dropdown
    var editModal = document.getElementById('editModal')
    // Show the edit modal
    editModal.addEventListener('show.bs.modal', function (event) {
        // Get the button that triggered the modal
        var button = event.relatedTarget
        var id = button.getAttribute('data-id')
        var product_name = button.getAttribute('data-product_name')
        var category = button.getAttribute('data-category')
        var stock = button.getAttribute('data-stock')
        var price = button.getAttribute('data-price')
        var expiry_date = button.getAttribute('data-expiry_date')
        // Get the modal title
        var modalTitle = editModal.querySelector('.modal-title')
        var modalBodyInputId = editModal.querySelector('#edit-id')
        var modalBodyInputProductName = editModal.querySelector('#edit-product_name')
        var modalBodyInputCategory = editModal.querySelector('#edit-category')
        var modalBodyInputStock = editModal.querySelector('#edit-stock')
        var modalBodyInputPrice = editModal.querySelector('#edit-price')
        var modalBodyInputExpiryDate = editModal.querySelector('#edit-expiry_date')

        modalTitle.textContent = 'Edit Product'
        modalBodyInputId.value = id
        modalBodyInputProductName.value = product_name
        modalBodyInputCategory.value = category
        modalBodyInputStock.value = stock
        modalBodyInputPrice.value = price
        modalBodyInputExpiryDate.value = expiry_date

        // Set the selected category in the dropdown
        var options = modalBodyInputCategory.options;
        for (var i = 0; i < options.length; i++) {
            if (options[i].value == category) {
                options[i].selected = true;
                break;
            }
        }
    })

    var deleteModal = document.getElementById('deleteModal')
    deleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget
        var id = button.getAttribute('data-id')

        var modalBodyInputId = deleteModal.querySelector('#delete-id')
        modalBodyInputId.value = id
    })
</script>