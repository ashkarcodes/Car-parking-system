<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($conn, $_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT admin_id, name, password FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['name'];
            header("Location: dashboard.php");
            exit();
        }
    }
    set_message("Invalid email or password.", "danger");
    header("Location: login.php");
    exit();
}

$page_title = "Admin Login";
$asset_path = "../";
include '../includes/header.php';
?>

<div class="auth-box">
    <h2>Admin Login</h2>
    <?php show_message(); ?>
    <form method="POST">
        <div><label>Email</label><input type="email" name="email" required></div>
        <div><label>Password</label><input type="password" name="password" required></div>
        <button type="submit">Login</button>
    </form>
    <p style="margin-top:14px;font-size:0.85rem;color:var(--muted)">
        Default: admin@parking.com / admin123
    </p>
</div>

<?php include '../includes/footer.php'; ?>
