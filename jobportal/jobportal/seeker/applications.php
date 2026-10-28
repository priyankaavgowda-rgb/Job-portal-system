<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seeker') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';

$seeker_id = $_SESSION['user_id'];

$sql = "SELECT a.*, j.title, COALESCE(j.company_name_override, e.company_name) as company_name, j.location 
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        JOIN employer_profiles e ON j.employer_id = e.user_id 
        WHERE a.seeker_id = $seeker_id 
        ORDER BY a.applied_at DESC";
$applications = $conn->query($sql);

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="profile.php" class="list-group-item list-group-item-action"><i class="fas fa-user-circle me-2"></i>My Profile</a>
            <a href="applications.php" class="list-group-item list-group-item-action active"><i class="fas fa-file-alt me-2"></i>My Applications</a>
            <a href="../jobs.php" class="list-group-item list-group-item-action"><i class="fas fa-search me-2"></i>Search Jobs</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">My Applications</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Job Title</th>
                                <th>Company</th>
                                <th>Location</th>
                                <th>Applied On</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($applications->num_rows > 0): ?>
                                <?php while($app = $applications->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($app['title']) ?></strong></td>
                                    <td><?= htmlspecialchars($app['company_name']) ?></td>
                                    <td><?= htmlspecialchars($app['location']) ?></td>
                                    <td><?= date('M d, Y', strtotime($app['applied_at'])) ?></td>
                                    <td>
                                        <?php
                                            $badge_class = 'bg-secondary';
                                            if ($app['status'] == 'shortlisted') $badge_class = 'bg-info';
                                            elseif ($app['status'] == 'selected') $badge_class = 'bg-success';
                                            elseif ($app['status'] == 'rejected') $badge_class = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= ucfirst($app['status']) ?></span>
                                    </td>
                                    <td><a href="../job_details.php?id=<?= $app['job_id'] ?>" class="btn btn-sm btn-outline-primary">View Job</a></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-4">You haven't applied for any jobs yet. <a href="../jobs.php">Find jobs</a></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
