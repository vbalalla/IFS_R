<?php

$connect = mysqli_connect('localhost','root',"") or die("Unable to connect");
$select_db = mysqli_select_db($connect,'recruit') or die("Unable to connect to database");
echo $_POST['criteria'];
echo $_POST['name'];
echo $_POST['weight'];

    if(!empty($_POST['name'])&&!empty($_POST['criteria'])&&!empty($_POST['weight']))
    {
		$criteria=$_POST['criteria'];
		$sql1 = mysqli_query($connect,"insert into criteria values ('','$criteria')");
		
     foreach ($_POST['name'] as $key => $value) 
        {
            $item = $_POST["name"][$key];
            
            $qty = $_POST["weight"][$key];
			
			
			
			
			
			$idRow=mysqli_query($connect,"select criteriaID from criteria where criteriaName='$criteria'");
			$idRowGet=$idRow->fetch_Row();
			$id=$idRowGet[0];
            $sql = mysqli_query($connect,"insert into evaluation values ('','$item', '$qty','$id')");        
        }

    }
	
	else{
		
		
		
		
		throw new Exception("Please fill all the required values");
	}
?>