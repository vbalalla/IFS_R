<?php
session_start();
if(session_destroy()) // Destroying All Sessions
{
    header("Location: loginInterface.php"); // Redirecting To Home Page
}
?>