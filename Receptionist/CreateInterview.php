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
					//echo $interviewName." hello"; echo $rSessionID;		
		}
		echo "INSERT INTO interview (Name, RSID, IntPanID,duration,interviewStatusID) VALUES ('$interviewName','$rSessionID', $intPanID, $duration,'is001')";
		echo $interviewName; echo $rSessionID;		
		$lstID=mysqli_query($connect,"SELECT MAX(IntID) FROM interview");
		$lasID=$lstID->fetch_row();
		$lastID=(int)$lasID[0];
		$lastID=$lastID+1;
		echo "SELECT MAX(IntID) FROM interview WHERE Name=$interviewName AND RSID=$rSessionID";
		echo $lastID;
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
