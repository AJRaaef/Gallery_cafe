<?php
session_start();
include 'db.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION['staff_username']) || $_SESSION['staff_type'] != 'admin') {
    header('Location: staff_login.php');
    exit();
}

// Handle form submissions for adding or editing tables
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_table'])) {
        $table_name = mysqli_real_escape_string($conn, $_POST['table_name']);
        $availability = mysqli_real_escape_string($conn, $_POST['availability']);
        
        $query = "INSERT INTO tables (table_name, availability) VALUES ('$table_name', '$availability')";
        mysqli_query($conn, $query);
    } elseif (isset($_POST['edit_table'])) {
        $table_id = mysqli_real_escape_string($conn, $_POST['table_id']);
        $table_name = mysqli_real_escape_string($conn, $_POST['table_name']);
        $availability = mysqli_real_escape_string($conn, $_POST['availability']);
        
        $query = "UPDATE tables SET table_name = '$table_name', availability = '$availability' WHERE table_id = '$table_id'";
        mysqli_query($conn, $query);
    } elseif (isset($_POST['delete_table'])) {
        $table_id = mysqli_real_escape_string($conn, $_POST['table_id']);
        
        $query = "DELETE FROM tables WHERE table_id = '$table_id'";
        mysqli_query($conn, $query);
    }
}

// Fetch tables
$query = "SELECT * FROM tables";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tables</title>
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
        <h1>Manage Tables</h1>

        <!-- Add Table Form -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Add Table</h5>
                <form method="post">
                    <div class="form-group">
                        <label for="table_name">Table Name</label>
                        <input type="text" class="form-control" id="table_name" name="table_name" required>
                    </div>
                    <div class="form-group">
                        <label for="availability">Availability</label>
                        <select class="form-control" id="availability" name="availability" required>
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" name="add_table">Add Table</button>
                </form>
            </div>
        </div>

        <!-- Table List -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Existing Tables</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Availability</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['table_id']; ?></td>
                            <td><?php echo $row['table_name']; ?></td>
                            <td><?php echo $row['availability']; ?></td>
                            <td>
                                <!-- Edit Table Form -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="table_id" value="<?php echo $row['table_id']; ?>">
                                    <button type="submit" class="btn btn-warning btn-sm" name="edit_table">Edit</button>
                                </form>
                                <!-- Delete Table Form -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="table_id" value="<?php echo $row['table_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" name="delete_table">Delete</button>
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
