<?php
	require_once("Sql.php");
	
	class jobPosition{
		private $jpID;
		private $jobPositionName;
		
		public function __construct($name){
			$s = new Sql();
			$connect = $s->connectToDatabase('recruit');
			$jbID = $s->createJobPosition($connect,$name);
			
			$this->jpID = $jbID;
			$this->jobPositionName = $name;
		}
		
		public function setjpID($jpID){
			$this->jpID=$jpID;
		}
		
		public function getjpID(){
			return $this->jpID;
		}
		
		public function setJobName($name){
			$this->jobPositionName=$name;
		}
		
		public function getJobName(){
			return $this->jobPositionName;
		}
	}
?>