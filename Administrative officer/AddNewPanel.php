<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Add New Panel</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript">
jQuery(function(){
	var counter = 3;
	jQuery('a.add-member').click(function(event){
	event.preventDefault();
        counter++;
        var newRow = jQuery('<tr><td><input type="text" name="empid' +
            counter + '"/></td></tr>');
        jQuery('table.panelList').append(newRow);
    });
});
var x = document.getElementById("panel").rows.length - 1;
document.write(x);

</script>
</head>
<body>
<?php 
	require_once("Sql.php");
	require_once("GlobalVariables.php");
	
	$s = new Sql();
	$connect = $s->connectToDatabase($databaseName);
	
?>
<div class="divNewAddPanel">
<form method="post" action="#">
  Create a new interview panel:
  <br><br>
  <input type="radio" name="pstatus" value="Def"> Default
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <input type="radio" name="pstatus" value="Not-def"> Not-default
  <table id="panel" class="panelList">
	<tr>
		<th>EmpID</th>
	</tr>
	<tr>
		<td><input id="EID1" type="text" name="empid1" required class="FormTextInput"/></td>
	</tr>
	<tr>
		<td><input id="EID2" type="text" name="empid2" required class="FormTextInput"/></td>
	</tr>
	<tr>
		<td><input id="EID3" type="text" name="empid3" required class="FormTextInput"/></td>
	</tr>
  </table>
  
  
  <div style="text-align: left">         
        <div style="text-align: left">         
        <a href="#" title="" class="add-member">Add Member</a>    
   </div><br>  
  
  <input id="btnID1" type="submit" value="Create" href="AddNewPanel.php" >
  <input id="btnID2" type="button" value="Back" href="#" >
  
</form> 
</div>
</body>
</html>