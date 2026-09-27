<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_user();

$uid = $_SESSION['user_id'];

// Handle lifecycle actions
if (isset($_GET['action'], $_GET['id'])) {
    $booking_id = (int) $_GET['id'];
    $booking = $conn->query("SELECT * FROM bookings WHERE booking_id=$booking_id AND user_id=$uid")->fetch_assoc();

    if ($booking) {
        if ($_GET['action'] === 'park' && $booking['status'] === 'booked') {
            $conn->query("UPDATE bookings SET status='parked', parked_at=NOW() WHERE booking_id=$booking_id");
            $conn->query("UPDATE parking_spots SET status='occupied' WHERE spot_id=" . $booking['spot_id']);
            set_message("Vehicle marked as parked. Enjoy!");
        } elseif ($_GET['action'] === 'exit' && $booking['status'] === 'parked') {
            $conn->query("UPDATE bookings SET status='exited', exited_at=NOW() WHERE booking_id=$booking_id");
            set_message("You've exited. The spot will be marked available once the admin confirms.");
        } elseif ($_GET['action'] === 'cancel' && $booking['status'] === 'booked') {
            $conn->query("UPDATE bookings SET status='cancelled' WHERE booking_id=$booking_id");
            $conn->query("UPDATE parking_spots SET status='available' WHERE spot_id=" . $booking['spot_id']);
            set_message("Booking cancelled.");
        }
    }
    header("Location: my_bookings.php");
    exit();
}

$booking = $conn->query("
    SELECT b.*, p.spot_number, p.location, p.floor_level, p.rate_per_hour
    FROM bookings b JOIN parking_spots p ON b.spot_id = p.spot_id
    WHERE b.user_id=$uid AND b.status IN ('booked','parked','exited')
    ORDER BY b.booked_at DESC LIMIT 1
")->fetch_assoc();

$page_title = "My Booking";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>My Booking</h1>
<?php show_message(); ?>

<?php if ($booking): ?>
<div class="card">
    <h3>Spot <?php echo htmlspecialchars($booking['spot_number']); ?> — <?php echo htmlspecialchars($booking['location']); ?></h3>
    <p><?php echo htmlspecialchars($booking['floor_level']); ?> · $<?php echo number_format($booking['rate_per_hour'],2); ?>/hr</p>
    <p>Vehicle: <strong><?php echo htmlspecialchars($booking['vehicle_number']); ?></strong> (<?php echo ucfirst($booking['vehicle_type']); ?>)</p>
    <p>Status: <?php echo status_badge($booking['status']); ?></p>
    <p style="color:var(--muted);font-size:0.9rem;">
        Booked: <?php echo date('d M Y, h:i A', strtotime($booking['booked_at'])); ?>
        <?php if ($booking['parked_at']): ?><br>Parked: <?php echo date('d M Y, h:i A', strtotime($booking['parked_at'])); ?><?php endif; ?>
        <?php if ($booking['exited_at']): ?><br>Exited: <?php echo date('d M Y, h:i A', strtotime($booking['exited_at'])); ?><?php endif; ?>
    </p>

    <div class="action-links" style="margin-top:16px;">
        <?php if ($booking['status'] === 'booked'): ?>
            <a class="btn" href="?action=park&id=<?php echo $booking['booking_id']; ?>">Mark as Parked</a>
            <a class="btn btn-danger confirm-action" data-confirm="Cancel this booking?" href="?action=cancel&id=<?php echo $booking['booking_id']; ?>">Cancel Booking</a>
        <?php elseif ($booking['status'] === 'parked'): ?>
            <a class="btn btn-warning confirm-action" data-confirm="Confirm you are exiting the parking area?" href="?action=exit&id=<?php echo $booking['booking_id']; ?>">Exit Parking</a>
        <?php elseif ($booking['status'] === 'exited'): ?>
            <p style="color:var(--muted)">Waiting for admin to confirm and release the spot.</p>
        <?php endif; ?>
    </div>
</div>
<?php else: ?>
<div class="card">
    <p style="color:var(--muted)">You have no active booking right now.</p>
    <a class="btn" href="available_spots.php">Browse Available Spots</a>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
