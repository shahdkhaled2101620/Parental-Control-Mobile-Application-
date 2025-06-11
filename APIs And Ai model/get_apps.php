<?php
// get_apps.php
require_once 'db_connection.php'; // Include the database connection

// Get Child_ID from query parameters
$child_id = intval($_GET['child_id']);

// Validate input
if (empty($child_id)) {
    echo json_encode(["status" => "error", "message" => "Child ID is required."]);
    exit;
}

// Prepare and execute the SQL query
$stmt = $conn->prepare("SELECT * FROM Apps WHERE Child_ID = ?");
$stmt->bind_param("i", $child_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $apps = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(["status" => "success", "data" => $apps]);
} else {
    echo json_encode(["status" => "error", "message" => "No apps found for this child."]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>