<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';

// Handle user deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $id AND role != 'admin'");
    header("Location: users.php");
    exit();
}

$users = $conn->query("SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC");

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="users.php" class="list-group-item list-group-item-action active"><i class="fas fa-users-cog me-2"></i>Manage Users</a>
            <a href="jobs.php" class="list-group-item list-group-item-action"><i class="fas fa-briefcase me-2"></i>Manage Jobs</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">Manage Users</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users->num_rows > 0): ?>
                                <?php while($user = $users->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($user['name']) ?></strong></td>
                                    <td><a href="mailto:<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></a></td>
                                    <td><span class="badge bg-<?= $user['role'] == 'employer' ? 'primary' : 'success' ?>"><?= ucfirst($user['role']) ?></span></td>
                                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <a href="users.php?delete=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user? All associated data will be lost.')"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-4">No users found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
