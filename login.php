<?php
session_start();
include 'db.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'reservation.php';

    $query = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die('Prepare error (select user): ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['user_id']; // Assuming you have 'user_id' column in users table
            $redirect = $_SESSION['redirect_url'] ?? 'preorder.php';
            unset($_SESSION['redirect_url']); // Remove stored redirect URL after use
            if (isset($_GET['name'])) {
                $redirect .= '?name=' . urlencode($_GET['name']);
            }
            header('Location: ' . $redirect);
            exit();
        } else {
            echo "<script>alert('Invalid username or password'); window.location.href='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Invalid username or password'); window.location.href='login.php';</script>";
        exit();
    }
} else {
    // If redirect URL is passed in GET (from menu.php)
    if (isset($_GET['redirect'])) {
        $_SESSION['redirect_url'] = $_GET['redirect'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?> <!-- Include navigation -->

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Login</h4>
                    </div>
                    <div class="card-body">
                        <form action="login.php" method="POST">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" name="username" id="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect'] ?? 'reservation.php'); ?>">
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="signup.php?redirect=<?php echo urlencode($_GET['redirect'] ?? 'reservation.php'); ?>">Don't have an account? Sign up here</a>
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
