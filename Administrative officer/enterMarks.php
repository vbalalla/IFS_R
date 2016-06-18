<!doctype html>
<html>
<head>
<meta charset="utf-8">

<title>Interview Marks</title>
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

    include('phpSessions.php');
    
	require_once("Sql.php");
	require_once("GlobalVariables.php");
		
	$s = new Sql();
	$connect = $s->connectToDatabase($databaseName);
	$id="";
	if(isset($_GET['id'])){
		$id = $_GET['id'];
	}
	
echo $_SESSION['usrid'];
	
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
	$intID="";
	if(isset($_GET["int"])){
			$intID=$_GET["int"];
	}
	$cid="";
	if(isset($_GET["cand"])){
			$cid=$_GET["cand"];
	}

    $data = mysqli_query($connect, "SELECT * FROM candidate WHERE CandID = '$cid'");
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
	
    $criteria = mysqli_query($connect, "SELECT evalName FROM evaluation,interview,criteria WHERE criteria.criteriaID=interview.EvaCriID AND criteria.criteriaID=evaluation.criteriaID AND interview.IntID=$intID");
	
	$criteriaNameq = mysqli_query($connect, "SELECT criteriaID,criteriaName FROM criteria,interview WHERE criteriaID=interview.EvaCriID AND interview.IntID=$intID"); 
	$criteriaName = $criteriaNameq->fetch_row();
	$criteriaID=$criteriaName[0];
	
$table = "criteria".$criteriaID."_".$intID;   
	
?>

</head>

<body>
<div>
  <header>
    <aside class="asideRight">
        <span>
            <b id="welcome">Welcome : <i><?php echo $login_session; ?></i></b>
            <b id="logout"><a href="logout.php">Log Out</a></b>
        </span>

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
		<a href="interviewCandidates.php?int=<?php echo $intID;?>" class="aBack">Back</a> 
		<a href="recruitmentSessionHelp.php" class="aHelp">Help</a>
	  </nav>
  </aside>
  <div>
  <table width="100%" cellpadding="10">
  <tbody>
  <tr>
  <td>
  <form role="form" action="sendMarks.php?int=<?php echo $intID;?>&cand=<?php echo $cid;?>" method="post">
  <table width="100%" border="1" cellpadding="15" class="tableEnterCandidateDetails">
  <tbody>
  <?php while($row = $criteria->fetch_row()){
	$marksq = mysqli_query($connect, "SELECT $row[0] FROM $table WHERE CandID='$cid'");
	$marks = $marksq->fetch_row();
	?>
    <tr>

      <td width="100%">
          <?php echo $row[0];?>
          <br/>
      <input type="text" name="<?php echo $row[0];?>" value="<?php echo $marks[0];?>" class="textInputEnterCandidateDetails" placeholder="<?php echo $row[0];?>"></td>      
    </tr>
  <?php }?>
  <tr>
  <?php
$qureryToGetComment= mysqli_query($connect, "SELECT comment FROM $table WHERE CandID='$cid'");
//echo $table;
$comment="";
while($row1=$qureryToGetComment->fetch_row()){
	$comment=$row1[0];
}
//echo $comment;
?>
      <td width="100%">
      comments
      <br/>
      <textarea rows="8" cols="10" name="comments" value="<?php echo $comment;?>" class="textInputEnterCandidateDetails" placeholder="<?php echo $comment;?>"></textarea></td>      
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
