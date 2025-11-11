<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if the user is logged in and is operational staff
if (!isset($_SESSION['staff_username']) || $_SESSION['staff_type'] != 'operational staff') {
    header('Location:staff_login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_id = $_POST['reservation_id'];
    $status = $_POST['status'];

    // Update the status in the reservations table
    $update_query = "UPDATE reservations SET status = ? WHERE reservation_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('si', $status, $reservation_id);

    if ($stmt->execute()) {
        // Redirect back to the reservation details page after successful update
        header('Location: view_reservations.php?update=success');
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
