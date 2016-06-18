<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!--    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>-->
    <script src="js/bootstrap.min.js"></script>
    <script src="jquery/jquery.js"></script>
    <script src="jquery-ui-1.11.4/jquery-ui.js"></script>
    <script src="jquery-ui-1.11.4/jquery-ui.css"></script>

    <?php require_once("Sql.php");
		require_once("GlobalVariables.php");
	 ?>
<script>

</script>

</head>

<body>
<div class="col-md-1"></div>
<div class="col-md-4" style="padding-top: 20px">

    <?php
	$s = new Sql();
	$connect = $s->connectToDatabase($databaseName);
	$id="";
	if(isset($_GET['id'])){
		$id = $_GET['id'];
	}
	$sessionID="";
    if(isset($_GET['rsid'])){
		$sessionID = $_GET['rsid'];
	}
//    echo $id;
    $data = mysqli_query($connect, "SELECT * FROM candidate WHERE CandID = '$id'");
    $raw = $data->fetch_row();
	
	$stData = mysqli_query($connect, "SELECT candidatestatus.name FROM candidatestatus WHERE candstatusID = '$raw[8]'");
    $stRaw = $stData->fetch_row();
	
    $data1 = mysqli_query($connect, "SELECT submittedCV FROM cv WHERE cvID = '$raw[9]'");
    $raw1 = $data1->fetch_row();
//    echo $raw1[0];
    ?>

    <form role="form" action=<?php echo "form.php?id=".$id."&rsid=".$sessionID.""?> method="post">
        NIC: <input type="text" name="d1"  class="form-control" value=<?php echo $raw[1];?>><br>
        First name: <input type="text" name="d2" class="form-control" value=<?php echo $raw[2];?>><br>
        Last name: <input type="text" name="d3" class="form-control" value=<?php echo $raw[3];?>><br>
        Date of birth: <input type="date" name="d4" class="form-control" value=<?php echo $raw[4];?>><br>
        email: <input type="email" name="d5" class="form-control" value=<?php echo $raw[5];?>><br>
        Contact no: <input type="tel" name="d6" class="form-control" value=<?php echo $raw[6];?>><br>
        University: <input type="text" name="d7" class="form-control" value=<?php echo $raw[7];?>><br>
        Status: <input type="text" name="d8" class="form-control" value=<?php echo $stRaw[0];?>><br>
        <a class="btn btn-default" style="color: white; background-color: darkorchid" href="candidateList.php?rsid=<?php echo $sessionID?>" >back</a>
        <input class="btn btn-default" style="color: white; background-color: darkorchid" type="submit" value="Submit form">

    </form>

</div>
<div class="col-md-1"></div>


<div class="col-md-6">
    <object data="<?php echo $raw1[0];?>" type="application/pdf" width="600" height="650"
            style="padding-top: 10px; padding-bottom: 10px; background-color: darkorchid">
        
    </object>
</div>


</body>
</html>

