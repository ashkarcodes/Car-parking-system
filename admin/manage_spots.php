<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    if ($_GET['action'] === 'delete') {
        // Only allow deleting spots that are not currently in an active booking
        $active = $conn->query("SELECT * FROM parking_spots WHERE spot_id=$id AND status='available'")->fetch_assoc();
        if ($active) {
            $conn->query("DELETE FROM parking_spots WHERE spot_id=$id");
            set_message("Parking spot removed.");
        } else {
            set_message("Cannot delete a spot that is booked or occupied.", "danger");
        }
    } elseif ($_GET['action'] === 'force_available') {
        // Admin override — manually reset a stuck spot back to available
        $conn->query("UPDATE parking_spots SET status='available' WHERE spot_id=$id");
        $conn->query("UPDATE bookings SET status='completed', released_at=NOW() WHERE spot_id=$id AND status IN ('booked','parked','exited')");
        set_message("Spot manually reset to available.");
    }
    header("Location: manage_spots.php");
    exit();
}

$spots = $conn->query("SELECT * FROM parking_spots ORDER BY spot_number");

$page_title = "Manage Parking Spots";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Manage Parking Spots</h1>
<?php show_message(); ?>
<a class="btn" href="add_spot.php">+ Add Parking Spot</a>

<div class="card" style="margin-top:18px;">
<table>
<tr><th>Spot #</th><th>Location</th><th>Floor</th><th>Type</th><th>Rate/hr</th><th>Status</th><th>Actions</th></tr>
<?php while ($s = $spots->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($s['spot_number']); ?></td>
    <td><?php echo htmlspecialchars($s['location']); ?></td>
    <td><?php echo htmlspecialchars($s['floor_level']); ?></td>
    <td><?php echo ucfirst($s['vehicle_type']); ?></td>
    <td>$<?php echo number_format($s['rate_per_hour'], 2); ?></td>
    <td><?php echo status_badge($s['status']); ?></td>
    <td class="action-links">
        <?php if ($s['status'] === 'available'): ?>
            <a class="confirm-action" data-confirm="Delete this parking spot?" href="?action=delete&id=<?php echo $s['spot_id']; ?>">Delete</a>
        <?php else: ?>
            <a class="confirm-action" data-confirm="Force-reset this spot to available? Use this only if a booking is stuck." href="?action=force_available&id=<?php echo $s['spot_id']; ?>">Force Available</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>

<?php include '../includes/footer.php'; ?>
