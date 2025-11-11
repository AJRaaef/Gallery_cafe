<?php
session_start();
include 'db.php'; // Include your database connection file



// Handle the login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $staff_username = mysqli_real_escape_string($conn, $_POST['staff_username']);
    $staff_password = mysqli_real_escape_string($conn, $_POST['staff_password']);

    // Validate inputs
    $errors = [];
    if (empty($staff_username)) {
        $errors[] = "Staff username is required.";
    }
    if (empty($staff_password)) {
        $errors[] = "Staff password is required.";
    }

    if (empty($errors)) {
        // Fetch staff details based on the username
        $staff_query = "SELECT * FROM staff WHERE staff_username='$staff_username'";
        $staff_result = mysqli_query($conn, $staff_query);

        if ($staff_result && mysqli_num_rows($staff_result) > 0) {
            $staff_row = mysqli_fetch_assoc($staff_result);

            // Verify the password
            if (password_verify($staff_password, $staff_row['staff_password'])) {
                // Password is correct, set session variables
                $_SESSION['staff_id'] = $staff_row['staff_id'];
                $_SESSION['staff_username'] = $staff_row['staff_username'];
                $_SESSION['staff_type'] = $staff_row['staff_type'];

                // Redirect based on staff type
                if ($staff_row['staff_type'] == 'admin') {
                    header('Location: admin.php');
                } else if ($staff_row['staff_type'] == 'operational staff') {
                    header('Location: operational_staff.php');
                }
                exit();
            } else {
                $errors[] = "Invalid password.";
            }
        } else {
            $errors[] = "Staff username not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Staff Login</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (!empty($errors)) {
                            echo '<div class="alert alert-danger">';
                            foreach ($errors as $error) {
                                echo '<p>' . $error . '</p>';
                            }
                            echo '</div>';
                        }
                        ?>
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="staff_username">Username</label>
                                <input type="text" id="staff_username" name="staff_username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="staff_password">Password</label>
                                <input type="password" id="staff_password" name="staff_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
