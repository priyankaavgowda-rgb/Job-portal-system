<?php
session_start();
require_once 'includes/db.php';

$search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$job_type = isset($_GET['job_type']) ? $conn->real_escape_string($_GET['job_type']) : '';

$sql = "SELECT j.*, COALESCE(j.company_name_override, e.company_name) as company_name FROM jobs j 
        JOIN employer_profiles e ON j.employer_id = e.user_id 
        WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (j.title LIKE '%$search%' OR e.company_name LIKE '%$search%' OR j.location LIKE '%$search%')";
}
if (!empty($category)) {
    $sql .= " AND j.category = '$category'";
}
if (!empty($job_type)) {
    $sql .= " AND j.job_type = '$job_type'";
}

$sql .= " ORDER BY j.created_at DESC";
$result = $conn->query($sql);

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-light">
            <div class="card-body">
                <form action="jobs.php" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="q" class="form-control form-control-lg" placeholder="Search by title, company, location" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="category" class="form-select form-select-lg">
                            <option value="">All Categories</option>
                            <option value="IT" <?= $category == 'IT' ? 'selected' : '' ?>>IT & Software</option>
                            <option value="Finance" <?= $category == 'Finance' ? 'selected' : '' ?>>Finance</option>
                            <option value="Marketing" <?= $category == 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                            <option value="Engineering" <?= $category == 'Engineering' ? 'selected' : '' ?>>Engineering</option>
                            <option value="Healthcare" <?= $category == 'Healthcare' ? 'selected' : '' ?>>Healthcare</option>
                            <option value="Sales and Business Development" <?= $category == 'Sales and Business Development' ? 'selected' : '' ?>>Sales and Business Development</option>
                            <option value="HR and Administration" <?= $category == 'HR and Administration' ? 'selected' : '' ?>>HR and Administration</option>
                            <option value="Teaching" <?= $category == 'Teaching' ? 'selected' : '' ?>>Teaching</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="job_type" class="form-select form-select-lg">
                            <option value="">All Job Types</option>
                            <option value="Full Time" <?= $job_type == 'Full Time' ? 'selected' : '' ?>>Full Time</option>
                            <option value="Part-time" <?= $job_type == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                            <option value="Work From Home" <?= $job_type == 'Work From Home' ? 'selected' : '' ?>>Work From Home</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<h3 class="mb-4">Job Search Results (<?= $result->num_rows ?> found)</h3>

<div class="row">
    <?php if ($result->num_rows > 0): ?>
        <?php while($job = $result->fetch_assoc()): ?>
            <div class="col-12 mb-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div>
                            <h4 class="card-title text-primary fw-bold mb-1">
                                <a href="job_details.php?id=<?= $job['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($job['title']) ?></a>
                                <?php if($job['status'] == 'closed'): ?>
                                    <span class="badge bg-danger ms-2 fs-6">Closed</span>
                                <?php endif; ?>
                            </h4>
                            <h6 class="card-subtitle mb-2 text-muted"><i class="fas fa-building me-1"></i><?= htmlspecialchars($job['company_name']) ?></h6>
                            <div class="text-secondary small mt-2">
                                <span class="me-3"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($job['location']) ?></span>
                                <span class="me-3"><i class="fas fa-money-bill-wave me-1"></i><?= htmlspecialchars($job['salary']) ?></span>
                                <span class="me-3"><i class="fas fa-briefcase me-1"></i><?= htmlspecialchars($job['category']) ?></span>
                                <span><i class="fas fa-clock me-1"></i><?= htmlspecialchars($job['job_type'] ?? 'Full Time') ?></span>
                            </div>
                        </div>
                        <div class="mt-3 mt-md-0 text-md-end">
                            <a href="job_details.php?id=<?= $job['id'] ?>" class="btn btn-outline-primary mb-2 w-100">View Details</a><br>
                            <small class="text-muted">Posted: <?= date('M d, Y', strtotime($job['created_at'])) ?></small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info text-center">No jobs found matching your criteria.</div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
