<?php
session_start();
include 'db.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION['staff_username']) || $_SESSION['staff_type'] != 'admin') {
    header('Location: staff_login.php');
    exit();
}

// Fetch preorder details summary
$query_preorder = "SELECT COUNT(p.order_id) as total_orders, SUM(p.guests) as total_guests, SUM(pi.quantity) as total_items,
        SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) AS prepending_count,
        SUM(CASE WHEN order_status = 'confirmed' THEN 1 ELSE 0 END) AS preconfirmed_count,
        SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) AS precancelled_count
                   FROM preorder p
                   LEFT JOIN preorder_items pi ON p.order_id = pi.order_id";
$result_preorder = mysqli_query($conn, $query_preorder);
$row_preorder = mysqli_fetch_assoc($result_preorder);

// Initialize variables with default values if no data is returned
$total_orders = $row_preorder['total_orders'] ?? 0;
$total_guests = $row_preorder['total_guests'] ?? 0;
$total_items = $row_preorder['total_items'] ?? 0;
$preorder_confirmed = $row_preorder['preconfirmed_count'] ?? 0;
$preorder_pending = $row_preorder['prepending_count'] ?? 0;
$preorder_cancelled = $row_preorder['precancelled_count'] ?? 0;

// Fetch reservation details summary
$query_reservation = "SELECT COUNT(reservation_id) as total_reservations, SUM(guests) as total_reservation_guests,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed_count,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_count
                      FROM reservations";
$result_reservation = mysqli_query($conn, $query_reservation);
$row_reservation = mysqli_fetch_assoc($result_reservation);

// Initialize variables with default values if no data is returned
$total_reservations = $row_reservation['total_reservations'] ?? 0;
$total_reservation_guests = $row_reservation['total_reservation_guests'] ?? 0;
$confirmed_count = $row_reservation['confirmed_count'] ?? 0;
$cancelled_count = $row_reservation['cancelled_count'] ?? 0;
$pending_count = $row_reservation['pending_count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
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
            color: #333;
        }
        .icon {
            color: #333;
        }
        .icon-stats {
            font-size: 2rem;
            color: #333;
        }
        .details {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
    </style>
</head>
<body>
    <?php include 'admin_nav.php'; ?> <!-- Include admin navigation -->

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center">Admin Dashboard</h1>
                <div class="row mt-4">
                    <!-- Existing Cards -->
                    <div class="col-md-4 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="icon mb-3">
                                    <i class="fas fa-user-plus fa-3x"></i>
                                </div>
                                <h5 class="card-title">Create Accounts</h5>
                                <a href="create_accounts.php" class="btn btn-primary">Create</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="icon mb-3">
                                    <i class="fas fa-utensils fa-3x"></i>
                                </div>
                                <h5 class="card-title">Edit Menu Items</h5>
                                <a href="edit_menu.php" class="btn btn-primary">Edit</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="icon mb-3">
                                    <i class="fas fa-utensils fa-3x"></i>
                                </div>
                                <h5 class="card-title">Add Menu Items</h5>
                                <a href="add_menu.php" class="btn btn-primary">Add</a>
                            </div>
                        </div>
                    </div>
                    <!-- Preorder Details -->
                    <div class="col-md-6 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="icon mb-3">
                                    <i class="fas fa-list-alt fa-3x"></i>
                                </div>
                                <h5 class="card-title">Preorder Details</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="details"><i class="fas fa-receipt icon-stats"></i> Total Orders: <?= $total_orders ?></p>
                                        <p class="details"><i class="fas fa-users icon-stats"></i> Total Guests: <?= $total_guests ?></p>
                                        <p class="details"><i class="fas fa-utensils icon-stats"></i> Total Items: <?= $total_items ?></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="details"><i class="fas fa-check-circle icon-stats"></i> Confirmed: <?= $preorder_confirmed ?></p>
                                        <p class="details"><i class="fas fa-clock icon-stats"></i> Pending: <?= $preorder_pending ?></p>
                                        <p class="details"><i class="fas fa-times-circle icon-stats"></i> Cancelled: <?= $preorder_cancelled ?></p>
                                    </div>
                                </div>
                                <a href="admin_preorders.php" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                    <!-- Reservation Details -->
                    <div class="col-md-6 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="icon mb-3">
                                    <i class="fas fa-chair fa-3x"></i>
                                </div>
                                <h5 class="card-title">Reservation Details</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="details"><i class="fas fa-receipt icon-stats"></i> Total Bookings: <?= $total_reservations ?></p>
                                        <p class="details"><i class="fas fa-users icon-stats"></i> Total Guests: <?= $total_reservation_guests ?></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="details"><i class="fas fa-check-circle icon-stats"></i> Confirmed: <?= $confirmed_count ?></p>
                                        <p class="details"><i class="fas fa-clock icon-stats"></i> Pending: <?= $pending_count ?></p>
                                        <p class="details"><i class="fas fa-times-circle icon-stats"></i> Cancelled: <?= $cancelled_count ?></p>
                                    </div>
                                </div>
                                <a href="admin_reservations.php" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                    <!-- Manage Tables -->
                    <div class="col-md-4 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="icon mb-3">
                                    <i class="fas fa-table fa-3x"></i>
                                </div>
                                <h5 class="card-title">Manage Tables</h5>
                                <a href="manage_tables.php" class="btn btn-primary">Manage Tables</a>
                            </div>
                        </div>
                    </div>
                    <!-- Manage Parking Slots -->
                    <div class="col-md-4 mb-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="icon mb-3">
                                    <i class="fas fa-parking fa-3x"></i>
                                </div>
                                <h5 class="card-title">Manage Parking Slots</h5>
                                <a href="manage_parking.php" class="btn btn-primary">Manage Parking Slots</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

