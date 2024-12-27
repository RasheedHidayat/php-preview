<?php
// Set headers for CORS and JSON response
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Database credentials
$servername = "localhost";
$username = "root";
$password = "AaBbCc@1";
$database = "test";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Establish database connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Fetch and decode incoming data
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate incoming data
    if (!isset($data['batchId'])) {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "Batch ID is required"]);
        exit;
    }

    $batchId = $data['batchId'];

    // Ensure the table exists (Create it if necessary for demo purposes)
    $conn->query("
        CREATE TABLE IF NOT EXISTS batches (
            batch_id VARCHAR(255) PRIMARY KEY,
            status VARCHAR(255)
        )
    ");

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE batches SET status = ? WHERE batch_id = ?");
    $status = "updated";
    $stmt->bind_param("ss", $status, $batchId);

    // Execute the query
    if ($stmt->execute()) {
        // Check if any rows were affected
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "Batch updated successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "No batch found with the given ID"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update batch"]);
    }

    $stmt->close();
    $conn->close();
} catch (mysqli_sql_exception $e) {
    // Handle database errors
    http_response_code(500); // Internal Server Error
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
    exit;
}
?>
