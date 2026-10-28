<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';

// Handle job deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM jobs WHERE id = $id");
    header("Location: jobs.php");
    exit();
}

// Handle job status change
if (isset($_GET['action']) && isset($_GET['id'])) {
    $job_id = (int)$_GET['id'];
    $action = $_GET['action'] == 'close' ? 'closed' : 'open';
    $conn->query("UPDATE jobs SET status = '$action' WHERE id = $job_id");
    header("Location: jobs.php");
    exit();
}

$jobs = $conn->query("SELECT j.*, e.company_name FROM jobs j JOIN employer_profiles e ON j.employer_id = e.user_id ORDER BY j.created_at DESC");

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="users.php" class="list-group-item list-group-item-action"><i class="fas fa-users-cog me-2"></i>Manage Users</a>
            <a href="jobs.php" class="list-group-item list-group-item-action active"><i class="fas fa-briefcase me-2"></i>Manage Jobs</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">Manage Jobs</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Job Title</th>
                                <th>Company</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Posted On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($jobs->num_rows > 0): ?>
                                <?php while($job = $jobs->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($job['title']) ?></strong></td>
                                    <td><?= htmlspecialchars($job['company_name']) ?></td>
                                    <td><?= htmlspecialchars($job['category']) ?></td>
                                    <td>
                                        <form method="GET" action="jobs.php" class="d-flex align-items-center mb-0">
                                            <input type="hidden" name="id" value="<?= $job['id'] ?>">
                                            <select name="action" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                                                <option value="open" <?= $job['status'] == 'open' ? 'selected' : '' ?>>Open</option>
                                                <option value="close" <?= $job['status'] == 'closed' ? 'selected' : '' ?>>Closed</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($job['created_at'])) ?></td>
                                    <td>
                                        <a href="../job_details.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-info text-white" target="_blank" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="jobs.php?delete=<?= $job['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this job? All applications will also be deleted.')"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-4">No jobs found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
