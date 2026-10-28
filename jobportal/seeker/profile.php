<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seeker') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';

$seeker_id = $_SESSION['user_id'];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $skills = $conn->real_escape_string($_POST['skills']);
    $education = $conn->real_escape_string($_POST['education']);
    $experience = $conn->real_escape_string($_POST['experience']);

    $resume_query = "";
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx'];
        $filename = $_FILES['resume']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), $allowed)) {
            $new_filename = time() . '_' . $seeker_id . '.' . $ext;
            $dest = '../assets/uploads/resumes/' . $new_filename;
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $dest)) {
                $resume_query = ", resume_path = '$dest'";
            }
        }
    }

    $sql = "UPDATE seeker_profiles SET 
            phone = '$phone', 
            address = '$address', 
            skills = '$skills', 
            education = '$education',
            experience = '$experience'
            $resume_query
            WHERE user_id = $seeker_id";
            
    if ($conn->query($sql) === TRUE) {
        $success = "Profile updated successfully.";
    }
}

$profile = $conn->query("SELECT * FROM seeker_profiles WHERE user_id = $seeker_id")->fetch_assoc();
$user_info = $conn->query("SELECT name, email FROM users WHERE id = $seeker_id")->fetch_assoc();

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="profile.php" class="list-group-item list-group-item-action active"><i class="fas fa-user-circle me-2"></i>My Profile</a>
            <a href="applications.php" class="list-group-item list-group-item-action"><i class="fas fa-file-alt me-2"></i>My Applications</a>
            <a href="../jobs.php" class="list-group-item list-group-item-action"><i class="fas fa-search me-2"></i>Search Jobs</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">My Profile</h4>
            </div>
            <div class="card-body">
                <?php if($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Full Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user_info['name']) ?>" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user_info['email']) ?>" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($profile['address'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="skills" class="form-label">Skills (comma separated)</label>
                        <input type="text" class="form-control" id="skills" name="skills" value="<?= htmlspecialchars($profile['skills'] ?? '') ?>" placeholder="e.g. PHP, HTML, CSS, JavaScript">
                    </div>

                    <div class="mb-3">
                        <label for="education" class="form-label">Education</label>
                        <textarea class="form-control" id="education" name="education" rows="3"><?= htmlspecialchars($profile['education'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="experience" class="form-label">Work Experience</label>
                        <textarea class="form-control" id="experience" name="experience" rows="3"><?= htmlspecialchars($profile['experience'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="resume" class="form-label">Resume (PDF, DOC, DOCX)</label>
                        <input class="form-control" type="file" id="resume" name="resume">
                        <?php if (!empty($profile['resume_path'])): ?>
                            <small class="text-muted mt-2 d-block">Current Resume: <a href="<?= htmlspecialchars($profile['resume_path']) ?>" target="_blank" class="fw-bold">View Resume</a></small>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
