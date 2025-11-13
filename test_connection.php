<?php
// Test script to check if everything is configured correctly
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Anti-Sleep Alarm System - Configuration Test</h1>";
echo "<hr>";

// Test 1: Check if config.php exists
echo "<h2>Test 1: Configuration File</h2>";
if (file_exists('config.php')) {
    echo "✓ config.php exists<br>";
    require_once 'config.php';
    echo "✓ config.php loaded successfully<br>";
} else {
    echo "✗ config.php NOT FOUND!<br>";
    die("Please ensure config.php is in the same directory.");
}

echo "<hr>";

// Test 2: Database Connection
echo "<h2>Test 2: Database Connection</h2>";
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        echo "✗ Connection failed: " . $conn->connect_error . "<br>";
    } else {
        echo "✓ Successfully connected to MySQL<br>";
        echo "✓ Database: " . DB_NAME . "<br>";
    }
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 3: Check if database exists
echo "<h2>Test 3: Database Structure</h2>";
if (isset($conn) && !$conn->connect_error) {
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "✓ Table 'users' exists<br>";
        
        // Count users
        $result = $conn->query("SELECT COUNT(*) as count FROM users");
        $row = $result->fetch_assoc();
        echo "✓ Number of users: " . $row['count'] . "<br>";
    } else {
        echo "✗ Table 'users' NOT FOUND! Please import database.sql<br>";
    }
    
    // Check if recordings table exists
    $result = $conn->query("SHOW TABLES LIKE 'recordings'");
    if ($result->num_rows > 0) {
        echo "✓ Table 'recordings' exists<br>";
    } else {
        echo "✗ Table 'recordings' NOT FOUND! Please import database.sql<br>";
    }
}

echo "<hr>";

// Test 4: Check admin user
echo "<h2>Test 4: Default Admin User</h2>";
if (isset($conn) && !$conn->connect_error) {
    $result = $conn->query("SELECT * FROM users WHERE username = 'admin'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "✓ Admin user exists<br>";
        echo "✓ Username: " . $user['username'] . "<br>";
        echo "✓ User ID: " . $user['id'] . "<br>";
        
        // Test password verification
        if (password_verify('admin123', $user['password'])) {
            echo "✓ Default password 'admin123' is correct<br>";
        } else {
            echo "✗ Default password verification failed<br>";
        }
    } else {
        echo "✗ Admin user NOT FOUND!<br>";
        echo "<br><strong>Creating admin user...</strong><br>";
        
        $username = 'admin';
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $email = 'admin@antisleep.com';
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $email);
        
        if ($stmt->execute()) {
            echo "✓ Admin user created successfully!<br>";
            echo "✓ Username: admin<br>";
            echo "✓ Password: admin123<br>";
        } else {
            echo "✗ Failed to create admin user: " . $stmt->error . "<br>";
        }
    }
}

echo "<hr>";

// Test 5: Check uploads directory
echo "<h2>Test 5: Uploads Directory</h2>";
$upload_dir = 'uploads/videos/';

if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
    echo "✓ Created 'uploads' directory<br>";
}

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
    echo "✓ Created 'uploads/videos' directory<br>";
}

if (is_writable($upload_dir)) {
    echo "✓ Upload directory is writable<br>";
} else {
    echo "✗ Upload directory is NOT writable! Please set permissions to 777<br>";
}

echo "<hr>";

// Test 6: PHP Configuration
echo "<h2>Test 6: PHP Configuration</h2>";
echo "✓ PHP Version: " . phpversion() . "<br>";
echo "✓ Upload Max Filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "✓ Post Max Size: " . ini_get('post_max_size') . "<br>";
echo "✓ Max Execution Time: " . ini_get('max_execution_time') . " seconds<br>";

echo "<hr>";

// Test 7: Test login.php
echo "<h2>Test 7: Login Functionality</h2>";
echo "<a href='test_login.php' target='_blank'>Click here to test login API</a><br>";

echo "<hr>";
echo "<h2>Summary</h2>";
echo "If all tests show ✓, your system is properly configured.<br>";
echo "If you see any ✗, please fix those issues first.<br>";
echo "<br>";
echo "<a href='login.html'>Go to Login Page</a>";

if (isset($conn)) {
    $conn->close();
}
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
    h2 {
        color: #333;
        margin-top: 20px;
    }
    a {
        color: #667eea;
        text-decoration: none;
        font-weight: bold;
    }
    a:hover {
        text-decoration: underline;
    }
</style>