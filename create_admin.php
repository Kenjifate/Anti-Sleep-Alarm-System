<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// ============================================
// CHANGE THESE VALUES TO YOUR DESIRED CREDENTIALS
// ============================================
$new_username = 'admin';           // Change this to your username
$new_password = 'laters123';        // Change this to your password
$new_email = 'admin@antisleep.com'; // Change this to your email
// ============================================

echo "<h1>Create New User</h1>";
echo "<hr>";

try {
    $conn = getDBConnection();
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $new_username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<h2 style='color: orange;'>⚠️ User '{$new_username}' already exists. Updating password...</h2>";
        
        // Update existing user's password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET password = ?, email = ? WHERE username = ?");
        $stmt->bind_param("sss", $hashed_password, $new_email, $new_username);
        
        if ($stmt->execute()) {
            echo "✓ Password updated successfully!<br><br>";
        } else {
            echo "✗ Failed to update password: " . $stmt->error . "<br>";
            exit;
        }
    } else {
        echo "<h2>Creating new user '{$new_username}'...</h2>";
        
        // Hash the password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $new_username, $hashed_password, $new_email);
        
        if ($stmt->execute()) {
            echo "✓ <strong>User created successfully!</strong><br><br>";
        } else {
            echo "✗ Failed to create user: " . $stmt->error . "<br>";
            exit;
        }
    }
    
    echo "<h2>Password Hash Details:</h2>";
    echo "<div style='background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Plain text password:</strong> " . htmlspecialchars($new_password) . "<br>";
    echo "<strong>Hashed password:</strong> <span style='font-size: 12px; word-break: break-all;'>" . $hashed_password . "</span><br>";
    echo "</div>";
    
    // Verify the password works
    echo "<h2>Testing Password Verification:</h2>";
    
    // Retrieve the user
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $new_username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Test password
    if (password_verify($new_password, $user['password'])) {
        echo "✓ <span style='color: green; font-size: 18px;'><strong>Password verification SUCCESSFUL! ✓</strong></span><br>";
        echo "✓ You can now login with these credentials<br>";
    } else {
        echo "✗ <span style='color: red; font-size: 18px;'><strong>Password verification FAILED! ✗</strong></span><br>";
        echo "Something went wrong. Please try again.<br>";
    }
    
    echo "<br><h2>Your Login Credentials:</h2>";
    echo "<div style='background: #667eea; color: white; padding: 20px; border-radius: 10px; font-size: 18px;'>";
    echo "<strong>Username:</strong> " . htmlspecialchars($new_username) . "<br>";
    echo "<strong>Password:</strong> " . htmlspecialchars($new_password) . "<br>";
    echo "</div>";
    
    echo "<br><h2>All Users in Database:</h2>";
    $result = $conn->query("SELECT id, username, email, created_at FROM users");
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #667eea; color: white;'><th>ID</th><th>Username</th><th>Email</th><th>Created At</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><strong>" . htmlspecialchars($row['username']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><hr>";
    echo "<a href='login.html' style='background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 16px; display: inline-block;'>Go to Login Page →</a>";
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo "<div style='background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px;'>";
    echo "✗ <strong>Error:</strong> " . $e->getMessage();
    echo "</div>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background: #f5f5f5;
        max-width: 800px;
        margin: 20px auto;
    }
    h1 {
        color: #667eea;
    }
    h2 {
        color: #333;
        margin-top: 30px;
    }
</style>