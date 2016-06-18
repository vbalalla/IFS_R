<?php
require_once("Sql.php");
$s = new Sql();
$connectValue = $s->connectToDatabase('recruit');
$Designation="";
$fname=$_POST['fname'];
$lname=$_POST['lname'];
$email=$_POST['email'];
$contact=$_POST['contact'];
$username=$_POST['username'];
$password=$_POST['psw1'];
$type=$_POST['Designation'];
if($type=='admin_officer'){
	$Designation=2;
}else if($type=='system_admin'){
	$Designation=3;
}else if($type=='interview_panel'){
	$Designation=1;
}else{
	$Designation=4;
}
$val = mysqli_query($connectValue, "SELECT usrid FROM login WHERE username='$username'");
if(mysqli_num_rows($val)==0){
    mysqli_query($connectValue, "INSERT INTO login(username,password,type) VALUES('$username', '$password', '$type')");
	
	$userIDq = mysqli_query($connectValue, "SELECT MAX(usrid) FROM login WHERE username='$username'");
	$userID = $userIDq->fetch_row();
	
	$empIDq = mysqli_query($connectValue, "SELECT MAX(EmpID) FROM employee");
	$empID = $empIDq->fetch_row();
	$emp = $empID[0];
	$emp++;
	
	mysqli_query($connectValue, "INSERT INTO employee(EmpID,FirstName,LastName,email,TelNo,Designation,usrid) VALUES('$emp','$fname','$lname', '$email', '$contact',$Designation,$userID[0])");
    mysqli_close($connectValue);
    header("location: registration.php?remarks=success");
}
else
    header("location: registration.php?remarks=fail");
