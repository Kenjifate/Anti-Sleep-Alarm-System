<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'anti_sleep_alarm');

// Create connection
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        // Set charset to utf8
        $conn->set_charset("utf8");
        
        return $conn;
    } catch (Exception $e) {
        // Log error
        error_log("Database connection error: " . $e->getMessage());
        throw $e;
    }
}

// Close connection
function closeDBConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Set timezone
date_default_timezone_set('Asia/Manila');
?>