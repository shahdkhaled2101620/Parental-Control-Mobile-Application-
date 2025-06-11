<?php
// get_child_id.php
require_once 'db_connection.php'; // Include the database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Get JSON data from the request body
$data = json_decode(file_get_contents("php://input"), true);

// Extract input fields
$parent_id = intval($data['parent_id'] ?? 0); // Parent ID
$child_name = trim($data['child_name'] ?? ''); // Child Name

// Validate input
if (empty($parent_id) || empty($child_name)) {
    echo json_encode(["status" => "error", "message" => "Parent ID and Child Name are required."]);
    exit;
}

// Prepare and execute the SQL query
$stmt = $conn->prepare("SELECT Child_ID FROM Child WHERE Parent_ID = ? AND Full_Name = ?");
$stmt->bind_param("is", $parent_id, $child_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "status" => "success",
        "message" => "Child ID retrieved successfully.",
        "child_id" => $row['Child_ID']
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "No child found with the given Parent ID and Child Name."]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>