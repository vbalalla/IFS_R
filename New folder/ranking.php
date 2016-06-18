
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
		
		$criteriaID="";
		if(isset($_GET["criteria"])){
				$criteriaID=$_GET["criteria"];
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

	$intID="";
	if(isset($_GET['int'])){
		$intID=$_GET['int'];
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
		<a href="#" class="aBack">Back</a> 
		<a href="#" class="aHelp">Help</a>
	  </nav>
  </aside>
  <table width="84%" height="107" border="1" align="center" class="sortable" id="tableCVDetails">
    <thead>
    <tr>
      <th width="10%" scope="col">Candidate ID</th>      
      <th width="20%" scope="col">Name</th>    
      <th width="12%" scope="col">Status</th>
    </tr>
  </thead>
  <tbody>
 
    <?php					
        $candidates = mysqli_query($connect,"SELECT DISTINCT CandID,FirstName,LastName,candStatusID FROM candidate,cv,interview WHERE candidate.cvID=cv.cvID AND cv.RSID=interview.RSID AND interview.IntID=$intID AND (candidate.candStatusID='CS001' OR candidate.candStatusID='CS005')");
		
			while($raw = $candidates->fetch_row()){
				
			?>
			<script> var id = <?php echo(json_encode($raw[0])); ?>;</script>

            <tr class="tableRow" id = <?php echo $raw[0] ?> href="marksMatrix.php?int=<?php echo $intID; ?>&cand=<?php echo $raw[0] ?>&criteria=<?php echo $criteriaID;?>">
                <td><?php echo $raw[0] ;?></td>
                <td><?php echo $raw[1]." ".$raw[2]; ?></td>
                <td><?php echo $raw[3]; ?></td>                
                <?php } ?>
    </tr>
    </tbody>
            

</table>

  
<footer>Copyright 2015 &copy;</footer>

</div>
</body>
</html>
