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
<title>criteria areas</title>
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

#interviewButton {
	background-image: url(images/recruitmentSession/interview.png);
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	padding-left: 3%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
}
#interviewButton:hover {
	background-image: url(images/recruitmentSession/interview1.png);
}
input[type=submit] {
	padding-left: 8%;
	padding-right: 5px;
	color: #281A2B;
	background-image: url(images/recruitmentSession/submit.png);
	background-repeat: no-repeat;
	padding-top: 11px;
	padding-bottom: 24px;
	width: 18%;
	margin-right: 4%;
	font-family: "OpenSans Regular";
	border-style: none;
	border-color: #E9E1E1;
	background-color: #dbccce;
	font-weight: 600;
	font-size: 1.2em;
	margin-left: 2%;
	margin-top: 3%;
	-webkit-transition: all 0.3s ease 0s;
	-o-transition: all 0.3s ease 0s;
	transition: all 0.3s ease 0s;
	}
	input[type=reset] {
	padding-right: 5px;
	color: #281A2B;
	background-image: url(images/recruitmentSession/cancel.png);
	background-repeat: no-repeat;
	padding-top: 11px;
	padding-bottom: 24px;
	width: 18%;
	margin-right: 15%;
	font-family: "OpenSans Regular";
	border-style: none;
	border-color: #E9E1E1;
	background-color: #dbccce;
	font-weight: 600;
	font-size: 1.2em;
	margin-left: 3%;
	margin-top: 3%;
	padding-left: 8%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
	}
input[type=submit]:hover {
		background-image: url(images/recruitmentSession/submit2.png);
	}
input[type=reset]:hover {
		background-image: url(images/recruitmentSession/cancel1.png);
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
		$criteriaID="";
		if(isset($_GET["criteriaID"])){
			
				$criteriaID=$_GET["criteriaID"];
		}
		
?>

</head>
<body>
<div>
  <header>
    <aside class="asideRight">
		<input type="search" class="searchbox"><img src="images/searchIcon.png" width="15" height="15" alt=""/>
      
		<a href="index.php" class="navHome"> Home</a>
		<a href="help.html" class="navHelp">Help </a></aside>
    
    <aside class="asideLeft"></aside> 
  </header>
  
  <aside class="asideLeftIcons">
	  <nav>
		<a href="evaluationCriteriaAdd.php" class="aBack">Back</a> 
		<a href="evaluationCriteriaHelp.html" class="aHelp">Help</a>
       
	  </nav>
  </aside>
  </a>
  
  
  
  <table width="83%" border="1" class="sortable" align="center" id="tableCommon">
  <tbody>
    <tr>
	  
      <th width="20%" scope="col">evaluation name</th>
      <th width="10%" scope="col">weight %</th>
     
    </tr>
   
   <?php 
	$query1 = mysqli_query($connect, "SELECT IntID FROM interview WHERE RSID = '".$rSessionID."' ORDER BY IntID DESC LIMIT 1" );
	$intID ="";
	while($raw = $query1->fetch_row()){
		$intID = $raw[0];
	}
	//echo $intID;
	
	
	
	
		
	$query3 = mysqli_query($connect,"SELECT * from evaluation where criteriaID='$criteriaID'");
	while($data1 = $query3 -> fetch_row()){ 
//	echo $data1[0];
		?>
	
                <tr  id = <?php echo $data1[0]?>></tr>
					
                    <td class="tableData" name=<?php echo $data1[0] ?>><?php echo $data1[1] ?></td>
                    <td class="tableData" name=<?php echo $data1[0] ?>><?php echo $data1[2] ?></td>
                   
                </tr>
	<?php
	}
	?>
    
  </tbody>
</table>




<a href="EvaluationCriteriaDatabase.php?rsid=<?php echo $rSessionID?>&criteriaID=<?php echo $criteriaID?>&intID=<?php echo $intvID?>&rname=<?php echo $rName?>&rjob=<?php echo $rJob?>&rdate=<?php echo $rDate?>&rstatus=<?php echo $rStatus?>">
<input id="btnID" type="submit" value="Add to Interview"> </a>

<input type="reset" value="Cancel"/>


  
  
<footer>Copyright 2015 &copy;</footer>

</div>
</body>
</html>
