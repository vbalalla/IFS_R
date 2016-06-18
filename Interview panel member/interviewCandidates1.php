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


<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Interview Candidate List</title>

<script type="text/javascript" src="js/sortable.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css">
<script src="jquery/jquery.js"></script>
<script src="jquery-ui-1.11.4/jquery-ui.js"></script>
<script src="jquery-ui-1.11.4/jquery-ui.css"></script>
<script src="jquery-ui-1.11.4/jquery.min.js"></script>
<script src="jquery-ui-1.11.4/jquery-ui.min.js"></script>

<style type="text/css">
	.asideLeftIcons {
		margin-left: 9%;
		width: 70%;
		margin-bottom: 5%;
		text-align: left;
	}
.tabelSessionDetails {
	border-collapse: collapse;
	background-color: #FFFFFF;
	color: #281A2B;
	text-align: left;
	border-color: #FFFFFF;
	float: left;
}
.tableInputRejectedCVNumber {
	color: #281A2B;
	text-align: left;
	border-collapse: collapse;
	background-color: #FFFFFF;
	border-color: #FFFFFF;
	float: right;
}
.divSessionDetails {
	background-color: #DBCCCE;
	display: inline-block;
	width: 82%;
	margin-left: 8%;
	margin-right: 8%;
	padding-top: 1%;
	padding-right: 1%;
	padding-bottom: 1%;
	padding-left: 1%;
}
.thSessionTablesHeddings {
	padding-left: 5%;
}
#addCVButton {
	background-image: url(images/recruitmentSession/createNew.png);
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	width: 20%;
	padding-left: 2%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
}
#interviewButton {
	background-image: url(images/recruitmentSession/interview.png);
	width:25%;
	bgcolor: #E9E1E1;
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	padding-left: 5%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
}
#addCVButton:hover {
	background-image: url(images/recruitmentSession/createNew2.png);
}
#interviewButton:hover {
	background-image: url(images/recruitmentSession/interview1.png);
}
#tableCVDetails {
	color: #281A2B;
	background-color: #FFFFFF;
	border-collapse: collapse;
	border-color: #EBBEF5;
	margin-bottom: 3%;
	margin-top: 2%;
}
</style>

<?php
	
		$rSessionID="";
		if(isset($_GET["rsid"])){
				$rSessionID=$_GET["rsid"];
		}	
		
		
		//echo $rSessionID;
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
	
    
		
		$intID="";
		if(isset($_GET["int"])){
				$intID=$_GET["int"];
		}
?>

<!--javascript for the table-->
<script type="text/javascript">

<!-- clickable raws-->
    $(document).ready(function(){
        $('.tableRow').click(function(){
            window.location = $(this).attr('href');
            return false;
        });

		/*function changeSessionStatus(id,rsid){
			$.ajax({
				type: "GET",
				url: "changeSessionStatus.php",
				data: "sessionStatusID="+id+"&RSID="+rsid,
				success:
			});
		};*/	
    });	

</script>

<?php require_once("Sql.php");
	require_once("GlobalVariables.php");
	$s = new Sql();
	$connect = $s->connectToDatabase($databaseName);

	global $db, $user, $pass;

	$interview = mysqli_query($connect, "SELECT interview.Name FROM interview WHERE IntID=$intID");
	$intName = $interview->fetch_row();
	
	$jobq = mysqli_query($connect, "SELECT jbName FROM jobpositon,interview,recruitmentsession WHERE recruitmentsession.RSID=interview.RSID AND jobpositon.jbID=recruitmentsession.jobPositionID AND interview.IntID=$intID");
	$job = $jobq->fetch_row();
?>

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
		<a href="a_interviews.php?rsid=<?php echo $rSessionID; ?>&rname=<?php echo $rName; ?>&rjob=<?php echo $rJob; ?>&rdate=<?php echo $rDate; ?>&rstatus=<?php echo $rStatus; ?>" class="aBack">Back</a> 
		<a href="recruitmentSessionHelp.php" class="aHelp">Help</a>
	  </nav>
  </aside>
 
  <div class="divSessionDetails">
  <table width="50%" border="1" cellpadding="5" class="tabelSessionDetails">
  <tbody>
    <?php
		$rSessionID="";
		if(isset($_GET["rsid"])){
				$rSessionID=$_GET["rsid"];
		}	
		
		
		//echo $rSessionID;
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
	?>
    <tr>	
      <th width="25%" class="thSessionTablesHeddings" scope="row">Interview Name :</th>
      <td width="50%"><?php echo $intName[0]; ?></td>
    </tr>
    <tr>
      <th class="thSessionTablesHeddings" scope="row">Job Position&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</th>
      <td><?php echo $job[0]; ?></td>
    </tr>
  </tbody>
</table>

<table width="40%" border="1" cellpadding="5" class="tableInputRejectedCVNumber">
  <tbody>
  <?php
  
	$countCVs = mysqli_query($connect,"SELECT COUNT(DISTINCT CandID) FROM candidate,cv,interview WHERE candidate.cvID=cv.cvID AND cv.RSID=interview.RSID AND interview.IntID=$intID AND (candidate.candStatusID='CS001' OR candidate.candStatusID='CS005')");
	$totalCVs = $countCVs->fetch_row();
  
	$criteria = mysqli_query($connect, "SELECT evalName FROM evaluation,interview,criteria WHERE criteria.criteriaID=interview.EvaCriID AND criteria.criteriaID=evaluation.criteriaID AND interview.IntID=$intID");
  	
 // $countRejectedCVs = mysqli_query($connect, "SELECT COUNT(cv.RSID) from CV,candidate where cv.RSID='$rSessionID' AND cv.cvID=candidate.cvID AND (candStatusID='CS002' OR candStatusID='CS006' OR candStatusID='CS009')");
  //$totalRejectedCVs = $countRejectedCVs->fetch_row();
    $has = $criteria->fetch_row();
	mysqli_data_seek($criteria, 0);
	
	$criteriaNameq = ""; $criteriaName = ""; $table = "";
	if($has != null){
		$criteriaNameq = mysqli_query($connect, "SELECT criteriaName,criteriaID FROM criteria,interview WHERE criteriaID=interview.EvaCriID AND interview.IntID=$intID"); 
		$criteriaName = $criteriaNameq->fetch_row();
		$criteriaID=$criteriaName[1];
		$table = "criteria".$criteriaID."_".$intID; 	
	}
	
	
  ?>
    <tr>
      <th width="20%" class="thSessionTablesHeddings" scope="row">Candidates in interview:</th>
      <td width="20%"><?php echo $totalCVs[0] ?></td>
    </tr>
	<tr>
      <th width="20%" class="thSessionTablesHeddings" scope="row">&nbsp;</th>
      <td width="20%"></td>
    </tr>
    </tbody>
</table>
</div>

<?php if($has != null){ ?>

  
  
  <table width="84%" height="107" border="1" align="center" class="sortable" id="tableCVDetails">
  <thead>
    <tr>
      <th width="7%" scope="col">ID</th>
      <th width="20%" scope="col">Name</th>
	  <?php while($row = $criteria->fetch_row()){?>
      <th width="14%" scope="col"><?php echo $row[0]; ?></th>
      <?php } ?>
	  <th width="25%" scope="col">Comments</th>
    </tr>
  </thead>
  <tbody>
    <?php					
        $candidates = mysqli_query($connect,"SELECT DISTINCT CandID,FirstName,LastName FROM candidate,cv,interview WHERE candidate.cvID=cv.cvID AND cv.RSID=interview.RSID AND interview.IntID=$intID AND (candidate.candStatusID='CS001' OR candidate.candStatusID='CS005')");
			while($raw = $candidates->fetch_row()){
				
			?>
			<script> var id = <?php echo(json_encode($raw[0])); ?>;</script>
            <tr class="tableRow" id = <?php echo $raw[0] ?> href="enterMarks.php?int=<?php echo $intID; ?>&cand=<?php echo $raw[0] ?>">
                <td><?php echo $raw[0] ?></td>
                <td><?php echo $raw[1]." ".$raw[2]?></td>
				<?php mysqli_data_seek($criteria, 0);
				while($row = $criteria->fetch_row()){
					$row[0]=str_replace(" ","_",$row[0]);
					//echo $row[0];
					
					$marksq = mysqli_query($connect, "SELECT $row[0] FROM $table WHERE CandID='$raw[0]'");
					//echo "SELECT $row[0] FROM $table WHERE CandID='$raw[0]'";
					$marks = $marksq->fetch_row();	
				?>
                <td><?php echo $marks[0]; ?></td>
                <?php } ?>
				
                
				<?php
				$queryToGetComment="SELECT comment FROM $table WHERE CandID='$raw[0]' AND panelMember='E002'";
				$comment= mysqli_query($connect, $queryToGetComment);
				while ($commentResult=$comment->fetch_row()){
				?>
				
			
                
                <td><?php echo $commentResult[0]; ?></td>
                
                <?php }?>
                
            </tr>
    <?php } ?>
  </tbody>
</table>

<div class="divSessionDetails">  
  <a href="ranking.php?int=<?php echo $intID; ?>&criteria=<?php echo $criteriaID; ?>"><input type="button" id="interviewButton" value="Mark Summary"></a>
</div>
<br/><br/>

  <?php } else {
	  echo "<br><center><p style=\"color:orange;\"><b>Please add an evaluation criteria to proceed</b></p></center><br>";
  }?>
  
<footer>Copyright 2015 &copy;</footer>

</div>
</body>
</html>
