<?php
// get_children.php

require_once 'db_connection.php'; // Include the database connection

// Get Parent_ID from query parameters
$parent_id = intval($_GET['parent_id']);

// Validate input
if (empty($parent_id)) {
    echo json_encode(["status" => "error", "message" => "Parent ID is required."]);
    exit;
}

// Prepare and execute the SQL query
$stmt = $conn->prepare("SELECT * FROM Child WHERE Parent_ID = ?");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $children = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(["status" => "success", "data" => $children]);
} else {
    echo json_encode(["status" => "error", "message" => "No children found for this parent."]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>