<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if the user is logged in and is operational staff
if (!isset($_SESSION['staff_username']) || $_SESSION['staff_type'] != 'operational staff') {
    header('Location: staff_login.php');
    exit();
}

// Handle status update actions
if (isset($_POST['update_status']) && isset($_POST['reservation_id'])) {
    $reservation_id = $_POST['reservation_id'];
    $status = $_POST['update_status'];

    // Update the reservation status in the database
    $update_query = "UPDATE reservations SET status = '$status' WHERE reservation_id = $reservation_id";
    $update_result = mysqli_query($conn, $update_query);

    if (!$update_result) {
        die('Update query failed: ' . mysqli_error($conn));
    } else {
        header('Location: view_reservations.php?update=success');
        exit();
    }
}

// Fetch reservations
$reservation_query = "
    SELECT 
        r.reservation_id, 
        u.username, 
        t.table_name, 
        p.park_name, 
        r.date, 
        r.time, 
        r.guests, 
        r.status
    FROM reservations r
    JOIN users u ON r.user_id = u.user_id
    LEFT JOIN tables t ON r.table_id = t.table_id
    LEFT JOIN parking p ON r.parking_id = p.parking_id
    ORDER BY r.reservation_id DESC";
$reservation_result = mysqli_query($conn, $reservation_query);

if (!$reservation_result) {
    die('Query failed: ' . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 30px;
        }
        h2 {
            color: #333;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .btn {
            font-size: 0.9em;
        }
        .alert {
            display: none;
        }
        .btn-group-action {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Reservation Details</h2>
        <a href="operational_staff.php" class="btn btn-secondary mb-4">Back</a>
        <div class="alert alert-success" id="success-alert">
            Status updated successfully!
        </div>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Reservation ID</th>
                    <th>Username</th>
                    <th>Table Name</th>
                    <th>Parking Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Guests</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($reservation_result)) { ?>
                    <tr id="reservation-<?php echo $row['reservation_id']; ?>">
                        <td><?php echo $row['reservation_id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['table_name'] ?? 'Not Assigned'; ?></td>
                        <td><?php echo $row['park_name'] ?? 'Not Assigned'; ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo $row['time']; ?></td>
                        <td><?php echo $row['guests']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <div class="btn-group btn-group-action" role="group">
                                <form method="post" action="view_reservations.php">
                                    <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
                                    <button type="submit" class="btn btn-primary btn-action" name="update_status" value="Confirmed">Confirm</button>
                                    <button type="submit" class="btn btn-danger btn-action" name="update_status" value="Cancelled">Cancel</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('update') === 'success') {
                $('#success-alert').show();
                setTimeout(() => {
                    $('#success-alert').hide();
                }, 1500);
            }
        });
    </script>
</body>
</html>
