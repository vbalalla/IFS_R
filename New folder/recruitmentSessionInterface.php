<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Recruitment Session</title>

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

<?php
  require_once("GlobalVariables.php");
  global $db, $user, $pass;

  try {
    $dbh= new PDO($db,$user,$pass);

    $sql = $dbh->prepare("SELECT recruitmentsession.RSID, name, dateCreated, jbName, status FROM RecruitmentSession,jobpositon,sessionstatus where jobPositionID=jbID and recruitmentsession.sessionStatusID=sessionstatus.sessionStatusID");
	$loadStatus = $dbh->prepare("SELECT sessionStatusID,status FROM sessionStatus");
	
    if($sql->execute()) {
       $sql->setFetchMode(PDO::FETCH_ASSOC);
    }
	if($loadStatus->execute()) {
       $loadStatus->setFetchMode(PDO::FETCH_ASSOC);
    }
  }
  catch(Exception $error) {
      echo '<p>', $error->getMessage(), '</p>';
  }

?>

<!--javascript for the table-->
<script type="text/javascript">

<!-- clickable raws-->
    $(document).ready(function(){
        $('table td').click(function(){
            window.location = $(this).attr('href');
            return false;
        });
    });
	
</script>


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
		<a href="index.php" class="aBack">Back</a> 
		<a href="createRecruitmentSession.php" class="aCreateNew">Create New</a> 
		<a href="recruitmentSessionHelp.php" class="aHelp">Help</a>
	  </nav>
  </aside>
  <table width="83%" border="1" align="center" cellpadding="5" class="sortable" id="existingSessionsTable">
    <thead>
    <tr>
      <th width="40%" scope="col">Session Name</th>
      <th width="25%" scope="col">Job Position</th>
      <th width="20%" scope="col">Date</th>
	  <th width="30%" scope="col">Status</th>
    </tr>
    </thead>
    <tbody>
    <?php while($row = $sql->fetch()) { ?>
	<script> var id = <?php echo(json_encode($row['RSID'])); ?>;</script>
		<tr>			
			<td id=<?php echo $row['RSID']?> href="candidateList.php?rsid=<?php echo $row['RSID']?>&rname=<?php echo $row['name']?>&rjob=<?php echo $row['jbName']?>&rdate=<?php echo $row['dateCreated']?>&rstatus=<?php echo $row['status']?>"><?php echo $row['name']?></td>
			<td id=<?php echo $row['RSID']?> href="candidateList.php?rsid=<?php echo $row['RSID']?>&rname=<?php echo $row['name']?>&rjob=<?php echo $row['jbName']?>&rdate=<?php echo $row['dateCreated']?>&rstatus=<?php echo $row['status']?>"><?php echo $row['jbName']?></td>
			<td id=<?php echo $row['RSID']?> href="candidateList.php?rsid=<?php echo $row['RSID']?>&rname=<?php echo $row['name']?>&rjob=<?php echo $row['jbName']?>&rdate=<?php echo $row['dateCreated']?>&rstatus=<?php echo $row['status']?>"><?php echo $row['dateCreated']?></td>
			<td id=<?php echo $row['RSID']?> href="candidateList.php?rsid=<?php echo $row['RSID']?>&rname=<?php echo $row['name']?>&rjob=<?php echo $row['jbName']?>&rdate=<?php echo $row['dateCreated']?>&rstatus=<?php echo $row['status']?>"><?php echo $row['status']?></td>
		</tr>
		<?php }?>
    </tbody>
  </table>

<footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>
