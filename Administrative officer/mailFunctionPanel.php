<?php


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
$Venue = "";
if(isset($_POST["ven"])){
	$candID = $_POST["ven"];
}
echo $Venue;

$IntID = "";
if(isset($_POST["intrwID"])){
	$candID = $_POST["intrwID"];
}
echo $IntID;

$getJobName = mysqli_query($connect, "SELECT jbName FROM jobpositon,recruitmentsession,interview WHERE jobpositon.jbID=recruitmentsession.jobPositionID AND recruitmentsession.RSID=interview.RSID AND interview.IntID=$IntID");
$subject = "Interview for the ";
$jobPosition = "";
while($data1 = $getJobName -> fetch_row()){
	$subject .= $data1[0];
	$jobPosition = $data1[0];
}
echo $subject;

$dateTime = mysqli_query($connect,"SELECT schdate,schfrom,schto FROM interviewschedule WHERE IntID=$IntID");

$body="The interview for the job position ".$jobPosition." will be held on following dates.<br><br>Scheduled dates and times for the Interview :<br>";
while($data2 = $dateTime->fetch_row()){
	$body = $body . "<br>Date : ". $data2[0]." Time: From ".$data2[1]." To ".$data2[2];
	
}
$body.= "<br><br>Thank You.";
echo $body;

$emailList = mysqli_query($connect,"SELECT email FROM interview,interviewpanelmemberdetails,employee WHERE employee.EmpID=interviewpanelmemberdetails.EmpID AND interviewpanelmemberdetails.IntPanID=interview.IntPanID AND interview.IntID=$IntID");
while($data3 = $emailList -> fetch_row()){
	sendMail($data3[0],$subject,$body);
	echo $data3[0]."<br>";
}




?>