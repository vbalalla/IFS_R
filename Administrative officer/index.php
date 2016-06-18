<!-- Administrative Officer -->
<?php
include('../phpSessions.php');

if(isset($_SESSION['login_user'])){
    if($_SESSION['type']=='system_admin'){
        header("location: System administrator/systemAdministratorHome.php");
    }
    if($_SESSION['type']=='receptionist'){
        header("location: Receptionist/recruitmentSessionInterface.php");
    }
    if($_SESSION['type']=='interview_panel'){
        header("location: Interview panel member/index.php");
    }
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>IFS Resume Trekker</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico"/>
<link href="css/style.css" rel="stylesheet" type="text/css">

<script src="calendar/js/jquery.js"></script>
<script src="calendar/js/responsive-calendar.js"></script>

<link href="calendar/css/responsive-calendar.css" rel="stylesheet" media="screen">
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
  
  <aside class="secRight">
	<div class="calendarDiv" width="100%">
      <!-- Responsive calendar - START -->
    	<div class="responsive-calendar">
        <div class="controls">
            <a class="pull-left" data-go="prev"><img src="images/calPre.png">&nbsp;&nbsp;</a>
            <h4><span data-head-year></span> <span data-head-month></span></h4>
            <a class="pull-right" data-go="next">&nbsp;&nbsp;<img src="images/calNext.png"></a>
        </div><hr/>
        <div class="day-headers">
          <div class="day header">Mon</div>
          <div class="day header">Tue</div>
          <div class="day header">Wed</div>
          <div class="day header">Thu</div>
          <div class="day header">Fri</div>
          <div class="day header">Sat</div>
          <div class="day header">Sun</div>
        </div>
        <div class="days" data-group="days">
          
        </div>
      </div>
      <!-- Responsive calendar - END -->
    </div>
    <script src="calendar/js/jquery.js"></script>
    <script src="calendar/js/responsive-calendar.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        $(".responsive-calendar").responsiveCalendar({
          time: '2016-01',
          events: {
			 
			<?php 
			 require_once("Sql.php");
			 $s = new Sql();
        	 $connectValue = $s->connectToDatabase('recruit');
			 $result = mysqli_query($connectValue, "SELECT schdate FROM interviewschedule");
			 if ($result->num_rows > 0){
				 while($row=mysqli_fetch_array($result,MYSQLI_NUM)) {
			 ?>
			 <?php echo "\"".$row[0]."\": {},\n";?>
			 <?php 
				 }
			 }
			 
			 ?>
            }
			
        });
      });
    </script>
  </aside>
  
  <aside class="secLeft">
  <table>
    <tr>
      <td style="width: 90%">
        <nav><ul class="navDownList">
			<li><a href="recruitmentSessionInterface.php" class="recruitmentSession">Recruitment Session</a></li>
			<li><a href="thresholdInterface.php" class="createJobPosition"> Change Threshold Period</a></li>
			<li><a href="evaluationCriteria.php" class="evaluationCriteria"> Evaluation Criteria</a></li>
			<li><a href="interviewPanelHome.php" class="searchCandidate"> Interview Panel</a></li>
			<li><a href="ReportGenerate/index.php" class="viewStatisticalReport"> View Statistical Report</a></li>
			</ul>
		  </nav>
      </td>

    </tr>
  </table>
  </aside>
  
<footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>
