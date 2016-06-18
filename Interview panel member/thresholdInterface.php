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


<?php require_once("threshold.php"); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Change Threshold period</title>
<script type="text/javascript" src="js/jquery.js"> </script>
<script type="text/javascript" src="js/scriptThreshold.js"> </script>
<script src="sweetalert/dist/sweetalert.min.js"></script> 
<link rel="stylesheet" type="text/css" href="sweetalert/dist/sweetalert.css">
<link href="css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
	.asideLeftIcons {
		margin-left: 9%;
		width: 70%;
		margin-bottom: 5%;
		text-align: left;
	}
	
	.FormNumberInput {
	font-family: "OpenSans Regular";
	font-size: 1.2em;
	font-weight: 400;
	color: #000000;
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
	color: #BEA7AA;
	}
	input[type=button] {
	padding-left: 8%;
	padding-right: 5px;
	color: #281A2B;
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
	input[type=button]:hover {
		background-image: url(images/recruitmentSession/submit2.png);
	}
	input[type=reset]:hover {
		background-image: url(images/recruitmentSession/cancel1.png);
	}
</style>	

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
		<a href="index.php" class="aBack">Back</a> 
		<a href="recruitmentSessionHelp.php" class="aHelp">Help</a>
	  </nav>
  </aside>
  <section class="formSectionRight">
  
  <form id="Form" method="post">
  Threshold period in months :  
  <input type="number" step="1" required class="FormNumberInput"  min=0 id="thresholdYears" size ="2" name="ThresholdYears" style="width:50px" value=<?php 
  $t=new Threshold();
  echo round(($t->getThresholdPeriod())*12);
  ?> />
  
  
  
  <br/>
    <br/>
  <input id="btnID" name="change" type="button" value="Save"  />
   <input type="reset" value="Cancel"/>
  </form>
  </section>
  <footer>Copyright 2015 &copy;</footer>
  </div>
  </body>
  </html>
  



