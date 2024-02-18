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