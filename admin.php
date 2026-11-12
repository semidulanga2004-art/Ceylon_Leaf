<?php

session_start();
include 'db_connect.php';

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
            $uploadFileDir = 'uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $stmt = $conn->prepare(
                    "INSERT INTO products (name, image_url, price, description)
                     VALUES (?, ?, ?, ?)"
                );
                $stmt->bind_param(
                    "ssds",
                    $name,
                    $dest_path,
                    $price,
                    $description
                );

                if ($stmt->execute()) {
                    $message = "Tea product added successfully!";
                } else {
                    $error = "Database insertion failed.";
                }

                $stmt->close();
            } else {
                $error = "Error moving uploaded file to destination directory.";
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

    $stmt = $conn->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $imagePath = $row['image_url'];

        if (file_exists($imagePath) && strpos($imagePath, 'uploads/') === 0) {
            unlink($imagePath);
        }
    }

    $stmt->close();

    $delete_stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $delete_stmt->bind_param("i", $id);

    if ($delete_stmt->execute()) {
        $message = "Product deleted successfully!";
    } else {
        $error = "Failed to delete product.";
    }

    $delete_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CeylonLeaf</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

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
            <label for="price">Price ($)</label>
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

