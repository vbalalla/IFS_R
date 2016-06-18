<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>IFS Resume Trekker</title>
<link href="css/style.css" rel="stylesheet" type="text/css">
<link href="css/formStyle.css" rel="stylesheet" type="text/css">
<script src="js/jquery.min.js"></script>
<script> 
function clicked(){
	alert("Evaluation Criteria Added Successfully");
}
</script>
</head>
<?php 
		require_once("Sql.php");
		require_once("GlobalVariables.php");
		$s = new Sql();
		$connect = $s->connectToDatabase($databaseName);
		
		$results = mysqli_query($connect,"SELECT * FROM criteria");
				
		$intID="";
		if(isset($_GET["int"])){
				$intID=$_GET["int"];
		}	
		
		$inter = mysqli_query($connect,"SELECT * FROM interview WHERE IntID=$intID");
		$row = $inter->fetch_row();
		$selected = mysqli_query($connect,"SELECT * FROM criteria WHERE criteriaID=$row[3]");
?>

<body>
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
		<a href="evalCriteria.php?int=<?php echo $intID;?>" class="aBack">Back</a> 
	  </nav>
  </aside>
  
  <div>
  <section class="formSectionRight">
  <form method="post" action="selectEval.php?int=<?php echo $intID; ?>">
  
	<select id="jbposition" name = "selectEva" class="formSelect" required placeholder="">
		<?php 
		if($row[3]==0){ ?>
		<option value="" selected disabled>Select Evaluation Criteria</option>
		<?php }else{ $cri=$selected->fetch_row(); ?>
		<option value="<?php echo $cri[0];?>" selected><?php echo $cri[1];?></option>
		<?php } ?>
		<?php while($row = $results->fetch_row()){
			if($row[0]!=$cri[0]){
			?>
		<option value=<?php echo $row[0];?>><?php echo $row[1];?></option>
		<?php }} ?>
    </select><br/>
	
	<input id="btnID" type="submit" value="Save" onclick="clicked()" />
    <a href="evalCriteria.php?int=<?php echo $intID?>"><input type="reset" value="Cancel" /></a>
    
  </form>
  </section>
  </div>
  
  <footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>