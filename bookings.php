<?php
session_start();
include("db.php");

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's name
$username = $_SESSION['username'];

// Fetch username and get that user_id 
$user_query = "SELECT `user_id` FROM `users` WHERE username = ?;";
$stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
if (!$user_result) {
    die('User query failed: ' . mysqli_error($conn));
}

$user_row = mysqli_fetch_assoc($user_result);

if (!$user_row) {
    die('User not found for username: ' . $username);
}

$user_id = $user_row['user_id'];

// Fetch preorder details
$preorder_query = "
    SELECT po.*, pi.preorder_item_id, pi.id, pi.quantity, ai.name AS item_name, t.table_name, p.park_name
    FROM preorder po
    JOIN preorder_items pi ON po.order_id = pi.order_id
    JOIN all_items ai ON pi.id = ai.id
    LEFT JOIN tables t ON po.table_id = t.table_id
    LEFT JOIN parking p ON po.parking_id = p.parking_id
    WHERE po.user_id = ?
";
$stmt = $conn->prepare($preorder_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$preorder_result = $stmt->get_result();

// Group preorder items by order_id
$preorders = [];
while ($row = $preorder_result->fetch_assoc()) {
    $order_id = $row['order_id'];
    if (!isset($preorders[$order_id])) {
        $preorders[$order_id] = [
            'order_id' => $row['order_id'],
            'date' => $row['date'],
            'time' => $row['time'],
            'guests' => $row['guests'],
            'table_name' => $row['table_name'],
            'park_name' => $row['park_name'],
            'order_status' => $row['order_status'],
            'items' => []
        ];
    }
    $preorders[$order_id]['items'][] = [
        'item_name' => $row['item_name'],
        'quantity' => $row['quantity']
    ];
}

// Fetch reservation details
$reservation_query = "
    SELECT r.*, p.park_name, t.table_name
    FROM reservations r
    LEFT JOIN parking p ON r.parking_id = p.parking_id
    LEFT JOIN tables t ON r.table_id = t.table_id
    WHERE r.user_id = ?
";
$stmt = $conn->prepare($reservation_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reservation_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0px;
        }
        h1, h2 {
            text-align: center;
            color: #343a40;
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #343a40;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-top: 50px;
        }

        .container h3{
            display: flex;
            justify-content: center;
        }
        .cancel-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
        }
        .cancel-btn:hover {
            background-color: #c82333;
        }
        .confirm-button{
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
        }
        .confirm-button:hover{
            background-color: #0056b3;
        }
    </style>
</head>

<body>
<?php include("nav.php")?>
    <div class="container">
        <h3>Your Booking Details</h3>

        <h4>Preorder Details</h4>
        <?php if (count($preorders) > 0): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Food Items</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Guests</th>
                    <th>Table</th>
                    <th>Parking</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($preorders as $preorder): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($preorder['order_id']); ?></td>
                        <td>
                            <ul>
                                <?php foreach ($preorder['items'] as $item): ?>
                                    <li><?php echo htmlspecialchars($item['item_name']) . ' (Quantity: ' . htmlspecialchars($item['quantity']) . ')'; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td><?php echo htmlspecialchars($preorder['date']); ?></td>
                        <td><?php echo htmlspecialchars($preorder['time']); ?></td>
                        <td><?php echo htmlspecialchars($preorder['guests']); ?></td>
                        <td><?php echo htmlspecialchars($preorder['table_name']); ?></td>
                        <td><?php echo htmlspecialchars($preorder['park_name']); ?></td>
                        <td><?php echo htmlspecialchars($preorder['order_status']); ?></td>
                        <td>
                            <?php if ($preorder['order_status'] == 'Cancelled'): ?>
                                <form method="post" action="confirm_bookings.php">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($preorder['order_id']); ?>">
                                    <button type="submit" class="confirm-button">Preorder</button>
                                </form>
                            <?php else: ?>
                                <form method="post" action="cancel_bookings.php">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($preorder['order_id']); ?>">
                                    <button type="submit" class="cancel-btn">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No preorders found.</p>
        <?php endif; ?>

        <h4>Reservation Details</h4>
        
        <?php if ($reservation_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Reservation ID</th>
                    <th>Table Name</th>
                    <th>Parking Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Guests</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php while ($reservation = $reservation_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reservation['reservation_id']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['table_name']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['park_name']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['date']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['time']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['guests']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['status']); ?></td>
                        <td>
                            <?php if ($reservation['status'] == 'Cancelled'): ?>
                                <form method="post" action="confirm_bookings.php">
                                    <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($reservation['reservation_id']); ?>">
                                    <button type="submit" class="confirm-button">Reserved</button>
                                </form>
                            <?php else: ?>
                                <form method="post" action="cancel_bookings.php">
                                    <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($reservation['reservation_id']); ?>">
                                    <button type="submit" class="cancel-btn">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No reservations found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
