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
<title>IFS Resume Trekker</title>
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>

<?php
$intID="";
if(isset($_GET["int"])){
	$intID=$_GET["int"];
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
        <a href="index.php" class="navHome"> Home</a>
        <a href="../UserManual/UserManual.html" class="navHelp">Help </a>
      </form>

    </aside>
    <aside class="asideLeft"></aside>
  </header>
  <br><br><br>
  <nav><ul class="navDownList">    
    <li><a href="addEvalCriteria.php?int=<?php echo $intID; ?>" class="evaluationCriteria"> Add/Edit Evaluation Criteria</a></li><br>
    <li><a href="interviewCandidates.php?int=<?php echo $intID; ?>" class="createJobPosition"> Enter Interview Marks</a></li>
    </ul>
  </nav>
  <br><br><br>
  <footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>
