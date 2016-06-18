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
 
<?php
  require_once("GlobalVariables.php");
  global $db, $user, $pass;

  try {
    $dbh= new PDO($db,$user,$pass);

    $sql = $dbh->prepare("SELECT RSID, name, dateCreated, jbName, sessionStatusID FROM RecruitmentSession LEFT JOIN  jobpositon ON jobPositionID=jbID");

    if($sql->execute()) {
       $sql->setFetchMode(PDO::FETCH_ASSOC);
    }
  }
  catch(Exception $error) {
      echo '<p>', $error->getMessage(), '</p>';
  }

?>
   

</head>

<body>

    <nav class="navbar navbar-default" >  
        <div class="container">
            
                <ul class="nav navbar-nav">
					<li><a href="home.php">Home</a></li>
					<li class="active"><a href="sessions.php">Sessions</a></li>
					<li><a href="reports.php">Reports</a></li>
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
				<button type="submit" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-search"></span><i class="icon-search"></i></button>
				
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
			

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <a href="RecruitmentSessionForm.php"><button class="btn btn-block btn-lg">Create a Recruitment Session</button></a>
            </div>
            <div class="col-md-4"></div>
        </div>
		
		<div class="row"></div>
		
		<div class="row" align="center">
        <div class="col-md-8" align="center">
            <div class="panel-group">
                <div class="page-header"><h2 align="center">Recruitment Sessions<h2/></div>
                <div class="panel-body">
                    <ul class="nav nav-pills nav-stacked">
                      <?php while($row = $sql->fetch()) { ?>       
                        
                         <li><a href="candidateList.php?rsid=<?php echo $row['RSID']?>"><?php echo "<pre><span class=\"inner-pre\" style=\"font-size: 16px; color: blue\"\">".$row['name']."\t\t".$row['dateCreated']."\t\t".$row['jbName']."</span></pre> "; ?></a></li>
                         <?php }?>
                        
                    

                    </ul>
                </div>
            </div>

        </div>
		
		<div class="col-md-3" align="center">
			<br><br><br><br><br><br><br>
			<select class="form-control" name="value"  id="sortOptions">
				<option value="">Select sort field</option><br />
				<option value="field1"> Name  </option><br />
				<option value="field2"> Date Created  </option><br />
				<option value="field3"> Job Position </option><br />
			</select>
			<br>
			<button type="button" class="btn btn-success">Sort Sessions</button>
		</div>
        

		</div>	
			
                
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
