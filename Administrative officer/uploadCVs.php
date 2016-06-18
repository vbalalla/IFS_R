<?php 
require_once("Sql.php");
require_once("GlobalVariables.php");
require_once("RecruitmentSession.php");
$sessionID="sesID";
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

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Upload CVs</title>

<link href="css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
	.asideLeftIcons {
		margin-left: 9%;
		width: 70%;
		margin-bottom: 5%;
		text-align: left;
	}

	#uploadCVAddCVButton {
		background-image: url(images/recruitmentSession/createNew.png);
		padding-top: 11px;
		padding-bottom: 15px;
		background-color: transparent;
		background-repeat: no-repeat;
		padding-left: 3%;
		-webkit-transition: all 0.3s ease;
		-o-transition: all 0.3s ease;
		transition: all 0.3s ease;
	}
	#uploadCVUploadCVButton {
		background-image: url(images/recruitmentSession/upload.png);
		padding-top: 11px;
		padding-bottom: 15px;
		background-color: transparent;
		background-repeat: no-repeat;
		padding-left: 6%;
		-webkit-transition: all 0.3s ease;
		-o-transition: all 0.3s ease;
		transition: all 0.3s ease;
	}
	#viewCVs {
		background-image: url(images/recruitmentSession/interview.png);
		padding-top: 11px;
		padding-bottom: 15px;
		background-color: transparent;
		background-repeat: no-repeat;
		width: 20%;
		padding-left: 6%;
		-webkit-transition: all 0.3s ease;
		-o-transition: all 0.3s ease;
		transition: all 0.3s ease;
	}
	.tableShowCVToAdd {
		border-color: #FFFFFF;
		border-style: hidden;
		border-collapse: collapse;
		background-color: #FFFFFF;
		font-family: "OpenSans Regular";
		color: #281A2B;
		text-align: left;
		margin-top: 2%;
	}
	#uploadCVAddCVButton:hover {
		background-image: url(images/recruitmentSession/createNew2.png);
	}
	#uploadCVUploadCVButton:hover {
		background-image: url(images/recruitmentSession/upload1.png);
	}
	#viewCVs:hover {
		background-image: url(images/recruitmentSession/interview1.png);
	}
</style>

<script>
	function _(el){
		return document.getElementById(el);
	}
	function uploadFile(){
		var file = _("customFile").files[0];
		// alert(file.name+" | "+file.size+" | "+file.type);
		var formdata = new FormData();
		formdata.append("customFile", file);
		var ajax = new XMLHttpRequest();
		ajax.upload.addEventListener("progress", progressHandler, false);
		ajax.addEventListener("load", completeHandler, false);
		ajax.addEventListener("error", errorHandler, false);
		ajax.addEventListener("abort", abortHandler, false);
		ajax.open("POST", "UploadCVs.php");
		ajax.send(formdata);
	}
	function progressHandler(event){
		_("loaded_n_total").innerHTML = "Uploaded "+event.loaded+" bytes of "+event.total;
		var percent = (event.loaded / event.total) * 100;
		_("progressBar").value = Math.round(percent);
		_("status").innerHTML = Math.round(percent)+"% uploaded... please wait";
	}
	function completeHandler(event){
		_("status").innerHTML = event.target.responseText;
		_("progressBar").value = 0;
	}
	function errorHandler(event){
		_("status").innerHTML = "Upload Failed";
	}
	function abortHandler(event){
		_("status").innerHTML = "Upload Aborted";
	}
		
</script>


</head>

<body onload="fileUploadFunction()">
<div>
  <header>
    <aside class="asideRight">
      <form action="SearchInterface.php" method="get">
        <input name="Search" type="search" class="searchbox" ><img src="images/searchIcon.png" width="15" height="15" alt=""/>
        <a href="index.php" class="navHome"> Home</a>
        <a href="help.php" class="navHelp">Help </a>
      </form>
    </aside>
    <aside class="asideLeft"></aside> 
  </header>
  
  <aside class="asideLeftIcons">
	  <nav>
		<a href="candidateList.php?rsid=<?php if(isset($_GET["id"])){echo $_GET["id"];}?>&rname=<?php echo $rName?>&rjob=<?php echo $rJob?>&rdate=<?php echo $rDate?>&rstatus=<?php echo $rStatus?>" class="aBack">Back</a> 
		<a href="recruitmentSessionHelp.php" class="aHelp">Help</a>
	  </nav>
  </aside>
  <form action="UploadCVs.php?id=<?php if(isset($_GET["id"])){echo $_GET["id"];}?>&rname=<?php echo $rName?>&rjob=<?php echo $rJob?>&rdate=<?php echo $rDate?>&rstatus=<?php echo $rStatus?>" enctype="multipart/form-data" method="post">
  <div class="divDarkRectangle">
  <button id="uploadCVAddCVButton" value="Add CVs">
	  <span>Add CVs</span>
	  <input id="customFile" onchange="fileUploadFunction()" value="Add CVs" style="position:absolute;z-index:2;top:5;left:0;opacity:0;background-color:transparent;color:transparent;" accept="application/pdf,.docx" data-filename-placement="inside" type="file" class="file_input" name="fileToUpload[]" multiple>
	  <!--<input id="customFile" onchange="fileUploadFunction()" value="Add CVs" style="position:absolute;z-index:2;top:5;left:0;opacity:0;background-color:transparent;color:transparent;" accept="application/pdf,.docx" data-filename-placement="inside" type="file" class="file_input" name="fileToUpload[]" multiple>-->
  </button>	  
  <button id="uploadCVUploadCVButton" value="Upload CVs">
	  <span>Upload CVs</span>
	  <input id="submitBtn" onclick="uploadFile()" style="position:absolute;z-index:2;top:5;left:0;opacity:0;background-color:transparent;color:transparent;" name="submit" type="submit"/>
  </button>
  <a href="candidateList.php?rsid=<?php if(isset($_GET["id"])){echo $_GET["id"];}?>&rname=<?php echo $rName?>&rjob=<?php echo $rJob?>&rdate=<?php echo $rDate?>&rstatus=<?php echo $rStatus?>"><input type="button"  id="viewCVs" value="View Uploads" ></a>
  
  </form>
	<!--  <progress max="100" value="" class="progressBar"> </progress>
	  
	  <table width="99%" border="1" cellpadding="5" class="tableShowCVToAdd">
	  <tbody>
		<tr>
		  <th scope="col"><input type="checkbox">&nbsp;</th>
		</tr>
		<tr>
		  <td><input type="checkbox">&nbsp;</td>
		</tr>
	  </tbody>
	</table> -->
<!--<progress id="progressBar" value="0" max="100" style="width:400px;"></progress>-->
  <h3 id="status"></h3>
  <p id="loaded_n_total"></p>
	
	
	<ul id="fileList" class="list-group"></ul>
  </div>
	<script>
		function fileUploadFunction(){
			var temp = document.getElementById("feedBack");
			
			//if (temp != null) {
				//temp.parentNode.removeChild(temp);
			//}
			var x = document.getElementById("customFile");
			var txt = "";
			var list = document.getElementById("fileList");
			if ('files' in x) {
				if (x.files.length == 0) {
					txt = "Select files to upload.";
				} else {
					for (var i = 0; i < x.files.length; i++) {
						var listItem = document.createElement('li');
						listItem.setAttribute('class','list-group-item');
						txt += "<strong>File " + (i+1) + "</strong><br>";
						var file = x.files[i];
						if ('name' in file) {
							txt += "<strong>File name: </strong>" + file.name +" "+" "+" " ;
						}
						if ('size' in file) {
							txt += "<strong>Size: </strong>" + file.size + " bytes <br>";
						}
						listItem.innerHTML = txt;
						list.appendChild(listItem);
						txt = "";
					}
				}
			} 
			else {
				if (x.value == "") {
					txt += "Select files to upload.";
				} else {
					txt += "The files property is not supported by your browser!";
					txt  += "<br>The path of the selected file: " + x.value; // If the browser does not support the files property, it will return the path of the selected file instead. 
				}
			}
		}
	</script>
<?php

if(isset($_GET["id"])){
	$sessionID = $_GET["id"];
}
if(isset($_FILES['fileToUpload'])){
	$file = $_FILES['fileToUpload'];
    $fileCount = count($file["name"]);
	
	//Used only for viewing purposes
	$text = "";
	
	$s = new Sql();
	$connectValue = $s->connectToDatabase($databaseName);
	
    for($i = 0; $i < $fileCount; $i++){
		$cv = $s->createNewCV($connectValue, $i);
		$candidate = null;
		//$sessionID = $_SESSION['rSessionID'];
		//if (isset($_COOKIE['rSessionID'])){
			//$sessionID = $_COOKIE['rSessionID'];
		//}
		
		//echo $_GET["id"];
		//$sessionID = $_GET["id"];
		
	//	$sessionID="RS055";	
		//print_r($cv);
		echo "<div>";
		echo "<ul id=\"feedBack\" class=\"list-group\">";
		
		if($cv != null){
			$candidate = $s->createNewCandidate($connectValue, $cv, $sessionID);
			
			//echo ($candidate->getCandStatus());
			if(($candidate->getCandStatus())==1){
				echo "<li  class=\"list-group-item list-group-item-success\">".$file["name"][$i]." got rejected due to threshold period issue</li>";
			}else{
				echo "<li  class=\"list-group-item list-group-item-success\">".$file["name"][$i]." upload is successful</li>";
			}
			
		}else{
			echo "<li class=\"list-group-item list-group-item-danger\">".$file["name"][$i]." upload is unsuccessful</li>";
		}
		echo "</ul>";
		echo "</div>";
		
		if($candidate != null){
			$candID = $candidate->getCandID();
			$nic = $candidate->getNIC();
			$firstName = $candidate->getFirstName();
			$lastName = $candidate->getLastName();
			$dob = $candidate->getDateOfBirth();
			$email = $candidate->getEmail();
			$contactNo = $candidate->getContactNo();
			$university = $candidate->getUniversity();
			
			$text = $text."\n".($i+1)."  ".$candID."  ".$nic."  ".$firstName." ".$lastName."  ".$dob."  ".$email." ".$contactNo." ".$university;
		}		
	}
	
	//header("Location: UploadCVs.php");
	//die();
}

?>

  
<footer>Copyright 2015 &copy;</footer>

</div>
</body>
</html>
