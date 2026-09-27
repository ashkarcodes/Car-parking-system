<?php
require_once 'config/db.php';
$page_title = "Home";
$asset_path = "";
include 'includes/header.php';
?>

<div class="landing-hero">
    <h1>Find & Book Parking, Instantly.</h1>
    <p>View live parking availability, reserve a spot, and manage your session — all in one place.</p>

    <div class="role-cards">
        <div class="role-card">
            <h3>🛠 Admin</h3>
            <p>Manage users, monitor bookings & release spots.</p>
            <a class="btn" href="admin/login.php">Admin Login</a>
        </div>
        <div class="role-card">
            <h3>🚗 User</h3>
            <p>Book a parking spot and track your session.</p>
            <a class="btn" href="user/login.php">User Login</a>
            <a class="btn btn-secondary" href="user/register.php">Register</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
