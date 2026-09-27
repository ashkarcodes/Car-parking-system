<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    if ($_GET['action'] === 'block') {
        $conn->query("UPDATE users SET status='blocked' WHERE user_id=$id");
        set_message("User blocked.");
    } elseif ($_GET['action'] === 'unblock') {
        $conn->query("UPDATE users SET status='active' WHERE user_id=$id");
        set_message("User unblocked.");
    }
    header("Location: manage_users.php");
    exit();
}

$users = $conn->query("
    SELECT u.*,
    (SELECT COUNT(*) FROM bookings b WHERE b.user_id=u.user_id) AS total_bookings,
    (SELECT COUNT(*) FROM bookings b WHERE b.user_id=u.user_id AND b.status IN ('booked','parked','exited')) AS active_bookings
    FROM users u ORDER BY u.created_at DESC
");

$page_title = "Manage Users";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Manage Users</h1>
<?php show_message(); ?>

<div class="card">
<table>
<tr><th>Name</th><th>Email</th><th>Phone</th><th>Total Bookings</th><th>Active Now</th><th>Status</th><th>Actions</th></tr>
<?php while ($u = $users->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($u['name']); ?></td>
    <td><?php echo htmlspecialchars($u['email']); ?></td>
    <td><?php echo htmlspecialchars($u['phone']); ?></td>
    <td><?php echo $u['total_bookings']; ?></td>
    <td><?php echo $u['active_bookings']; ?></td>
    <td><?php echo status_badge($u['status']); ?></td>
    <td class="action-links">
        <?php if ($u['status'] === 'active'): ?>
            <a href="?action=block&id=<?php echo $u['user_id']; ?>">Block</a>
        <?php else: ?>
            <a href="?action=unblock&id=<?php echo $u['user_id']; ?>">Unblock</a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>

<?php include '../includes/footer.php'; ?>
