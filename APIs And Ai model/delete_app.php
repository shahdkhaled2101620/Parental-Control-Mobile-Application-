<?php
require_once 'db_connection.php'; // Include the database connection

// Get JSON data from the request body
$data = json_decode(file_get_contents("php://input"), true);

// Extract input fields
$app_id = intval($data['app_id'] ?? 0); // ID of the app to delete
$child_id = intval($data['child_id'] ?? 0); // ID of the child

// Validate input
if (empty($app_id) || empty($child_id)) {
    echo json_encode(["status" => "error", "message" => "App ID and Child ID are required."]);
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

// Delete the app
$stmt_delete_app = $conn->prepare("DELETE FROM Apps WHERE App_ID = ? AND Child_ID = ?");
$stmt_delete_app->bind_param("ii", $app_id, $child_id);

if ($stmt_delete_app->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "App deleted successfully.",
        "app_id" => $app_id,
        "child_id" => $child_id
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete app: " . $stmt_delete_app->error]);
}

// Close the statements and connection
$stmt_check_app->close();
$stmt_delete_app->close();
$conn->close();
?>