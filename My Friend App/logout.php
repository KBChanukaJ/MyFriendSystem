<?php
// Initialize session
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the Home page
header('Location: index.php');
exit();
?>
