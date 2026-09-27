<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $spot_number = clean($conn, $_POST['spot_number']);
    $location = clean($conn, $_POST['location']);
    $floor_level = clean($conn, $_POST['floor_level']);
    $vehicle_type = clean($conn, $_POST['vehicle_type']);
    $rate = (float) $_POST['rate_per_hour'];

    $check = $conn->prepare("SELECT spot_id FROM parking_spots WHERE spot_number = ?");
    $check->bind_param("s", $spot_number);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        set_message("A spot with this number already exists.", "danger");
        header("Location: add_spot.php");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO parking_spots (spot_number, location, floor_level, vehicle_type, rate_per_hour) VALUES (?,?,?,?,?)");
    $stmt->bind_param("ssssd", $spot_number, $location, $floor_level, $vehicle_type, $rate);
    $stmt->execute();

    set_message("Parking spot added successfully.");
    header("Location: manage_spots.php");
    exit();
}

$page_title = "Add Parking Spot";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Add New Parking Spot</h1>
<div class="card">
    <form method="POST">
        <div><label>Spot Number</label><input type="text" name="spot_number" placeholder="e.g. A-04" required></div>
        <div><label>Location</label><input type="text" name="location" placeholder="e.g. Main Entrance Block" required></div>
        <div><label>Floor Level</label><input type="text" name="floor_level" placeholder="e.g. Ground / First Floor" value="Ground"></div>
        <div>
            <label>Vehicle Type</label>
            <select name="vehicle_type">
                <option value="car">Car</option>
                <option value="bike">Bike</option>
                <option value="both">Both</option>
            </select>
        </div>
        <div><label>Rate per Hour (₹/$)</label><input type="number" step="0.01" name="rate_per_hour" value="20.00" required></div>
        <button type="submit">Add Spot</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
