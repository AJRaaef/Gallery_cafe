<?php
session_start(); // Add the missing semicolon here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>The Gallery Cafe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
          .carousel-caption {
            background-color: rgba(255, 255, 255, 0.7); /* Transparent white background */
            padding: 10px; /* Padding around the text */
            border-radius: 5px; /* Rounded corners for the box */
        }
        .carousel-caption h5, .carousel-caption p {
            color: black; /* Text color black */
        }
    </style>
</head>

<body>
    <?php
    include("nav.php");
    ?>

    <div class="container mt-5">
        <!-- Bootstrap Carousel -->
        <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <?php
                include 'db.php';  // Include your database connection file

                // Query to fetch promotions
                $query = "SELECT * FROM promotions";
                $result = mysqli_query($conn, $query);

                // Check if query execution is successful
                if (!$result) {
                    die('Error executing query: ' . mysqli_error($conn));
                }

                // Counter for carousel indicators
                $indicator_count = 0;

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $active = ($indicator_count == 0) ? 'active' : '';
                        echo '<li data-target="#carouselExampleCaptions" data-slide-to="' . $indicator_count . '" class="' . $active . '"></li>';
                        $indicator_count++;
                    }
                }
                ?>
            </ol>
            <div class="carousel-inner">
                <?php
                // Reset the result pointer and counter
                mysqli_data_seek($result, 0);
                $indicator_count = 0;

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $active = ($indicator_count == 0) ? 'active' : '';
                        echo '<div class="carousel-item ' . $active . '">';
                        echo '<img src="' . $row['image_url'] . '" class="d-block w-100" alt="Promotion Image">';
                        echo '<div class="carousel-caption d-none d-md-block">';
                        echo '<h5>' . $row['description'] . '</h5>';
                        echo '<p>Valid: ' . date('M j, Y', strtotime($row['start_date'])) . ' - ' . date('M j, Y', strtotime($row['end_date'])) . '</p>';
                        echo '</div>';
                        echo '</div>';
                        $indicator_count++;
                    }
                } else {
                    echo "<p>No promotions currently available.</p>";
                }

                mysqli_close($conn);
                ?>
            </div>
            <button class="carousel-control-prev" type="button" data-target="#carouselExampleCaptions" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-target="#carouselExampleCaptions" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </button>
        </div>
    </div>

    <footer class="text-center mt-5">
        <div class="container">
            <p>&copy; 2024 The Gallery Café. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        document.getElementById('bookTableBtn').addEventListener('click', function() {
            <?php if(isset($_SESSION['username'])): ?>
                window.location.href = 'reservations.html';
            <?php else: ?>
                window.location.href = 'login.php';
            <?php endif; ?>
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
</body>
</html>
