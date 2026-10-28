<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';

$employer_id = $_SESSION['user_id'];

$user_email = $conn->query("SELECT email FROM users WHERE id = $employer_id")->fetch_assoc()['email'];
$is_master = ($user_email === 'employer@company.com');

// Handle status update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $job_id = (int)$_GET['id'];
    $action = $_GET['action'] == 'close' ? 'closed' : 'open';
    if ($is_master) {
        $conn->query("UPDATE jobs SET status = '$action' WHERE id = $job_id");
    } else {
        $conn->query("UPDATE jobs SET status = '$action' WHERE id = $job_id AND employer_id = $employer_id");
    }
    header("Location: jobs.php");
    exit();
}

if ($is_master) {
    $jobs = $conn->query("SELECT j.*, COALESCE(j.company_name_override, e.company_name) as display_company FROM jobs j JOIN employer_profiles e ON j.employer_id = e.user_id ORDER BY j.created_at DESC");
} else {
    $jobs = $conn->query("SELECT * FROM jobs WHERE employer_id = $employer_id ORDER BY created_at DESC");
}

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="profile.php" class="list-group-item list-group-item-action"><i class="fas fa-building me-2"></i>Company Profile</a>
            <a href="post_job.php" class="list-group-item list-group-item-action"><i class="fas fa-plus-circle me-2"></i>Post a Job</a>
            <a href="jobs.php" class="list-group-item list-group-item-action active"><i class="fas fa-briefcase me-2"></i>Manage Jobs</a>
            <a href="applications.php" class="list-group-item list-group-item-action"><i class="fas fa-users me-2"></i>Applications</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Manage Posted Jobs</h4>
                <a href="post_job.php" class="btn btn-primary btn-sm">Post New Job</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Job Title</th>
                                <?php if($is_master): ?><th>Company</th><?php endif; ?>
                                <th>Category</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($jobs->num_rows > 0): ?>
                                <?php while($job = $jobs->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($job['title']) ?></strong></td>
                                    <?php if($is_master): ?><td><?= htmlspecialchars($job['display_company']) ?></td><?php endif; ?>
                                    <td><?= htmlspecialchars($job['category']) ?></td>
                                    <td><?= date('M d, Y', strtotime($job['deadline'])) ?></td>
                                    <td>
                                        <form method="GET" action="jobs.php" class="d-flex align-items-center mb-0">
                                            <input type="hidden" name="id" value="<?= $job['id'] ?>">
                                            <select name="action" class="form-select form-select-sm w-auto me-2" onchange="this.form.submit()">
                                                <option value="open" <?= $job['status'] == 'open' ? 'selected' : '' ?>>Open</option>
                                                <option value="close" <?= $job['status'] == 'closed' ? 'selected' : '' ?>>Closed</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="../job_details.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-info text-white" target="_blank" title="View"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="<?= $is_master ? '6' : '5' ?>" class="text-center py-4">You haven't posted any jobs yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
