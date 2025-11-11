<?php
session_start();
include 'db.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION['staff_username']) || $_SESSION['staff_type'] != 'admin') {
    header('Location: staff_login.php');
    exit();
}

// Fetch unique categories and cuisine types from the database
$cousin_types = [];

// Fetch cuisine types
$result_cousin_types = mysqli_query($conn, "SELECT DISTINCT cousin_type FROM all_items");
while ($row = mysqli_fetch_assoc($result_cousin_types)) {
    $cousin_types[] = $row['cousin_type'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $cousin_type = $_POST['cousin_type'] === 'other' ? $_POST['new_cousin_type'] : $_POST['cousin_type'];
    $item_category = $_POST['item_category'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Check if item already exists
    $query_check = "SELECT COUNT(*) AS count FROM all_items WHERE name = ?";
    $stmt_check = mysqli_prepare($conn, $query_check);
    mysqli_stmt_bind_param($stmt_check, 's', $name);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_bind_result($stmt_check, $count);
    mysqli_stmt_fetch($stmt_check);
    mysqli_stmt_close($stmt_check);

    if ($count > 0) {
        echo '<script>alert("An item with this name already exists.");</script>';
    } else {
        // Upload image
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            echo '<script>alert("Sorry, file already exists.");</script>';
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["image"]["size"] > 500000) {
            echo '<script>alert("Sorry, your file is too large.");</script>';
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo '<script>alert("Sorry, your file was not uploaded.");</script>';
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars(basename($_FILES["image"]["name"])) . " has been uploaded.";

                // Insert item into database
                $image_url = $target_file;
                $query = "INSERT INTO all_items (name, cousin_type, item_category, description, price, image_url)
                          VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'ssssds', $name, $cousin_type, $item_category, $description, $price, $image_url);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // Redirect to success page or another page
                echo "<script>alert('Item has been added successfully!'); window.location.href='add_menu.php';</script>";
                exit();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu Items</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 500;
        }
        .card-body {
            padding: 2rem;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
    <script>
        function handleSelectChange(selectElement, inputElementId) {
            var inputElement = document.getElementById(inputElementId);
            if (selectElement.value === 'other') {
                inputElement.style.display = 'block';
                inputElement.required = true;
            } else {
                inputElement.style.display = 'none';
                inputElement.required = false;
            }
        }
    </script>
</head>
<body>
    <?php include 'admin_nav.php'; ?> <!-- Include admin navigation -->

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center">Add Menu Items</h1>
                <div class="card">
                    <div class="card-body">
                        <form action="add_menu.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="cousin_type">Cuisine Type:</label>
                                <select class="form-control" id="cousin_type" name="cousin_type" onchange="handleSelectChange(this, 'new_cousin_type')" required>
                                    <option value="">Select a cuisine type</option>
                                    <?php foreach($cousin_types as $type): ?>
                                        <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                                    <?php endforeach; ?>
                                    <option value="other">Other</option>
                                </select>
                                <input type="text" class="form-control mt-2" id="new_cousin_type" name="new_cousin_type" placeholder="Enter new cuisine type" style="display:none;">
                            </div>
                            <div class="form-group">
                                <label for="item_category">Item Category:</label>
                                <select class="form-control" id="item_category" name="item_category" required>
                                    <option value="special food">Special Food</option>
                                    <option value="beverages">Beverages</option>
                                    <option value="meal">Meal</option>
                                </select>
                                <input type="text" class="form-control mt-2" id="new_item_category" name="new_item_category" placeholder="Enter new item category" style="display:none;">
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="price">Price:</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="5.00" required>
                            </div>
                            <div class="form-group">
                                <label for="image">Select Image:</label>
                                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Item</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
