<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Interview Panels</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico"/>

<script src="sweetalert/dist/sweetalert.min.js"></script> 
<link rel="stylesheet" type="text/css" href="sweetalert/dist/sweetalert.css">

<script type="text/javascript" src="js/sortable.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css">
<script src="jquery/jquery.js"></script>
<script src="jquery-ui-1.11.4/jquery-ui.js"></script>
<script src="jquery-ui-1.11.4/jquery-ui.css"></script>
<!--<script src="jquery-ui-1.11.4/jquery.min.js"></script>-->
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
.adelete {
	padding-left: 70px;
	padding-right: 5px;
	color: #281A2B;
	background-image: url(images/recruitmentSession/Delete.png);
	background-repeat: no-repeat;
	padding-top: 11px;
	padding-bottom: 24px;
	margin-right: 4%;
	-webkit-transition: all 0.3s ease 0s;
	-o-transition: all 0.3s ease 0s;
	transition: all 0.3s ease 0s;
}
.adelete:hover {
	background-image: url(images/recruitmentSession/Delete1.png);
	padding-top: 3px;
	}
</style>

<script type="text/javascript">

<!-- clickable raws-->
    $(document).ready(function(){
        $('.tableData').click(function(){
            window.location = $(this).attr('href');
            return false;
        });

			
    });

</script>
<script src="js/deletePanel.js"></script>

</head>

<?php 
		require_once("Sql.php");
		require_once("GlobalVariables.php");
		$s = new Sql();
		$connect = $s->connectToDatabase($databaseName);
		
		/*$rSessionID="";
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
		}*/

?>

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
		<a href="createInterviewPanel.php" class="aCreateNew">Create New</a> 
		<a href="#" id="deletePanel" class="adelete">Delete Panel</a> 
		<a href="recruitmentSessionHelp.php" class="aHelp">Help</a>
	  </nav>
  </aside>
  <table width="83%" border="1" align="center" cellpadding="5" class="sortable" id="existingSessionsTable">
  <tbody>
    <tr>
	  <th width="1%" scope="col"></th>
      <th width="40%" scope="col">Interview Panel Name</th>
      </tr>
	  <?php					
		$data = mysqli_query($connect, "SELECT IntPanID,IntPanName,status FROM interviewpanel");
		$dpanel = mysqli_query($connect, "SELECT IntPanID,IntPanName,status FROM interviewpanel WHERE status='default'");
		$dpanelRow = $dpanel->fetch_row();
		
		while($raw = $data->fetch_row()) { ?>
		<!--<script> var id = <?php echo(json_encode($raw[0])); ?>;</script>--->
            <tr class="tableRow" href="editInterviewPanel.php?pid=<?php echo $raw[0] ?>">
				<td><input id="<?php echo $raw[0] ?>" name="selectPanel" type="checkbox"></td>
                <td href="editInterviewPanel.php?pid=<?php echo $raw[0] ?>" class="tableData" name="intName" id = <?php echo $raw[0] ?> ><?php echo $raw[1]?>&nbsp;&nbsp;<b><?php echo $raw[2]?></b></td>
            </tr>
    <?php } ?>
    </tbody>
</table>
<input id="defaultPanel" type="hidden" value="<?php echo $dpanelRow[0]; ?>" >
<footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>
