<?php 
require_once("Sql.php");
require_once("GlobalVariables.php");

$s = new Sql();
$connect = $s->connectToDatabase($databaseName);

$id="";
if(isset($_GET['id'])){
	$id = $_GET['id'];
}
$sessionID="";
if(isset($_GET['rsid'])){
	$sessionID = $_GET['rsid'];
}
$rName="";
if(isset($_GET["rname"])){
		$rName=$_GET["rname"];
}
$rJob="";
if(isset($_GET["rjob"])){
		$rJob=$_GET["rjob"];
}
$rDate="";
if(isset($_GET["rdate"])){
		$rDate=$_GET["rdate"];
}
$rStatus="";
if(isset($_GET["rstatus"])){
		$rStatus=$_GET["rstatus"];
}


//echo $id;
$d1=$_POST["d1"];
$d2=$_POST["d2"];
$d3=$_POST["d3"];
$d4=$_POST["d4"];
$d5=$_POST["d5"];
$d6=$_POST["d6"];
$d7=$_POST["d7"];
$d8=$_POST["d8"];

$error = 0;
$errormsg = "*";

//Validate first name
$nameErr1 = "";
if (!preg_match("/^[a-zA-Z ]*$/",$d2)) {
  $nameErr1 = "Only letters and white space allowed in First Name";
  $errormsg = $errormsg."    *".$nameErr1;
	$error = 1;
	echo $nameErr1;
}

//Validate last name
$nameErr2 = "";
if (!preg_match("/^[a-zA-Z ]*$/",$d3)) {
  $nameErr2 = "Only letters and white space allowed in Last Name";
  $errormsg = $errormsg."    *".$nameErr2;
  $error = 1;
  echo $nameErr2;
}

//Validate email
$emailErr = "";
if (!filter_var($d5, FILTER_VALIDATE_EMAIL)) {
  $emailErr = "Invalid email address"; 
  $errormsg = $errormsg."    *".$emailErr;
  $error = 1;
  if($d5 == ""){
	  $error = 0;
  }
  echo $emailErr;
}

//Validate NIC
$nicErr = "";
if (!preg_match("/[0-9]{9}+(V|v)/",$d1)) {
  $nicErr = "Invalid NIC";
  $errormsg = $errormsg."    *".$nicErr;
  $error = 1;
  if($d1 == ""){
	  $error = 0;
  }
echo $nicErr;  
}

//Validate ContactNo
$contactErr = "";
if (!preg_match("/[0][0-9]{9}/",$d6)) {
  $contactErr = "Invalid Contact No"; 
  $errormsg = $errormsg."    *".$contactErr;
  $error = 1;
  echo $contactErr;  
}


if($error == 0){
	$conn = mysqli_query($connect, "UPDATE candidate SET NIC= '$d1',FirstName='$d2',LastName='$d3',DateOfBirth='$d4',email='$d5',ContactNo='$d6',University='$d7', candStatusID='$d8' WHERE CandID = '$id'");
	header("Location: candidateList.php?rsid=$sessionID&rname=$rName&rjob=$rJob&rdate=$rDate&rstatus=$rStatus");
	die();
}else{
	header("Location: enterCandiateDetails.php?id=$id&rsid=$sessionID&rname=$rName&rjob=$rJob&rdate=$rDate&rstatus=$rStatus&valid=1&msg=$errormsg");
	die();
}

//header("Location: candidateList.php?rsid=$sessionID&rname=$rName&rjob=$rJob&rdate=$rDate&rstatus=$rStatus");
//die();
?>
