<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config.php';

// Get POST data
$recording_id = isset($_POST['recording_id']) ? intval($_POST['recording_id']) : 0;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

// Validate input
if ($recording_id <= 0 || $user_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid recording ID or user ID'
    ]);
    exit;
}

try {
    $conn = getDBConnection();
    
    // First, get the recording details to delete the file
    $stmt = $conn->prepare("SELECT video_path FROM recordings WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $recording_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Recording not found or you do not have permission to delete it'
        ]);
        exit;
    }
    
    $recording = $result->fetch_assoc();
    $video_path = $recording['video_path'];
    $stmt->close();
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM recordings WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $recording_id, $user_id);
    
    if ($stmt->execute()) {
        // Delete the physical file
        if (file_exists($video_path)) {
            if (unlink($video_path)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recording deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recording deleted from database but file could not be removed'
                ]);
            }
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Recording deleted from database (file already removed)'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete recording from database'
        ]);
    }
    
    $stmt->close();
    closeDBConnection($conn);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>