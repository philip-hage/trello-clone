<?php
// Start the session
session_start();

session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page
header('Location: ' . $system['address'] . 'auth/login');
?>