<?php
require_once 'includes/db.php';

// Find old tech innovations employer
$res = $conn->query("SELECT id FROM users WHERE email = 'hr@techinnovations.com'");
if ($res->num_rows > 0) {
    $tech_id = $res->fetch_assoc()['id'];
    
    // Find the default employer
    $res2 = $conn->query("SELECT id FROM users WHERE email = 'employer@company.com'");
    if ($res2->num_rows > 0) {
        $default_employer_id = $res2->fetch_assoc()['id'];
        
        // Update jobs and applications
        $conn->query("UPDATE jobs SET employer_id = $default_employer_id WHERE employer_id = $tech_id");
        $conn->query("UPDATE employer_profiles SET company_name = 'Tech Innovations', company_description = 'Leading software company building the future.', location = 'San Francisco, CA' WHERE user_id = $default_employer_id");
        
        // Delete hr@techinnovations.com
        $conn->query("DELETE FROM users WHERE id = $tech_id");
        echo "Fixed DB mapping!";
    }
} else {
    echo "Nothing to fix.";
}
?>
