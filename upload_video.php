<?php
header('Content-Type: application/json');
require_once 'config.php';

// Get POST data
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

// Validate user_id
if ($user_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid user ID'
    ]);
    exit;
}

// Check if video file is uploaded
if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'success' => false,
        'message' => 'No video file uploaded or upload error occurred'
    ]);
    exit;
}

$video = $_FILES['video'];

// Validate file type
$allowed_types = ['video/mp4', 'video/avi', 'video/mov', 'video/wmv'];
$file_type = mime_content_type($video['tmp_name']);

if (!in_array($file_type, $allowed_types)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid file type. Only video files are allowed.'
    ]);
    exit;
}

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/videos/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique filename
$file_extension = pathinfo($video['name'], PATHINFO_EXTENSION);
$new_filename = 'drowsiness_' . date('YmdHis') . '_' . uniqid() . '.' . $file_extension;
$upload_path = $upload_dir . $new_filename;

// Move uploaded file
if (move_uploaded_file($video['tmp_name'], $upload_path)) {
    $file_size = filesize($upload_path);
    
    // Save to database
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO recordings (user_id, video_path, video_name, file_size) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $user_id, $upload_path, $new_filename, $file_size);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Video uploaded successfully',
            'video_path' => $upload_path,
            'video_name' => $new_filename,
            'recording_id' => $stmt->insert_id
        ]);
    } else {
        // Delete file if database insert fails
        unlink($upload_path);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save video information to database'
        ]);
    }
    
    $stmt->close();
    closeDBConnection($conn);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to move uploaded file'
    ]);
}
?>