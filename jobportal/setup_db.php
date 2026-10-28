<?php
$host = 'localhost';
$username = 'root';
$password = '';

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS jobportal_db";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select database
$conn->select_db("jobportal_db");

// SQL to create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'employer', 'seeker') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS employer_profiles (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        company_name VARCHAR(255) NOT NULL,
        company_description TEXT,
        website VARCHAR(255),
        location VARCHAR(255),
        logo VARCHAR(255),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS seeker_profiles (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        phone VARCHAR(20),
        address TEXT,
        skills TEXT,
        education TEXT,
        experience TEXT,
        resume_path VARCHAR(255),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS jobs (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        employer_id INT(11) NOT NULL,
        title VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        location VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        requirements TEXT NOT NULL,
        salary VARCHAR(100),
        status ENUM('open', 'closed') DEFAULT 'open',
        deadline DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS applications (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        job_id INT(11) NOT NULL,
        seeker_id INT(11) NOT NULL,
        status ENUM('applied', 'shortlisted', 'rejected', 'selected') DEFAULT 'applied',
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
        FOREIGN KEY (seeker_id) REFERENCES users(id) ON DELETE CASCADE
    )"
];

foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

// Insert mock data if users table is empty
$result = $conn->query("SELECT * FROM users");
if ($result->num_rows == 0) {
    $hashed_password = password_hash('password123', PASSWORD_DEFAULT);
    
    // Insert Admin
    $conn->query("INSERT INTO users (name, email, password, role) VALUES ('Admin User', 'admin@jobportal.com', '$hashed_password', 'admin')");
    
    // Insert Employer
    $conn->query("INSERT INTO users (name, email, password, role) VALUES ('Employer User', 'employer@company.com', '$hashed_password', 'employer')");
    $employer_id = $conn->insert_id;
    $conn->query("INSERT INTO employer_profiles (user_id, company_name, company_description, location) VALUES ($employer_id, 'Tech Corp', 'A leading tech company', 'New York')");

    // Insert Seeker
    $conn->query("INSERT INTO users (name, email, password, role) VALUES ('Job Seeker', 'seeker@gmail.com', '$hashed_password', 'seeker')");
    $seeker_id = $conn->insert_id;
    $conn->query("INSERT INTO seeker_profiles (user_id, phone, address, skills) VALUES ($seeker_id, '1234567890', '123 Main St, NY', 'PHP, MySQL, Web Development')");

    echo "Default users created successfully!<br>";
    echo "Admin: admin@jobportal.com / password123<br>";
    echo "Employer: employer@company.com / password123<br>";
    echo "Seeker: seeker@gmail.com / password123<br>";
}

$conn->close();
?>
