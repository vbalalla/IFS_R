<?php //session_start();
require_once("Sql.php");
require_once("GlobalVariables.php");
require_once("RecruitmentSession.php");
$sessionID="sesID";
?>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="css/bootstrap.min.css" rel="stylesheet">

	<!--<script src="jquery-2.1.3.min.js"></script>-->
	
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/bootstrap.file-input.js"></script>
	
	<script src="jquery-ui-1.11.4/jquery-ui.js"></script>
    <title>CV & Recruitment Management System</title>
	
	<link href="css/bootstrap.min.css" rel="stylesheet">

    <!--<link href="css/cbr.css" rel="stylesheet">-->
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="jquery/jquery.js"></script>
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

	<nav class="navbar navbar-default" >  
        <div class="container">
            
                <ul class="nav navbar-nav">
					<li><a href="home.php">Home</a></li>
					<li><a href="sessions.php">Sessions</a></li>
					<li><a href="reports.php">Reports</a></li>
					<li class="active"><a href="UploadCVs.php">Input CVs</a></li>
				</ul>   
            
          </div>
		  
    </nav>


<div class="container">
	<h1 align="center">Input CVs</h1>
	<hr>
	<form action="UploadCVs.php?id=<?php if(isset($_GET["id"])){echo $_GET["id"];}?>" enctype="multipart/form-data" method="post">
		<div class="btn-group">
			<span class="btn btn-success fileinput-button">
				<i class="glyphicon glyphicon-plus"></i>
				<span>Add files ...</span>
				<input id="customFile" onchange="fileUploadFunction()" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' accept="application/pdf,.docx" data-filename-placement="inside" type="file" class="file_input" name="fileToUpload[]" multiple>
			</span>
	
			<button type="submit" class="btn btn-primary start">
				<i class="glyphicon glyphicon-upload"></i>
				<span>Start upload</span>
				<input id="submitBtn" onclick="uploadFile()" name="submit" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' type="submit"/>
			</button>		
		</div>	
	</form>
	<progress id="progressBar" value="0" max="100" style="width:400px;"></progress>
  <h3 id="status"></h3>
  <p id="loaded_n_total"></p>
	
	
	<ul id="fileList" class="list-group"></ul>
</div>

<script>
		function fileUploadFunction(){
			var temp = document.getElementById("feedBack");
			
			if (temp != null) {
				temp.parentNode.removeChild(temp);
			}
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
//echo $_GET["id"];

if(isset($_GET["id"])){
	$sessionID = $_GET["id"];
	//$_GET["id"]=$sessionID;
	//echo $sessionID;
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
		
		echo "<ul id=\"feedBack\" class=\"list-group\">";
		if($cv != null){
			$candidate = $s->createNewCandidate($connectValue, $cv, $sessionID);
			echo "<li  class=\"list-group-item list-group-item-success\">".$file["name"][$i]." upload is successful</li>";
		}else{
			echo "<li class=\"list-group-item list-group-item-danger\">".$file["name"][$i]." upload is unsuccessful</li>";
		}
		echo "</ul>";
		
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
</body>