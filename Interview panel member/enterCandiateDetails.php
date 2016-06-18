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
<title>Session Details</title>
<script src="sweetalert/sweetalert-master/dist/sweetalert.min.js"></script>
<script src="sweetalert/sweetalert-master/dist/sweetalert-dev.js"></script>
<link rel="stylesheet" href="sweetalert/sweetalert-master/dist/sweetalert.css" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
	.asideLeftIcons {
		margin-left: 9%;
		width: 70%;
		margin-bottom: 5%;
		text-align: left;
	}
	.tableEnterCandidateDetails {
		margin-top: 3%;
		margin-bottom: 3%;
		padding-left: 5px;
		padding-right: 5px;
		color: #281A2B;
		text-align: left;
		border-style: hidden;
		border-color: #FFFFFF;
		border-collapse: collapse;
		background-color: #FFFFFF;
	}
	.textInputEnterCandidateDetails {
		color: #BEA7AA;
		font-family: "OpenSans Regular";
		font-weight: 400;
		font-size: 1.2em;
		text-align: left;
		padding-left: 1%;
		width: 90%;
	}
	.emailInputEnterCandidateDetails {
		width: 90%;
		padding-left: 1%;
		color: #BEA7AA;
		text-align: left;
		font-family: "OpenSans Regular";
		font-weight: 400;
		font-size: 1.2em;
	}
	.numberInputEnterCandidateDetails {
		width: 90%;
		padding-left: 1%;
		color: #BEA7AA;
		font-family: "OpenSans Regular";
		font-weight: 400;
		font-size: 1.2em;
		text-align: left;
	}
	.dateInputEnterCandidateDetails {
		width: 90%;
		padding-left: 1%;
		color: #BEA7AA;
		font-family: "OpenSans Regular";
		font-weight: 400;
		font-size: 1em;
	}
	input[type=submit] {
		padding-left: 12%;
		padding-right: 5px;
		color: #281A2B;
		background-image: url(images/recruitmentSession/submit.png);
		background-repeat: no-repeat;
		padding-top: 11px;
		padding-bottom: 24px;
		width: 30%;
		margin-right: 2%;
		font-family: "OpenSans Regular";
		border-style: none;
		border-color: #E9E1E1;
		background-color: #FFFFFF;
		font-weight: 600;
		font-size: 1.2em;
		margin-left: 1%;
		margin-top: 2%;
		-webkit-transition: all 0.3s ease;
		-o-transition: all 0.3s ease;
		transition: all 0.3s ease;
	}
	
	input[type=reset] {
		padding-left: 12%;
		padding-right: 5px;
		color: #281A2B;
		background-image: url(images/recruitmentSession/cancel.png);
		background-repeat: no-repeat;
		padding-top: 11px;
		padding-bottom: 24px;
		width: 35%;
		margin-right: 2%;
		font-family: "OpenSans Regular";
		border-style: none;
		border-color: #E9E1E1;
		background-color: #FFFFFF;
		font-weight: 600;
		font-size: 1.2em;
		margin-left: 1%;
		margin-top: 2%;
		-webkit-transition: all 0.3s ease;
		-o-transition: all 0.3s ease;
		transition: all 0.3s ease;
	}

	#buttonPreviousEnterCandidateDetails {
		background-image: url(images/recruitmentSession/previous.png);
		padding-top: 11px;
		padding-bottom: 24px;
		background-repeat: no-repeat;
		padding-left: 15%;
		margin-left: 1%;
		margin-top: 4%;
		margin-right: 2%;
		padding-right: 5px;
		background-color: #FFFFFF;
		width: 40%;
		-webkit-transition: all 0.3s ease;
		-o-transition: all 0.3s ease;
		transition: all 0.3s ease;
	}
	#buttonNextEnterCandidateDetails {
		background-image: url(images/recruitmentSession/next.png);
		padding-top: 11px;
		padding-bottom: 24px;
		background-repeat: no-repeat;
		padding-left: 12%;
		margin-left: 4%;
		margin-top: 4%;
		margin-right: 2%;
		padding-right: 5px;
		background-color: #FFFFFF;
		width: 30%;
		-webkit-transition: all 0.3s ease;
		-o-transition: all 0.3s ease;
		transition: all 0.3s ease;
	}
	#buttonNextEnterCandidateDetails:hover {
		background-image: url(images/recruitmentSession/next1.png);
	}
	#buttonPreviousEnterCandidateDetails:hover {
		background-image: url(images/recruitmentSession/previous1.png);
	}
	input[type=submit]:hover {
		background-image: url(images/recruitmentSession/submit2.png);
	}
	input[type=reset]:hover {
		background-image: url(images/recruitmentSession/cancel1.png);
	}
</style>
<?php 
$valid="";
if(isset($_GET["valid"])){
	$valid=$_GET["valid"];
}

$error="";
if(isset($_GET["msg"])){
	$error=$_GET["msg"];
}
?>
<script>
var v=<?php echo $valid?>;
var m="<?php echo $error?>";
if(v=="1"){
	alert(m);
	//swal('Congratulations!', 'Your message has been succesfully sent', 'success');
}
</script>

<?php 
	require_once("Sql.php");
	require_once("GlobalVariables.php");
		
	$s = new Sql();
	$connect = $s->connectToDatabase($databaseName);
	$id="";
	if(isset($_GET['id'])){
		$id = $_GET['id'];
	}
	$sessionID="";
    if(isset($_GET['rsid'])){
		$sessionID = $_GET['rsid'];
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

    $data = mysqli_query($connect, "SELECT * FROM candidate WHERE CandID = '$id'");
    $raw = $data->fetch_row();
	
	$stData = mysqli_query($connect, "SELECT candidatestatus.name FROM candidatestatus WHERE candstatusID = '$raw[8]'");
    $stRaw = $stData->fetch_row();
	
    $data1 = mysqli_query($connect, "SELECT submittedCV FROM cv WHERE cvID = '$raw[9]'");
    $raw1 = $data1->fetch_row();
	
	$loadStatus = mysqli_query($connect, "SELECT candstatusID,candidatestatus.name FROM candidatestatus");
	
	$next = mysqli_query($connect, "SELECT CandID FROM candidate,recruitmentsession,cv WHERE recruitmentsession.RSID=cv.RSID AND candidate.CVID=cv.CVID AND recruitmentsession.RSID='$sessionID' AND CandID = (SELECT MIN(CandID) FROM candidate where CandID >'$id')");
    $nextRecord = $next->fetch_row();
	
	$previous = mysqli_query($connect, "SELECT CandID FROM candidate WHERE CandID = (SELECT MAX(CandID) FROM candidate where CandID <'$id')");
    $previousRecord = $previous->fetch_row();
	
    
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
		<a href="candidateList.php?rsid=<?php echo $sessionID?>&rname=<?php echo $rName?>&rjob=<?php echo $rJob?>&rdate=<?php echo $rDate?>&rstatus=<?php echo $rStatus?>"" class="aBack">Back</a> 
		<a href="recruitmentSessionHelp.php" class="aHelp">Help</a>
	  </nav>
  </aside>
  
  <div>
  <table width="100%" cellpadding="10">
  <tbody>
  <tr>
  <td>
  <form role="form" action="form.php?id=<?php echo $raw[0]?>&rsid=<?php echo $sessionID?>&rname=<?php echo $rName?>&rjob=<?php echo $rJob?>&rdate=<?php echo $rDate?>&rstatus=<?php echo $rStatus?>" method="post">
  <table width="100%" border="1" cellpadding="15" class="tableEnterCandidateDetails">
  <tbody>
    <tr>
      <td width="100%"><input type="text" style="color:#A901DB" name="d1" value="<?php echo $raw[1];?>" class="textInputEnterCandidateDetails" placeholder="NIC No"></td>
      <td width="0%" rowspan="9">&nbsp;</td>
    </tr>
    <tr>
      <td><input type="text" style="color:#A901DB" name="d2" value="<?php echo $raw[2];?>" class="textInputEnterCandidateDetails" placeholder="First Name"></td>
      </tr>
    <tr>
      <td><input type="text" style="color:#A901DB" name="d3" value="<?php echo $raw[3];?>" class="textInputEnterCandidateDetails" placeholder="Last Name"></td>
      </tr>
    <tr>
      <td style="color: #BEA7AA">Date of Birth <input type="date" style="color:#A901DB" name="d4" value="<?php echo $raw[4];?>" class="dateInputEnterCandidateDetails" placeholder="Date of Birth"></td>
      </tr>
    <tr>
      <td><input type="email" style="color:#A901DB" name="d5" value="<?php echo $raw[5];?>" class="emailInputEnterCandidateDetails" placeholder="e mail" ></td>
      </tr>
    <tr>
      <td><input type="number" style="color:#A901DB" name="d6" value="<?php echo $raw[6];?>" class="numberInputEnterCandidateDetails" placeholder="Contact No"></td>
      </tr>
    <tr>
      <td><input type="text" style="color:#A901DB" name="d7" value="<?php echo $raw[7];?>" class="textInputEnterCandidateDetails" placeholder="University"></td>
      </tr>
    <tr>
      <td>
	  <select id="status" name="d8" class="formSelect">
			<option value="<?php echo $raw[8]?>" selected><?php echo $stRaw[0]?></option>
			<?php while($getRow = $loadStatus->fetch_row()) { 
					if($getRow[1]!=$stRaw[0]){ ?>
						<option name="d8" value="<?php echo $getRow[0]?>"><?php echo $getRow[1]?></option>
			<?php   }
				  }?>
		</select>
	  </td>
    </tr>
    <tr>
      <td height="150" width="500">
	  <p>
      <input type="submit" value="Save">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  <input type="reset" value="Cancel">
      <br/>
	  <?php 
		if($previousRecord[0]!=null){
			$previousLink="enterCandiateDetails.php?id=$previousRecord[0]&rsid=$sessionID&rname=$rName&rjob=$rJob&rdate=$rDate&rstatus=$rStatus";
		}else{			
			$previousLink="#";
		}
		if($nextRecord[0]!=null){
			$nextLink="enterCandiateDetails.php?id=$nextRecord[0]&rsid=$sessionID&rname=$rName&rjob=$rJob&rdate=$rDate&rstatus=$rStatus";
		}else{
			$nextLink="#";
		}?>
      <a href="<?php echo $previousLink?>"><input type="button" id="buttonPreviousEnterCandidateDetails" value="Previous" ></a>
      <a href="<?php echo $nextLink?>"><input type="button" id="buttonNextEnterCandidateDetails" value="Next" ></a>
	  </p>
      </td>
    </tr>
    </tbody>
	</table>
	</form>
	</td>
	
	<td>	
    <object data="<?php echo $raw1[0];?>" type="application/pdf" width="600" height="700"
            style="padding-top: 10px; padding-bottom: 10px; background-color: darkorchid">
    </object>	
	</td>
	
	</tr>
	</tbody>
	</table>
	
  </div>
  
<footer>Copyright 2015 &copy;</footer>

</div>
</body>
</html>
