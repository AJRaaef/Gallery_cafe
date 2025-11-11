<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$user_id = null;
$errors = [];

// Fetch user_id from the users table
$username = $_SESSION['username'];
$user_query = "SELECT user_id FROM users WHERE username='$username'";
$user_result = mysqli_query($conn, $user_query);

if (!$user_result) {
    die('User query failed: ' . mysqli_error($conn));
}

$user_row = mysqli_fetch_assoc($user_result);

if (!$user_row) {
    die('User not found for username: ' . $username);
}


// Function to check if the date is in the past
function isDateInPast($date) {
    $currentDate = date('Y-m-d');
    return $date < $currentDate;
}

$user_id = $user_row['user_id'];

// Handle the reservation form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $table_id = mysqli_real_escape_string($conn, $_POST['table_id']);
    $parking_id = mysqli_real_escape_string($conn, $_POST['parking_id']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $guests = mysqli_real_escape_string($conn, $_POST['guests']);

    // Handle "None" selection
    if ($parking_id === '') {
        $parking_id = '3'; // Set to 3 if "None" is selected
    }


    // Validate inputs
    if (empty($table_id)) {
        $errors[] = "Please select a table.";
    }
    if (empty($parking_id)) {
        $errors[] = "Please select a parking slot.";
    }
    if (empty($date)) {
        $errors[] = "Please select a date.";
    }
    if (empty($time)) {
        $errors[] = "Please select a time.";
    }
    if (empty($guests)) {
        $errors[] = "Please enter the number of guests.";
    }

    // Check if the selected table, date, and time are already reserved
    if (empty($errors)) {
        $check_query = "SELECT * FROM reservations WHERE table_id='$table_id' AND date='$date' AND time='$time' AND status='Confirmed'";
        $check_preorder_query = "SELECT * FROM preorder WHERE table_id='$table_id' AND date='$date' AND time='$time' AND order_status='Confirmed'";
        
        $check_result = mysqli_query($conn, $check_query);
        $check_preorder_result = mysqli_query($conn, $check_preorder_query);

        if (mysqli_num_rows($check_result) > 0 || mysqli_num_rows($check_preorder_result) > 0) {
            $errors[] = "Sorry, this table is already reserved for the selected date and time. Please select another table or date.";
        } else {
            $query = "INSERT INTO reservations (user_id, table_id, parking_id, date, time, guests, status) 
                      VALUES ('$user_id', '$table_id', '$parking_id', '$date', '$time', '$guests', 'Pending')";

            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Your reservation has been considered! Kindly please wait for the reservation confirmation message from our staff.'); window.location.href='check_status.php';</script>";
            } else {
                echo "<script>alert('Error: Could not make the reservation.'); window.location.href='reservation.php';</script>";
                echo "Error: " . mysqli_error($conn); // For debugging purposes
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('booktable1.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 90vh;
            font-family: Arial, sans-serif;
            
        }
        .reservation-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        
        .reservation-card {
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent background */
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }
        .card-header {
            background: url('simple.webp') no-repeat center center/cover;
            color: white;
            font-size: 1.5rem;
            text-align: center;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004080;
        }
        .alert {
            margin-bottom: 1rem;
        }
        
    </style>
</head>


<body>
<?php include 'nav.php'; ?> <!-- Include navigation -->
    <div class="reservation-container">
        <div class="reservation-card">
            <div class="card-header">
                <h4>Book a Table and Parking Slot</h4>
            </div>
            <div class="card-body">
                <!-- Display errors if any -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form id="reservationForm" action="reservation.php" method="POST">
                    <div class="form-group">
                        <label for="date">Date:</label>
                        <input type="date" name="date" id="date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Time:</label>
                        <input type="time" name="time" id="time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="table_id">Table ID:</label>
                        <select name="table_id" id="table_id" class="form-control" required>
                            <option value="">Select Table</option>
                            <option value="3">None</option> <!-- "None" option with value 0 -->

                        </select>
                    </div>
                    <div class="form-group">
                        <label for="parking_id">Parking Slot:</label>
                        <select name="parking_id" id="parking_id" class="form-control" required>
                            <option value="">Select Parking Slot</option>
                            <option value="NULL">None</option> <!-- Added "None" option -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="guests">Number of Guests:</label>
                        <input type="number" name="guests" id="guests" class="form-control" required min="2">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Book Table and Parking Slot</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
$(document).ready(function () {
    function loadAvailableOptions() {
        var date = $('#date').val();
        var time = $('#time').val();
        if (date && time) {
            $.ajax({
                type: 'POST',
                url: 'check_availability.php',
                data: { date: date, time: time },
                dataType: 'json',
                success: function (response) {
                    $('#table_id').html('<option value="">Select Table</option>');
                    $('#parking_id').html('<option value="">Select Parking Slot</option><option value="3">None</option>');
                    
                    if (response.tables_available) {
                        $.each(response.tables, function (index, table) {
                            $('#table_id').append('<option value="' + table.table_id + '">' + table.table_name + '</option>');
                        });
                    } else {
                        $('#table_id').html('<option value="">No tables available for this date and time pls select other data or time</option>');
                    }

                    if (response.parking_available) {
                        $.each(response.parking, function (index, slot) {
                            $('#parking_id').append('<option value="' + slot.parking_id + '">' + slot.park_name + '</option>');
                        });
                    } else {
                        $('#parking_id').html('<option value="">No parking slots available for this date and time pls check other date or time</option><option value="3">None</option>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error checking availability:', error);
                }
            });
        }
    }

    $('#date').change(function () {
            var selectedDate = new Date($(this).val());
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            if (selectedDate < today) {
                alert('The selected date is in the past. Please choose a future date.');
                $(this).val('');
            } else {
                loadAvailableOptions();
            }
        });

        $('#time').change(function () {
            loadAvailableOptions();
        });

    $('#date, #time').change(function () {
        loadAvailableOptions();
    });

    $('#table_id, #parking_id').focus(function () {
        if (!$('#date').val() || !$('#time').val()) {
            alert('Please select date and time first.');
            $(this).blur(); // Remove focus from the current field
        }
    });

    $('#reservationForm').submit(function (e) {
        if (!$('#date').val() || !$('#time').val()) {
            alert('Please select date and time first.');
            e.preventDefault();
        }
    });
});
</script>
</body>
</html>
