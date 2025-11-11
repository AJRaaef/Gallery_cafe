<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
    if (isset($_POST['order_id'])) {
        $order_id = $_POST['order_id'];
        $query = "UPDATE preorder SET order_status = 'Confirmed' WHERE order_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        echo'<script>alert("Preorder confirmed succesfully");window.location.href="bookings.php";</script>';
        $stmt->close();
    } 
    if (isset($_POST['reservation_id'])) {
        $reservation_id = $_POST['reservation_id'];
        $query = "UPDATE reservations SET status = 'Confirmed' WHERE reservation_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        echo'<script>alert("Reservation Confirmed succesfully");window.location.href="bookings.php";</script>';
        $stmt->close();
    }
}

else{
    echo'<script>aler("Sorry we cancelled ");window.location.href="bookings.php";</script> ';
}



$conn->close();
?>
