<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Events - The Gallery Cafe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include("nav.php"); ?> <!-- Include navigation -->

    <div class="container mt-5">
        <h1 class="text-center mb-4">Special Events</h1>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <img src="springfestival.jpeg" class="card-img-top" alt="Spring Festival">
                    <div class="card-body">
                        <h5 class="card-title">Spring Festival</h5>
                        <p class="card-text">Join us for our annual Spring Festival celebration featuring live music, special menus, and more!</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <img src="summerbbq.webp" class="card-img-top" alt="Summer BBQ Night">
                    <div class="card-body">
                        <h5 class="card-title">Summer BBQ Night</h5>
                        <p class="card-text">Enjoy our Summer BBQ Night with delicious grilled treats and refreshing beverages under the stars.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5">
        <div class="container">
            <p>&copy; 2024 The Gallery Cafe. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>
</body>
</html>
