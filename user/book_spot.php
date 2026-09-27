<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_user();

$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: available_spots.php");
    exit();
}

$spot_id = (int) $_POST['spot_id'];
$vehicle_number = clean($conn, $_POST['vehicle_number']);
$vehicle_type = $_POST['vehicle_type'] === 'bike' ? 'bike' : 'car';

// Prevent a user from holding two active bookings at once
$active = $conn->query("SELECT * FROM bookings WHERE user_id=$uid AND status IN ('booked','parked','exited')")->fetch_assoc();
if ($active) {
    set_message("You already have an active booking. Finish it before booking another spot.", "danger");
    header("Location: my_bookings.php");
    exit();
}

// Re-check the spot is still available (guards against a race with another user)
$conn->begin_transaction();
$spot = $conn->query("SELECT * FROM parking_spots WHERE spot_id=$spot_id AND status='available' FOR UPDATE")->fetch_assoc();

if (!$spot) {
    $conn->rollback();
    set_message("Sorry, this spot was just booked by someone else. Please choose another.", "danger");
    header("Location: available_spots.php");
    exit();
}

$stmt = $conn->prepare("INSERT INTO bookings (spot_id, user_id, vehicle_number, vehicle_type, status) VALUES (?,?,?,?,'booked')");
$stmt->bind_param("iiss", $spot_id, $uid, $vehicle_number, $vehicle_type);
$stmt->execute();

$conn->query("UPDATE parking_spots SET status='booked' WHERE spot_id=$spot_id");
$conn->commit();

set_message("Spot " . $spot['spot_number'] . " booked successfully! Mark it as 'Parked' once you arrive.");
header("Location: my_bookings.php");
exit();
