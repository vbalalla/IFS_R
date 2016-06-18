<?php
session_start(); // Starting Session
$error='';// Variable To Store Error Message
$usrerror='';
$passerror='';
if (isset($_POST['submit'])) {
    if (empty($_POST['username'])) {
        $usrerror = "User name is empty";
    }
    elseif(empty($_POST['password'])){
        $passerror = "Password is empty";
    }
    else
    {
// Define $username and $password
        $username=$_POST['username'];
        $password=$_POST['password'];
// Establishing Connection with Server by passing server_name, user_id and password as a parameter
        $connection = mysqli_connect("localhost", "root", "");
// To protect MySQL injection for Security purpose
        $username = stripslashes($username);
        $password = stripslashes($password);
        $username = mysql_real_escape_string($username);
        $password = mysql_real_escape_string($password);
// Selecting Database
        $db = mysqli_select_db($connection,"recruit");
// SQL query to fetch information of registerd users and finds user match.
        //$query1 = mysqli_query($connection, "select * from login where password='$password' AND username='$username' and IntPanID='$panelID'");
        $query = mysqli_query($connection, "select * from login where password='$password' AND username='$username'");
        $rows = mysqli_num_rows($query);
        if ($rows == 1) {
            $data = mysqli_fetch_assoc($query);
            $_SESSION['login_user']=$username; // Initializing Session
            $_SESSION['type']=$data['type'];
            $_SESSION['usrid']=$data['usrid'];
            if($data['type'] == 'admin_officer'){
                header("location: ../index.php"); // Redirecting To Other Page
            }
            if($data['type'] == 'system_admin'){
                header("location: System administrator/systemAdministratorHome.php"); // Redirecting To Other Page
            }
            if($data['type'] == 'receptionist'){
                header("location: Receptionist/recruitmentSessionInterface.php"); // Redirecting To Other Page
            }
            if($data['type'] == 'interview_panel'){
                header("location: Interview panel member/index.php"); // Redirecting To Other Page
            }

        } else {
            $error = "Username or Password is invalid";
        }
        mysqli_close($connection); // Closing Connection
    }
}
?>