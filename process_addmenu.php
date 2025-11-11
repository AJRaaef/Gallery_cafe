
<!-- it is not in used in this code but in this code it will create it -->

<?php
session_start();
include 'db.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION['staff_username']) || $_SESSION['staff_type'] != 'admin') {
    header('Location: staff_login.php');
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $cousin_type = mysqli_real_escape_string($conn, $_POST['cousin_type']);
    $item_category = mysqli_real_escape_string($conn, $_POST['item_category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);

    // File upload handling
    $uploadDir = __DIR__ . "/uploads/"; // Adjust this to your desired upload directory
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
    }

    $target_file = $uploadDir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if (!$check) {
        echo "Error: File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (max 5MB)
    if ($_FILES["image"]["size"] > 5000000) {
        echo "Error: File is too large. Max 5MB is allowed.";
        $uploadOk = 0;
    }

    // Allow certain file formats (only jpg, jpeg, png, gif)
    $allowed_extensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowed_extensions)) {
        echo "Error: Only JPG, JPEG, PNG, GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Error: Your file was not uploaded.";
    } else {
        // Attempt to upload file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // File uploaded successfully, now prepare to insert data into the database
            $image_url = "uploads/" . basename($target_file); // Adjust the path as per your structure

            // Construct the SQL query to insert into your 'all_items' table
            $query = "INSERT INTO all_items (name, cousin_type, item_category, description, price, image_url)
                      VALUES ('$name', '$cousin_type', '$item_category', '$description', $price, '$image_url')";

            // Execute the query
            if (mysqli_query($conn, $query)) {
                // Success message or redirect to a success page
                echo "Item added successfully.";
                // Redirect to a success page or another appropriate location
                header('Location: add_menu.php');
                exit();
            } else {
                // Error handling if the query fails
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            // Error handling for file upload failure
            echo "Error: There was an error uploading your file.";
        }
    }
}
?>
