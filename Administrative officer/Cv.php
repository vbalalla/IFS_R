<?php
/* Regular Expressions are in this class*/ 
/*new Candidate object is created here*/
require_once("vendor/autoload.php");
require_once("Docxtotext.php");
require_once("GlobalVariables.php");
require_once("Candidate.php");

class CV{
	private $cVID = "";
	private $submittedCV = "";
	private $submittedDate = "";
	private $fileTypeIndicator = "";	
	private $matchToFlag=false;
		
	public function __construct($cVID){
		$this->cVID = $cVID;		
	}
		
	public function setSubmittedCVPath($path){
		$this->submittedCV = $path;
	}
	
	public function getSubmittedCVPath(){
		return $this->submittedCV;
	}
	
	public function setSubmittedDate($date){
		$this->submittedDate = $date;
	}
	
	public function getSubmittedDate(){
		return $this->submittedDate;
	}
	
	public function getCvID(){
		return $this->cVID;
	}
	
	public function getFileType(){
		return $this->fileTypeIndicator;
	}

	public function fileUpload($i){
		
		$target_dir = "uploads/";
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"][$i]);
		$uploadOk = 1;
		$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
		
		if(isset($_POST["submit"])){
			
		}
		// Check if file already exists
		//if (file_exists($target_file)) {
		//	$uploadOk = 0;
		//}
		
		// Check file size
		if ($_FILES["fileToUpload"]["size"][$i] > 2097152) {
			$uploadOk = 0;
		}
		
		//Check file type
		if ($fileType != "pdf" && $fileType != "docx") {
			$uploadOk = 0;
		}else{
			$this->fileTypeIndicator = $fileType;
		}		
		
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			//echo "Sorry, your file was not uploaded.";
			
			return null;
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) {
				//echo "The file ". basename( $_FILES["fileToUpload"]["name"][$i]). " has been uploaded.";
				return $target_file;
			} else {
				//echo "Sorry, there was an error uploading your file.";
				return null;
			}
			
		}		
		
	}
	
	public function extractPdf(){
		//Parse pdf file and build necessary objects.
		$parser = new \Smalot\PdfParser\Parser();
		$pdf = $parser->parseFile($this->submittedCV);
 
		$text = $pdf->getText();		
		
		return $text;
	}
	
	public function extractWordDocument(){
		$text = docxToText($this->submittedCV);
		return $text;
	}

/*Regular expression matching*/
/*This method is called at sql.php*/
	public function identifyDetails($text, $lastCandidateID, $cv, $connect, $s, $sessionID){
		$patternEmail = '/[A-Za-z0-9!#$%&\'\=*+\/\?^_`{|}\.~-]+@[A-Za-z0-9!#$%&\'\=*+\/\?^_`{|}\.~-]+\.(?:[A-Za-z]{4}|lk|com|org|net|edu|biz|info|asia)/';
		$patternNIC = '/[0-9]{9}+(V|v)/';
		$patternDateOfBirth1 = '/([1|2][0-9\.\/-]{4}[0|1][0-9][\.\/-][0|1|2|3][0-9])/';
		$patternDateOfBirth2 = '/([0|1|2|3][0-9][\.\/-][0|1|2|3][0-9][\.\/-][1|2][0-9]{3})/';
		$patternContactNo1 = '/[0][0-9]{9}/';
		$patternContactNo2 = '/[0][0-9]{2}[-" "][0-9]{7}/';
		$patternContactNo3 = '/[+|0][0-9]{11}/';
		
		preg_match_all($patternNIC, $text, $nicList);
		preg_match_all($patternEmail, $text, $emailList);		
		preg_match_all($patternDateOfBirth1, $text, $dobList1);
		preg_match_all($patternDateOfBirth2, $text, $dobList2);
		preg_match_all($patternContactNo1, $text, $contactList1);		
				
				
				/*** new Candidate Object is created***/
				/*and putting extracted details to that object's attributes*/
		if ($lastCandidateID == null) {
			$newCandidate = new Candidate("C001");
		} else {
			++$lastCandidateID;
			
			$newCandidate = new Candidate("$lastCandidateID");
		}		
		
		if($nicList[0] != null){
			$newCandidate->setNIC($nicList[0][0]);
						
		}
		else{
			/*If NIC is not extracted,cand status would be marked to unchecked*/
			$newCandidate->setCandStatus("CS003");	
		}
			
		
		if($emailList[0] != null){
			$newCandidate->setEmail($emailList[0][0]);
		}				
		
		$minYear = 3000;
		$minDOB = "";
		
		if($dobList1[0] != null){			
			for($i = 0; $i < sizeof($dobList1[0]); $i++){
				$year = $dobList1[0][0][0].$dobList1[0][0][1].$dobList1[0][0][2].$dobList1[0][0][3];
				if($year <= $minYear){
					$minYear = $year;
					$minDOB = $dobList1[0][$i];
				}
			}			
		}
		
		if($dobList2[0] != null){			
			for($i = 0; $i < sizeof($dobList2[0]); $i++){			
				$date = $this->changeDateFormat($dobList2[0][$i]);				
				if(strlen($date)==10){
					$year = $date[0].$date[1].$date[2].$date[3];
					if($year <= $minYear){
						$minYear = $year;
						$minDOB = $date;
					}
				}
			}
		}		
		
		if(date("Y") - $minYear <= 15){
			$minDOB="";
		}
	
		$newCandidate->setDateOfBirth($minDOB);
		
		if($contactList1[0] != null){
			$newCandidate->setContactNo($contactList1[0][0]);
		}else{
			preg_match_all($patternContactNo2, $text, $contactList2);		
			if($contactList2[0] != null){
				$contact = $this->changeContactNoFormat($contactList2[0][0]);
				$newCandidate->setContactNo($contact);
			}else{
				preg_match_all($patternContactNo3, $text, $contactList3);
				if($contactList3[0] != null){
					$contact = $this->changeContactNoFormat($contactList3[0][0]);
					$newCandidate->setContactNo($contact);
				}
			}
		
		}
		
		if($nicList[0] != null){
			if(sizeof($nicList[0]) == 1){
				$thresholdRejected = $s->checkInDB($nicList[0][0], $cv, $newCandidate, $connect, $sessionID);
				
				if($thresholdRejected==1){
					$newCandidate->setCandStatus($thresholdRejected);
				}
			}			
		}
		else{

			$matchToFlag=$s->needToFlag($cv,$newCandidate,$connect,$sessionID);
		}
		
				
		return $newCandidate;
	}
	
	public function changeDateFormat($date){
		$field1 = $date[0].$date[1];
		$field2 = $date[3].$date[4];
		$field3 = $date[6].$date[7].$date[8].$date[9];
		
		if($field1>12){
			return $field3."-".$field2."-".$field1;
		}else if($field2>12){
			return $field3."-".$field1."-".$field2;
		}else{
			return $field3."-".$field2."-".$field1;
		}		
	}
	
	function changeContactNoFormat($contactNo){
		$length = strlen($contactNo);
		$formattedNo = "";
		if($length == 11){
			for($i = 0; $i < $length; $i++){			
				if($i == 3){
					continue;
				}
				$formattedNo = $formattedNo.$contactNo[$i];
			}
		}else if($length == 12){
			if($contactNo[0] == '0' || $contactNo[0] == '+'){
				$formattedNo = "0";
				for($i = 3; $i < $length; $i++){			
					$formattedNo = $formattedNo.$contactNo[$i];
				}
			}
		}	
		return $formattedNo;
	}
	/* This method is called in sql.php after creating Cv object*/
	public function createCV($lastID, $i){
		if ($lastID == null) {
			$newCV = new CV("CV001");
		} else {
			++$lastID;
			$newCV = new CV("$lastID");
		}		
		
		$filePath = $newCV->fileUpload($i);
		
		if ($filePath != null) {
			$newCV->submittedCV = $filePath;
			$newCV->submittedDate = date("Y-m-d");			
			return $newCV;
		} else {
			return null;
		}
		
	}
	
}

?>

