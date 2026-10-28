<?php
require_once 'includes/db.php';

// 1. Add job_type column to jobs table
$check_col = $conn->query("SHOW COLUMNS FROM jobs LIKE 'job_type'");
if ($check_col->num_rows == 0) {
    $conn->query("ALTER TABLE jobs ADD COLUMN job_type VARCHAR(50) DEFAULT 'Full Time' AFTER category");
}

// 2. Re-assign all jobs to employer@company.com
$res = $conn->query("SELECT id FROM users WHERE email = 'employer@company.com'");
if ($res->num_rows > 0) {
    $default_employer_id = $res->fetch_assoc()['id'];
    $conn->query("UPDATE jobs SET employer_id = $default_employer_id");
} else {
    // If somehow deleted, recreate
    $password = password_hash('password123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (name, email, password, role) VALUES ('Common Employer', 'employer@company.com', '$password', 'employer')");
    $default_employer_id = $conn->insert_id;
    $conn->query("INSERT INTO employer_profiles (user_id, company_name) VALUES ($default_employer_id, 'Tech Innovations')");
    $conn->query("UPDATE jobs SET employer_id = $default_employer_id");
}

// 3. Add new jobs for new categories
$new_jobs = [
    [
        'title' => 'Sales Executive',
        'category' => 'Sales and Business Development',
        'job_type' => 'Full Time',
        'location' => 'New York, NY',
        'salary' => '$60,000 + Commission',
        'deadline' => date('Y-m-d', strtotime('+30 days')),
        'description' => 'Looking for an aggressive sales executive to drive revenue growth.',
        'requirements' => '2+ years in B2B sales'
    ],
    [
        'title' => 'HR Manager',
        'category' => 'HR and Administration',
        'job_type' => 'Work From Home',
        'location' => 'Remote',
        'salary' => '$80,000',
        'deadline' => date('Y-m-d', strtotime('+20 days')),
        'description' => 'Manage remote HR operations and employee relations.',
        'requirements' => '5+ years HR experience'
    ],
    [
        'title' => 'Online English Tutor',
        'category' => 'Teaching',
        'job_type' => 'Part-time',
        'location' => 'Remote',
        'salary' => '$25/hr',
        'deadline' => date('Y-m-d', strtotime('+15 days')),
        'description' => 'Teach English to international students part-time.',
        'requirements' => 'TEFL certification required'
    ]
];

foreach ($new_jobs as $job) {
    $title = $conn->real_escape_string($job['title']);
    $category = $conn->real_escape_string($job['category']);
    $job_type = $conn->real_escape_string($job['job_type']);
    $location = $conn->real_escape_string($job['location']);
    $salary = $conn->real_escape_string($job['salary']);
    $deadline = $conn->real_escape_string($job['deadline']);
    $description = $conn->real_escape_string($job['description']);
    $requirements = $conn->real_escape_string($job['requirements']);
    
    // Check if exists
    $check = $conn->query("SELECT id FROM jobs WHERE title = '$title'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO jobs (employer_id, title, category, job_type, location, salary, deadline, description, requirements) 
                      VALUES ($default_employer_id, '$title', '$category', '$job_type', '$location', '$salary', '$deadline', '$description', '$requirements')");
    }
}

// Update existing jobs to have varied job types randomly for visual effect
$conn->query("UPDATE jobs SET job_type = 'Work From Home' WHERE title LIKE '%Frontend%' OR title LIKE '%Developer%'");
$conn->query("UPDATE jobs SET job_type = 'Part-time' WHERE title LIKE '%Analyst%'");

echo "Upgrade complete!";
?>
