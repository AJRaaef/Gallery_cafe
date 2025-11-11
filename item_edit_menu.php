<?php
session_start();
include("db.php"); // Include database connection

// Check if the user is logged in as an admin
if (!isset($_SESSION['staff_username']) || $_SESSION['staff_type'] != 'admin') {
    header('Location: staff_login.php');
    exit();
}

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Check if the 'name' parameter is passed in the URL
if (!isset($_GET['name'])) {
    header('Location: edit_menu.php'); // Redirect if 'name' parameter is not provided
    exit();
}

// Fetch item details based on 'name' parameter
$name = sanitize_input($_GET['name']);
$query = "SELECT * FROM all_items WHERE name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $name);
$stmt->execute();
$result = $stmt->get_result();

// Check if item exists
if ($result->num_rows == 0) {
    header('Location: edit_menu.php'); // Redirect if item does not exist
    exit();
}

$item = $result->fetch_assoc();
$old_image_url = $item['image_url']; // Store the current image URL

// Handle form submission for updating item details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $new_name = sanitize_input($_POST['name']);
    $cousin_type = sanitize_input($_POST['cousin_type']);
    $item_category = sanitize_input($_POST['item_category']);
    $description = sanitize_input($_POST['description']);
    $price = floatval($_POST['price']); // Ensure price is treated as a float
    $new_image_url = $old_image_url; // Default to current image if no new image uploaded

    // Handle image upload if a new image is selected
    if ($_FILES['image']['size'] > 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
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
            echo "Sorry, your file was not uploaded.";

        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $new_image_url = $target_file;
                echo "The file ". htmlspecialchars(basename($_FILES["image"]["name"])) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
                error_log("Error uploading file: " . print_r($_FILES, true)); // Log error
            }
        }
    }

    // Update query
    $update_query = "UPDATE all_items SET name=?, cousin_type=?, item_category=?, description=?, price=?, image_url=? WHERE id=?";
    $stmt = $conn->prepare($update_query);
    // Assuming 'item_category' is a string, bind it accordingly ('s' for string)
    $stmt->bind_param('ssssdsi', $new_name, $cousin_type, $item_category, $description, $price, $new_image_url, $item['id']);
    
    // Execute the update statement
    if ($stmt->execute()) {
        // Redirect to edit_menu.php after successful update
        echo '<script>alert("Item Edited Successfully!");window.location.href="edit_menu.php";</script>';
        exit();
    } else {
        // Handle update error
        echo "Error updating item: " . $stmt->error;
        error_log("Error updating item: " . $stmt->error); // Log error
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item - <?php echo htmlspecialchars($item['name']); ?> - The Gallery Cafe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Add custom styles as needed */
    </style>
</head>
<body>
  
    <?php include("admin_nav.php"); ?> <!-- Include navigation -->

    <div class="container mt-5">
        <h2>Edit Item: <?php echo htmlspecialchars($item['name']); ?></h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?name=' . urlencode($item['name'])); ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Item Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($item['name']); ?>">
            </div>
            <div class="form-group">
                <label for="cousin_type">Cuisine Type</label>
                <input type="text" class="form-control" id="cousin_type" name="cousin_type" value="<?php echo htmlspecialchars($item['cousin_type']); ?>">
            </div>
            <div class="form-group">
                <label for="item_category">Category</label>
                <select class="form-control" id="item_category" name="item_category">
                    <option value="special food" <?php if ($item['item_category'] === 'special food') echo 'selected'; ?>>Special Food</option>
                    <option value="beverages" <?php if ($item['item_category'] === 'beverages') echo 'selected'; ?>>Beverages</option>
                    <option value="meal" <?php if ($item['item_category'] === 'meal') echo 'selected'; ?>>Meal</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($item['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($item['price']); ?>">
            </div>
            <div class="form-group">
                <label for="image">Current Image</label><br>
                <img src="<?php echo htmlspecialchars($old_image_url); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="max-width: 200px;">
            </div>
            <div class="form-group">
                <label for="image">Upload New Image</label>
                <input type="file" class="form-control-file" id="image" name="image">
            </div>
            <button type="submit" class="btn btn-primary">Update Item</button>
        </form>
    </div>

    <footer class="text-center mt-5">
        <div class="container">
            <p>&copy; 2024 The Gallery Café. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-pzjw8f+ua7Kw1TIqX8+KEqGELNJmzEkxB+Ph6f5io1F5r2O3/sDk6aS2loN2V9T8" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+YQ4r4a2fA4nA2kIGK1t4jv8s7ySg0gK4n6+7OtkjEBgL1b2+MII4+Ihm2QAIpFz" crossorigin="anonymous"></script>
</body>
</html>
