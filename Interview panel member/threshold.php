<?php
require_once("Sql.php");
require_once("GlobalVariables.php");

class Threshold{

	private $thresholdPeriodYears;
	
	
	public function __construct(){
		$s = new Sql();
		global $databaseName;
		$connectValue = $s->connectToDatabase($databaseName);
		$row=$s->selectRecords($connectValue,'config');
		$this->thresholdPeriodYears=$row[1];
		
	}
	
	
	public function getThresholdPeriod(){
		return $this->thresholdPeriodYears;
	}
	public function setThresholdPeriod($thresholdPeriod){
		$this->thresholdPeriodYears=$thresholdPeriod;
		$s = new Sql();
		global $databaseName;
        $connectValue = $s->connectToDatabase($databaseName);
		$row=$s->insertToConfig($connectValue,$thresholdPeriod);
		
		
	}
	

	}
?>
