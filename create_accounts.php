<?php
session_start();
include 'db.php';

// Function to sanitize input data
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Function to validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Check if the user is logged in as an admin
if (!isset($_SESSION['staff_username'])) {
    header('Location: staff_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = sanitize($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $email = sanitize($_POST['email']);
    
    // Validate email format
    if (!isValidEmail($email)) {
        $message = "Invalid email format.";
    } else {
        // Validate and format phone number (+94XXXXXXXXX)
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $phone = preg_replace('/[^0-9]/', '', $phone); // Remove non-numeric characters
        if (strlen($phone) === 9) {
            $phone = '+94' . $phone; // Append +94 to the 9-digit number
        } else {
            $message = "Phone number should be exactly 9 digits.";
        }
        
        $staff_type = sanitize($_POST['staff_type']);

        // Insert into staff table
        $query_staff = "INSERT INTO staff (staff_username, staff_password, staff_email, staff_phone, staff_type) 
                        VALUES ('$username', '$password', '$email', '$phone', '$staff_type')";
        
       

        $message = '';
        if (mysqli_query($conn, $query_staff)) {
            $message = "Account created successfully.";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Accounts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'admin_nav.php'; ?> <!-- Include admin navigation -->

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Create Account</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-info"><?= $message ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+94</span>
                                    </div>
                                    <input type="text" class="form-control" id="phone" name="phone" pattern="[0-9]{9}" required>
                                </div>
                                <small class="form-text text-muted">Enter 9 digits only.</small>
                            </div>
                            <div class="form-group">
                                <label for="staff_type">Staff Type</label>
                                <select class="form-control" id="staff_type" name="staff_type" required>
                                    <option value="admin">Admin</option>
                                    <option value="operational staff">Operational Staff</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
