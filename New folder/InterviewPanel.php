<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Interview Panel</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico"/>

<script type="text/javascript" src="js/sortable.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css">

<style type="text/css">
	.asideLeftIcons {
	margin-left: 9%;
	width: 70%;
	margin-bottom: 5%;
	text-align: left;
	}
#existingSessionsTable {
	border-collapse: collapse;
	background-color: #FFFFFF;
	border-color: #EBBEF5;
	margin-bottom: 3%;
	color: #281A2B;
}
</style>

</head>

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
		<a href="index.html" class="aBack">Back</a> 
		<a href="createNewInterview.html" class="aCreateNew">Create New</a> 
		<a href="recruitmentSessionHelp.html" class="aHelp">Help</a>
	  </nav>
  </aside>
  <table width="83%" border="1" align="center" cellpadding="5" class="sortable" id="existingSessionsTable">
  <tbody>
    <tr>
      <th width="40%" scope="col">Interview Panel Name</th>
      </tr>
    <tr>
      <td>Interview Panel 01 (Default)</td>
      </tr>
    <tr>
      <td>&nbsp;</td>
      </tr>
    <tr>
      <td>&nbsp;</td>
      </tr>
    <tr>
      <td>&nbsp;</td>
      </tr>
    <tr>
      <td>&nbsp;</td>
      </tr>
    <tr>
      <td>&nbsp;</td>
      </tr>
    <tr>
      <td>&nbsp;</td>
      </tr>
    </tbody>
</table>

<footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>
