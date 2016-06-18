<?php
require_once("Sql.php");
require_once("GlobalVariables.php");
$s = new Sql();
$connect = $s->connectToDatabase($databaseName);

function changePanel($id,$connect){
	$intPanel = mysqli_query($connect, "SELECT FirstName, LastName, email, TelNo FROM employee,interviewpanelmemberdetails,interviewpanel WHERE employee.EmpID=interviewpanelmemberdetails.EmpID AND interviewpanelmemberdetails.IntPanID=interviewpanel.IntPanID AND interviewpanel.intPanID=$id");
	return $intPanel;
}

$intName = "";
if(isset($_POST["intPanel"])){
	$intName  = $_POST["intPanel"];	
}
echo $_POST["intPanel"];	
$results = changePanel($intName,$connect);
?>

<script src="js/jquery.min.js"></script>
<script src="js/jquery.js"></script>
<script>
$(document).ready(
function() 
{
    
		$.post("createNewInterview.php",
		    {
				results: "<?php echo $results; ?>"
				
			},
			function(data)
			{

			//	$('#aa').val(data); 
			}
		);
	
}
);
</script>