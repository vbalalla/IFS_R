<?php
require_once("Sql.php");
require_once("GlobalVariables.php");
$s = new Sql();
$connect = $s->connectToDatabase($databaseName);

$intName = "";
if(isset($_GET["intPanel"])){
	$intName  = $_GET["intPanel"];	
}

$intPanel = mysqli_query($connect, "SELECT employee.EmpID,FirstName, LastName, email, TelNo,interviewpanel.IntPanID FROM employee,interviewpanelmemberdetails,interviewpanel WHERE employee.EmpID=interviewpanelmemberdetails.EmpID AND interviewpanelmemberdetails.IntPanID=interviewpanel.IntPanID AND interviewpanel.intPanID=$intName");
echo "<table width=\"100%\" border=\"1\" cellpadding=\"5\" class=\"sortable\" id=\"tableCommon1\" >
	  <tbody>
		<tr>
		  <th width=\"37%\" scope=\"col\">Name</th>
		  <th width=\"37%\" scope=\"col\">email</th>
		  <th width=\"26%\" scope=\"col\">Contact No</th>
		</tr>
	";
	
	while($raw = $intPanel->fetch_row()) { 
		 (json_encode($raw[0]));
         echo   "<tr>                
                <td class=\"tableData\" name=\"name1\" id = ".$raw[0].">".$raw[1]." ".$raw[2]."</td>
                <td class=\"tableData\" name=\"email1\" id = ".$raw[0].">".$raw[3]."</td>
                <td class=\"tableData\" name=\"contact1\" id = ".$raw[0].">".$raw[4]."</td>                
            </tr>
			<input id=\"intPan\" type=\"hidden\" value=".$raw[5].">";
		} 
		
	  echo "</tbody>
	</table>";
?>

