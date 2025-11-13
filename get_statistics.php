<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config.php';

// Get user_id from GET parameters
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Validate user_id
if ($user_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid user ID'
    ]);
    exit;
}

try {
    // Get database connection
    $conn = getDBConnection();
    
    // Get total incidents
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM recordings WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_incidents = $result->fetch_assoc()['total'];
    $stmt->close();
    
    // Get today's incidents
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) as today FROM recordings WHERE user_id = ? AND DATE(timestamp) = ?");
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $today_incidents = $result->fetch_assoc()['today'];
    $stmt->close();
    
    // Get total storage used (in MB)
    $stmt = $conn->prepare("SELECT SUM(file_size) as total_size FROM recordings WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_size = $result->fetch_assoc()['total_size'];
    $storage_used = $total_size ? round($total_size / (1024 * 1024), 2) : 0;
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'total_incidents' => $total_incidents,
        'today_incidents' => $today_incidents,
        'storage_used' => $storage_used
    ]);
    
    closeDBConnection($conn);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'total_incidents' => 0,
        'today_incidents' => 0,
        'storage_used' => 0
    ]);
}
?>