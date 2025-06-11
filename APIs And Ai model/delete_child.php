<?php
// delete_child.php

require_once 'db_connection.php'; // Include the database connection

// Enable detailed error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Get JSON data from the request body
$input = file_get_contents("php://input");
if (empty($input)) {
    echo json_encode(["status" => "error", "message" => "No data received."]);
    exit;
}

$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON format."]);
    exit;
}

// Extract input fields
$full_name = trim($data['full_name'] ?? '');
$parent_id = intval($data['parent_id'] ?? 0);

// Validate input
if (empty($full_name) || empty($parent_id)) {
    echo json_encode(["status" => "error", "message" => "Full name and Parent ID are required."]);
    exit;
}

// Check if the child exists
$stmt_check_child = $conn->prepare("SELECT Child_ID FROM Child WHERE Full_Name = ? AND Parent_ID = ?");
$stmt_check_child->bind_param("si", $full_name, $parent_id);
$stmt_check_child->execute();
$result = $stmt_check_child->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Child not found or does not belong to this parent."]);
    $stmt_check_child->close();
    exit;
}

// Delete the child
$child_id = $result->fetch_assoc()['Child_ID']; // Get the Child_ID
$stmt_delete_child = $conn->prepare("DELETE FROM Child WHERE Child_ID = ?");
$stmt_delete_child->bind_param("i", $child_id);

if ($stmt_delete_child->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Child deleted successfully.",
        "child_id" => $child_id
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete child: " . $stmt_delete_child->error]);
}

// Close the statements and connection
$stmt_check_child->close();
$stmt_delete_child->close();
$conn->close();
?>