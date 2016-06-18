<?php
//setcookie('rSessionID','');
// Start the session
//if(!isset($_SESSION)){
    //session_start();
//}
require_once("Sql.php");
class RecruitmentSession{
	private $RSID;  
	private $name;
	private $dateCreated;
	private $jobPosition;
	public function __construct($name,$jb){
		$s = new Sql();
        $connectValue = $s->connectToDatabase('recruit');
        $this->name = $name;
		$this->dateCreated=date("Y-m-d") ;
		$this->jobPosition=$jb;
		$sessionID = $s->setSessionQuery($connectValue,$name,$this->
		dateCreated,$jb);
		$this->RSID=$sessionID; 		
		// Set session variables
		//$_SESSION['rSessionID'] = $sessionID;
		//if (isset($_COOKIE['rSessionID'])){
			//$_COOKIE['rSessionID'] = $sessionID;
		//}
	}
	
	public function getRSID(){
		return $this->RSID;
	}
	public function getName(){
		return $this->name;
	}
	public function getDateCreated(){
		return $this->dateCreated;
	}
	public function getJobPosition(){
		return $this->jobPosition;
	}
	public function getJobPositionName(){
		$s = new Sql();
        $connect = $s->connectToDatabase('recruit');
		$data = mysqli_query($connect,"SELECT jbName FROM jobpositon WHERE jbID='$this->jobPosition'");
		$row = $data->fetch_row();
		return $row[0];
	}	
	public function getStatus(){
		$s = new Sql();
        $connect = $s->connectToDatabase('recruit');
		$data = mysqli_query($connect,"SELECT sessionstatus.status FROM sessionstatus,recruitmentsession WHERE recruitmentsession.sessionStatusID=sessionstatus.sessionStatusID AND RSID='$this->RSID'");
		$row = $data->fetch_row();
		return $row[0];
	}
	
}
?>
