<?php 
	require_once("Sql.php");
	require_once("GlobalVariables.php");
		
	$s = new Sql();
	$connect = $s->connectToDatabase($databaseName);
	
	if(isset($_POST['d8'])){
	if ($_POST['d8']=='Selected'){
		
		$changeStatus = mysqli_query($connect, "UPDATE ");
		
		
		}
	
	
	}
?>