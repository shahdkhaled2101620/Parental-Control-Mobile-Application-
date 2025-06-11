<?php
// add_child.php

require_once 'db_connection.php'; // Include the database connection

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
$image_path = trim($data['image_path'] ?? ''); // Path to the uploaded image
$parent_id = intval($data['parent_id'] ?? 0); // Parent ID (must be an integer)

// Debugging: Log received data
error_log("Received data: Full_Name=$full_name, Image_Path=$image_path, Parent_ID=$parent_id");

// Validate input
if (empty($full_name) || empty($image_path) || empty($parent_id)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

// Prepare and execute the SQL query
$stmt = $conn->prepare("INSERT INTO Child (Full_Name, Image_Path, Parent_ID) VALUES (?, ?, ?)");
if (!$stmt) {
    error_log("Failed to prepare statement: " . $conn->error);
    echo json_encode(["status" => "error", "message" => "Failed to prepare statement: " . $conn->error]);
    exit;
}

$stmt->bind_param("ssi", $full_name, $image_path, $parent_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Child added successfully."]);
} else {
    error_log("Failed to add child: " . $stmt->error);
    echo json_encode(["status" => "error", "message" => "Failed to add child: " . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>