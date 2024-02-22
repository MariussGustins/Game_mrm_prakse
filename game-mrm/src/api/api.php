<?php
global $conn;
include "./db.php";


if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
    // you want to allow, and if so:
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 1000');
}
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
    }

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
    }
    exit(0);
}

$res = array('error' => false);
$action='';

if (isset($_GET['action'])) {

    $action=$_GET['action'];
}
if($action=='register'){

    $first_name=$_POST['first_name'];
    $last_name=$_POST['last_name'];
    $email=$_POST['email'];
    $username=$_POST['username'];
    $password=$_POST['password'];

    $sql="INSERT INTO `users`(`id`, `first_name`, `last_name`, `email`, `username`, `password`) VALUES(NULL,'$first_name','$last_name','$email','$username','$password')";
    $result=$conn->query($sql);
    if($result===true){
        $res['error']=false;
        $res['message']="User Added Successfully";
    }else{
        $res['error']=true;
        $res['message']="Somthing Went Wrong";
    }

}


if($action=='login'){
    $username=$_POST['username'];
    $password=$_POST['password'];
    $sql="Select * from users where username='$username' AND password='$password'";
    $result=$conn->query($sql);
    $num=mysqli_num_rows($result);
    if($num > 0){
        // Retrieve the username from the session
        $loggedInUser = $_SESSION['username'];

// Fetch the username from the database
        $sqlUsername = "SELECT username FROM users WHERE username = '$loggedInUser'";
        $resultUsername = $conn->query($sqlUsername);

        if ($resultUsername->num_rows > 0) {
            // Fetch the username from the result
            $row = $resultUsername->fetch_assoc();
            $usernameFromDatabase = $row['username'];
        } else {
            $usernameFromDatabase = "Guest"; // Default to Guest if username not found
        }

        $res['message']="Login Successfuly";
    }
    else{
        $res['error']=true;
        $res['message']="Your Login Username or Password is invalid";
    }
}
if ($action == 'save_score') {
    // Retrieve the score, best_result, and username from the request
    $score = $_POST['score'];
    $best_result = $_POST['best_result'];
    $logusername = $_POST['logusername'];

    echo "Received data: score - $score, best_result - $best_result, username - $logusername";

    // Fetch user ID from database using the session username
    $query = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $logusername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['id'];

        // Check if the user has an existing record in user_scores
        $query = "SELECT * FROM user_scores WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $existing_result = $stmt->get_result();

        if ($existing_result->num_rows > 0) {
            // Update the existing record with the new score and best result
            $query = "UPDATE user_scores SET score = ?, best_result = ? WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iii", $score, $best_result, $user_id);
            $stmt->execute();
        } else {
            // Insert a new record for the user's score
            $query = "INSERT INTO user_scores (user_id, score, best_result) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iii", $user_id, $score, $best_result);
            $stmt->execute();
        }

        // Return response indicating successful score insertion or update
        $response = array('error' => false, 'message' => 'Score saved successfully');
        echo json_encode($response);
        exit();
    } else {
        // If the user is not found, return an error message
        echo json_encode(array('error' => true, 'message' => 'User not found'));
        exit();
    }
}

if ($action == 'get_highest_score') {
    // Check if the username parameter is provided
    if(isset($_GET['username'])) {
        $username = $_GET['username'];

        // Fetch the user ID for the provided username
        $query = "SELECT id FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['id'];

            // Fetch the highest score for the provided user ID
            $query = "SELECT best_result AS highest_score FROM user_scores WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $highestScore = $row['highest_score'];
                echo json_encode(array('highestScore' => $highestScore, 'error' => false));
            } else {
                // No score found for the provided username
                echo json_encode(array('error' => 'No score found for the provided username'));
            }
        } else {
            // User not found
            echo json_encode(array('error' => 'User not found'));
        }
    } else {
        // Username parameter is missing
        echo json_encode(array('error' => 'Username parameter is missing'));
    }
}




if ($action == 'check_login') {
    session_start(); // Start the session
    $res['logged_in'] = isset($_SESSION['username']);
    if ($res['logged_in']) {
        $res['username'] = $_SESSION['username']; // Send username if logged in
    }
    // Send response
    header('Content-Type: application/json');
    echo json_encode($res);
    exit();
}

?>