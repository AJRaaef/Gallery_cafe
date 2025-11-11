<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);

    $response = [
        'tables' => [],
        'parking' => [],
        'tables_available' => false,
        'parking_available' => false
    ];

    // Check available tables
    $table_query = "
        SELECT t.table_id, t.table_name 
        FROM tables t 
        LEFT JOIN reservations r ON t.table_id = r.table_id AND r.date = '$date' AND r.time = '$time' AND r.status = 'Confirmed'
        LEFT JOIN preorder p ON t.table_id = p.table_id AND p.date = '$date' AND p.time = '$time' AND p.order_status = 'Confirmed'
        WHERE t.availability = 'Available' AND r.table_id IS NULL AND p.table_id IS NULL
    ";
    $table_result = mysqli_query($conn, $table_query);
    if (!$table_result) {
        echo json_encode(['error' => 'Error querying tables: ' . mysqli_error($conn)]);
        exit();
    }
    while ($table_row = mysqli_fetch_assoc($table_result)) {
        $response['tables'][] = $table_row;
    }

    if (!empty($response['tables'])) {
        $response['tables_available'] = true;
    }

    // Check available parking slots
    $parking_query = "
        SELECT p.parking_id, p.park_name 
        FROM parking p 
        LEFT JOIN reservations r ON p.parking_id = r.parking_id AND r.date = '$date' AND r.time = '$time' AND r.status = 'Confirmed'
        LEFT JOIN preorder pr ON p.parking_id = pr.parking_id AND pr.date = '$date' AND pr.time = '$time' AND pr.order_status = 'Confirmed'
        WHERE p.availability_status = 'Available' AND r.parking_id IS NULL AND pr.parking_id IS NULL
    ";
    $parking_result = mysqli_query($conn, $parking_query);
    if (!$parking_result) {
        echo json_encode(['error' => 'Error querying parking slots: ' . mysqli_error($conn)]);
        exit();
    }
    while ($parking_row = mysqli_fetch_assoc($parking_result)) {
        $response['parking'][] = $parking_row;
    }

    if (!empty($response['parking'])) {
        $response['parking_available'] = true;
    }

    echo json_encode($response);
}
?>
