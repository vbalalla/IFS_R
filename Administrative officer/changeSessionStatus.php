<?php
	require_once("Sql.php");

	$rStatusID="";
	$RSID="";
	if(isset($_POST['sessionStatusID'])){
		$rStatusID=$_POST['sessionStatusID'];
	}
	if(isset($_POST['RSID'])){
		$RSID=$_POST['RSID'];
	}
	
	$s = new Sql();
	$s->changeSessionStatus($RSID,$rStatusID);
?>
