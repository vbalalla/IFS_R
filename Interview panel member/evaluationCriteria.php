<!-- Interview panel member -->
<?php
  include('../phpSessions.php');
  if($_SESSION['type']=='admin_officer')
    header("location: ../Administrative officer/index.php");
  
  if($_SESSION['type']=='receptionist')
    header("location: ../Receptionist/recruitmentSessionInterface.php");

	if($_SESSION['type']=='system_admin')
    header("location: ../System administrator/systemAdministratorHome.php");
  
  ?>


<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Create Evaluation Criteria</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico"/>

<script type="text/javascript" src="js/sortable.js"></script>
<link href="css/style2.css" rel="stylesheet" type="text/css">
<style type="text/css">
	.asideLeftIcons {
		margin-left: 9%;
		width: 70%;
		margin-bottom: 5%;
		text-align: left;
	}
	#add {
	background-image: url(images/recruitmentSession/createNew.png);
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	padding-left: 4%;
	width: 18%;
	background-color: #E9E1E1;
	margin-left: 9%;
	margin-bottom: 3%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
	}
	#remove {
		background-image: url(images/recruitmentSession/Delete.png);
		padding-top: 11px;
		padding-bottom: 15px;
		background-repeat: no-repeat;
		padding-left: 4%;
		width: 19%;
		background-color: #E9E1E1;
		margin-bottom: 3%;
		-webkit-transition: all 0.3s ease;
		-o-transition: all 0.3s ease;
		transition: all 0.3s ease;
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
	.headingsNormal {
		margin-left: 9%;
		font-size: 1.2em;
	}
	#add:hover {
		background-image: url(images/recruitmentSession/createNew2.png);
	}
	#remove:hover {
		background-image: url(images/recruitmentSession/Delete1.png);
	}
	.createNewPanelTextBox {
		width: 30%;
		font-family: "OpenSans Regular";
		font-size: 0.85em;
		padding-left: 1%;
		margin-left: 3%;
	}
	input[type=submit] {
	padding-left: 8%;
	padding-right: 5px;
	color: #281A2B;
	background-image: url(images/recruitmentSession/submit.png);
	background-repeat: no-repeat;
	padding-top: 11px;
	padding-bottom: 24px;
	width: 18%;
	margin-right: 4%;
	font-family: "OpenSans Regular";
	border-style: none;
	border-color: #E9E1E1;
	background-color: #dbccce;
	font-weight: 600;
	font-size: 1.2em;
	margin-left: 2%;
	margin-top: 3%;
	-webkit-transition: all 0.3s ease 0s;
	-o-transition: all 0.3s ease 0s;
	transition: all 0.3s ease 0s;
	}
	input[type=reset] {
	padding-right: 5px;
	color: #281A2B;
	background-image: url(images/recruitmentSession/cancel.png);
	background-repeat: no-repeat;
	padding-top: 11px;
	padding-bottom: 24px;
	width: 18%;
	margin-right: 15%;
	font-family: "OpenSans Regular";
	border-style: none;
	border-color: #E9E1E1;
	background-color: #dbccce;
	font-weight: 600;
	font-size: 1.2em;
	margin-left: 3%;
	margin-top: 3%;
	padding-left: 8%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
	}
	
	#evaluationCriteriaSaveBtn {
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
	#evaluationCriteriaCancelBtn {
	background-image: url(images/recruitmentSession/cancel.png);
	padding-top: 11px;
	padding-bottom: 15px;
	background-repeat: no-repeat;
	padding-left: 3%;
	width: 15%;
	margin-left: 3%;
	background-color: #E9E1E1;
	margin-bottom: 5%;
	margin-top: 1%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
	}
	
	#evaluationCriteriaSaveBtn:hover {
	background-image: url(images/recruitmentSession/submit2.png);
	}
	#evaluationCriteriaCancelBtn:hover {
	background-image: url(images/recruitmentSession/cancel1.png);
	}

	input[type=submit]:hover {
		background-image: url(images/recruitmentSession/submit2.png);
	}
	input[type=reset]:hover {
		background-image: url(images/recruitmentSession/cancel1.png);
	}
</style>

</head>

<?php
$rSessionID="";
		if(isset($_GET["rsid"])){
				$rSessionID=$_GET["rsid"];
		}	
		
		$intvID="";
		if(isset($_GET["int"])){
				$intvID=$_GET["int"];
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
?>

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
        <a href="index.php" class="navHome"> Home</a>
        <a href="../UserManual/UserManual.html" class="navHelp">Help </a>
      </form>

    </aside>
    <aside class="asideLeft"></aside>
  </header>
  
  <aside class="asideLeftIcons">
	  <nav>
		<a href="index.php" class="aBack">Back</a> 
		<a href="evaluationCriteriaHelp.php" class="aHelp">Help</a>
	  </nav>
  </aside>
  
 <form action="test1.php?rsid=<?php echo $rSessionID?>&rname=<?php echo $rName?>&rjob=<?php echo $rJob?>&rdate=<?php echo $rDate?>&rstatus=<?php echo $rStatus?>" method="post" name="f">
  <br>
  <br>
  <strong class="headingsNormal">Evaluation Criteria Name : </strong>
  
  <input type="text" name="criteria" class="createNewPanelTextBox" placeholder="Criteria Name">
  <br/>
  <br/>
  
  <INPUT type="button" id="add" value="Add Row" onClick="addRow('dataTable')" />

<INPUT type="button" id="remove" value="Delete Row" onClick="deleteRow('dataTable')" />

 
  
  
  <table width="83%" border="1" align="center" class="table-editable" id="tableCommon">
  <thead>
    <tr>
      <th width="5%" scope="col"></th>
      <th width="35%" scope="col">Name</th>
      <th width="30%" scope="col">Data type</th>
      <th width="30%" scope="col">Weight %</th>
    </tr>
   </thead>
<tbody id="dataTable">


  </tbody>
</table>


<input name="submit" type="submit" id="evaluationCriteriaSaveBtn" value="Save" href="evaluationCriteria.php"/>
<input type="reset" id="evaluationCriteriaCancelBtn" value="Cancel"/>
</form>

<script>

  function addRow(tableID) { 

        var table = document.getElementById(tableID);

        var rowCount = table.rows.length;
        var row = table.insertRow(rowCount);

        var cell1 = row.insertCell(0);
        var element1 = document.createElement("input");
        element1.type = "checkbox";
        element1.name="chkbox[]";
        cell1.appendChild(element1);

        var cell2 = row.insertCell(1);
        cell2.innerHTML = "<input type='text' name='name[]'>";

        var cell3 = row.insertCell(2);
        cell3.innerHTML = "<input type='text'  name='dataType[]' />";

        var cell4 = row.insertCell(3);
        cell4.innerHTML =  "<input type='text'  name='weight[]' />";
        }
		
	function deleteRow(tableID) {
        try {
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;

        for(var i=0; i<rowCount; i++) {
            var row = table.rows[i];
            var chkbox = row.cells[0].childNodes[0];
            if(null != chkbox && true == chkbox.checked) {
                table.deleteRow(i);
                rowCount--;
                i--;
            }
        }
        }catch(e) {
            alert(e);
        }
    }
</script>


<footer>Copyright 2015 &copy;</footer>

</div>
</body>
</html>
