<?php
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
?>