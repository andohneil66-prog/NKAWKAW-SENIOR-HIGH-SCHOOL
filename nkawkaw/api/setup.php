<?php
$host = 'localhost';
$db   = 'nkawkaw_shs';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $db");
    $pdo->exec("USE $db");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id VARCHAR(20) PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        phone VARCHAR(20),
        class VARCHAR(20),
        track VARCHAR(50),
        password VARCHAR(255) NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS news (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        category ENUM('academic', 'sports', 'events', 'announcement') DEFAULT 'announcement',
        content TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        category VARCHAR(50) DEFAULT 'general',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(20),
        subject VARCHAR(100),
        score INT,
        grade VARCHAR(5),
        exam_type VARCHAR(50),
        exam_date DATE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id)
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins WHERE username = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO admins (username, password) VALUES ('admin', 'neil2010/11/7')");
    }
    
    echo "Database setup complete! Tables created: students, news, gallery, results, admins";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>