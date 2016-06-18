<?php
$id = "";
if(isset($_GET['id'])){
$id = $_GET['id'];
}

require_once("Sql.php");
$s = new Sql();
$connectValue = $s->connectToDatabase('recruit');

mysqli_query($connectValue, "DELETE FROM Employee WHERE EmpID='$id'");
mysqli_query($connectValue, "DELETE FROM login WHERE EmpID='$id'");

mysqli_close($connectValue);
header("location: AdminUsers.php");

?>