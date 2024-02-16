<?php
// Database connection
$servername = "sql11.freesqldatabase.com";
$username = "sql11684548";
$password = "C9UjfKj5PY";
$database = "sql11684548";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Login handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"));

    $username = $conn->real_escape_string($data->username);
    $password = $conn->real_escape_string($data->password);

    // Perform your password verification logic here
    // For demonstration purposes, this script assumes a simple comparison with plaintext passwords
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Login successful
        $response = array('success' => true);
        echo json_encode($response);
    } else {
        // Login failed
        $response = array('success' => false);
        echo json_encode($response);
    }
}

$conn->close();
?>
