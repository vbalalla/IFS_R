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
<title>Create Recruitment Session</title>
<?php 
		require_once("Sql.php");
		require_once("GlobalVariables.php");
		$s = new Sql();
		$connect = $s->connectToDatabase($databaseName);
		
		$results = mysqli_query($connect,"SELECT * FROM jobpositon");
		
?>	
<link href="css/style.css" rel="stylesheet" type="text/css">
<script src="js/jquery.min.js"></script>
<style type="text/css">
	.asideLeftIcons {
		margin-left: 9%;
		width: 70%;
		margin-bottom: 5%;
		text-align: left;
	}
	
	.FormTextInput {
	font-family: "OpenSans Regular";
	font-size: 1.2em;
	font-weight: bold;
	color: #330000;
	margin-left: 2%;
	margin-top: 1%;
	margin-bottom: 1%;
	margin-right: 5%;
	padding-left: 1%;
	padding-right: 7%;
	}
	.formSelect {
	font-family: "OpenSans Regular";
	font-weight: 400;
	font-size: 1.2em;
	margin-top: 1%;
	margin-left: 2%;
	margin-bottom: 1%;
	margin-right: 5%;
	padding-left: 1%;
	padding-right: 8.4%;
	color: #000000;
	}
	input[type=submit] {
	padding-left: 8%;
	padding-right: 5px;
	color: #000000;
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
	.formSectionRight {
		background-color: #DBCCCE;
		margin-left: 8%;
		margin-right: 30%;
		padding-bottom: 1%;
		margin-bottom: 5%;
		width: 60%;
	}
	input[type=submit]:hover {
		background-image: url(images/recruitmentSession/submit2.png);
	}
	input[type=reset]:hover {
		background-image: url(images/recruitmentSession/cancel1.png);
	}
</style>	

<script>
jQuery(function(){
    $('#new-job').click(function(){
		$( "#jbposition" ).replaceWith( '<input name="newJob" type="text" required class="FormTextInput" placeholder="Enter New Job Position"/>' );
		document.getElementById("new-job").remove();
	});
});
</script>

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
		<a href="recruitmentSessionInterface.php" class="aBack">Back</a> 
		<a href="recruitmentSessionHelp.php" class="aHelp">Help</a>
	  </nav>
  </aside>
  <section class="formSectionRight">
  <form method="post" action="main1.php">
    <input id="sessionID" name="sessionName" type="text" required class="FormTextInput" placeholder="Session Name"/><br/>
    <select id="jbposition" name = "selectPost" class="formSelect" required placeholder="Select Job Position">
		<option value="" selected disabled>Select Job Position</option>
		<?php while($row = $results->fetch_row()){?>
		<option value=<?php echo $row[0];?>><?php echo $row[1];?></option>
		<?php } ?>
        </select><br/>
	&nbsp;&nbsp;&nbsp;	
	<a href="#" id="new-job">+ Add new job position</a>
	<br>
    <input id="btnID" type="submit" value="Create" href="uploadCVs.php"/>
    <input type="reset" value="Cancel"/>
    
  </form>
  </section>
  
    
<footer>Copyright 2015 &copy;</footer>

</div>
</body>
</html>

