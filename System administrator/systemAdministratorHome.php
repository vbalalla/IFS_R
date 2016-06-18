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
<html>
<head>
<meta charset="utf-8">
<title>IFS Resume Trekker</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico"/>
<link href="css/style.css" rel="stylesheet" type="text/css">
  
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
  
  <nav><ul class="navDownList">
    <li><a href="AdminUsers.php" class="usersAdminhome">Users</a></li>
    <li><a href="createBackup.php" class="backupAdminHome"> Backup</a></li>
    <li><a href="deleteRecords.php" class="deleteRecordsAdminHome"> Delete Records</a></li>
  </ul>
  </nav>
  
  <footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>
