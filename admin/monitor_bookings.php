<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

// Release a spot after the user has exited
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'release') {
    $booking_id = (int) $_GET['id'];
    $booking = $conn->query("SELECT * FROM bookings WHERE booking_id=$booking_id AND status='exited'")->fetch_assoc();
    if ($booking) {
        $conn->query("UPDATE bookings SET status='completed', released_at=NOW() WHERE booking_id=$booking_id");
        $conn->query("UPDATE parking_spots SET status='available' WHERE spot_id=" . $booking['spot_id']);
        set_message("Spot released and made available again.");
    } else {
        set_message("This booking is not ready to be released.", "danger");
    }
    header("Location: monitor_bookings.php");
    exit();
}

$filter = $_GET['filter'] ?? 'active';
$where = "b.status IN ('booked','parked','exited')";
if ($filter === 'exited') $where = "b.status = 'exited'";
if ($filter === 'all') $where = "1=1";

$bookings = $conn->query("
    SELECT b.*, u.name AS user_name, u.email, p.spot_number, p.location
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN parking_spots p ON b.spot_id = p.spot_id
    WHERE $where
    ORDER BY b.booked_at DESC
");

$page_title = "Monitor Bookings";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Monitor Bookings</h1>
<?php show_message(); ?>

<div class="action-links" style="margin-bottom:16px;">
    <a class="btn <?php echo $filter==='active'?'':'btn-secondary'; ?>" href="?filter=active">Active</a>
    <a class="btn <?php echo $filter==='exited'?'':'btn-secondary'; ?>" href="?filter=exited">Awaiting Release</a>
    <a class="btn <?php echo $filter==='all'?'':'btn-secondary'; ?>" href="?filter=all">All</a>
</div>

<div class="card">
<table>
<tr><th>User</th><th>Spot</th><th>Vehicle</th><th>Status</th><th>Booked At</th><th>Actions</th></tr>
<?php while ($b = $bookings->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($b['user_name']); ?><br><small style="color:var(--muted)"><?php echo htmlspecialchars($b['email']); ?></small></td>
    <td><?php echo htmlspecialchars($b['spot_number']); ?> — <?php echo htmlspecialchars($b['location']); ?></td>
    <td><?php echo htmlspecialchars($b['vehicle_number']); ?> (<?php echo ucfirst($b['vehicle_type']); ?>)</td>
    <td><?php echo status_badge($b['status']); ?></td>
    <td><?php echo date('d M Y, h:i A', strtotime($b['booked_at'])); ?></td>
    <td class="action-links">
        <?php if ($b['status'] === 'exited'): ?>
            <a class="btn confirm-action" data-confirm="Confirm the vehicle has left and release this spot?" href="?action=release&id=<?php echo $b['booking_id']; ?>">Release Spot</a>
        <?php else: ?>
            <span style="color:var(--muted)">—</span>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
<?php if ($bookings->num_rows === 0): ?>
<tr><td colspan="6" style="text-align:center;color:var(--muted)">No bookings found.</td></tr>
<?php endif; ?>
</table>
</div>

<?php include '../includes/footer.php'; ?>
