<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'db_connection.php'; // Include the database connection

// Get JSON data from the request body
$data = json_decode(file_get_contents("php://input"), true);

// Extract input fields
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

// Validate input
if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Email and password are required."]);
    exit;
}

// Check if the email exists in the database
$stmt_check_email = $conn->prepare("SELECT Parent_ID, Password_Hash, Full_Name FROM Parent WHERE Email = ?");
$stmt_check_email->bind_param("s", $email);
$stmt_check_email->execute();
$result = $stmt_check_email->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
    $stmt_check_email->close();
    exit;
}

// Fetch the parent ID, hashed password, and full name
$row = $result->fetch_assoc();
$parent_id = $row['Parent_ID'];
$hashed_password = $row['Password_Hash'];
$full_name = $row['Full_Name'];

// Verify the password
if (!password_verify($password, $hashed_password)) {
    echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
    $stmt_check_email->close();
    exit;
}

// Close the statement
$stmt_check_email->close();

// Return success response with basic user information
echo json_encode([
    "status" => "success",
    "message" => "Login successful.",
    "user" => [
        "parent_id" => $parent_id,
        "full_name" => $full_name,
        "email" => $email
    ]
]);

// Close the database connection
$conn->close();
?>