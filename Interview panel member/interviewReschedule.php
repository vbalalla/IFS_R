<!-- Interview panel member -->
<?php
  include('../phpSessions.php');
  if($_SESSION['type']=='admin_officer')
    header("location: ../Administrative officer/index.php");
  
  if($_SESSION['type']=='receptionist')
    header("location: ../Receptionist/recruitmentSessionInterface.php");

	if($_SESSION['type']=='system_admin')
    header("location: ../System administrator/systemAdministratorHome.php");
  
  ?>


<?php
//session_start();
/*if(isset($_COOKIE["mail"])) {
    echo "Hello";
}*/
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Interview Schedules</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico"/>
<script src="sweetalert/dist/sweetalert.min.js"></script> 
<link rel="stylesheet" type="text/css" href="sweetalert/dist/sweetalert.css">

<script type="text/javascript" src="js/sortable.js"></script>
<script src="js/jquery.min.js"></script>
<script>
	function send(email,date,time,candID,rjob,intType,rsession){
		
		var getdate = document.getElementById(date).value;
		var gettime = document.getElementById(time).value;
		var getvenue = document.getElementById('venueName').value;
		
		var getmessage = document.getElementById('notice').value;
		if(getdate=='' && gettime==''){
			swal("Please fill date and time", "", "error");
		}
		else if(getdate==''){
			swal("Please fill date", "", "error");
		}
		else if(gettime==''){
			swal("Please fill time", "", "error");
		}else{
			swal("Mail sent successfully", "", "success");
		}
		//alert(getdate,gettime);
		var button = document.getElementsByName(candID)[0];
		button.disabled = true;
		//alert(document.getElementsByClassName(candID)[0].value);
		//window.location = "mailFunction.php?em="+email+"d="+getdate+"t="+gettime;
		$(document).ready(
			function() 
			{
				
							
					//$.post('CreateInterview.php',$('#tableCommon :input').serialize());
					
					$.post("mailFunction.php",
						{
							date:getdate,
							time:gettime,
							em:email,
							ven:getvenue,
							msg:getmessage,
							job:rjob,
							intrw:intType,
							session:rsession,
							cand:candID
							
						},
						function(data)
						{
							//alert(venue);
						}
					);
							
				
			}
			);
	}
	
</script>

<link href="css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
	.asideLeftIcons {
		margin-left: 9%;
		width: 70%;
		margin-bottom: 5%;
		text-align: left;
	}

	#interviewScheduleConfirmButton {
	width: auto;
	font-size: 0.85em;
	background-color: #FFFFFF;
	-webkit-transition: all 0.2s ease;
	-o-transition: all 0.2s ease;
	transition: all 0.2s ease;
	}
	#interviewScheduleRescheduleButton {
	background-image: url(images/recruitmentSession/reschedule.png);
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	margin-left: 9%;
	margin-bottom: 3%;
	padding-left: 2%;
	background-color: #E9E1E1;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
	}
.textInputInterviewSchedule {
	color: #BEA7AA;
	font-family: "OpenSans Regular";
	font-weight: 100;
	font-size: 0.85em;
	text-align: left;
	padding-left: 1%;
	width: 40%;
	margin-left: 1%;
	}
#interviewScheduleConfirmButton:hover {
	background-color: #DBCCCE;
}
#interviewScheduleRescheduleButton:hover {
	background-image: url(images/recruitmentSession/reschedule1.png);
}
#interviewVenueTable{
	margin-bottom: 3%;
	border-style: hidden;
	border-color: #E9E1E1;
	border-collapse: collapse;
	margin-top: 3%;
	text-align: left;
	padding-left: 2%;
	font-size: 1.2em;
}
#InteviewPanelNotiBtn {
	background-image: url(images/recruitmentSession/interviewPanel.png);
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	padding-left: 5%;
	width: 20%;
	margin-right: 4%;
	background-color: #E9E1E1;
	margin-left: 9%;
	margin-bottom: 5%;
	margin-top: 1%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
}
#InteviewPanelNotiBtn:hover {
	background-image: url(images/recruitmentSession/interviewPanel1.png);
}
</style>
<?php 
		require_once("Sql.php");
		require_once("GlobalVariables.php");
		$s = new Sql();
		$connect = $s->connectToDatabase($databaseName);
		
		$rSessionID="";
		if(isset($_GET["rsid"])){
				$rSessionID=$_GET["rsid"];
		}	
		
		$rJob="";
		if(isset($_GET["rjob"])){
				$rJob=$_GET["rjob"];
		}
		
		$intvID="";
		if(isset($_GET["int"])){
				$intvID=$_GET["int"];
		}
		
		$duration=0;
		if(isset($_GET["dur"])){
				$duration=$_GET["dur"];
		}
		
		$cid="C009";
		if(isset($_POST["cid"])){
				$cid=$_POST["cid"];
		}
		echo $cid;
		
?>
<script>
/*$(document).ready(
function() 
{
    $('#InteviewPanelNotiBtn').click(function(){
				
		//$.post('CreateInterview.php',$('#tableCommon :input').serialize());
		
		$.post("sendEmailToPanel.php",
		    {
				venue:$('#venueName').val(),
				notice:$('#notice').val(),
				rsid:"<?php echo $rSessionID; ?>"
				rjob:"<?php echo $rJob; ?>"
				intID:"<?php echo $intID?>"
				IntType:"<?php echo $intType?>"
				
			},
			function(data)
			{
				alert(venue);
			}
		);
				
	});
}
);*/
</script>
</head>
<body>
<div>
  <header>
    <aside class="asideRight">
        <span>
            <b id="welcome">Welcome : <i><a href="myAccount.php" style="color: #ffffff"><?php echo $login_session; ?></a></i></b>
            <b id="logout"><a href="../logout.php">Log Out</a></b>
        </span>

      <form action="SearchInterface.php" method="get">
        <input name="Search" type="search" class="searchbox" ><img src="images/searchIcon.png" width="15" height="15" alt=""/>
        <a href="index.php" class="navHome"> Home</a>
        <a href="../UserManual/UserManual.html" class="navHelp">Help </a>
      </form>

    </aside>
    <aside class="asideLeft"></aside>
  </header>
  
  <aside class="asideLeftIcons">
	  <nav>
		<a href="interviews.php?rsid=<?php echo $rSessionID?>&rjob=<?php echo $rJob?>" class="aBack">Back</a> 
		<a href="recruitmentSessionHelp.html" class="aHelp">Help</a>
	  </nav>
  </aside>
  <table width="84%" border="1" align="center" id="interviewVenueTable">
  <tbody>
    <tr>
      <th width="20%" scope="row">Venue for interview :</th>
      <td width="84%"><input id="venueName" type="text" class="textInputInterviewSchedule" placeholder="Enter Venue" required></td>
	</tr>
	<tr>
      <th width="20%" scope="row">Special Notices :</th>
      <td width="100%"><input id="notice" type="text" class="textInputInterviewSchedule" placeholder="Enter Notice"></td>
	</tr>
  </tbody>
</table>
  
  <table width="83%" border="1" class="sortable" align="center" id="tableCommon">
  <tbody>
    <tr>
	  
      <th width="10%" scope="col">Cand ID</th>
      <th width="14%" scope="col">NIC</th>
      <th width="14%" scope="col">Name</th>
      <th width="14%" scope="col">email</th>
      <th width="12%" scope="col">Contact No</th>
	  <th width="14%" scope="col">Date</th>
      <th width="14%" scope="col">Time</th>
      <th width="14%" scope="col">Confirmation</th>
    </tr>
   
   <?php 
	$query1 = mysqli_query($connect, "SELECT IntID FROM interview WHERE RSID = '".$rSessionID."' ORDER BY IntID DESC LIMIT 1" );
	$intID ="";
	while($raw = $query1->fetch_row()){
		$intID = $raw[0];
	}
	//echo $intID;
	$query2 = mysqli_query($connect,"SELECT IntID FROM interview WHERE RSID = '".$rSessionID."'");
	
	$noOfInt = 0;
	while($data = $query2-> fetch_row()){
		$noOfInt = $noOfInt + 1 ;
	}
	//echo $noOfInt."<br>";
	
	$candStatus = "";
	$intType = "";
	switch($noOfInt){
		case "1" : $candStatus = 'CS001'; $intType = "First Interview"; break;
		case "2" : $candStatus = 'CS005'; $intType = "Second Interview"; break;
	}
	//echo $candStatus."<br>";
	//echo $intType;
	$candStatus = "CS005";
	
	/*$getSlots = mysqli_query($connect, "SELECT IntSchID,schdate,schfrom,schto FROM interviewschedule WHERE IntID=$intvID");
	
	$slots = array(); $m = 0;
	while($r = $getSlots -> fetch_row()){
		$slots[$m] = $r;
		$m = $m+1;
	}
	$n = 0;
	$date = $slots[0][1];
	$time = $slots[0][2];
	$from = $slots[0][3];*/
	
		
	$query3 = mysqli_query($connect,"SELECT CandID,NIC,FirstName,LastName,email,ContactNo FROM candidate,cv WHERE candidate.candStatusID = '".$candStatus."' AND candidate.cvID = cv.cvID AND AND CandID='$cid' cv.RSID = '".$rSessionID."'");
	while($data1 = $query3 -> fetch_row()){ 
	
		?>
		<!--<script> var id = <?php echo(json_encode($raw[0])); ?>;</script>-->
                <tr  id = <?php echo $data1[0]?>>
					
                    <td class="tableData" name=<?php echo $data1[0] ?>><?php echo $data1[0] ?></td>
                    <td class="tableData" name=<?php echo $data1[0] ?>><?php echo $data1[1] ?></td>
                    <td class="tableData" name=<?php echo $data1[0] ?>><?php echo $data1[2]." ".$data1[3] ?></td>
                    <td class="tableData" name=<?php echo $data1[0] ?>><?php echo $data1[4] ?></td>
                    <td class="tableData" name=<?php echo $data1[0] ?>><?php echo $data1[5] ?></td>
					<td><input id=<?php echo $data1[0]."d" ?> type="date" value="" required/></td>
					<td><input id=<?php echo $data1[0]."t"?> type="time" value="" required/></td>
					<td align="center"><input class="<?php echo $data1[0]?>" type="button" name=<?php echo $data1[0] ?> id="interviewScheduleConfirmButton" value="Confirm" onclick="send('<?php echo $data1[4] ?>','<?php echo $data1[0]."d" ?>','<?php echo $data1[0]."t"?>','<?php echo $data1[0] ?>','<?php echo $rJob ?>','<?php echo $intType ?>','<?php echo $rSessionID ?>')"></td>
					<?php //echo $_SESSION["mail"]; ?>
                </tr>
	<?php
		/*if($n < $m){
			if($time != ""){
				$t = explode(":",$time);
				$f = explode(":",$from);
				$minutes = ($t[1]+(int)$duration);
				if($minutes >= 60){
					$minutes = $minutes - 60;
					$t[0] = $t[0]+1;
					if($minutes < 10){
						$minutes = "0".$minutes;
					}
					if($t[0]<10){
						$t[0] = "0".$t[0];
					}
				}
				if($f[0] > $t[0]){
					$time = $t[0].":".$minutes.":".$t[2];			
				}else if($f[0] == $t[0]){
					if($minutes < $f[1]){
						$time = $t[0].":".$minutes.":".$t[2];
					}else{
						if($n+1 < $m){
							$n = $n+1;
							$date = $slots[$n][1];
							$time = $slots[$n][2];
							$from = $slots[$n][3];
						}else{
							$date = "";
							$time = "";
						}					
					}
				}else{
					if($n+1 < $m){
						$n = $n+1;
						$date = $slots[$n][1];
						$time = $slots[$n][2];
						$from = $slots[$n][3];
					}else{
						$date = "";
						$time = "";
					}				
				}
			}
		}else{
			$date = "";
			$time = "";
		}*/
	}
   ?>
    
  </tbody>
</table>
<input type="button" id="InteviewPanelNotiBtn" value="Send to Panel"/>
<!---<a href="sendEmailToPanel.php?rsid=<?php echo $rSessionID?>&rjob=<?php echo $rJob?>&IntType=<?php echo $intType?>&IntID=<?php echo $intID?>"><input type="button" id="interviewSchedule" value="Schedule"/> --->
<input type="button" id="interviewScheduleRescheduleButton" value="Reschedule">


  
  
<footer>Copyright 2015 &copy;</footer>

</div>
</body>
</html>
