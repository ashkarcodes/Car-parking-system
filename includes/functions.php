<?php
/**
 * Shared helper functions used across all modules.
 */

function clean($conn, $value) {
    return htmlspecialchars(trim($conn->real_escape_string($value)));
}

function require_admin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../admin/login.php");
        exit();
    }
}

function require_user() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../user/login.php");
        exit();
    }
}

function set_message($msg, $type = 'success') {
    $_SESSION['flash_message'] = $msg;
    $_SESSION['flash_type'] = $type;
}

function show_message() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'success';
        echo '<div class="alert alert-' . $type . '">' . $_SESSION['flash_message'] . '</div>';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

// Returns a small badge span for a given status string
function status_badge($status) {
    $map = [
        'available' => 'badge-available',
        'booked'    => 'badge-booked',
        'occupied'  => 'badge-occupied',
        'parked'    => 'badge-occupied',
        'exited'    => 'badge-exited',
        'completed' => 'badge-available',
        'active'    => 'badge-available',
        'blocked'   => 'badge-occupied',
        'cancelled' => 'badge-exited',
    ];
    $class = $map[$status] ?? 'badge-booked';
    return '<span class="badge ' . $class . '">' . ucfirst($status) . '</span>';
}
?>
