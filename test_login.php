<?php
// Test login functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Login API Test</h1>";
echo "<hr>";

// Simulate POST request
$_POST['username'] = 'admin';
$_POST['password'] = 'admin123';

echo "<h2>Testing with credentials:</h2>";
echo "Username: admin<br>";
echo "Password: admin123<br>";
echo "<hr>";

echo "<h2>API Response:</h2>";
echo "<pre>";

// Capture output
ob_start();
include 'login.php';
$response = ob_get_clean();

echo htmlspecialchars($response);
echo "</pre>";

echo "<hr>";
echo "<h2>Parsed Response:</h2>";
$data = json_decode($response, true);
if ($data) {
    echo "<strong>Success:</strong> " . ($data['success'] ? 'Yes' : 'No') . "<br>";
    echo "<strong>Message:</strong> " . $data['message'] . "<br>";
    if (isset($data['user_id'])) {
        echo "<strong>User ID:</strong> " . $data['user_id'] . "<br>";
        echo "<strong>Username:</strong> " . $data['username'] . "<br>";
    }
} else {
    echo "Failed to parse JSON response<br>";
    echo "Raw response: " . htmlspecialchars($response);
}

echo "<hr>";
echo "<a href='test_connection.php'>Back to Configuration Test</a> | ";
echo "<a href='login.html'>Go to Login Page</a>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background: #f5f5f5;
    }
    h1 {
        color: #667eea;
    }
    pre {
        background: #fff;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    a {
        color: #667eea;
        text-decoration: none;
        font-weight: bold;
    }
</style>