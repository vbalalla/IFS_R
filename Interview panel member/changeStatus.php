<?php

require_once("Sql.php");
require_once("GlobalVariables.php");
$s = new Sql();
$connect = $s->connectToDatabase($databaseName);


$intID="";
	if(isset($_GET['int'])){
		$intID=$_GET['int'];
		}
		
$criteriaID="";
		if(isset($_GET["criteria"])){
				$criteriaID=$_GET["criteria"];
		}
		
$changeInterviewStatus= mysqli_query($connect,"UPDATE interview SET interviewStatusID='is002' WHERE intID='$intID'");

header("location: a_interviews.php");

?>