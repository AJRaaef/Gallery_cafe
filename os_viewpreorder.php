<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if the user is logged in and is operational staff
if (!isset($_SESSION['staff_username']) || $_SESSION['staff_type'] != 'operational staff') {
    header('Location: staff_login.php');
    exit();
}

// Fetch preorders with joined tables for username, table_name, park_name, food_name, and quantity
$preorder_query = "
    SELECT 
        po.order_id, 
        u.username, 
        t.table_name, 
        p.park_name, 
        po.date, 
        po.time, 
        po.order_status,
        GROUP_CONCAT(CONCAT(ai.name, ' (', pi.quantity, ')') SEPARATOR ', ') AS food_details
    FROM preorder po
    JOIN users u ON po.user_id = u.user_id
    JOIN tables t ON po.table_id = t.table_id
    JOIN parking p ON po.parking_id = p.parking_id
    JOIN preorder_items pi ON po.order_id = pi.order_id
    JOIN all_items ai ON pi.id = ai.id
    GROUP BY po.order_id, u.username, t.table_name, p.park_name, po.date, po.time, po.order_status";
$preorder_result = mysqli_query($conn, $preorder_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preorder Details</title>
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
<?php include 'staff_nav.php'; ?>
    <div class="container">
        <h2 class="mb-4">Preorder Details</h2>
      
        <div class="alert alert-success" id="success-alert">
            Status updated successfully!
        </div>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Username</th>
                    <th>Table Name</th>
                    <th>Parking Name</th>
                    <th>Food Details</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($preorder_result)) { ?>
                    <tr id="order-<?php echo $row['order_id']; ?>">
                        <td><?php echo $row['order_id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['table_name']; ?></td>
                        <td><?php echo $row['park_name']; ?></td>
                        <td>
                            <ul>
                                <?php
                                $food_items = explode(', ', $row['food_details']);
                                foreach ($food_items as $item) {
                                    echo "<li>$item</li>";
                                }
                                ?>
                            </ul>
                        </td>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo $row['time']; ?></td>
                        <td class="order-status"><?php echo $row['order_status']; ?></td>
                        <td>
                            <div class="btn-group btn-group-action" role="group">
                            <form method="post" action="update_preorder.php" class="d-inline">
                                <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                <input type="hidden" name="order_status" value="Confirmed">
                                <button type="submit" class="btn btn-success btn-action">Confirm</button>
                            </form>
                            <form method="post" action="update_preorder.php" class="d-inline">
                                <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                <input type="hidden" name="order_status" value="Cancelled">
                                <button type="submit" class="btn btn-danger btn-action">Cancel</button>
                            </form>

                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Status Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to update the status to <span id="statusText"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmBtn">Yes, Update</button>
                </div>
            </div>
        </div>
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