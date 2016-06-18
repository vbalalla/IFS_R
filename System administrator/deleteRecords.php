<?php
include('../phpSessions.php');
if($_SESSION['type']=='admin_officer')
    header("location: ../Administrative officer/index.php");

if($_SESSION['type']=='interview_panel')
    header("location: ../Interview panel member/index.php");

if($_SESSION['type']=='receptionist')
    header("location: ../Receptionist/recruitmentSessionInterface.php");


?>


<!doctype html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>IFS Resume Trekker</title>
    <link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico"/>
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">
    #searchAndDeleteRecordsDiv {
	margin-left: 3%;
	background-color: #DBCCCE;
	margin-right: 40%;
	padding-left: 1%;
	padding-top: 1%;
	margin-bottom: 5%;
	padding-right: 1%;
}
input[type=submit] {
	padding-left: 13%;
	padding-right: 5px;
	color: #281A2B;
	background-image: url(images/recruitmentSession/search.png);
	background-repeat: no-repeat;
	padding-top: 11px;
	padding-bottom: 24px;
	margin-right: 2%;
	font-family: "OpenSans Regular";
	border-style: none;
	border-color: #E9E1E1;
	background-color: #DBCCCE;
	font-weight: 600;
	font-size: 1.2em;
	margin-left: 1%;
	margin-top: 5%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
	margin-bottom: 5%;
	}
	input[type=submit]:hover {
	background-image: url(images/recruitmentSession/search1.png);
	}
    p {
	font-size: 1em;
	font-weight: 600;
}
    </style>
    <script type="text/javascript" src="jquery/jquery.js"></script>
    <script type="text/javascript">
        $(function() {
            $('#checkboxOne, #checkboxTwo').click(function() {
                var cb1 = $('#checkboxOne').is(':checked');
                $('#key').prop('disabled', !cb1);
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
                <a href="systemAdministratorHome.php" class="navHome"> Home</a>
                <a href="../UserManual/UserManual.html" class="navHelp">Help </a>
            </form>
        </aside>
        <aside class="asideLeft"></aside>
    </header>

    <aside class="asideLeftIcons" style="padding-left: 80px">
        <nav>
            <a href="systemAdministratorHome.php" class="aBack">Back</a>
            <a href="recruitmentSessionHelp.php" class="aHelp">Help</a>
        </nav>
    </aside>
    <div style="padding-top: 30px">
      <div id="searchAndDeleteRecordsDiv">
        <h3>Search and Delete records</h3><hr/>
        <form id="myForm" action="searchAndDelete.php" method="post">
          <p><input type="radio" name="radio" <?php if (isset($radio) && $radio=="candidate") echo "checked";?> value="candidate">Candidates</input></p>
          <p><input type="radio" name="radio" <?php if (isset($radio) && $radio=="session") echo "checked";?> value="session">Sessions</input></p>
          <p><input type="radio" name="radio" <?php if (isset($radio) && $radio=="interview") echo "checked";?> value="interview">Interviews</input></p> <br/>
          <p>&nbsp;&nbsp;&nbsp;&nbsp;use a keyword to search: <input type="checkbox" name="checkbox1" id="checkboxOne" /></p>
          <p><span style="padding-left: 75px">Enter the keyword :</span><input type="text" id="key" name="key" placeholder="keyword" disabled /></p>
          <input type="submit" value="search" name="search">
          </form>
      </div>
    </div>




    <footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>
