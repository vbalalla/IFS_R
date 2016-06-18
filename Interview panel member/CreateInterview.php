<?php 
		require_once("Sql.php");
		require_once("GlobalVariables.php");
		$s = new Sql();
		$connect = $s->connectToDatabase($databaseName);
		
		$interviewName="";
		if(isset($_POST["interviewname"])){
				$interviewName=$_POST["interviewname"];
		}
				
		$intPanID=0;
		if(isset($_POST["intPanID"])){
				$intPanID=(int)$_POST["intPanID"];
		}
		
		$rSessionID="";
		if(isset($_POST["rsid"])){
				$rSessionID=$_POST["rsid"];
		}
		
		$duration=0;
		if(isset($_POST["duration"])){
				$duration=$_POST["duration"];
		}
				
		if($interviewName != "" && $intPanID != 0 && $rSessionID != ""){
			mysqli_query($connect,"INSERT INTO interview (Name, RSID, IntPanID,duration,interviewStatusID) VALUES ('$interviewName','$rSessionID', $intPanID, $duration,'is001')");
		}
		//echo "INSERT INTO interview (Name, RSID, IntPanID,duration,interviewStatusID) VALUES ('$interviewName','$rSessionID', $intPanID, $duration,'is001')";
				
		$lastID=$s->increaseID($connect,"interview","IntID",1);
		$lastID=(int)$lastID--;
		$schedule_data = isset($_POST['schedule']) ? $_POST['schedule'] : array();
		foreach($schedule_data as $schedule){
			$date = $schedule['date'];
			$from = $schedule['from'];
			$to = $schedule['to'];
			
			if(!empty($date) && !empty($from) && !empty($to)){
				mysqli_query($connect,"INSERT INTO interviewschedule (schdate, schfrom, schto, IntID) VALUES ('$date','$from','$to',$lastID)");
			}				
		}		
		
?>
