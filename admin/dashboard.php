<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

$user_count = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$spot_total = $conn->query("SELECT COUNT(*) c FROM parking_spots")->fetch_assoc()['c'];
$spot_available = $conn->query("SELECT COUNT(*) c FROM parking_spots WHERE status='available'")->fetch_assoc()['c'];
$spot_booked = $conn->query("SELECT COUNT(*) c FROM parking_spots WHERE status='booked'")->fetch_assoc()['c'];
$spot_occupied = $conn->query("SELECT COUNT(*) c FROM parking_spots WHERE status='occupied'")->fetch_assoc()['c'];
$pending_release = $conn->query("SELECT COUNT(*) c FROM bookings WHERE status='exited'")->fetch_assoc()['c'];

$page_title = "Admin Dashboard";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Admin Dashboard</h1>
<?php show_message(); ?>

<div class="grid">
    <div class="stat-box"><div class="number"><?php echo $user_count; ?></div><div class="label">Registered Users</div></div>
    <div class="stat-box"><div class="number"><?php echo $spot_total; ?></div><div class="label">Total Spots</div></div>
    <div class="stat-box"><div class="number"><?php echo $spot_available; ?></div><div class="label">Available Now</div></div>
    <div class="stat-box"><div class="number"><?php echo $spot_booked; ?></div><div class="label">Booked</div></div>
    <div class="stat-box"><div class="number"><?php echo $spot_occupied; ?></div><div class="label">Occupied</div></div>
    <div class="stat-box"><div class="number"><?php echo $pending_release; ?></div><div class="label">Awaiting Release</div></div>
</div>

<div class="card" style="margin-top:24px;">
    <h3>Quick Actions</h3>
    <div class="action-links">
        <a class="btn" href="add_spot.php">+ Add Parking Spot</a>
        <a class="btn btn-secondary" href="manage_spots.php">Manage Spots</a>
        <a class="btn btn-secondary" href="monitor_bookings.php">Monitor Bookings</a>
        <a class="btn btn-secondary" href="manage_users.php">Manage Users</a>
        <a class="btn btn-secondary" href="parking_history.php">Parking History</a>
    </div>
</div>

<?php if ($pending_release > 0): ?>
<div class="card">
    <h3>⚠️ Spots Awaiting Release</h3>
    <p style="color:var(--muted)">
        <?php echo $pending_release; ?> vehicle(s) have exited but their spot hasn't been marked available yet.
        <a href="monitor_bookings.php?filter=exited">Review and release them →</a>
    </p>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
