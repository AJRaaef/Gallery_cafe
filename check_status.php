<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Fetch user_id from the users table
$username = $_SESSION['username'];
$user_query = "SELECT user_id FROM users WHERE username='$username'";
$user_result = mysqli_query($conn, $user_query);

if (!$user_result) {
    die('User query failed: ' . mysqli_error($conn));
}

$user_row = mysqli_fetch_assoc($user_result);

if (!$user_row) {
    die('User not found for username: ' . $username);
}

$user_id = $user_row['user_id'];

// Fetch reservation details for the logged-in user
$reservation_query = "
    SELECT r.*, t.table_name, p.park_name 
    FROM reservations r
    LEFT JOIN tables t ON r.table_id = t.table_id
    LEFT JOIN parking p ON r.parking_id = p.parking_id
    WHERE r.user_id='$user_id' 
    ORDER BY r.reservation_id DESC 
    LIMIT 1
";
$reservation_result = mysqli_query($conn, $reservation_query);

if (!$reservation_result) {
    die('Reservation query failed: ' . mysqli_error($conn));
}

$reservation_row = mysqli_fetch_assoc($reservation_result);

// Initialize message variable
$message = "";

if (!$reservation_row) {
    $message = "No reservations found.";
} else {
    $status = $reservation_row['status'];
    if (strtolower($status) == 'pending') {
        $message = "Your reservation is pending. Kindly wait for the reservation confirmation message from our staff.";
    } elseif (strtolower($status) == 'confirmed') {
        $message = "Your reservation has been confirmed.";
    } elseif (strtolower($status) == 'cancelled') {
        $message = "Your reservation has been cancelled.";
    }
}

// Handle AJAX request for dynamic status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'check_status') {
    echo $message; // Output the message for AJAX request
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Reservation Status</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?> <!-- Include navigation -->

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Check Reservation Status</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($reservation_row): ?>
                            <p><strong>Reservation Details:</strong></p>
                            <p><strong>Date:</strong> <?php echo $reservation_row['date']; ?></p>
                            <p><strong>Time:</strong> <?php echo $reservation_row['time']; ?></p>
                            <p><strong>Number of Guests:</strong> <?php echo $reservation_row['guests']; ?></p>
                            <p><strong>Table:</strong> <?php echo $reservation_row['table_name']; ?></p>
                            <p><strong>Parking:</strong> <?php echo $reservation_row['park_name']; ?></p>
                            <!-- Add more details as needed -->
                            <hr>
                        <?php endif; ?>
                        <p id="status-message">Loading your reservation status...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function checkReservationStatus() {
                $.ajax({
                    url: 'check_status.php',
                    method: 'POST',
                    data: { action: 'check_status' },
                    success: function(response) {
                        $('#status-message').text(response);
                    }
                });
            }

            // Initial check
            checkReservationStatus();

            // Check every 5 seconds
            setInterval(checkReservationStatus, 5000);

            // Hide the status message after 1 minute
            setTimeout(function() {
                $('#status-message').fadeOut();
            }, 60000);
        });
    </script>
</body>
</html>
