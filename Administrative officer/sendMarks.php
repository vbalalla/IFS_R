<?php
require_once("Sql.php");
require_once("GlobalVariables.php");
    include('phpSessions.php');
		
$s = new Sql();
$connect = $s->connectToDatabase($databaseName);

$intID="";
if(isset($_GET["int"])){
	$intID=$_GET["int"];
}

$cid="";
if(isset($_GET["cand"])){
	$cid=$_GET["cand"];
}
echo $_SESSION['usrid'];

$criteria = mysqli_query($connect, "SELECT evalName FROM evaluation,interview,criteria WHERE criteria.criteriaID=interview.EvaCriID AND criteria.criteriaID=evaluation.criteriaID AND interview.IntID=$intID");

$criteriaNameq = mysqli_query($connect, "SELECT criteriaName FROM criteria,interview WHERE criteriaID=interview.EvaCriID AND interview.IntID=$intID"); 
$criteriaName = $criteriaNameq->fetch_row();

$queryToGetCriteriaID=mysqli_query($connect,"SELECT EvaCriID FROM interview WHERE IntID=$intID");
while($row3 = $queryToGetCriteriaID->fetch_row()){
		$criteriaID=$row3[0];
}


	
$table = "criteria".$criteriaID."_".$intID;
  

while($row = $criteria->fetch_row()){
	$value = NULL;
	if(isset($_POST["$row[0]"])){
		$value = $_POST["$row[0]"];
	}
		
	mysqli_query($connect, "UPDATE $table SET $row[0]=$value WHERE CandID='$cid'");
}

$a=$_POST['comments'];
echo $a;
mysqli_query($connect, "UPDATE $table SET comment='$a' WHERE CandID='$cid'");

//header("Location: interviewCandidates.php?int=$intID");
?>