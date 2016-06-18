<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Session Form</title>
<script type="text/javascript" src="/project/js/jquery.js"> </script>
<script type="text/javascript" src="/project/js/script.js"> </script>

    <link href="css/bootstrap.min.css" rel="stylesheet">   
    <script src="js/bootstrap.min.js"></script>
	 
	<script>
		$(document).ready(
		function() 
		{
			$('#btnID').click(function(){
				$.post("main1.php",
					{
						sessionname:$('#sessionID').val(),
						value:$('#jbposition').val()
						
					},
					function(data)
					{
					//	$('#aa').val(data); 
					}
				);
			});
		}
		);	
	</script> 
	
	
	
</head>

<body>


<!--<form method="post" action="RecruitmentSession.php"> -->
<nav class="navbar navbar-default" >  
        <div class="container">
            
                <ul class="nav navbar-nav">
					<li><a href="home.php">Home</a></li>
					<li class="active"><a href="sessions.php">Sessions</a></li>
					<li><a href="reports.php">Reports</a></li>
					<li><a href="UploadCVs.php">Input CVs</a></li>
				</ul>   
            
          </div>
		  
    </nav>

<h1 align="center">Create New Recruitment Session</h1>
<hr>
<div class="col-md-1"></div>
    <div class="col-md-4">
	
    <form role="form" style="padding-top:40px">
        <select class="form-control" name="value"  id="jbposition">
            <option value="">create job position</option><br />
            <option value="jp001"> Software Engineer  </option><br />
            <option value="jp002"> Software Architect  </option><br />
            <option value="jp003"> Business Analysist</option><br />
            <option value="jp004"> Marketing Assistant </option><br />
        </select>        
    
   <br> Session name:<br>
<input  type="text"  class="form-control" id="sessionID"/>
<br>

<input type="button" class="btn btn-default" style="color:white;background-color:darkorchid" value="Input CVs" id="btnID" onclick= "window.location='main1.php';" />

</form>

</div>
<!-- <button onclick="f()">Upload CVs</button> -->

</body>
</html>