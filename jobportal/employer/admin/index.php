<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';

// Get counts
$users_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role != 'admin'")->fetch_assoc()['count'];
$jobs_count = $conn->query("SELECT COUNT(*) as count FROM jobs")->fetch_assoc()['count'];
$apps_count = $conn->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'];
$employers_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'employer'")->fetch_assoc()['count'];

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action active"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="users.php" class="list-group-item list-group-item-action"><i class="fas fa-users-cog me-2"></i>Manage Users</a>
            <a href="jobs.php" class="list-group-item list-group-item-action"><i class="fas fa-briefcase me-2"></i>Manage Jobs</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <h3 class="mb-4">Admin Dashboard</h3>
        <p>Welcome, <strong>Administrator</strong>.</p>
        
        <div class="row mb-4">
            <div class="col-md-3 mb-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h1 class="display-5"><i class="fas fa-users"></i></h1>
                        <h3><?= $users_count ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <h1 class="display-5"><i class="fas fa-building"></i></h1>
                        <h3><?= $employers_count ?></h3>
                        <p>Employers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body text-center">
                        <h1 class="display-5"><i class="fas fa-briefcase"></i></h1>
                        <h3><?= $jobs_count ?></h3>
                        <p>Total Jobs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body text-center">
                        <h1 class="display-5"><i class="fas fa-file-alt"></i></h1>
                        <h3><?= $apps_count ?></h3>
                        <p>Total Applications</p>
                    </div>
                </div>
            </div>
        </div>
        
        <h4 class="mb-3">Recent Users</h4>
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_users = $conn->query("SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC LIMIT 5");
                            if ($recent_users->num_rows > 0):
                                while($user = $recent_users->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><span class="badge bg-<?= $user['role'] == 'employer' ? 'primary' : 'success' ?>"><?= ucfirst($user['role']) ?></span></td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" class="text-center">No users found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end">
                    <a href="users.php" class="btn btn-sm btn-outline-primary">View All Users</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
