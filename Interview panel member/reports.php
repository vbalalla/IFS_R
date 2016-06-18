<html lang="en">

<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="css/bootstrap.min.css" rel="stylesheet">

   
    <script src="jquery-2.1.3.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <title>CV & Recruitment Management System</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/cbr.css" rel="stylesheet">

    

</head>

<body>

    <nav class="navbar navbar-default" >  
        <div class="container">
            
                <ul class="nav navbar-nav">
					<li><a href="home.php">Home</a></li>
					<li><a href="sessions.php">Sessions</a></li>
					<li class="active"><a href="reports.php">Reports</a></li>
					<li><a href="UploadCVs.php">input CVs</a></li>
				</ul>   
            
          </div>
		  
    </nav>

    <div class="container">

        
        <div class="row">
            <div class="box">
			<!--Search box-->
			<form method = "post" action="search.php" class = "pull-down  navbar-search" align ="right">
				<div class="input-append">
				<input class="search-query input-lg"  name="search_query" type="text" placeholder=" Search" > 
				<button type="submit" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-search"></span><i class="icon-search"></i> Search</button>
				
				<!--Help button-->
				<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-question-sign"></span>  Help</button>
				
				<!--Help description-->
				<div class="modal fade" id="myModal" role="dialog">
					<div class="modal-dialog modal-lg">
					  <div class="modal-content">
						<div class="modal-header">
						  <button type="button" class="close" data-dismiss="modal">&times;</button>
						  <h4 class="modal-title">Help</h4>
						</div>
						<div class="modal-body">
						  <p>Helping factors includes here</p>
						</div>
						<div class="modal-footer">
						  <button type="button" class="btn btn-default" data-dismiss="modal">Back</button>
						</div>
					  </div>
					</div>
				</div>

				</div>
				
			</form>
			
    
    <h2>reports</h2>
    
			
			
			
			
			
                
            </div>
        </div>

       

    </div>
    <!-- /.container -->

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <p>CV & Recruitment Management System</p>
                </div>
            </div>
        </div>
    </footer>


</body>

</html>
