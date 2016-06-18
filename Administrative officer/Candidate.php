<?php 

class Candidate{
	private $candID = null;
	private $NIC = "";
	private $firstName = "";
	private $lastName = "";
	private $dateOfBirth = "";
	private $email = "";
	private $contactNo = "";
	private $university = "";
	private $candidateStatusID="";
		
	public function __construct($candID){
		$this->candID = $candID;
		
	}

	public function setCandStatus($cndStatusID){
		$this->candidateStatusID = $cndStatusID;
	}
	
	public function getCandStatus(){
		return $this->candidateStatusID;
	}
	
	public function getCandID(){
		return $this->candID;
	}	
	
	public function setNIC($nic){
		$this->NIC = $nic;
	}
	
	public function getNIC(){
		return $this->NIC;
	}	
	
	public function setFirstName($firstName){
		$this->firstName = $firstName;
	}
	
	public function getFirstName(){
		return $this->firstName;
	}
	
	public function setLastName($lastName){
		$this->lastName = $lastName;
	}
	
	public function getLastName(){
		return $this->lastName;
	}
	
	public function setDateOfBirth($dob){
		$this->dateOfBirth = $dob;
	}
	
	public function getDateOfBirth(){
		return $this->dateOfBirth;
	}
	
	public function setEmail($email){
		$this->email = $email;
	}
	
	public function getEmail(){
		return $this->email;
	}
	
	public function setContactNo($contactNo){
		$this->contactNo = $contactNo;
	}
	
	public function getContactNo(){
		return $this->contactNo;
	}
	
	public function setUniversity($university){
		$this->university = $university;
	}
	
	public function getUniversity(){
		return $this->university;
	}	
}

?>
