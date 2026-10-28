<?php
session_start();
require_once 'includes/db.php';
include 'includes/header.php';

$sql = "SELECT j.*, COALESCE(j.company_name_override, e.company_name) as company_name, e.logo FROM jobs j 
        JOIN employer_profiles e ON j.employer_id = e.user_id 
        ORDER BY j.created_at DESC LIMIT 6";
$result = $conn->query($sql);
?>
<!-- Hero Section -->
<div class="hero-section text-center py-5 mb-5 shadow-sm">
    <div class="container py-5">
        <h1 class="display-3 fw-bolder text-white mb-3">Find Your Dream Job Today</h1>
        <p class="lead text-white-50 mb-5 fs-4">Connect with top employers and opportunities across the globe.</p>
        
        <div class="card p-3 shadow-lg mx-auto search-card" style="max-width: 800px; border-radius: 1rem;">
            <form class="row g-2 align-items-center" action="jobs.php" method="GET">
                <div class="col-md-9">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" class="form-control border-0 shadow-none bg-white" placeholder="Job title, keywords, or company...">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-pill shadow">Search Jobs</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Browse by Category -->
<div class="container mb-5 pb-4 border-bottom">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0">Browse by Category</h3>
        <a href="jobs.php" class="text-decoration-none fw-bold text-primary">All Categories <i class="fas fa-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-4">
        <?php
        $categories = [
            ['name' => 'IT & Software', 'icon' => 'fa-laptop-code', 'val' => 'IT'],
            ['name' => 'Finance', 'icon' => 'fa-chart-line', 'val' => 'Finance'],
            ['name' => 'Marketing', 'icon' => 'fa-bullhorn', 'val' => 'Marketing'],
            ['name' => 'Engineering', 'icon' => 'fa-cogs', 'val' => 'Engineering'],
            ['name' => 'Healthcare', 'icon' => 'fa-heartbeat', 'val' => 'Healthcare'],
            ['name' => 'Sales', 'icon' => 'fa-chart-pie', 'val' => 'Sales and Business Development'],
            ['name' => 'HR & Admin', 'icon' => 'fa-users', 'val' => 'HR and Administration'],
            ['name' => 'Teaching', 'icon' => 'fa-chalkboard-teacher', 'val' => 'Teaching'],
        ];
        foreach ($categories as $cat):
            // Get count
            $cat_val = $cat['val'];
            $cat_count = $conn->query("SELECT COUNT(id) as c FROM jobs WHERE category='$cat_val'")->fetch_assoc()['c'];
        ?>
        <div class="col-6 col-md-4 col-lg">
            <a href="jobs.php?category=<?= $cat['val'] ?>" class="text-decoration-none">
                <div class="card category-card h-100 text-center p-4 border-0 shadow-sm bg-white rounded-4">
                    <div class="icon-circle mx-auto mb-3 text-primary bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 50%;">
                        <i class="fas <?= $cat['icon'] ?> fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1"><?= $cat['name'] ?></h6>
                    <small class="text-muted"><?= $cat_count ?> Open Positions</small>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Featured Jobs -->
<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0">Featured Jobs</h3>
        <a href="jobs.php" class="text-decoration-none fw-bold text-primary">View All <i class="fas fa-arrow-right ms-1"></i></a>
    </div>
    
    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while($job = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card job-card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="card-body p-4 position-relative">
                            <?php if ($job['status'] == 'closed'): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger mb-3 px-3 py-2 rounded-pill fw-semibold">Closed</span>
                            <?php else: ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2 rounded-pill fw-semibold"><?= htmlspecialchars($job['category']) ?></span>
                            <?php endif; ?>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary mb-3 px-3 py-2 rounded-pill fw-semibold ms-1"><?= htmlspecialchars($job['job_type'] ?? 'Full Time') ?></span>
                            
                            <h5 class="card-title fw-bold mb-1">
                                <a href="job_details.php?id=<?= $job['id'] ?>" class="text-decoration-none text-dark stretched-link"><?= htmlspecialchars($job['title']) ?></a>
                            </h5>
                            
                            <h6 class="text-muted mb-3"><i class="fas fa-building me-2"></i><?= htmlspecialchars($job['company_name']) ?></h6>
                            
                            <div class="d-flex align-items-center text-secondary small mb-2">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i> <?= htmlspecialchars($job['location']) ?>
                            </div>
                            <div class="d-flex align-items-center text-secondary small mb-3">
                                <i class="fas fa-money-bill-wave me-2 text-success"></i> <?= htmlspecialchars($job['salary']) ?>
                            </div>
                            
                            <hr class="text-muted opacity-25">
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2">
                                <small class="text-muted"><i class="far fa-clock me-1"></i> <?= date('M d', strtotime($job['created_at'])) ?></small>
                                <?php if ($job['status'] == 'open'): ?>
                                    <span class="fw-bold text-primary apply-text">Apply Now <i class="fas fa-arrow-right ms-1 small"></i></span>
                                <?php else: ?>
                                    <span class="fw-bold text-danger apply-text">Closed <i class="fas fa-times ms-1 small"></i></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm">
                <i class="fas fa-search fs-1 text-muted mb-3"></i>
                <p class="text-muted fs-5 m-0">No jobs posted yet.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="text-center mt-5">
        <a href="jobs.php" class="btn btn-outline-primary btn-lg px-5 rounded-pill fw-bold shadow-sm">Browse All Jobs</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
