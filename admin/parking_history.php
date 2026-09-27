<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

$history = $conn->query("
    SELECT b.*, u.name AS user_name, p.spot_number, p.location, p.rate_per_hour,
    TIMESTAMPDIFF(MINUTE, b.parked_at, b.exited_at) AS duration_minutes
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN parking_spots p ON b.spot_id = p.spot_id
    WHERE b.status = 'completed'
    ORDER BY b.released_at DESC
");

$page_title = "Parking History";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Parking History</h1>

<div class="card">
<table>
<tr><th>User</th><th>Spot</th><th>Vehicle</th><th>Parked</th><th>Exited</th><th>Duration</th><th>Est. Cost</th></tr>
<?php while ($h = $history->fetch_assoc()):
    $mins = $h['duration_minutes'] ?? 0;
    $hours = max(1, ceil($mins / 60));
    $cost = $hours * $h['rate_per_hour'];
?>
<tr>
    <td><?php echo htmlspecialchars($h['user_name']); ?></td>
    <td><?php echo htmlspecialchars($h['spot_number']); ?> — <?php echo htmlspecialchars($h['location']); ?></td>
    <td><?php echo htmlspecialchars($h['vehicle_number']); ?></td>
    <td><?php echo $h['parked_at'] ? date('d M, h:i A', strtotime($h['parked_at'])) : '—'; ?></td>
    <td><?php echo $h['exited_at'] ? date('d M, h:i A', strtotime($h['exited_at'])) : '—'; ?></td>
    <td><?php echo $mins > 0 ? $mins . ' min' : '—'; ?></td>
    <td>$<?php echo number_format($cost, 2); ?></td>
</tr>
<?php endwhile; ?>
<?php if ($history->num_rows === 0): ?>
<tr><td colspan="7" style="text-align:center;color:var(--muted)">No completed sessions yet.</td></tr>
<?php endif; ?>
</table>
</div>

<?php include '../includes/footer.php'; ?>
