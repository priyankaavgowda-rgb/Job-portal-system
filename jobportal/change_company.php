<?php
require_once 'includes/db.php';

// Add override column if not exists
$check_col = $conn->query("SHOW COLUMNS FROM jobs LIKE 'company_name_override'");
if ($check_col->num_rows == 0) {
    $conn->query("ALTER TABLE jobs ADD COLUMN company_name_override VARCHAR(255) NULL AFTER employer_id");
}

// Assign fake company names to the existing jobs
$companies = ['Google', 'Microsoft', 'Amazon', 'Facebook', 'Netflix', 'Apple', 'Tesla', 'SpaceX', 'Uber'];
$res = $conn->query("SELECT id FROM jobs");
$i = 0;
while ($row = $res->fetch_assoc()) {
    $id = $row['id'];
    $comp = $companies[$i % count($companies)];
    $conn->query("UPDATE jobs SET company_name_override = '$comp' WHERE id = $id");
    $i++;
}

echo "Added company name overrides successfully!";
?>
