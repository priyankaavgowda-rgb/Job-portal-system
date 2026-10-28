<?php
require_once 'includes/db.php';
$conn->query("UPDATE jobs SET deadline = '2026-08-15'");
echo 'Deadlines updated to 2026-08-15 successfully!';
?>
