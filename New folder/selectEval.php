<?php
require_once("Sql.php");
require_once("GlobalVariables.php");

$eval = "";
if(isset($_POST["selectEva"])){
	$eval = $_POST["selectEva"];
}

$intID="";
if(isset($_GET["int"])){
	$intID=$_GET["int"];
}

$s = new Sql();
$connect = $s->connectToDatabase($databaseName);
		
mysqli_query($connect,"UPDATE interview SET EvaCriID=$eval WHERE IntID=$intID");

$criteria = mysqli_query($connect,"SELECT * FROM criteria WHERE criteriaID=$eval");
$row = $criteria->fetch_row();

$evaluation = mysqli_query($connect,"SELECT * FROM evaluation WHERE criteriaID=$eval");
$candidates = mysqli_query($connect,"SELECT DISTINCT CandID FROM candidate,cv,interview WHERE candidate.cvID=cv.cvID AND cv.RSID=interview.RSID AND interview.IntID=$intID AND (candidate.candStatusID='CS001' OR candidate.candStatusID='CS005')");

$createTable = "CREATE TABLE $row[1]_$intID(MarkID int AUTO_INCREMENT,CandID VARCHAR(8)";
$i=0;
while($col = $evaluation->fetch_row()){
	$createTable = ($createTable).",$col[1] int";
	echo $col[1];
}
$createTable = $createTable.",CONSTRAINT PRIMARY KEY (MarkID), CONSTRAINT FOREIGN KEY(CandID)REFERENCES candidate(CandID) ON DELETE CASCADE ON UPDATE CASCADE)";
mysqli_query($connect,$createTable);

while($n = $candidates->fetch_row()){
	mysqli_query($connect,"INSERT INTO $row[1]_$intID(CandID) VALUES ('$n[0]')");
}

//header("Location: evalCriteria.php?int=$intID");
		
?>