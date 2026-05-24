<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once('mysql_connect.php');

$car        = isset($_GET['car'])        ? trim($_GET['car'])        : '';
$start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date   = isset($_GET['end_date'])   ? trim($_GET['end_date'])   : '';

if (empty($car) || empty($start_date) || empty($end_date)) {
    echo json_encode(['available' => null, 'message' => 'Missing parameters.']);
    mysqli_close($dbc);
    exit;
}

if (!strtotime($start_date) || !strtotime($end_date)) {
    echo json_encode(['available' => null, 'message' => 'Invalid dates.']);
    mysqli_close($dbc);
    exit;
}

if (strtotime($end_date) <= strtotime($start_date)) {
    echo json_encode(['available' => null, 'message' => 'End date must be after start date.']);
    mysqli_close($dbc);
    exit;
}

$car_escaped   = mysqli_real_escape_string($dbc, $car);
$start_escaped = mysqli_real_escape_string($dbc, $start_date);
$end_escaped   = mysqli_real_escape_string($dbc, $end_date);

$vehicle_result = mysqli_query($dbc, "SELECT vehicle_id FROM vehicles WHERE vehicle_name='$car_escaped' LIMIT 1");

if (!$vehicle_result || mysqli_num_rows($vehicle_result) === 0) {
    echo json_encode([
        'available'       => true,
        'conflict_count'  => 0,
        'conflict_ranges' => [],
        'message'         => 'Car is available on the selected dates!'
    ]);
    mysqli_close($dbc);
    exit;
}

$vehicle = mysqli_fetch_assoc($vehicle_result);
$vehicle_id = (int) $vehicle['vehicle_id'];

$conflict_query = "
    SELECT start_date, end_date, customer_id
    FROM bookings
    WHERE vehicle_id = $vehicle_id
      AND booking_status = 'Confirmed'
      AND start_date IS NOT NULL
      AND end_date IS NOT NULL
      AND (start_date <= '$end_escaped' AND end_date >= '$start_escaped')
    ORDER BY start_date ASC
";

$result = mysqli_query($dbc, $conflict_query);

if (!$result) {
    echo json_encode(['available' => null, 'message' => 'Database error: ' . mysqli_error($dbc)]);
    mysqli_close($dbc);
    exit;
}

$conflicts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $conflicts[] = [
        'from' => date('M d, Y', strtotime($row['start_date'])),
        'to'   => date('M d, Y', strtotime($row['end_date']))
    ];
}

$conflict_count = count($conflicts);

if ($conflict_count === 0) {
    echo json_encode([
        'available'       => true,
        'conflict_count'  => 0,
        'conflict_ranges' => [],
        'message'         => 'Car is available on the selected dates!'
    ]);
} else {
    echo json_encode([
        'available'       => false,
        'conflict_count'  => $conflict_count,
        'conflict_ranges' => $conflicts,
        'message'         => "This car already has $conflict_count booking(s) overlapping your selected dates."
    ]);
}

mysqli_close($dbc);
?>
