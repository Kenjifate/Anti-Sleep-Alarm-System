-- Create database
CREATE DATABASE IF NOT EXISTS anti_sleep_alarm;
USE anti_sleep_alarm;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Recordings table
CREATE TABLE IF NOT EXISTS recordings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    video_path VARCHAR(255) NOT NULL,
    video_name VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- IMPORTANT: After running this SQL, you MUST run create_admin.php 
-- to create the admin user with the correct password hash
-- OR manually run the following (replace YOUR_HASH with actual hash from create_admin.php):

-- Example: INSERT INTO users (username, password, email) VALUES ('admin', 'YOUR_HASH_HERE', 'admin@antisleep.com');