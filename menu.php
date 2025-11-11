<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to check if the user is logged in
function isLoggedIn() {
    return isset($_SESSION['username']);
}

// Include database connection and other necessary files
include("db.php");

// Fetch items from database
$query = "SELECT * FROM all_items";
$result = $conn->query($query);
$items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - The Gallery Café</title>
    <!-- Include necessary CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Additional CSS styles as needed */
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
            border: 1px solid #ddd;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .no-items-message {
            text-align: center;
            margin-top: 20px;
        }
        .row {
            margin-top: 20px;
        }
        .quantity-input {
            width: 50px;
            text-align: center;
        }
        .cart-items-container {
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .cart-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .cart-item-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-item-info p {
            margin: 0;
        }
        .cart-item-total {
            font-weight: bold;
        }
        .cart-total {
            margin-top: 10px;
            text-align: right;
        }
        .cart-total h5 {
            margin: 0;
            font-weight: bold;
        }
        .preorder-button {
            margin-top: 10px;
            text-align: right;
        }
        .edit-delete-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 5px;
        }
        .edit-delete-buttons button {
            margin-left: 5px;
        }
    </style>
</head>
<body>

<?php include("nav.php"); ?> <!-- Include navigation -->

<div class="container mt-5">
    <!-- Search and filter buttons -->
    <div class="search-buttons-container">
        <!-- Search input and button -->
        <div class="input-group">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by cuisine type...">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button" id="searchButton">Search</button>
            </div>
        </div>
        <!-- Filter buttons -->
        <button class="btn btn-outline-primary filter-button active" data-category="all">All items</button>
        <button class="btn btn-outline-primary filter-button" data-category="special food">Special Food</button>
        <button class="btn btn-outline-primary filter-button" data-category="beverages">Beverages</button>
        <button class="btn btn-outline-primary filter-button" data-category="meal">Meals</button>
    </div>

    <?php
    if(!isLoggedIn()){
    echo '<p>Please <a href="login.php">Login</a> to make a preorder.</p>';
    }
    ?>

    <div class="row" id="menuItems">
        <?php
        if (empty($items)) {
            echo '<div class="col-md-12 no-items-message" id="noItemsMessage">';
            echo '<p>No items found.</p>';
            echo '</div>';
        } else {
            foreach ($items as $item) {
                echo '<div class="col-md-3 menu-item" data-category="' . $item['item_category'] . '" data-name="' . $item['name'] . '" data-price="' . $item['price'] . '">';
                echo '<img src="' . $item['image_url'] . '" class="img-fluid" alt="' . $item['name'] . '">';
                echo '<h4>' . $item['name'] . '</h4>';
                echo '<p class="cousin-type">' . $item['cousin_type'] . '</p>';
                echo '<p>' . $item['description'] . '</p>';
                echo '<p>$' . number_format($item['price'], 2) . '</p>';

                // Display "Add to Cart" button only if user is logged in
                if (isLoggedIn()) {
                    echo '<div class="form-group">';
                    echo '<input type="number" class="form-control quantity-input" value="1" min="1">';
                    echo '<button class="btn btn-primary mt-3 add-to-cart-button">Add to Cart</button>';
                    echo '</div>';
                } 
                echo '</div>';
            }
        }
        ?>
</div>
<div class="no-items-message" id="noItemsMessage" style="display: none;">
        <p>No items found.</p>
    </div>

    </div>
    <?php if (isLoggedIn()): ?>
    <div class="cart-items-container">
        <h3>Your Cart</h3>
        <div id="cartItemsList">
            <!-- Cart items will be dynamically added here -->
        </div>
        <div class="cart-total">
            <h5>Total Amount: $<span id="totalAmount">0.00</span></h5>
        </div>
        <form id="preorderForm" action="preorder.php" method="POST" style="display: none;">
            <input type="hidden" name="cart_data" id="cartDataInput">
        </form>
        
        <div class="preorder-button" style="display: none;">
            <button id="preorderButton" class="btn btn-success">Preorder Selected Items</button>
        </div>
        
    </div>   
    
    <?php endif; ?>
</div>

<footer class="text-center mt-5">
    <div class="container">
        <p>&copy; 2024 The Gallery Café. All Rights Reserved.</p>
    </div>
</footer>

<!-- Include necessary JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
<script>
   $(document).ready(function() {
    var cart = []; // Array to store cart items
    var totalAmount = 0; // Total amount variable

    // Function to update cart display
    function updateCartDisplay() {
        $('#cartItemsList').empty(); // Clear existing cart items
        totalAmount = 0; // Reset total amount

        // Loop through cart items and display in cart
        cart.forEach(function(item, index) {
            var itemTotal = item.quantity * item.price;
            totalAmount += itemTotal; // Update total amount
            var cartItemHtml = '<div class="cart-item">';
            cartItemHtml += '<div class="cart-item-info">';
            cartItemHtml += '<p>' + item.name + ' - Quantity: ' + item.quantity + '</p>';
            cartItemHtml += '<p class="cart-item-total">$' + itemTotal.toFixed(2) + '</p>';
            cartItemHtml += '<div class="edit-delete-buttons">';
            cartItemHtml += '<button class="btn btn-info btn-sm edit-item-button" data-index="' + index + '">Edit</button>';
            cartItemHtml += '<button class="btn btn-danger btn-sm delete-item-button" data-index="' + index + '">Delete</button>';
            cartItemHtml += '</div>';
            cartItemHtml += '</div>';
            cartItemHtml += '</div>';
            $('#cartItemsList').append(cartItemHtml); // Append item to cart list
        });

        // Update total amount display
        $('#totalAmount').text(totalAmount.toFixed(2));

        // Show preorder button if cart has items
        if (cart.length > 0) {
            $('.preorder-button').show();
        } else {
            $('.preorder-button').hide();
        }
    }

    // Add to cart button click event
    $('.add-to-cart-button').click(function() {
        var item = $(this).closest('.menu-item');
        var itemName = item.data('name');
        var itemPrice = item.data('price');
        var quantity = parseInt(item.find('.quantity-input').val());

        // Check if item already exists in cart
        var existingItemIndex = cart.findIndex(function(cartItem) {
            return cartItem.name === itemName;
        });

        if (existingItemIndex !== -1) {
            // If item exists, update quantity
            cart[existingItemIndex].quantity += quantity;
        } else {
            // If item does not exist, add to cart
            cart.push({
                name: itemName,
                price: itemPrice,
                quantity: quantity
            });
        }

        // Update cart display
        updateCartDisplay();

        // Alert user item added to cart successfully
        alert('Item "' + itemName + '" added to cart successfully!');
    });

    // Edit item button click event
    $(document).on('click', '.edit-item-button', function() {
        var index = $(this).data('index');
        var newQuantity = parseInt(prompt('Enter new quantity for ' + cart[index].name + ':', cart[index].quantity));

        // Validate and update quantity if new quantity is valid
        if (!isNaN(newQuantity) && newQuantity >= 1) {
            cart[index].quantity = newQuantity;
            totalAmount = 0; // Reset total amount
            updateCartDisplay(); // Update cart display
            alert('Quantity updated successfully!');
        } else {
            alert('Invalid quantity! Please enter a valid number.');
        }
    });

    // Delete item button click event
    $(document).on('click', '.delete-item-button', function() {
        var index = $(this).data('index');
        var itemName = cart[index].name;
        if (confirm('Are you sure you want to delete ' + itemName + ' from your cart?')) {
            cart.splice(index, 1); // Remove item from cart array
            totalAmount = 0; // Reset total amount
            updateCartDisplay(); // Update cart display
            alert('Item "' + itemName + '" deleted from cart successfully!');
        }
    });

    // Preorder button click event
    $('#preorderButton').click(function() {
        // Serialize cart data
        var cartData = JSON.stringify(cart);

        // Set the serialized cart data to the hidden input field
        $('#cartDataInput').val(cartData);

        // Submit the form
        $('#preorderForm').submit();

        alert('Preorder Successful! Total Amount: $' + totalAmount.toFixed(2));
        cart = [];
        totalAmount = 0;
        updateCartDisplay();
    });

    // Search button click event
    $('#searchButton').click(function() {
        var searchTerm = $('#searchInput').val().toLowerCase();
        var itemsFound = false; // Flag to check if any item matches the search term

        $('.menu-item').each(function() {
            var itemName = $(this).data('name').toLowerCase();
            if (itemName.includes(searchTerm)) {
                $(this).show();
                itemsFound = true; // At least one item matches the search term
            } else {
                $(this).hide();
            }
        });

        // Show or hide the no items message based on whether any items were found
        if (itemsFound) {
            $('#noItemsMessage').hide();
        } else {
            $('#noItemsMessage').show().text('No items found matching your search.');
        }
    });

    // Filter button click event
    $('.filter-button').click(function() {
        var category = $(this).data('category');
        $('.filter-button').removeClass('active');
        $(this).addClass('active');
        if (category === 'all') {
            $('.menu-item').show();
            $('#noItemsMessage').hide(); // Hide the message when showing all items
        } else {
            var itemsFound = false; // Flag to check if any item matches the selected category
            $('.menu-item').each(function() {
                var itemCategory = $(this).data('category');
                if (itemCategory === category) {
                    $(this).show();
                    itemsFound = true; // At least one item matches the category
                } else {
                    $(this).hide();
                }
            });
            // Show or hide the no items message based on whether any items were found
            if (!itemsFound) {
                $('#noItemsMessage').show().text('No items found in this category.');
            } else {
                $('#noItemsMessage').hide();
            }
        }
    });

    // Initialize: Show all items initially
    $('.menu-item').show();
});

</script>
</body>
</html>
