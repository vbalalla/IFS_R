<?php
// Start the session
//if(!isset($_SESSION)){
  //  session_start();
//}
require_once("Sql.php");
require_once("GlobalVariables.php");
class RecruitmentSession{
	//private $recSesID;
	private $name;
	private $dateCreated;
	private $jobPosition;
	public function __construct($name,$jb){
		$s = new Sql();
        $connectValue = $s->connectToDatabase($databaseName);
        $this->name = $name;
		$this->dateCreated=date("Y-m-d") ;
		$this->jobPosition=$jb;
		$RSID = $s->setSessionQuery($connectValue,$name,$this->
		dateCreated,$jb);
		echo "RecruitmentSession works ".$RSID."";
		//$recSesID = $RSID;
		// Set session variables
		//$_SESSION["rSessionID"] = $RSID;
		
		
	}
	
}
?>
