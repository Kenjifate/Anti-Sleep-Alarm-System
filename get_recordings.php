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
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';

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
    
    // Build query
    $query = "SELECT id, video_path, video_name, file_size, timestamp 
              FROM recordings 
              WHERE user_id = ?";
    
    $params = [$user_id];
    $types = "i";
    
    // Add date filter if provided
    if (!empty($filter_date)) {
        $query .= " AND DATE(timestamp) = ?";
        $params[] = $filter_date;
        $types .= "s";
    }
    
    $query .= " ORDER BY timestamp DESC";
    
    // Prepare and execute statement
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $recordings = [];
    while ($row = $result->fetch_assoc()) {
        $recordings[] = [
            'id' => $row['id'],
            'video_path' => $row['video_path'],
            'video_name' => $row['video_name'],
            'file_size' => $row['file_size'],
            'timestamp' => $row['timestamp']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'recordings' => $recordings
    ]);
    
    $stmt->close();
    closeDBConnection($conn);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'recordings' => []
    ]);
}
?>