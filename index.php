<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "root"; // Update with your MySQL username
$password = "AaBbCc@1"; // Update with your MySQL password
$dbname = "tasks"; // Desired database name

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Connect to MySQL server
    $conn = new mysqli($servername, $username, $password);

    // Check if the database exists
    $result = $conn->query("SHOW DATABASES LIKE '$dbname'");
    if ($result->num_rows === 0) {
        // Create the database if it doesn't exist
        $conn->query("CREATE DATABASE $dbname");
        echo json_encode(["success" => true, "message" => "Database '$dbname' created successfully"]);
    }

    // Select the database
    $conn->select_db($dbname);

    // Proceed with your operations (e.g., updating the batch)
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['batchId'])) {
        echo json_encode(["success" => false, "message" => "Batch ID is required"]);
        exit;
    }

    $batchId = $data['batchId'];

    // Example table creation (only for demo purposes)
    $conn->query("CREATE TABLE IF NOT EXISTS batches (batch_id VARCHAR(255) PRIMARY KEY, status VARCHAR(255))");

    // Example query to update the batch
    $sql = "UPDATE batches SET status='updated' WHERE batch_id='$batchId'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Batch updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating batch"]);
    }

    $conn->close();
} catch (mysqli_sql_exception $e) {
    
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
    exit;
}
