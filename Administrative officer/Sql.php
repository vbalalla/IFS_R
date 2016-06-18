<?php
/* object of the Cv class is created here under createNewCV */
require_once("Cv.php");
require_once("GlobalVariables.php");


class Sql{
	
	public static function connectToDatabase($databaseName){
		$user = 'root';
		$pass = '';
		$database = $databaseName;
		$connect = mysqli_connect('localhost',$user,$pass) or die("Unable to connect");
		$select_db = mysqli_select_db($connect,$database) or die("Unable to connect to database");
		return $connect;
	}
	
	/*This method is called at UploadCVs.php*/
	/*It purely creates an object of CV class*/
	public function createNewCV($connect, $i){
		$queryToGetLastcvID = mysqli_query($connect, "SELECT MAX(cvID) FROM cv");
		$row = $queryToGetLastcvID->fetch_row();

		$newCV = new CV("");						
		$newCV = $newCV->createCV($row[0], $i);
		
		return $newCV;
	}
	
	/* increase the primary key one by one*/
	public function increaseID($connect,$table,$column,$stringChoice){

			$queryToGetLastID = mysqli_query($connect, "SELECT MAX(".$column.") FROM ".$table);	
			$row = $queryToGetLastID->fetch_row();
			$lastID=$row[0];
			if ($lastID == null) {
				$lastID=$stringChoice;
			}
			else{
				$lastID++;
			}
			return $lastID;
			
	}
		/* This method is called at UploadCVs.php*/
		/*** $cv here is an CV object***/
	public function createNewCandidate($connect, $cv, $sessionID){
		$queryToGetLastCandidateID = mysqli_query($connect, "SELECT MAX(CandID) FROM candidate");
		$row = $queryToGetLastCandidateID->fetch_row();
		$text = "";
		$fileType = $cv->getFileType();
		
		if($fileType != ""){
			if($fileType == "pdf"){
				$text = $cv->extractPdf();
			}else if($fileType == "docx"){
				$text = $cv->extractWordDocument();
			}
		}
		
		/*identifyDetails return a new Candidate Object*/
		$newCandidate = $cv->identifyDetails($text, $row[0], $cv, $connect, $this, $sessionID);		
		
		return $newCandidate;
	}
	/*This method is called at Cv.php */
	public function checkInDB($NIC, $newCV, $newCandidate, $connect, $sessionID){
		global $db, $user, $pass;
		$dbh= new PDO($db,$user,$pass);
		$sql = 'SELECT cv.cvID FROM cv JOIN candidate ON cv.cvID=candidate.cvID WHERE ((submittedDate > NOW() - INTERVAL 365 day) AND (candidate.NIC="'.$NIC.'"))';
		
		// Use query() for "one-time" SQL requests
		// PDO::FETCH_ASSOC = return results in the form of an associative array
		$getRow = null;
		foreach($dbh->query($sql, PDO::FETCH_ASSOC) as $row){
			$getRow = $row;
		}
			//each $row = an associative array representing one row in the database
			
		if($getRow != null){
			$newCandidate->setCandStatus("CS002");
		}
			/*** If threshold period issue is not met ***/
		if($getRow == null){

		
			if($newCV != null){
				$cvID = $newCV->getCvID();
				$submittedCV = $newCV->getSubmittedCVPath();
				$submittedDate = $newCV->getSubmittedDate(); 
									
				//echo "<p>\n$cvID , $submittedCV , $submittedDate</p>";
				$queryToInsertToCV = mysqli_query($connect,"INSERT INTO cv VALUES('$cvID', '$submittedCV', '$submittedDate','$sessionID')");
			}			
				
			if($newCandidate != null){
				 $newCandidate->setCandStatus("CS003");
				$candID = $newCandidate->getCandID();
				$nic = $newCandidate->getNIC();
				$dob = $newCandidate->getDateOfBirth();
				$email = $newCandidate->getEmail();
				$contactNo = $newCandidate->getContactNo();
				$candStatus=$newCandidate->getCandStatus();
				//echo "<p>\n$candID , $nic , $dob, $email, $contactNo</p>";
				$queryToInsertToCandidate = mysqli_query($connect,"INSERT INTO candidate VALUES('$candID', '$nic', '','','$dob','$email','$contactNo','','$candStatus','$cvID')");
			}
			return 0;
		}else{
			return 1;
		}		
	}
	/*Function to ceck whether the candidate needs to flag for threshold period*/
	public Function needToFlag($newCV,$newCandidate,$connect,$sessionID){
				
				$row=$this->selectRecords($connect,'config');
				$thresholdPeriod=$row[1];
				$thresholdDays=$thresholdPeriod*365;
				$queryEmail =mysqli_query($connect,"SELECT cv.cvID FROM cv JOIN candidate ON cv.cvID=candidate.cvID WHERE ((submittedDate > NOW() - INTERVAL ".$thresholdDays." day) AND email='".$newCandidate->getEmail()."' OR DateOfBirth='".$newCandidate->getDateOfBirth()."');");	
				//'".$newCandidate->getEmail()."'"
				 $haveBefore=$queryEmail->fetch_row();
				//$queryDOB = mysqli_query($connect,"SELECT `CandID` FROM `candidate` WHERE DateOfBirth='".$newCandidate->getDateOfBirth()."'");
				//$dob=$queryDOB->fetch_row();
				if($haveBefore!=null){
					$newCandidate->setCandStatus("CS004");		
				}
				else{
					$newCandidate->setCandStatus("CS003");	
				}
				if($newCV != null){
				$cvID = $newCV->getCvID();
				$submittedCV = $newCV->getSubmittedCVPath();
				$submittedDate = $newCV->getSubmittedDate(); 
									
				
				$queryToInsertToCV = mysqli_query($connect,"INSERT INTO cv VALUES('$cvID', '$submittedCV', '$submittedDate','$sessionID')");
			}		
				
					if($newCandidate != null){
			 
				$candID = $newCandidate->getCandID();
				$nic = $newCandidate->getNIC();
				$dob = $newCandidate->getDateOfBirth();
				$email = $newCandidate->getEmail();
				$contactNo = $newCandidate->getContactNo();
				$candStatus=$newCandidate->getCandStatus();
				//echo "<p>\n$candID , $nic , $dob, $email, $contactNo</p>";
				$queryToInsertToCandidate = mysqli_query($connect,"INSERT INTO candidate VALUES('$candID', '$nic', '','','$dob','$email','$contactNo','','$candStatus','$cvID')");
				}
				
					
	}
	
	/* recruitment Session */
	
	public function setSessionQuery($connect,$name,$dateCreated,$jb){
			$RSID=$this->increaseID($connect,"recruitmentsession","RSID","RS001");
			//echo $RSID;
			$queryToInsertTorecruitmentsession = mysqli_query($connect,"INSERT INTO recruitmentsession VALUES('$RSID', '$name', '$dateCreated','$jb','ss001')");
	
			return $RSID;
	}
	
	//Function to get all records of a table	
	public function selectAllRecords($connect,$tableName){
		$data = mysqli_query($connect, 'SELECT * FROM '.$tableName.'');
		return $data;
	}
	//Function to get all records of a table	
	public function selectRecords($connect,$tableName){
		$query = mysqli_query($connect, 'SELECT * FROM '.$tableName.'');
		$row=$query->fetch_row();
		return $row;
	}
	
		
	public function changeSessionStatus($RSID,$sessionStatusID){
		global $databaseName;
		$connect = $this->connectToDatabase($databaseName);
		$data = mysqli_query($connect,"UPDATE recruitmentsession SET sessionStatusID='$sessionStatusID' WHERE RSID='$RSID'");
	}	
	/* threshold period */
	
	public function insertToConfig($connect,$value){
		$queryToInsertToConfig = mysqli_query($connect,"UPDATE `config` SET `years`=".$value." WHERE 1;");
	
}
	public function createJobPosition($connect,$jobName){
		$jbID=$this->increaseID($connect,"jobpositon","jbID","jp001");
		$queryToInsertJobPosition = mysqli_query($connect,"INSERT INTO jobpositon(jbID,jbName) VALUES ('$jbID','$jobName')");
		return $jbID;
	}
	
	
		
	
	
	
}
?>   
