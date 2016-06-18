<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Create Interview</title>
<script src="js/jquery.min.js"></script>
<script>
jQuery(function(){
    var counter = 1;
    jQuery('a.add-member').click(function(event){
        event.preventDefault();
        counter++;
        var newRow = jQuery('<tr><td><input id="empid"' + counter + '" type="text" name="empid"' +
            counter + '" value=""/></td><td><input type="text" name="name' +
            counter + '" value=""/></td><td><input type="text" name="email' +
            counter + '" value=""/></td><td><input type="text" name="contact' +
            counter + '" value=""/></td></tr>');
        jQuery('table.panel-list').append(newRow);
    });
});
jQuery(function(){
    var counter = 1;
    jQuery('a.addmore').click(function(event){
        event.preventDefault();
        counter++;
        var newRow = jQuery('<tr><td><input type="text" name="date' +
            counter + '" value=""/></td><td><input type="text" name="time' +
            counter + '" value=""/></td></tr>');
        jQuery('table.datetime').append(newRow);
    });
});

$(document).ready(
function() 
{
    $('#btnID').click(function(){
		var table = document.getElementById("panel");
		
		for (var i = 0, row; row = table.rows[i]; i++) {
			alert(document.getElementById("empid" + i));
			$.post("CreateInterview.php",
		    {
				empID:row.cells[0].getElementByTagName('input')[0]//,
				//value:$('#jbposition').val()
				
			},
			function(data,status){
				//alert("Data: " + data + "\nStatus: " + status);
			}	
			);
		}
				
	});
}
);

</script>
</head>
<body>
<div class="divInterviewDetails">
  <table width="50%" border="1" cellpadding="5" class="tabelInterviewDetails">
  <tbody>
	<?php
		#require_once('SelectInterview.php');
		require_once("Sql.php");
		require_once("GlobalVariables.php");
		$s = new Sql();
		$connect = $s->connectToDatabase($databaseName);
		
		$rSessionID="";
		if(isset($_GET["rsid"])){
				$rSessionID=$_GET["rsid"];
		}
		$rName="";
		if(isset($_GET["rname"])){
				$rName=$_GET["rname"];
		}
		$rJob="";
		if(isset($_GET["rjob"])){
				$rJob=$_GET["rjob"];
		}
		$rDate="";
		if(isset($_GET["rdate"])){
				$rDate=$_GET["rdate"];
		}
		$rStatus="";
		if(isset($_GET["rstatus"])){
				$rStatus=$_GET["rstatus"];
		}
		
		$intType="";
		if(isset($_GET['IntButton'])){ 
			$intType = $_GET['IntButton']; 
		} 
		//echo $intType;
		
	?>
    <tr>	
      <th width="25%" class="thInterviewTablesHeddings" scope="row">Session Name :</th>
      <td width="50%"><?php echo $rName; ?></td>
    </tr>
    <tr>
      <th class="thInterviewTablesHeddings" scope="row">Job Position&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</th>
      <td><?php echo $rJob; ?></td>
    </tr>
    <tr>
      <th class="thInterviewTablesHeddings" scope="row">Date Created&nbsp;&nbsp;:</th>
      <td><?php echo $rDate; ?></td>
    </tr>
	<tr>
      <th class="thInterviewTablesHeddings" scope="row">Status&nbsp;&nbsp;&nbsp;:</th>
      <td><?php echo $rStatus; ?></td>
    </tr>
	<tr>
      <th class="thInterviewTablesHeddings" scope="row">Interview Type&nbsp;&nbsp;&nbsp;:</th>
      <td><?php echo $intType; ?></td>
    </tr>
  </tbody>
</table>
</div>
<br><br>
<div class="divAddPanel">
<form method="post" action=<?php echo "CreateInterview.php?rsid=".$rSessionID.'&rname='.$rName.''?>>
  Interview Panel Members:
  <a href="AddNewPanel.php"><input type="button" name="newPanel" value="Add New Panel" style="float: right;"></a>
  <br><br>
  <table id="panel" class="panel-list" border="1" style="width:90%">
	  <thead>
		<tr>
			<th>EmpID</th>
			<th>Name</th>
			<th>Email</th>
			<th>Contact</th>
		</tr>
	  </thead>
	  <tbody>
     <?php					
		$data = mysqli_query($connect, "SELECT employee.EmpID,FirstName, LastName, email, TelNo FROM employee,interviewpanelmemberdetails,interviewpanel WHERE employee.EmpID=interviewpanelmemberdetails.EmpID AND interviewpanel.status='default'");
		while($raw = $data->fetch_row()) { ?>
		<!--<script> var id = <?php echo(json_encode($raw[0])); ?>;</script>--->
            <tr>
                <td class="tableData" name="empid1" id = <?php echo $raw[0] ?> ><?php echo $raw[0] ?></td>
                <td class="tableData" name="name1" id = <?php echo $raw[0] ?> ><?php echo $raw[1]." ".$raw[2] ?></td>
                <td class="tableData" name="email1" id = <?php echo $raw[0] ?> ><?php echo $raw[3]?></td>
                <td class="tableData" name="contact1" id = <?php echo $raw[0] ?> ><?php echo $raw[4] ?></td>
                
            </tr>
    <?php } ?>
  </tbody>
  </table>
  
  
  <div style="text-align: left">         
        <a href="#" title="" class="add-member">Add Member</a>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="#" title="" class="edit-member">Edit Member</a>    
   </div><br>
   
   <table class="datetime" border="1" style="width:50%">
	  <thead>
		<tr>
			<th>Date</th>
			<th>Time</th>
		</tr>
	  </thead>
	  <tbody>
		<tr>
			<td><input id="date" type="text" name="date1" required class="FormTextInput"/></td>
			<td><input id="time" type="text" name="time1" required class="FormTextInput"/></td>
		</tr>
	  </tbody>
   </table>
	
	<div style="text-align: left">         
        <a href="#" title="" class="addmore">Add New</a>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="#" title="" class="edit-date&time">Edit</a>    
   </div><br>
  
  <input id="btnID" type="submit" value="Create">
</form> 
</div>



</body>
</html>