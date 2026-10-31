<?php
/**
 * Database Configuration
 * School Management System
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'school_mgmt');
define('SITE_URL', 'http://localhost/aims');
define('SITE_NAME', 'School Management System');

// Create database connection
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Helper function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect function
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Flash message function
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Format date
function formatDate($date, $format = 'd M Y') {
    return date($format, strtotime($date));
}

// Generate unique ID
function generateId($prefix, $table, $column) {
    $db = getDB();
    $year = date('Y');
    $random = rand(1000, 9999);
    $id = $prefix . $year . $random;
    
    // Check if ID exists
    $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = ?");
    $stmt->execute([$id]);
    
    if ($stmt->fetchColumn() > 0) {
        return generateId($prefix, $table, $column);
    }
    
    return $id;
}

// Upload file function
function uploadFile($file, $directory = 'uploads/') {
    $target_dir = __DIR__ . '/../' . $directory;
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    if ($file['size'] > 5000000) { // 5MB max
        return ['success' => false, 'message' => 'File too large'];
    }
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => $directory . $new_filename];
    }
    
    return ['success' => false, 'message' => 'Failed to upload file'];
}
