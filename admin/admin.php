<?php

session_start();
include '../database/db_connect.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'others'
                . DIRECTORY_SEPARATOR . 'uploads';
            $uploadUrlBase = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\')
                . '/others/uploads/';

            if (
                !is_dir($uploadDirectory) &&
                !mkdir($uploadDirectory, 0775, true) &&
                !is_dir($uploadDirectory)
            ) {
                $error = "Unable to create the product image upload directory.";
            } else {
                $destinationPath = $uploadDirectory . DIRECTORY_SEPARATOR . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                    $imageUrl = $uploadUrlBase . $newFileName;
                    $stmt = $pdo->prepare(
                        "INSERT INTO products (name, image_url, price, description)
                         VALUES (?, ?, ?, ?)"
                    );

                    if ($stmt->execute([$name, $imageUrl, $price, $description])) {
                        $message = "Tea product added successfully!";
                    } else {
                        $error = "Database insertion failed.";
                    }
                } else {
                    $error = "Error moving uploaded file to destination directory.";
                }
            }
        } else {
            $error = "Upload failed. Allowed image formats: jpg, jpeg, png, webp.";
        }
    } else {
        $error = "Please upload a product image.";
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $imagePath = $row['image_url'];
        $uploadUrlBase = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\')
            . '/others/uploads/';

        if (strpos($imagePath, $uploadUrlBase) === 0) {
            $uploadedFilePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'others'
                . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR
                . basename($imagePath);

            if (is_file($uploadedFilePath)) {
                unlink($uploadedFilePath);
            }
        }
    }

    $delete_stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");

    if ($delete_stmt->execute([$id])) {
        $message = "Product deleted successfully!";
    } else {
        $error = "Failed to delete product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CeylonLeaf</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php include '../components/navbar.php'; ?>

<div class="admin-container">
    <h1 class="admin-title">Admin Dashboard</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="admin-grid">


    <div class="admin-card">
    <h2>Add Tea Product</h2>

    <form action="admin.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Tea Name</label>
            <input
                type="text"
                id="name"
                name="name"
                required
                placeholder="e.g., Ceylon Silver Tips"
            >
        </div>

        <div class="form-group">
            <label for="image">Upload Image</label>
            <input
                type="file"
                id="image"
                name="image"
                accept="image/*"
                required
            >
        </div>

        <div class="form-group">
            <label for="price">Price (Rs.)</label>
            <input
                type="number"
                step="0.01"
                id="price"
                name="price"
                required
                placeholder="15.00"
            >
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                rows="4"
                required
                placeholder="Write details about taste, origin, etc."
            ></textarea>
        </div>

        <button type="submit" name="add_product" class="auth-btn">
            Add Product
        </button>
    </form>
</div>

<div class="admin-card">
    <h2>Manage Existing Products</h2>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM products ORDER BY id DESC";
                $products = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($products)) {
                    foreach ($products as $row) {
                        echo "<tr>";
                        echo "<td><img src='" . htmlspecialchars($row['image_url']) .
                            "' class='admin-thumb' alt='product'></td>";
                        echo "<td><strong>" . htmlspecialchars($row['name']) .
                            "</strong></td>";
                        echo "<td>$" . number_format($row['price'], 2) . "</td>";
                        echo "<td class='action-cell'>
                                <a href='edit_product.php?id=" . $row['id'] .
                                "' class='btn-edit'>Edit</a>
                                <a href='admin.php?delete=" . $row['id'] .
                                "' class='btn-delete'
                                onclick='return confirm(\"Are you sure you want to delete this product?\");'>
                                Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No products found in database.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</div> <!-- closes admin-grid -->
</div> <!-- closes admin-container -->

<?php include '../components/footer.php'; ?>

<script src="../script.js"></script>
</body>
</html>
