<?php
// Start session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 1 for debugging

// Set headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Include config
if (!file_exists('config.php')) {
    echo json_encode([
        'success' => false,
        'message' => 'Configuration file not found'
    ]);
    exit;
}

require_once 'config.php';

// Get POST data
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Debug logging (remove in production)
error_log("Login attempt - Username: " . $username);

// Validate input
if (empty($username) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Username and password are required'
    ]);
    exit;
}

try {
    // Get database connection
    $conn = getDBConnection();
    
    if (!$conn) {
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed'
        ]);
        exit;
    }
    
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    
    if (!$stmt) {
        echo json_encode([
            'success' => false,
            'message' => 'Database query preparation failed: ' . $conn->error
        ]);
        exit;
    }
    
    $stmt->bind_param("s", $username);
    
    if (!$stmt->execute()) {
        echo json_encode([
            'success' => false,
            'message' => 'Database query execution failed: ' . $stmt->error
        ]);
        exit;
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Debug: Check password hash
        error_log("Stored hash: " . $user['password']);
        error_log("Verifying password...");
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user_id' => $user['id'],
                'username' => $user['username']
            ]);
            
            error_log("Login successful for user: " . $username);
        } else {
            // Password is incorrect
            echo json_encode([
                'success' => false,
                'message' => 'Invalid username or password'
            ]);
            
            error_log("Password verification failed for user: " . $username);
        }
    } else {
        // User not found
        echo json_encode([
            'success' => false,
            'message' => 'Invalid username or password'
        ]);
        
        error_log("User not found: " . $username);
    }
    
    $stmt->close();
    closeDBConnection($conn);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
    
    error_log("Login error: " . $e->getMessage());
}
?>