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

