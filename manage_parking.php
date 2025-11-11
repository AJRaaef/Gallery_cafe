<?php
session_start();
include 'db.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION['staff_username']) || $_SESSION['staff_type'] != 'admin') {
    header('Location: staff_login.php');
    exit();
}

// Handle form submissions for adding or editing parking slots
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_parking'])) {
        $park_name = mysqli_real_escape_string($conn, $_POST['park_name']);
        $availability_status = mysqli_real_escape_string($conn, $_POST['availability_status']);
        
        $query = "INSERT INTO parking (park_name, availability_status) VALUES ('$park_name', '$availability_status')";
        mysqli_query($conn, $query);
    } elseif (isset($_POST['edit_parking'])) {
        $parking_id = mysqli_real_escape_string($conn, $_POST['parking_id']);
        $park_name = mysqli_real_escape_string($conn, $_POST['park_name']);
        $availability_status = mysqli_real_escape_string($conn, $_POST['availability_status']);
        
        $query = "UPDATE parking SET park_name = '$park_name', availability_status = '$availability_status' WHERE parking_id = '$parking_id'";
        mysqli_query($conn, $query);
    } elseif (isset($_POST['delete_parking'])) {
        $parking_id = mysqli_real_escape_string($conn, $_POST['parking_id']);
        
        $query = "DELETE FROM parking WHERE parking_id = '$parking_id'";
        mysqli_query($conn, $query);
    }
}

// Fetch parking slots
$query = "SELECT * FROM parking";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Parking Slots</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <?php include 'admin_nav.php'; ?>

    <div class="container">
        <h1>Manage Parking Slots</h1>

        <!-- Add Parking Slot Form -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Add Parking Slot</h5>
                <form method="post">
                    <div class="form-group">
                        <label for="park_name">Parking Slot Name</label>
                        <input type="text" class="form-control" id="park_name" name="park_name" required>
                    </div>
                    <div class="form-group">
                        <label for="availability_status">Availability Status</label>
                        <select class="form-control" id="availability_status" name="availability_status" required>
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" name="add_parking">Add Parking Slot</button>
                </form>
            </div>
        </div>

        <!-- Parking Slot List -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Existing Parking Slots</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['parking_id']; ?></td>
                            <td><?php echo $row['park_name']; ?></td>
                            <td><?php echo $row['availability_status']; ?></td>
                            <td>
                                <!-- Edit Parking Slot Form -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="parking_id" value="<?php echo $row['parking_id']; ?>">
                                    <button type="submit" class="btn btn-warning btn-sm" name="edit_parking">Edit</button>
                                </form>
                                <!-- Delete Parking Slot Form -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="parking_id" value="<?php echo $row['parking_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" name="delete_parking">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
