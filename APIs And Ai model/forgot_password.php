<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'db_connection.php'; // Include the database connection

// Get JSON data from the request body
$data = json_decode(file_get_contents("php://input"), true);

// Extract input fields
$email = trim($data['email'] ?? '');
$new_password = trim($data['new_password'] ?? '');

// Validate input
if (empty($email) || empty($new_password)) {
    echo json_encode(["status" => "error", "message" => "Email and new password are required."]);
    exit;
}

// Check if the email exists in the database
$stmt_check_email = $conn->prepare("SELECT Parent_ID FROM Parent WHERE Email = ?");
$stmt_check_email->bind_param("s", $email);
$stmt_check_email->execute();
$result = $stmt_check_email->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Email not found."]);
    $stmt_check_email->close();
    exit;
}

// Fetch the Parent_ID associated with the email
$parent_id = $result->fetch_assoc()['Parent_ID'];

// Hash the new password securely
$password_hash = password_hash($new_password, PASSWORD_BCRYPT);

// Update the password in the database
$stmt_update_password = $conn->prepare("UPDATE Parent SET Password_Hash = ? WHERE Parent_ID = ?");
$stmt_update_password->bind_param("si", $password_hash, $parent_id);

if (!$stmt_update_password->execute()) {
    echo json_encode(["status" => "error", "message" => "Failed to update password: " . $stmt_update_password->error]);
    $stmt_update_password->close();
    exit;
}

// Close statements
$stmt_check_email->close();
$stmt_update_password->close();

// Close the database connection
$conn->close();

// Return success response
echo json_encode(["status" => "success", "message" => "Password updated successfully."]);
?>