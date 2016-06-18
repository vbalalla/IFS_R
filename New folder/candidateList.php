<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Session Details</title>

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
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	padding-left: 3%;
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
	
jQuery(function(){
    $( "#status" ).change(function() {
		$.post("changeSessionStatus.php",
		    {
				sessionStatusID:$('#status').val(),
				RSID:"<?php echo $rSessionID; ?>"				
			},
			function(data)
			{
				//alert(interviewname);
			}
		);
	});
});

</script>

<?php require_once("Sql.php");
	require_once("GlobalVariables.php");
	$s = new Sql();
	$connect = $s->connectToDatabase($databaseName);

	global $db, $user, $pass;

	try {
		$dbh= new PDO($db,$user,$pass);
		
		$loadStatus = $dbh->prepare("SELECT sessionStatusID,status FROM sessionStatus");
		
		if($loadStatus->execute()) {
			$loadStatus->setFetchMode(PDO::FETCH_ASSOC);
		}
	}
	catch(Exception $error) {
		echo '<p>', $error->getMessage(), '</p>';
	}

?>

</head>

<body>
<div>
  <header>
    <aside class="asideRight">
      <form action="SearchInterface.php" method="get">
        <input name="Search" type="search" class="searchbox" ><img src="images/searchIcon.png" width="15" height="15" alt=""/>
        <a href="index.php" class="navHome"> Home</a>
        <a href="help.php" class="navHelp">Help </a>
      </form>
    </aside>
    <aside class="asideLeft"></aside> 
  </header>
  
  <aside class="asideLeftIcons">
	  <nav>
		<a href="recruitmentSessionInterface.php" class="aBack">Back</a> 
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
      <th width="25%" class="thSessionTablesHeddings" scope="row">Session Name :</th>
      <td width="50%"><?php echo $rName; ?></td>
    </tr>
    <tr>
      <th class="thSessionTablesHeddings" scope="row">Job Position&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</th>
      <td><?php echo $rJob; ?></td>
    </tr>
    <tr>
      <th class="thSessionTablesHeddings" scope="row">Date Created&nbsp;&nbsp;:</th>
      <td><?php echo $rDate; ?></td>
    </tr>
  </tbody>
</table>

<table width="35%" border="1" cellpadding="5" class="tableInputRejectedCVNumber">
  <tbody>
  <?php
  $countCVs = mysqli_query($connect, "SELECT COUNT(RSID) from CV where cv.RSID='$rSessionID'");
  $totalCVs = $countCVs->fetch_row();
  
  $countRejectedCVs = mysqli_query($connect, "SELECT COUNT(cv.RSID) from CV,candidate where cv.RSID='$rSessionID' AND cv.cvID=candidate.cvID AND (candStatusID='CS002' OR candStatusID='CS006' OR candStatusID='CS009')");
  $totalRejectedCVs = $countRejectedCVs->fetch_row();
    
  ?>
    <tr>
      <th width="15%" class="thSessionTablesHeddings" scope="row">Inputted CVs&nbsp;&nbsp;:</th>
      <td width="30%"><?php echo $totalCVs[0] ?></td>
    </tr>
    <tr>
      <th class="thSessionTablesHeddings" scope="row">Rejected CVs&nbsp;&nbsp;:</th>
      <td><?php echo $totalRejectedCVs[0] ?></td>
    </tr>
	<tr>
      <th class="thSessionTablesHeddings" scope="row">Status&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</th>
      <td>
	  
		<select href="#" id="status" name="changeStatus" onchange="changeSessionStatus(this.value,<?php echo $rSessionID?>)" class="formSelect" method="post" required>
			<option id="stausOption" value="ss001" selected><?php echo $rStatus?></option>
			<?php $rows = $loadStatus->fetchAll();
				foreach($rows as $statusRow){
					if($statusRow['status']!=$rStatus){ ?>
						<option id="stausOption" value="<?php echo $statusRow['sessionStatusID']?>"><?php echo $statusRow['status']?></option>
			<?php   }
			}?>
		</select><br/>
	  </td>
    </tr>
  </tbody>
</table>
</div>

<div class="divSessionDetails">
  <a href="uploadCVs.php?id=<?php echo $rSessionID?>&rname=<?php echo $rName?>&rjob=<?php echo $rJob?>&rdate=<?php echo $rDate?>&rstatus=<?php echo $rStatus?>"><input type="button" id="addCVButton" value="Upload CVs"></a>
  <a href="a_interviews.php?rsid=<?php echo $rSessionID?>&rname=<?php echo $rName?>&rjob=<?php echo $rJob?>&rdate=<?php echo $rDate?>&rstatus=<?php echo $rStatus?>"><input type="button" id="interviewButton" value="Interview"></a>
</div>
  
  <table width="84%" height="107" border="1" align="center" class="sortable" id="tableCVDetails">
  <thead>
    <tr>
      <th width="7%" scope="col">ID</th>
      <th width="11%" scope="col">NIC</th>
      <th width="20%" scope="col">Name</th>
      <th width="14%" scope="col">Date of Birth</th>
      <th width="20%" scope="col">e-mail</th>
      <th width="16%" scope="col">Contact No</th>
	  <th width="16%" scope="col">University</th>
      <th width="12%" scope="col">Status</th>
    </tr>
  </thead>
  <tbody>
    <?php					
        $data = mysqli_query($connect, "SELECT candidate.CandID,NIC,FirstName,LastName,DateOfBirth,email,ContactNo,University,candidatestatus.name FROM candidate,recruitmentsession,cv,candidatestatus WHERE cv.RSID=recruitmentsession.RSID AND cv.cvID=candidate.cvID AND candidatestatus.candstatusID=candidate.candstatusID AND recruitmentsession.RSID='$rSessionID'");
		$color="";		
        while($raw = $data->fetch_row()) {     
			if($raw[8] == "Rejected - CV"){
				$color = "#FF0000";
			}else if($raw[8] == "Flagged - CV"){
				$color = "#FFCC33";
			}else if($raw[8] == "Rejected - interview"){
				$color = "#FF0000";
			}else if($raw[8] == "Unchecked - CV"){
				$color = "#FFFF99";
			}else{
				$color = "";
			}
			?>
			<script> var id = <?php echo(json_encode($raw[0])); ?>;</script>
            <tr bgcolor="<?php echo $color?>" class="tableRow" id = <?php echo $raw[0] ?> href="enterCandiateDetails.php?id=<?php echo $raw[0]?>&rsid=<?php echo $rSessionID?>&rname=<?php echo $rName?>&rjob=<?php echo $rJob?>&rdate=<?php echo $rDate?>&rstatus=<?php echo $rStatus?>">
                <td><?php echo $raw[0] ?></td>
                <td><?php echo $raw[1] ?></td>
                <td><?php echo $raw[2]." ".$raw[3]?></td>
                <td><?php echo $raw[4] ?></td>
                <td><?php echo $raw[5] ?></td>
                <td><?php echo $raw[6] ?></td>
                <td><?php echo $raw[7] ?></td>
                <td><?php echo $raw[8] ?></td>
            </tr>
    <?php } ?>
  </tbody>
</table>

  
<footer>Copyright 2015 &copy;</footer>

</div>
</body>
</html>
