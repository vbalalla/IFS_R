<?php

require_once("Sql.php");
require_once("GlobalVariables.php");
require_once("emailFunction.php");

$s = new Sql();
$connect = $s->connectToDatabase($databaseName);

$candID = "";
if(isset($_POST["cand"])){
	$candID = $_POST["cand"];
}
echo $candID;
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

$interview = "38";
if(isset($_POST["intrw"])){
	$interview = $_POST["intrw"];
}
$rSessionID= "";
if(isset($_POST["rsID"])){
	$rSessionID=$_POST["rsID"];
}
$getIntIDs = mysqli_query($connect,"SELECT IntID FROM interview WHERE RSID='".$rSessionID."' ORDER BY IntID ASC");
$intType="";
$intrw = array();
$i = 0;
while($data1 = $getIntIDs->fetch_row()){
	$intrw[$i] = $data1[0];
	$i++;	
}

	if($intrw[0]==$interview){
		$intType = "First Interview";
	}
	else if($intrw[1]==$interview){
		$intType = "Second Interview";
	}
	else if($intrw[2]==$interview){
		$intType = "Third Interview";
	}
	else if($intrw[3]==$interview){
		$intType = "Fourth Interview";
	}
	else if($intrw[4]==$interview){
		$intType = "Fifth Interview";
	}
	else if($intrw[5]==$interview){
		$intType = "Sixth Interview";
	}
	else if($intrw[6]==$interview){
		$intType = "Seventh Interview";
	}

//echo $intType;
$getJobName = mysqli_query($connect,"SELECT jbName FROM jobpositon,recruitmentsession WHERE jobpositon.jbID=recruitmentsession.jobPositionID AND recruitmentsession.RSID='".$rSessionID."'");
$jobName="";
while($data2 = $getJobName->fetch_row()){
	$jobName = $data2[0];
}
echo $jobName;
$subject = "The ".$intType." for the ".$jobName;
echo $subject;
$body = "You have been selected for the ".$intType." of the job position ".$jobName.".<br>The interview will be held on ".$date." at ".$time." in ".$venue.".<br>".$notice.".<br>Thank You.";
echo $body;
if($date == '' || $time == '' || $venue == ''){

}else{
	sendMail($email, $subject, $body);

	mysqli_query($connect,"INSERT INTO interviewdetails (date,time,CandID,IntID) VALUES ('".$date."','".$time."','".$candID."','".$interview."')");
}


?>