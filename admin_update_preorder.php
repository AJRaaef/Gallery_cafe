<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if the user is logged in and is operational staff
if (!isset($_SESSION['staff_username']) || $_SESSION['staff_type'] != 'admin') {
    header('Location: staff_login.php');
    exit();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    // Update the status in the preorder table
    $update_query = "UPDATE preorder SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_query);

    // Check if the statement was prepared correctly
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param('si', $order_status, $order_id);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        // Redirect back to the preorder details page after successful update
        header('Location: admin_preorders.php?update=success');
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Form submission method is not POST.";
}
?>