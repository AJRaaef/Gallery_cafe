<?php
session_start();
include("db.php"); // Include database connection

// Check if the user is logged in as an admin
if (!isset($_SESSION['staff_username']) || $_SESSION['staff_type'] != 'admin') {
    header('Location: staff_login.php');
    exit();
}

// Fetch all items from the database
$query = "SELECT * FROM all_items";
$result = $conn->query($query);

// Prepare an array to hold fetched items
$items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

// Function to check if items are present for current filter or search
function itemsExist($items, $categoryFilter, $searchText) {
    foreach ($items as $item) {
        // Check if item matches category filter or search text
        if (($categoryFilter === 'all' || $item['item_category'] === $categoryFilter) &&
            (empty($searchText) || stripos($item['cousin_type'], $searchText) !== false)) {
            return true; // Found matching item
        }
    }
    return false; // No matching items found
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Menu - The Gallery Café</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <style>
        .search-buttons-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-buttons-container .input-group {
            max-width: 600px;
            margin: 0 auto 15px auto;
        }
        .search-buttons-container button {
            margin: 5px;
        }
        .form-control {
            text-align: center;
            margin-top: 4px;
        }
        .menu-item {
            text-align: center;
            padding: 15px;
            border: 1px solid #ddd; /* Add border to each item */
            margin-bottom: 20px; /* Add space between rows */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add shadow for box effect */
        }
        .no-items-message {
            text-align: center;
            margin-top: 20px;
        }
        .row {
            margin-top: 20px;
        }
        .button-group {
            margin-top: 10px; /* Add space between buttons */
        }
        .btn-edit {
            margin-right: 10px; /* Add space to the right of the Edit button */
        }
    </style>
</head>
<body>
  
    <?php include("admin_nav.php"); ?> <!-- Include navigation -->

    <div class="container mt-5">
        <div class="search-buttons-container">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by cuisine type...">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button" id="searchButton">Search</button>
                </div>
            </div>
        </div>

        <div id="menuItems" class="row">
            <?php
            if (empty($items) || !itemsExist($items, 'all', '')) {
                echo '<div class="col-md-12 no-items-message">';
                echo '<p>No items found.</p>';
                echo '</div>';
            } else {
                foreach ($items as $item) {
                    echo '<div class="col-md-3 menu-item" data-category="' . $item['item_category'] . '">';
                    echo '<img src="' . $item['image_url'] . '" class="img-fluid" alt="' . $item['name'] . '">';
                    echo '<h4>' . $item['name'] . '</h4>';
                    echo '<p class="cousin-type">' . $item['cousin_type'] . '</p>';
                    echo '<p>' . $item['description'] . '</p>'; // Display item description
                    echo '<p>$' . number_format($item['price'], 2) . '</p>';
                    echo '<div class="button-group">';
                    echo '<a class="btn btn-primary btn-edit" href="item_edit_menu.php?name=' . urlencode($item['name']) . '">Edit</a>';
                    echo '<form action="item_delete.php" method="post" style="display:inline-block;">';
                    echo '<input type="hidden" name="item_id" value="' . $item['id'] . '">';
                    echo '<button type="submit" class="btn btn-danger">Delete</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>

    <footer class="text-center mt-5">
        <div class="container">
            <p>&copy; 2024 The Gallery Café. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    <script>
    $(document).ready(function() {
        // Search functionality
        $('#searchButton').click(function() {
            var searchText = $('#searchInput').val().toLowerCase();
            $('.menu-item').each(function() {
                var itemContent = $(this).text().toLowerCase(); // Get all text content in lowercase
                if (itemContent.includes(searchText)) {
                    $(this).show(); // Show items that match the search text
                } else {
                    $(this).hide(); // Hide items that don't match the search text
                }
            });
            checkItemsExist(); // Check if items exist after search
        });

        // Function to check if items exist and display message
        function checkItemsExist() {
            var visibleItemsCount = $('.menu-item:visible').length;
            if (visibleItemsCount === 0) {
                $('#menuItems').append('<div class="col-md-12 no-items-message"><p>No items found.</p></div>');
            } else {
                $('.no-items-message').remove(); // Remove existing no-items-message if items are found
            }
        }
    });
    </script>
</body>
</html>
