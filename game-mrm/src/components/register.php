<?php

// Retrieve JSON data from the request body
$data = json_decode(file_get_contents("php://input"));
// Retrieve form data
if (!empty($data)) {
    // Retrieve form data from the JSON object
    $firstName = $data->first_name;
    $lastName = $data->last_name;
    $email = $data->email;
    $username = $data->username;
    $password = $data->password;

$servername = "sql11.freesqldatabase.com";
$username = "sql11684548";
$password = "C9UjfKj5PY";
$database = "sql11684548";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL statement
$sql = "INSERT INTO users (first_name, last_name, email, username, password)
VALUES (?, ?, ?, ?, ?, ?, ?)";

// Bind parameters and execute the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssi", $firstName, $lastName, $email, $username, $password);

if ($stmt->execute()) {
// Registration successful
$response = ['success' => true, 'message' => 'User registered successfully'];
} else {
// Registration failed
$response = ['success' => false, 'message' => 'Failed to register user'];
}

// Close connection
$stmt->close();
$conn->close();

// Respond with JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'User registered successfully']);
} else {
    // No data received
    http_response_code(400);
    echo json_encode(['error' => 'No data received']);
}

