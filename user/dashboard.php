<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_user();

$uid = $_SESSION['user_id'];

$available_count = $conn->query("SELECT COUNT(*) c FROM parking_spots WHERE status='available'")->fetch_assoc()['c'];
$active_booking = $conn->query("
    SELECT b.*, p.spot_number, p.location FROM bookings b
    JOIN parking_spots p ON b.spot_id = p.spot_id
    WHERE b.user_id=$uid AND b.status IN ('booked','parked','exited')
    ORDER BY b.booked_at DESC LIMIT 1
")->fetch_assoc();
$total_sessions = $conn->query("SELECT COUNT(*) c FROM bookings WHERE user_id=$uid AND status='completed'")->fetch_assoc()['c'];

$page_title = "Dashboard";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
<?php show_message(); ?>

<div class="grid">
    <div class="stat-box"><div class="number"><?php echo $available_count; ?></div><div class="label">Spots Available Now</div></div>
    <div class="stat-box"><div class="number"><?php echo $active_booking ? 1 : 0; ?></div><div class="label">Active Booking</div></div>
    <div class="stat-box"><div class="number"><?php echo $total_sessions; ?></div><div class="label">Completed Sessions</div></div>
</div>

<?php if ($active_booking): ?>
<div class="card" style="margin-top:24px;">
    <h3>Your Active Booking</h3>
    <p>Spot <strong><?php echo htmlspecialchars($active_booking['spot_number']); ?></strong> — <?php echo htmlspecialchars($active_booking['location']); ?></p>
    <p>Vehicle: <?php echo htmlspecialchars($active_booking['vehicle_number']); ?> &nbsp; Status: <?php echo status_badge($active_booking['status']); ?></p>
    <a class="btn" href="my_bookings.php">Manage Booking</a>
</div>
<?php else: ?>
<div class="card" style="margin-top:24px;">
    <h3>No Active Booking</h3>
    <p style="color:var(--muted)">Browse available spots and book one to get started.</p>
    <a class="btn" href="available_spots.php">Browse Available Spots</a>
</div>
<?php endif; ?>

<div class="card">
    <h3>Quick Links</h3>
    <div class="action-links">
        <a class="btn btn-secondary" href="available_spots.php">Available Spots</a>
        <a class="btn btn-secondary" href="my_bookings.php">My Booking</a>
        <a class="btn btn-secondary" href="parking_history.php">My Parking History</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
