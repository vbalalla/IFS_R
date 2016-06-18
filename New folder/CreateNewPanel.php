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
$name = $array[1];

if($default=="11"){
	mysqli_query($connect,"UPDATE interviewpanel SET status='' WHERE status='default'");
	mysqli_query($connect,"INSERT INTO interviewpanel(IntPanName,status) VALUES('$name','default')");		
}else{
	mysqli_query($connect,"INSERT INTO interviewpanel(IntPanName) VALUES('$name')");
}

$nameData=mysqli_query($connect,"SELECT MAX(IntPanID) FROM interviewpanel WHERE IntPanName='$name'");
$nameRes = $nameData->fetch_row();
$length=count($array);

for($i=2;$i<$length;$i++){
	mysqli_query($connect,"INSERT INTO interviewpanelmemberdetails(IntPanID,EmpID) VALUES($nameRes[0],'$array[$i]')");
}

header("Location: interviewPanelHome.php");
die();

?>