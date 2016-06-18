<?php

require_once("Sql.php");
require_once("GlobalVariables.php");
require_once("emailFunction.php");<br />



$s = new Sql();
$connect = $s->connectToDatabase($databaseName);

$Venue = "";
if(isset($_POST["ven"])){
	$Venue = $_POST["ven"];
}
echo $Venue;

$IntID = "";
if(isset($_POST["intrwID"])){
	$IntID = $_POST["intrwID"];
}
//echo $IntID;

$getJobName = mysqli_query($connect, "SELECT jbName FROM jobpositon,recruitmentsession,interview WHERE jobpositon.jbID=recruitmentsession.jobPositionID AND recruitmentsession.RSID=interview.RSID AND interview.IntID=$IntID");
$subject = "Interview for the ";
$jobPosition = "";
while($data1 = $getJobName -> fetch_row()){
	$subject .= $data1[0];
	$jobPosition = $data1[0];
}
//echo $subject;

$dateTime = mysqli_query($connect,"SELECT schdate,schfrom,schto FROM interviewschedule WHERE IntID=$IntID");

$body="The interview for the job position ".$jobPosition." will be held on following dates at ".$Venue.".<br><br>Scheduled dates and times for the Interview :<br>";
while($data2 = $dateTime->fetch_row()){
	$body = $body . "<br>Date : ". $data2[0]." Time: From ".$data2[1]." To ".$data2[2];
	
}
$body.= "<br><br>Thank You.";
//echo $body;

$emailList = mysqli_query($connect,"SELECT email FROM interview,interviewpanelmemberdetails,employee WHERE employee.EmpID=interviewpanelmemberdetails.EmpID AND interviewpanelmemberdetails.IntPanID=interview.IntPanID AND interview.IntID=$IntID");
while($data3 = $emailList -> fetch_row()){
	if($Venue == ''){
		
	}else{
		sendMail($data3[0],$subject,$body);
		//echo $data3[0]."<br>";
	}
}




?>