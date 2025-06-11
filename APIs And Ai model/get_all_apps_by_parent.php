<?php
// get_all_apps_by_parent.php
require_once 'db_connection.php'; // Include the database connection

// Get Parent_ID from query parameters
$parent_id = intval($_GET['parent_id']);

// Validate input
if (empty($parent_id)) {
    echo json_encode(["status" => "error", "message" => "Parent ID is required."]);
    exit;
}

// Prepare and execute the SQL query to retrieve only App_IDs
$stmt = $conn->prepare("SELECT App_ID FROM Apps WHERE Parent_ID = ? AND Child_ID IS NULL");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch only the App_IDs
    $app_ids = [];
    while ($row = $result->fetch_assoc()) {
        $app_ids[] = $row['App_ID'];
    }
    echo json_encode(["status" => "success", "data" => $app_ids]);
} else {
    echo json_encode(["status" => "error", "message" => "No available apps found for this parent."]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>