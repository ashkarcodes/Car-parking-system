<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($conn, $_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, name, password, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($user['status'] === 'blocked') {
            set_message("Your account has been blocked by the admin.", "danger");
            header("Location: login.php");
            exit();
        }
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: dashboard.php");
            exit();
        }
    }
    set_message("Invalid email or password.", "danger");
    header("Location: login.php");
    exit();
}

$page_title = "Login";
$asset_path = "../";
include '../includes/header.php';
?>

<div class="auth-box">
    <h2>User Login</h2>
    <?php show_message(); ?>
    <form method="POST">
        <div><label>Email</label><input type="email" name="email" required></div>
        <div><label>Password</label><input type="password" name="password" required></div>
        <button type="submit">Login</button>
    </form>
    <p style="margin-top:14px;font-size:0.9rem;">No account? <a href="register.php">Register here</a></p>
</div>

<?php include '../includes/footer.php'; ?>
