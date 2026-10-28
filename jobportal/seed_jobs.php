<?php
require_once 'includes/db.php';

// Truncate existing jobs for a clean slate
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("TRUNCATE TABLE jobs");
$conn->query("TRUNCATE TABLE applications");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Create multiple employers
$companies = [
    ['name' => 'Tech Innovations', 'email' => 'hr@techinnovations.com', 'desc' => 'Leading software company building the future.', 'loc' => 'San Francisco, CA'],
    ['name' => 'Global Finance Corp', 'email' => 'careers@globalfinance.com', 'desc' => 'International banking and financial services.', 'loc' => 'New York, NY'],
    ['name' => 'HealthPlus Solutions', 'email' => 'jobs@healthplus.com', 'desc' => 'Innovating healthcare technology for everyone.', 'loc' => 'Boston, MA']
];

$employer_ids = [];
foreach ($companies as $comp) {
    $email = $comp['email'];
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $employer_ids[] = $check->fetch_assoc()['id'];
    } else {
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (name, email, password, role) VALUES ('{$comp['name']}', '$email', '$password', 'employer')");
        $emp_id = $conn->insert_id;
        $conn->query("INSERT INTO employer_profiles (user_id, company_name, company_description, location) VALUES ($emp_id, '{$comp['name']}', '{$comp['desc']}', '{$comp['loc']}')");
        $employer_ids[] = $emp_id;
    }
}

$jobs = [
    [
        'employer_id' => $employer_ids[0], // Tech Innovations
        'title' => 'Senior Frontend Developer',
        'category' => 'IT',
        'location' => 'Remote',
        'salary' => '$120,000 - $150,000',
        'deadline' => date('Y-m-d', strtotime('+30 days')),
        'status' => 'open',
        'description' => 'We are looking for a Senior Frontend Developer with expertise in React, Vue, or Angular to lead our frontend architecture.',
        'requirements' => '- 5+ years of experience in JavaScript\n- Strong CSS/SASS skills\n- Experience with modern frontend frameworks'
    ],
    [
        'employer_id' => $employer_ids[0], // Tech Innovations
        'title' => 'Product Designer',
        'category' => 'Engineering',
        'location' => 'San Francisco, CA',
        'salary' => '$110,000 - $140,000',
        'deadline' => date('Y-m-d', strtotime('-5 days')), // Closed job
        'status' => 'closed',
        'description' => 'Design user-friendly interfaces for our core SaaS product.',
        'requirements' => '- Strong portfolio\n- Figma expert'
    ],
    [
        'employer_id' => $employer_ids[1], // Global Finance Corp
        'title' => 'Financial Analyst',
        'category' => 'Finance',
        'location' => 'New York, NY',
        'salary' => '$80,000 - $100,000',
        'deadline' => date('Y-m-d', strtotime('+20 days')),
        'status' => 'open',
        'description' => 'We need a detail-oriented Financial Analyst to assist with budgeting, forecasting, and financial reporting.',
        'requirements' => '- Bachelor\'s degree in Finance or Accounting\n- Advanced Excel skills\n- 2+ years of financial analysis experience'
    ],
    [
        'employer_id' => $employer_ids[1], // Global Finance Corp
        'title' => 'Marketing Manager',
        'category' => 'Marketing',
        'location' => 'Chicago, IL',
        'salary' => '$90,000 - $110,000',
        'deadline' => date('Y-m-d', strtotime('+15 days')),
        'status' => 'open',
        'description' => 'Seeking a creative Marketing Manager to drive our brand strategy and digital marketing campaigns.',
        'requirements' => '- Bachelor\'s degree in Marketing\n- 3+ years experience in digital marketing\n- Proven track record of successful campaigns'
    ],
    [
        'employer_id' => $employer_ids[2], // HealthPlus Solutions
        'title' => 'Data Scientist',
        'category' => 'IT',
        'location' => 'Boston, MA',
        'salary' => '$130,000 - $160,000',
        'deadline' => date('Y-m-d', strtotime('+45 days')),
        'status' => 'open',
        'description' => 'Join our analytics team to build machine learning models and extract insights from large datasets.',
        'requirements' => '- Master\'s or Ph.D. in Computer Science, Statistics, or related field\n- Proficiency in Python, R, and SQL\n- Experience with TensorFlow or PyTorch'
    ],
    [
        'employer_id' => $employer_ids[2], // HealthPlus Solutions
        'title' => 'Clinical RN',
        'category' => 'Healthcare',
        'location' => 'Los Angeles, CA',
        'salary' => '$85,000 - $115,000',
        'deadline' => date('Y-m-d', strtotime('+10 days')),
        'status' => 'open',
        'description' => 'Seeking a compassionate Registered Nurse for our acute care facility.',
        'requirements' => '- Valid RN License\n- BLS and ACLS certification\n- 1+ year of hospital experience'
    ]
];

foreach ($jobs as $job) {
    $emp_id = $job['employer_id'];
    $title = $conn->real_escape_string($job['title']);
    $category = $conn->real_escape_string($job['category']);
    $location = $conn->real_escape_string($job['location']);
    $salary = $conn->real_escape_string($job['salary']);
    $deadline = $conn->real_escape_string($job['deadline']);
    $status = $conn->real_escape_string($job['status']);
    $description = $conn->real_escape_string($job['description']);
    $requirements = $conn->real_escape_string($job['requirements']);
    
    $sql = "INSERT INTO jobs (employer_id, title, category, location, salary, deadline, status, description, requirements) 
            VALUES ($emp_id, '$title', '$category', '$location', '$salary', '$deadline', '$status', '$description', '$requirements')";
    $conn->query($sql);
}

echo "Jobs seeded with multiple companies and open/closed statuses!";
?>
