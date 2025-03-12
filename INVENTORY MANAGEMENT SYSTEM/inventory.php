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
        <a href="#">🛒 Orders</a>
        <a href="#">📊 Reports</a>
        <a href="#">⚙️ Settings</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <h4>📦 Inventory Management</h4>
            <a href="logout.php" class="btn btn-dark">Logout</a>
        </div>

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
                                <td>
                                    <!-- EDIT BUTTON -->
                                    <button class="btn btn-edit btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" data-id="<?php echo $row['id']; ?>" data-product_name="<?php echo $row['product_name']; ?>" data-category="<?php echo $row['category']; ?>" data-stock="<?php echo $row['stock']; ?>" data-price="<?php echo $row['price']; ?>">✏️</button>
                                    <!-- DELETE STOCK FORM -->
                                    <form method="POST" class="d-inline-block">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_stock" class="btn btn-delete btn-sm">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label for="edit-product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="edit-product_name" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="edit-category" name="category" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="edit-stock" name="stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="edit-price" name="price" required>
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

    <script>
        var editModal = document.getElementById('editModal')
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget
            var id = button.getAttribute('data-id')
            var product_name = button.getAttribute('data-product_name')
            var category = button.getAttribute('data-category')
            var stock = button.getAttribute('data-stock')
            var price = button.getAttribute('data-price')

            var modalTitle = editModal.querySelector('.modal-title')
            var modalBodyInputId = editModal.querySelector('#edit-id')
            var modalBodyInputProductName = editModal.querySelector('#edit-product_name')
            var modalBodyInputCategory = editModal.querySelector('#edit-category')
            var modalBodyInputStock = editModal.querySelector('#edit-stock')
            var modalBodyInputPrice = editModal.querySelector('#edit-price')

            modalTitle.textContent = 'Edit Product'
            modalBodyInputId.value = id
            modalBodyInputProductName.value = product_name
            modalBodyInputCategory.value = category
            modalBodyInputStock.value = stock
            modalBodyInputPrice.value = price
        })
    </script>

</body>
</html>