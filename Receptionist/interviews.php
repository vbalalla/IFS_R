<?php

		  include_once('../phpSessions.php');
  if($_SESSION['type']=='admin_officer')
    header("location: ../Administrative officer/index.php");
  
  if($_SESSION['type']=='interview_panel')
    header("location: ../Interview panel member/index.php");
  
	if($_SESSION['type']=='system_admin')
    header("location: ../System administrator/systemAdministratorHome.php");
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Interview</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico"/>

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
#existingSessionsTable {
	border-collapse: collapse;
	background-color: #FFFFFF;
	border-color: #EBBEF5;
	margin-bottom: 3%;
	color: #281A2B;
}
</style>

<script type="text/javascript">

<!-- clickable raws-->
    $(document).ready(function(){
        $('.tableRow').click(function(){
            window.location = $(this).attr('href');
            return false;
        });

			
    });

</script>

</head>

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

?>


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
        <a href="recruitmentSessionInterface.php" class="navHome"> Home</a>
        <a href="../UserManual/UserManual.html" class="navHelp">Help </a>
      </form>

    </aside>
    <aside class="asideLeft"></aside>
  </header>
  
  <aside class="asideLeftIcons">
	  <nav>
		<a href="recruitmentSessionInterface.php" class="aBack">Back</a> 
		<a href="createNewInterview.php?rsid=<?php echo $rSessionID?>&rname=<?php echo $rName?>&rjob=<?php echo $rJob?>&rdate=<?php echo $rDate?>&rstatus=<?php echo $rStatus?>" class="aCreateNew">Create New</a> 
		<a href="recruitmentSessionHelp.php" class="aHelp">Help</a>
	  </nav>
  </aside>
  <table width="83%" border="1" align="center" cellpadding="5" class="sortable" id="existingSessionsTable">
  <tbody>
    <tr>
      <th width="40%" scope="col">Interview Name</th>
      </tr>
	  <?php					
		$data = mysqli_query($connect, "SELECT IntID, Name, duration FROM interview WHERE RSID='$rSessionID'");
		while($raw = $data->fetch_row()) { ?>
		<!--<script> var id = <?php echo(json_encode($raw[0])); ?>;</script>--->
            <tr class="tableRow" href="interviewSchedule.php?rsid=<?php echo $rSessionID?>&rname=<?php echo $rName?>&rjob=<?php echo $rJob?>&rdate=<?php echo $rDate?>&rstatus=<?php echo $rStatus?>&int=<?php echo $raw[0] ?>&dur=<?php echo $raw[2] ?>">
                <td class="tableData" name="intName" id = <?php echo $raw[0] ?> ><?php echo $raw[1] ?></td>
            </tr>
    <?php } ?>
    </tbody>
</table>

<footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>
