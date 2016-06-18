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
<title>Select Interview</title>
</head>

<body>
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
	<tr>
      <th class="thSessionTablesHeddings" scope="row">Status&nbsp;&nbsp;&nbsp;:</th>
      <td><?php echo $rStatus; ?></td>
    </tr>
  </tbody>
</table>
</div>
<br><br>

  <a href="CreateInterviewInterface.php?rsid=<?php echo $rSessionID ?>&rname=<?php echo $rName ?>&rjob=<?php echo $rJob ?>&rdate=<?php echo $rDate ?>&rstatus=<?php echo $rStatus ?>&IntButton=<?php echo "First Interview" ?>"><button type="button" name="IntButton1" value="First Interview">First Interview</button>
  <br><br>
  <a href="CreateInterviewInterface.php?rsid=<?php echo $rSessionID ?>&rname=<?php echo $rName ?>&rjob=<?php echo $rJob ?>&rdate=<?php echo $rDate ?>&rstatus=<?php echo $rStatus ?>&IntButton=<?php echo "Second Interview" ?>"><button type="button" name="IntButton2" value="First Interview">Second Interview</button>



</body>
</html>