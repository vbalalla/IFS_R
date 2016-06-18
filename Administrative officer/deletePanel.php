<?php
require_once("Sql.php");
require_once("GlobalVariables.php");
$s = new Sql();
$connect = $s->connectToDatabase($databaseName);		

$array=array();
if(isset($_GET['panel'])){
	$array=explode(",", $_GET['panel']);
}
print_r($array);
$length=count($array);

for($i=0;$i<$length;$i++){
	mysqli_query($connect,"DELETE FROM interviewpanel WHERE IntPanID=$array[$i]");		
	mysqli_query($connect,"DELETE FROM interviewpanelmemberdetails WHERE IntPanID=$array[$i]");		
}

header("Location: interviewPanelHome.php");
die();

?>