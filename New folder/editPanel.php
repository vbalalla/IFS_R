<?php
require_once("Sql.php");
require_once("GlobalVariables.php");
$s = new Sql();
$connect = $s->connectToDatabase($databaseName);		

$array=array();
if(isset($_GET['panel'])){
	$array=explode(",", $_GET['panel']);
}

if($array[0]!="11"){
	array_unshift($array,"");
}
	
$default = $array[0];
$id = $array[1];
$name = $array[2];

if($default=="11"){
	mysqli_query($connect,"UPDATE interviewpanel SET status='' WHERE status='default'");
	mysqli_query($connect,"UPDATE interviewpanel SET IntPanName='$name' , status='default' WHERE IntPanID=$id");		
}else{
	mysqli_query($connect,"UPDATE interviewpanel SET IntPanName='$name' WHERE IntPanID=$id");		
}

$length=count($array);

mysqli_query($connect,"DELETE FROM interviewpanelmemberdetails WHERE IntPanID=$id");		

for($i=2;$i<$length;$i++){
	mysqli_query($connect,"INSERT INTO interviewpanelmemberdetails(IntPanID,EmpID) VALUES($id,'$array[$i]')");
}

header("Location: editInterviewPanel.php?pid=$id");
die();

?>