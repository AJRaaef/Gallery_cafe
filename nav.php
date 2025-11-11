<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <title>The Gallery Cafe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap"> <!-- Add Font -->


    <style>
        .navbar {
    padding: 0.5rem 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Top shadow effect */
}

    </style>
</head>
<body>
    <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">
            <i class="fas fa-utensils"></i> <!-- Restaurant Icon -->
            The Gallery Cafe
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="event.php">Special Events</a></li>
                

                <?php
                if(isset($_SESSION['username'])){

                    echo '<li class="nav-item"><a class="nav-link" href="bookings.php">Bookings</a></li>';
                }
                ?>
            </ul>

            

            <?php
            // Determine the redirect link for login and signup
       

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    echo '<a class="btn btn-primary text-white navbar-btn-center" id="bookTableBtn" href="reservation.php">Book Table</a>';
    echo '<a class="btn btn-danger text-white navbar-btn-center ml-2" href="logout.php">Logout</a>';
} else {
    echo '<a class="btn btn-primary text-white navbar-btn-center" id="bookTableBtn" href="login.php?redirect=reservation.php">Book Table</a>';
    
    
}
?>
</header>
        </div>
    </nav>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>
</body>
</html>
