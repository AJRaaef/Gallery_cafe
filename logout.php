<?php
session_start(); // Start session if not already started

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

echo '';
// Redirect to the login page or another page after logout
header("Location: index.php");
exit;
?>
