<?php
session_start();

// Clear all session data
$_SESSION = array();

// Destroy session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy session
session_destroy();

require_once __DIR__ . '/../config/database.php';

// Redirect to login page
redirect(SITE_URL . '/auth/login.php');
