<?php
// check conncection
session_start();
include '../config/db_config.php';

header('Content-Type: application/json');

// Check if clientId is provided in the request
if (isset($_GET['clientid'])) {
    $clientid = $_GET['clientid'];

    // Prepare SQL statement excluding MeasurementId, Clientid, and username
    $stmt = $conn->prepare("SELECT shirt_length, chest, neck, back, long_sleeve, short_sleeve, round_sleeve, shoulder, waist, trouser_length, ankle, thigh, knee_length, head, hip, burst FROM measurements WHERE clientid = ?");
    $stmt->bind_param("i", $clientid); // 'i' specifies the variable type => 'integer'
    
    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if any measurements are found
    if ($result->num_rows > 0) {
        $measurements = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
        echo json_encode($measurements);
    } else {
        // In case no measurements found, return a JSON object with an error message
        echo json_encode(array("error" => "No measurements found for client ID: $clientid"));
    }
    
    // Close the prepared statement
    $stmt->close();
} else {
    // In case clientId is not set, return a JSON object with an error message
    echo json_encode(array("error" => "clientid not provided in the request"));
}

// Close the database connection
$conn->close();
?>