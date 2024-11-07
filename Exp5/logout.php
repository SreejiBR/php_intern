<?php
session_start();

// Destroy the session to log the admin out
session_destroy();

// Redirect to login page
header('Location: index.php');
exit;
?>
