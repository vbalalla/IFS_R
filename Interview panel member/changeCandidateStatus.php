<?php 
	require_once("Sql.php");
	require_once("GlobalVariables.php");
		
	$s = new Sql();
	$connect = $s->connectToDatabase($databaseName);
	
	$id="";
	if(isset($_GET['cand'])){
		$id = $_GET['cand'];
	}
	
	$criteria="";
	if(isset($_GET['criteria'])){
		$criteria = $_GET['criteria'];
	}
	
	$intID="";
	if(isset($_GET["int"])){
			$intID=$_GET["int"];
	}
	
	if(isset($_POST['d8'])){
	if ($_POST['d8']=='Selected'){
		
		$changeStatus = mysqli_query($connect, "UPDATE candidate SET candStatusID='CS005' WHERE CandID='$id'");
		
		}
	elseif($_POST['d8']=='Rejected'){
		$changeStatus = mysqli_query($connect, "UPDATE candidate SET candStatusID='CS006' WHERE CandID='$id'");
		
		}
	
	
	}
	
header("Location: ranking.php?int=$intID&criteria=$criteria");
?>