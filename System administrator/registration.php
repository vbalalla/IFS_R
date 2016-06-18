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
<title>Registration</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico"/>

<script type="text/javascript" src="js/sortable.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css">

<style type="text/css">
	.asideLeftIcons {
	margin-left: 9%;
	width: 70%;
	margin-bottom: 5%;  
	text-align: left;
	}
	.headingsNormal {
		margin-left: 9%;
		font-size: 1.2em;
	}

.regestration {
	padding-left: 120px;
}
.headingmid {
	margin-left: 250px;
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
                <a href="systemAdministratorHome.php" class="navHome"> Home</a>
                <a href="../UserManual/UserManual.html" class="navHelp">Help </a>
            </form>
        </aside>
        <aside class="asideLeft"></aside>
    </header>
  
  <aside class="asideLeftIcons">
	  <nav>
		<a href="AdminUsers.php" class="aBack">Back</a>
		<a href="recruitmentSessionHelp.html" class="aHelp">Help</a>
	  </nav>
  </aside>
  <div align="center">
      <?php
      $remarks="";
      if(isset($_GET['remarks'])){
        $remarks=$_GET['remarks'];
        if ($remarks=='success')
        {
          ?>
    <h3 style="color: green">
      <?php
          echo 'Registration Success!';
        }
        ?>
        </h3>
        <?php
        if ($remarks=='fail')
        {
          ?>
    <h3 style="color: red">
          <?php
          echo 'Registration fail! User Name already exist';
          ?>
    </h3>
        <?php
        }
      }
?>

  </div>

    <strong class="headingmid"> Create New User </strong><br/><br/>
    <form id="regForm" action="registrationDatabase.php" method="POST">
  <table class="regestration">

  
  <tr><td><strong > First name </strong></td><td><input id="fname" name="fname" type="text" size="19"></td></tr>
  <tr><td><strong > Last name </strong></td><td><input id="lname" name="lname" type="text" size="19"></td></tr>
  <tr><td><strong > email </strong></td><td><input id="email" name="email" type="email" size="19"></td></tr>
  <tr><td><strong > Contact No </strong></td><td><input id="contact" name="contact" type="number" size="19"></td></tr>
    <td><strong > Username </strong></td><td><input id="username" name="username" type="text" size="19"></td></tr>
  <tr><td><strong > password </strong></td><td><input id="psw1" name="psw1" type="password" size="19"></td></tr>
  
<!--  <tr><td><strong >Confirm password </strong></td><td><input id="psw2" name="psw2" type="password" size="19"></td></tr>-->
  <tr><td><strong >Designation </strong></td>
  <td><select name="Designation">
    <option value="admin_officer">Administrative officer</option>
    <option value="interview_panel">Interview panel member</option>
	<option value="receptionist">Receptionist</option>
    <option value="system_admin">System Administrator</option>
</select></td></tr> 
  <tr><td></td><td><input type="submit" value="Submit"></td></tr>
  
 </table>
 </form>

 
 

  
  <br/><br/><br/>
  

<footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>
