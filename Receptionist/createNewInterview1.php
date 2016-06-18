<?php
  include_once('../phpSessions.php');
  if($_SESSION['type']=='admin_officer')
    header("location: ../Administrative officer/index.php");
  
  if($_SESSION['type']=='interview_panel')
    header("location: ../Interview panel member/index.php");
  
	if($_SESSION['type']=='system_admin')
    header("location: ../System administrator/systemAdministratorHome.php");

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Create New Interview</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico"/>

<script type="text/javascript" src="js/sortable.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.js"></script>
<script src="jquery/jquery.js"></script>
<script src="jquery-ui-1.11.4/jquery-ui.js"></script>
<script src="jquery-ui-1.11.4/jquery-ui.css"></script>
<script src="jquery-ui-1.11.4/jquery.min.js"></script>
<script src="jquery-ui-1.11.4/jquery-ui.min.js"></script>

<link href="css/formStyle.css" rel="stylesheet" type="text/css">
<link href="css/style.css" rel="stylesheet" type="text/css">
<link href="css/tableStyle.css" rel="stylesheet" type="text/css">
<style type="text/css">
	.asideLeftIcons {
		margin-left: 9%;
		width: 70%;
		margin-bottom: 5%;
		text-align: left;
	}

	#interviewPanelAddNewPanel {
	background-image: url(images/recruitmentSession/interviewPanel.png);
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	padding-left: 3%;
	width: 34%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
	}
	#interviewPanelAddMemberBtn {
	background-image: url(images/recruitmentSession/createNew.png);
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	padding-left: 4%;
	width: 22%;
	-webkit-transition: all 0.3s;
	-o-transition: all 0.3s;
	transition: all 0.3s;
	}
#interviewPanelEditMemberBtn {
	background-image: url(images/recruitmentSession/edit.png);
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	padding-left: 4%;
	width: 22%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
}
#interviewPanelAddNewPanel:hover {
	background-image: url(images/recruitmentSession/interviewPanel1.png);
}
#interviewPanelAddMemberBtn:hover {
	background-image: url(images/recruitmentSession/createNew2.png);
}
#interviewPanelEditMemberBtn:hover {
	background-image: url(images/recruitmentSession/edit1.png);
}
.textInputInterviewPanel {
	color: #BEA7AA;
	font-family: "OpenSans Regular";
	font-weight: 100;
	font-size: 0.85em;
	text-align: left;
	padding-left: 1%;
	width: 27%;
	margin-left: 1%;
	}
#InteviewPanelSaveBtn {
	background-image: url(images/recruitmentSession/submit.png);
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	padding-left: 5%;
	width: 11%;
	margin-right: 4%;
	background-color: #E9E1E1;
	margin-left: 9%;
	margin-bottom: 5%;
	margin-top: 1%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
}
#interviewPanelCancelBtn {
	background-image: url(images/recruitmentSession/cancel.png);
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	padding-left: 3%;
	width: 15%;
	margin-left: 4%;
	background-color: #E9E1E1;
	margin-bottom: 5%;
	margin-top: 1%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
}
#interviewSchedule {
	background-image: url(images/recruitmentSession/interviewPanel.png);
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	padding-left: 3%;
	width: 15%;
	margin-left: 4%;
	background-color: #E9E1E1;
	margin-bottom: 5%;
	margin-top: 1%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
}
#InteviewPanelSaveBtn:hover {
	background-image: url(images/recruitmentSession/submit2.png);
}
#interviewPanelCancelBtn:hover {
	background-image: url(images/recruitmentSession/cancel1.png);
}
#interviewSchedule:hover {
	background-image: url(images/recruitmentSession/interviewPanel1.png);
}
#interviewPanelNameTable{
	margin-bottom: 3%;
	border-style: hidden;
	border-color: #E9E1E1;
	border-collapse: collapse;
	margin-top: 3%;
	text-align: left;
	padding-left: 2%;
	font-size: 1.2em;
}
</style>

<?php 
$rSessionID="";
if(isset($_GET["rsid"])){
	$rSessionID=$_GET["rsid"];
}
$rJob="";
if(isset($_GET["rjob"])){
	$rJob=$_GET["rjob"];
}
?>

<script>
/*jQuery(function(){
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
});*/
jQuery(function(){
    //var counter = 1;
    $('#add-row').click(function(){
		var newRow = $('#tableCommon tbody tr').last().clone().appendTo($('#tableCommon tbody'));
		var newIndex  = newRow.index();
		newRow.find(':input').each(function(){
			$(this).val('');
			$(this).attr('name', $(this).attr('name').replace(/\d+/, newIndex));
		});
	});
});

$(document).ready(
function() 
{
    $('#InteviewPanelSaveBtn').click(function(){
				
		$.post('CreateInterview.php',$('#tableCommon :input').serialize());
		
		$.post("CreateInterview.php",
		    {
				interviewname:$('#interviewName').val(),
				intPanID:$('#intPan').val(),
				duration:$('#dur').val(),
				rsid:"<?php echo $rSessionID; ?>"
				
			},
			function(data)
			{
				//alert($('#dur').val());
			}
		);
		//window.location.replace("CreateInterview.php");
		window.location.replace("interviews.php?rsid="+"<?php echo $rSessionID; ?>");
				
	});	
}
);

</script>

</head>

<body>
<div>
  <header>
    <aside class="asideRight">
        <span>
            <b id="welcome">Welcome : <i><a href="myAccount.php" style="color: #ffffff"><?php echo $login_session; ?></a></i></b>
            <b id="logout"><a href="../logout.php">Log Out</a></b>
        </span>

      <form action="SearchInterface.php" method="get">
        <input name="Search" type="search" class="searchbox" ><img src="images/searchIcon.png" width="15" height="15" alt=""/>
        <a href="recruitmentSessionInterface.php" class="navHome"> Home</a>
        <a href="../UserManual/UserManual.html" class="navHelp">Help </a>
      </form>

    </aside>
    <aside class="asideLeft"></aside>
  </header>
  
  <aside class="asideLeftIcons">
	  <nav>
		<a href="interviews.php?rsid=<?php echo $rSessionID;?>" class="aBack">Back</a> 
		<a href="recruitmentSessionHelp.php" class="aHelp">Help</a>
	  </nav>
  </aside>
  
  <table width="84%" border="1" align="center" id="interviewPanelNameTable">
  <tbody>
    <tr>
      <th width="16%" scope="row">Interview Name :</th>
      <td width="84%"><input id="interviewName" type="text" class="textInputInterviewPanel" placeholder="Enter interview name"></td> <!-- This column is for Interview name -->
    </tr>
  </tbody>
</table>

	
	<?php
	
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
		
		function changeInterface(){
			$res="";
			if(isset($_POST["results"])){
				$res=$_POST["results"];
				//echo "reulst ar attached";
			}else{
				//echo "So sorry";
			}
			//while($row = $res->fetch_row()) { 
				//echo $row[0];
			//}
		}
	
	?>
	
<script>
jQuery(function(){
    $( "#interviewPanelName" ).change(function() {
		var intPanID = $('#interviewPanelName').val();
		
		var str=intPanID;
			if (str == "") {
				document.getElementById("panelTable").innerHTML = "";
				return;
			} else { 
				if (window.XMLHttpRequest) {
					// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp = new XMLHttpRequest();
				} else {
					// code for IE6, IE5
					xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						document.getElementById("panelTable").innerHTML = xmlhttp.responseText;
					}
				};
				xmlhttp.open("GET","changeInterviewPanel.php?intPanel="+str,true);
				xmlhttp.send();
			}
		
				
		//alert(intPanID);
		/*
		$.post("changeInterviewPanel.php",
		    {
				intPanel:$('#interviewPanelName').val()
								
			},
			function(data)
			{
				alert("Is this working");
				
			}
		);*/
		//window.location.replace("changeInterviewPanel.php");
		
	});
});
</script>
<?php //changeInterface(); ?>
  
  <div class="divDarkRectangle">
  	<strong class="headingsInADiv"> Interview Panel</strong><br/><hr/><br/>
    <input type="button" id="interviewPanelAddNewPanel" value="Select an Interview Panel">
	
	<select href="#" id="interviewPanelName" class="formSelect" method="post" required>
	<?php 
	$intPanel = mysqli_query($connect, "SELECT * FROM interviewpanel");
	while($row = $intPanel->fetch_row()) { 
		if($row[2]=='default'){
	?>		
	<option id=<?php echo $row[0] ?> value=<?php echo $row[0] ?> selected ><?php echo $row[1] ?></option>
	<?php }else{ ?>
	<option id=<?php echo $row[0] ?> value=<?php echo $row[0] ?> ><?php echo $row[1] ?></option>
	<?php } }?>
	</select>
<table width="99%" border="1" cellpadding="5" class="sortable" id="tableCommon1" >
	  <tbody>
		<tr>
		  <th width="37%" scope="col">Name</th>
		  <th width="37%" scope="col">email</th>
		  <th width="26%" scope="col">Contact No</th>
		</tr>
		<?php					
		$data = mysqli_query($connect, "SELECT employee.EmpID, FirstName, LastName, email, TelNo, interviewpanel.IntPanID FROM employee,interviewpanelmemberdetails,interviewpanel WHERE employee.EmpID=interviewpanelmemberdetails.EmpID AND interviewpanelmemberdetails.IntPanID=interviewpanel.IntPanID AND interviewpanel.status='default'");
		while($raw = $data->fetch_row()) { ?>
		<!--<script> var id = <?php echo(json_encode($raw[0])); ?>;</script>--->
            <tr>                
                <td class="tableData" name="name1" id = <?php echo $raw[0] ?> ><?php echo $raw[1]." ".$raw[2] ?></td>
                <td class="tableData" name="email1" id = <?php echo $raw[0] ?> ><?php echo $raw[3]?></td>
                <td class="tableData" name="contact1" id = <?php echo $raw[0] ?> ><?php echo $raw[4] ?></td>                
            </tr>
			<input id="intPan" type="hidden" value=<?php echo $raw[5];?>>
		<?php } ?>
		
	  </tbody>
	</table>
    <!--<input type="button" id="interviewPanelAddMemberBtn" value="Add Member">
    <input type="button" id="interviewPanelEditMemberBtn" value="Edit Member">-->

  </div>
  
  <br/><br/>
  
  <div class="divDarkRectangle" >
  <strong class="headingsInADiv"> Convenient Time Slots</strong><br/><hr/>
 	<table width="99%" border="1" cellpadding="5" class="sortable" id="tableCommon">
	  <tbody>
		<tr>
		  <th width="34%" scope="col">Date</th>
		  <th width="33%" scope="col">From</th>
		  <th width="33%" scope="col">To</th>
		</tr>
		<tr>
		<td><input id="d[0]" type="date" name="schedule[0][date]" required/></td>
		<td><input id="d[0]" type="time" name="schedule[0][from]" required/></td>
		<td><input id="d[0]" type="time" name="schedule[0][to]" required/></td>
		</tr>
	  </tbody>
	</table>
 	<a href="#" id="add-row" class="addmore"><strong> Add Time Slot </strong></a>
	<strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Interview duration : </strong>
    <input type="text" id="dur" class="textInputInterviewPanel" placeholder="Interview Duration per Candidate"><strong>&nbsp;minutes</strong><br/>
    </div>
    
  <input type="button" id="InteviewPanelSaveBtn" value="Save"/>
  <input type="button" id="interviewPanelCancelBtn" value="Cancel"/>
  <a href="interviewSchedule.php?rsid=<?php echo $rSessionID?>&rjob=<?php echo $rJob?>"><input type="button" id="interviewSchedule" value="Schedule"/> 
<footer>Copyright 2015 &copy;</footer>

</div>
</body>
</html>
