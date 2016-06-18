<?php
require_once("Sql.php");
require_once("GlobalVariables.php");
$s = new Sql();
$connect = $s->connectToDatabase($databaseName);

		$rSessionID="";
		if(isset($_GET["rsid"])){
				$rSessionID=$_GET["rsid"];
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
		
		$intvID="";
		if(isset($_GET["int"])){
				$intvID=$_GET["int"];
		}
		
		$duration=0;
		if(isset($_GET["dur"])){
				$duration=$_GET["dur"];
		}
		
		$cid=array();
		if(isset($_GET["cands"])){
				$cid=explode(",", $_GET["cands"]);
		}
		
		$date=array();
		if(isset($_GET["dates"])){
				$date=explode(",", $_GET["dates"]);
		}
		
		$time=array();
		if(isset($_GET["times"])){
				$time=explode(",", $_GET["times"]);
		}
		
	$length=count($cid);
$datastr = $cid[0]; $j=1;
for($i=0;$i<$length;$i++){
	mysqli_query($connect,"INSERT INTO interviewreschedule(CandID,intID,date,time) VALUES ('$cid[$i]',$intvID,'$date[$i]','$time[$i]')");		
	if($j<$length){
		$datastr = $datastr.",".$cid[$j];
	}
	$j++;
}

header("Location: interviewReschedule.php?rsid=$rSessionID&rname=$rName&rjob=$rJob&rdate=$rDate&rstatus=$rStatus&int=$intvID&cands=$datastr");
die();
?>