<html>
<head></head>
<body>
<?php
require_once("RecruitmentSession.php");
require_once("jobPosition.php");

		$jb=""; $name=""; $newJob="";
        if(isset($_POST['selectPost'])){
    		$jb=$_POST['selectPost'];
  		}
	    if(isset($_POST['sessionName'])){
    		$name=$_POST['sessionName'];
  		}
		if(isset($_POST['newJob'])){
    		$newJob=$_POST['newJob'];
  		}
		
		/*if(empty($_POST['selectPost']) && empty($_POST['sessionName'])){
    		echo '<script language="javascript">';
			echo 'alert("Please enter session name and select a job position")';
			echo '</script>';
			header("Location: createRecruitmentSession.php?aid=1");
  		}else if(empty($_POST['sessionName'])){
			echo '<script language="javascript">';
			echo 'alert("Please enter session name")';
			echo '</script>';
			header("Location: createRecruitmentSession.php?aid=2");
		}else if(empty($_POST['selectPost'])){
			echo '<script language="javascript">';
			echo 'alert("Please select a job position")';
			echo '</script>';
			header("Location: createRecruitmentSession.php?aid=3");
		}else{
			$rsObject=new RecruitmentSession($name,$jb);
			$sessionID = $rsObject->getRSID();
			echo "<meta http-equiv=\"refresh\" content=\"0;url=UploadCVs.php?id=".$sessionID.">";
		}*/	
		$jobPos = null;
		if($newJob!=""){
			$jobPos = new jobPosition($newJob);
			$jb = $jobPos->getjpID();
		}	
		
		$sessionID="";
		if($jb!="" && $name!=""){
			$rsObject=new RecruitmentSession($name,$jb);
			$sessionID = $rsObject->getRSID();
			$rName = $rsObject->getName();
			$rJob = $rsObject->getJobPositionName();
			$rDate = $rsObject->getDateCreated();
			$rStatus = $rsObject->getStatus();
		}else 
		
//header("Location: UploadCVs.php?id=".$sessionID."");
//die();
?>
<meta http-equiv="refresh" content="0;url=uploadCVs.php?id=<?php echo $sessionID?>&rname=<?php echo $rName?>&rjob=<?php echo $rJob?>&rdate=<?php echo $rDate?>&rstatus=<?php echo $rStatus?>">
</body>
</html>
