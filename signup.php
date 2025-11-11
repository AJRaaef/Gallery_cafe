<?php
session_start();
include 'db.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate user input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $usertype = 'customer'; // Default usertype
    
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'preorder.php';

    if (empty($username) || empty($password) || empty($confirmPassword) || empty($email) || empty($phone)) {
        echo "<script>alert('All fields are required.'); window.location.href='signup.php';</script>";
        exit();
    }

    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.'); window.location.href='signup.php';</script>";
        exit();
    }


    // Validate password format
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_@])[A-Za-z\d\W_@]{8,12}$/', $password)) {
        echo "<script>alert('Password must be 8-12 characters long, include at least one uppercase letter, one lowercase letter, one digit, and one special character (e.g., @, #, $, etc.).'); window.location.href='signup.php';</script>";
        exit();
    }

   
    if (!preg_match('/[A-Za-z]/', $username) || preg_match('/^\d+$/', $username)) {
        $username_error = 'Username must include at least one letter and cannot consist solely of numbers.';
    }


    // Check if username is already taken
    $check_query = "SELECT * FROM users WHERE username=?";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bind_param('s', $username);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Username already exists. Please choose a different username.'); window.location.href='signup.php';</script>";
        exit();
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user details into database
    $insert_query = "INSERT INTO users (username, password, email, phone, user_type) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_query);

    if ($stmt_insert === false) {
        // Handle prepare error
        die('Prepare error (insert user): ' . htmlspecialchars($conn->error));
    }

    $stmt_insert->bind_param('sssss', $username, $hashedPassword, $email, $phone, $usertype);

    if ($stmt_insert->execute()) {
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $stmt_insert->insert_id; // Assuming you want to set the user_id in session
        header('Location: ' . $redirect);
        exit();
    } else {
        // Handle execute error
        die('Execute error (insert user): ' . htmlspecialchars($stmt_insert->error));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?> <!-- Include navigation -->

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Sign Up</h4>
                    </div>
                    <div class="card-body">
                        <form action="signup.php" method="POST">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" name="username" id="username" class="form-control" 
                                       value="<?php echo htmlspecialchars($username); ?>" 
                                       title="Username must include at least one letter and cannot consist solely of numbers." 
                                       required>
                                <small class="error"><?php echo $username_error ?? ''; ?></small>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" name="password" id="password" class="form-control" 
                                       pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_@])[A-Za-z\d\W_@]{8,12}$" 
                                       title="Password must be 8-12 characters long, include at least one uppercase letter, one lowercase letter, one digit, and one special character (e.g., @, #, $, etc.)." 
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">Confirm Password:</label>
                                <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" id="email" class="form-control" 
                                       title="Please enter a valid email address (e.g., user@example.com)." 
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone:</label>
                                <input type="text" name="phone" id="phone" class="form-control" required>
                            </div>
                            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect'] ?? 'preorder.php'); ?>">
                            <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="login.php?redirect=<?php echo urlencode($_GET['redirect'] ?? 'reservation.php'); ?>">Already have an account? Login here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
