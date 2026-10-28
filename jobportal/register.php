<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; // 'employer' or 'seeker'

    // Check if email exists
    $check_sql = "SELECT id FROM users WHERE email = '$email'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $error = "Email already registered.";
    } else {
        $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
        if ($conn->query($sql) === TRUE) {
            $user_id = $conn->insert_id;
            
            // Create empty profile
            if ($role == 'employer') {
                $conn->query("INSERT INTO employer_profiles (user_id, company_name) VALUES ($user_id, '$name')");
            } else {
                $conn->query("INSERT INTO seeker_profiles (user_id) VALUES ($user_id)");
            }

            $success = "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card mt-5">
            <div class="card-body p-4">
                <h3 class="text-center mb-4"><i class="fas fa-user-plus text-primary"></i> Register</h3>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name / Company Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label d-block">I am a...</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="role" id="role_seeker" value="seeker" checked>
                            <label class="form-check-label" for="role_seeker">Job Seeker</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="role" id="role_employer" value="employer">
                            <label class="form-check-label" for="role_employer">Employer</label>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Register</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
