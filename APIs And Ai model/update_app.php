<?php
require_once 'db_connection.php'; // Include the database connection

// Get JSON data from the request body
$data = json_decode(file_get_contents("php://input"), true);

// Extract input fields
$app_id = intval($data['app_id'] ?? 0); // ID of the app to update
$child_id = intval($data['child_id'] ?? 0); // ID of the child
$app_name = trim($data['app_name'] ?? '');
$app_status = intval($data['app_status'] ?? 0); // TRUE = 1, FALSE = 0
$limit_time = trim($data['limit_time'] ?? '');

// Validate input
if (empty($app_id) || empty($child_id) || empty($app_name) || empty($limit_time)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

// Validate the time format (HH:MM:SS)
if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $limit_time)) {
    echo json_encode(["status" => "error", "message" => "Invalid time format. Use HH:MM:SS."]);
    exit;
}

// Check if the app exists and belongs to the specified child
$stmt_check_app = $conn->prepare("SELECT App_ID FROM Apps WHERE App_ID = ? AND Child_ID = ?");
$stmt_check_app->bind_param("ii", $app_id, $child_id);
$stmt_check_app->execute();
$result = $stmt_check_app->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "App not found or does not belong to this child."]);
    $stmt_check_app->close();
    exit;
}

// Update the app details
$stmt_update_app = $conn->prepare("UPDATE Apps SET App_Name = ?, App_Status = ?, Limit_Time = ? WHERE App_ID = ? AND Child_ID = ?");
$stmt_update_app->bind_param("sissi", $app_name, $app_status, $limit_time, $app_id, $child_id);

if ($stmt_update_app->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "App updated successfully.",
        "app_id" => $app_id,
        "child_id" => $child_id
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update app: " . $stmt_update_app->error]);
}

// Close the statements and connection
$stmt_check_app->close();
$stmt_update_app->close();
$conn->close();
?>