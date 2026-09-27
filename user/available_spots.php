<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_user();

$uid = $_SESSION['user_id'];

// A user can only hold one active booking at a time
$active = $conn->query("SELECT * FROM bookings WHERE user_id=$uid AND status IN ('booked','parked','exited')")->fetch_assoc();

$spots = $conn->query("SELECT * FROM parking_spots WHERE status='available' ORDER BY spot_number");

$page_title = "Available Spots";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Available Parking Spots</h1>
<?php show_message(); ?>

<?php if ($active): ?>
<div class="alert alert-danger">
    You already have an active booking for vehicle <?php echo htmlspecialchars($active['vehicle_number']); ?>. Complete or exit it before booking another spot.
    <a href="my_bookings.php">View my booking →</a>
</div>
<?php endif; ?>

<div class="grid">
<?php while ($s = $spots->fetch_assoc()): ?>
    <div class="spot-card">
        <div class="spot-number"><?php echo htmlspecialchars($s['spot_number']); ?></div>
        <div class="spot-meta">
            <?php echo htmlspecialchars($s['location']); ?> · <?php echo htmlspecialchars($s['floor_level']); ?><br>
            Type: <?php echo ucfirst($s['vehicle_type']); ?> · $<?php echo number_format($s['rate_per_hour'],2); ?>/hr
        </div>
        <?php if (!$active): ?>
        <form method="POST" action="book_spot.php">
            <input type="hidden" name="spot_id" value="<?php echo $s['spot_id']; ?>">
            <div><label>Vehicle Number</label><input type="text" name="vehicle_number" placeholder="e.g. KA-01-AB-1234" required></div>
            <div>
                <label>Vehicle Type</label>
                <select name="vehicle_type">
                    <option value="car">Car</option>
                    <option value="bike">Bike</option>
                </select>
            </div>
            <button type="submit">Book This Spot</button>
        </form>
        <?php else: ?>
            <button disabled style="opacity:0.5;cursor:not-allowed;">Book This Spot</button>
        <?php endif; ?>
    </div>
<?php endwhile; ?>
<?php if ($spots->num_rows === 0): ?>
    <p style="color:var(--muted)">No parking spots available right now. Please check back later.</p>
<?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
