<?php

// Start the session
//session_start();
		require_once("Sql.php");
		require_once("GlobalVariables.php");
		$s = new Sql();
		$connect = $s->connectToDatabase($databaseName);
error_reporting(E_ALL);
require_once 'vendor1/autoload.php';


  function sendMail($email, $subject, $body){ //function parameters, 3 variables.
	$mail = new PHPMailer();
	$mail->IsSMTP();
	//$mail->SMTPDebug = 2;
	$mail->SMTPAuth = true;
	
	$mail->Host = "smtp.gmail.com";
	$mail->Username = "ifsworld88@gmail.com";
	$mail->Password = "ifs@1234";
	$mail->SMTPSecure = "ssl";
	$mail->Port = 465;
	$mail->From = "ifsworld88@gmail.com";
	$mail->FromName = "IFS R&D International";
	$mail->AddReplyTo("ifsworld88@gmail.com","IFS");
	
	//$mail->AddAddress("fshalika.fdo@gmail.com","shalika");
	//$mail->AddAddress("shalikafernando9@gmail.com","shalika");
	$mail->addBCC("$email");
	
	$mail->WordWrap = 50;
	$mail->IsHTML(true);
	//$mail->addAttachment('images/ifs.png','ifs.png');//if needed
	
	$mail->Subject = $subject;
	$mail->Body = $body;
	$_SESSION["mail"] = "";
	if($mail->send())
	{
		//echo "sent mail";
		//$_SESSION["mail"] = "success";
		//setcookie("mail", "Success");

	}else{
		//echo "send mail failed" . $mail->ErrorInfo;
		//$_SESSION["mail"] = "failed";
		//setcookie("mail", "Failed");
	}
}
$candID = "";
if(isset($_POST["cand"])){
	$candID = $_POST["cand"];
}
$date = "";
if(isset($_POST["date"])){
	$date = $_POST["date"];
}

$time = "";
if(isset($_POST["time"])){
	$time = $_POST["time"];
}

$email = "";
if(isset($_POST["em"])){
	$email = $_POST["em"];
}
$venue = "";
if(isset($_POST["ven"])){
	$venue = $_POST["ven"];
}



$notice = "";
if(isset($_POST["msg"])){
	$notice = $_POST["msg"];
}
$jobPos = "";
if(isset($_POST["job"])){
	$jobPos = $_POST["job"];
}
$interview = "";
if(isset($_POST["intrw"])){
	$interview = $_POST["intrw"];
}
$rSessionID = "";
if(isset($_POST["session"])){
	$rSessionID = $_POST["session"];
}
$subject = $interview." for the ".$jobPos;
$body = "The interview will be held on ".$date." at ".$time." in ".$venue.".<br>".$notice.".<br>Thank You.";

if($date == '' || $time == ''){

}else{
	sendMail($email, $subject, $body);
}
$queryGetIntID = mysqli_query($connect,"SELECT MAX(IntID) FROM interview WHERE RSID = '".$rSessionID."'");
$getIntID = $queryGetIntID->fetch_row();
$IntID = $getIntID[0];

mysqli_query($connect,"INSERT INTO interviewdetails (date,time,CandID,IntID) VALUES ('".$date."','".$time."','".$candID."','".$IntID."')");

?>