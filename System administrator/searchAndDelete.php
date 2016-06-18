<?php
  include('../phpSessions.php');
  if($_SESSION['type']=='admin_officer')
    header("location: ../Administrative officer/index.php");
  
  if($_SESSION['type']=='interview_panel')
    header("location: ../Interview panel member/index.php");
  
  if($_SESSION['type']=='receptionist')
    header("location: ../Receptionist/recruitmentSessionInterface.php");

	
  ?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>IFS Resume Trekker</title>
    <link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico"/>
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <script src="sweetalert-master/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" type="text/css" href="sweetalert-master/dist/sweetalert.css">
    
</head>

<body>
<div>
    <header>
        <aside class="asideRight">

      <span>
            <b id="welcome">Welcome : <i><?php echo $login_session; ?></i></b>
            <b id="logout"><a href="logout.php">Log Out</a></b>
      </span>
            <form>
                <input type="search" class="searchbox"><img src="images/searchIcon.png" width="15" height="15" alt=""/>

                <a href="systemAdministratorHome.php" class="navHome"> Home</a>
                <a href="help.html" class="navHelp">Help </a></aside>
            </form>


        <aside class="asideLeft"></aside>
    </header>

    <aside class="asideLeftIcons" style="padding-left: 80px">
        <nav>
            <a href="deleteRecords.php" class="aBack">Back</a>
            <a href="recruitmentSessionHelp.php" class="aHelp">Help</a>
        </nav>
    </aside>
<?php

require_once("Sql.php");
$s = new Sql();
$connectValue = $s->connectToDatabase('recruit');

$word = '';
if(isset($_POST['key']))
    $word = $_POST['key'];

if(!isset($_POST['radio'])){
    header("Location: deleteRecords.php");
}


if($_POST['radio'] == 'candidate')
    $result = mysqli_query($connectValue, "SELECT * FROM candidate WHERE email LIKE '%$word%' OR LastName LIKE '%$word%' OR FirstName LIKE '%$word%'");
elseif($_POST['radio'] == 'session')
    $result = mysqli_query($connectValue, "SELECT * FROM recruitmentsession WHERE name LIKE '%$word%'");
elseif($_POST['radio'] == 'interview')
    $result = mysqli_query($connectValue, "SELECT * FROM interview WHERE Name LIKE '%$word%'");

if ($result->num_rows > 0) {
    echo "<br><h3 style='padding-left: 100px'>Search Results</h3>";
    // output data of each row
    ?>
    <div>
        <form action="delete.php?type=<?php echo $_POST['radio']?>" method="post">
        <table width="80%" border="2" style="border-color: purple; border-width: 5px" bgcolor="white">
            <?php
            while($row=mysqli_fetch_array($result,MYSQLI_NUM)) {
                ?>

                <tr>
                    <td style="width: 50px">
                        <?php echo $row[0]?>
                    </td>
                    <td style="width: 150px">
                        <?php echo $row[1]?>
                    </td>
                    <td style="width: 250px">
                        <?php echo $row[2]?>
                    </td>
                    <td style="width: 250px">
                        <?php echo $row[3]?>
                    </td>
                    <td style="width: 100px">
                        <input type="checkbox" name="check_list[]" value=<?php echo $row[0]?> />
                    </td>
                </tr>

            <?php
            }
            ?>
        </table>
            <p style="padding-left: 60%"><input type="submit" value="Delete Selected Results" style="width: 200px; height: 40px; border-color: purple"></p>
            </form>
    </div>

        <?php
        } else {
    ?>
    <div style="border-color: purple; padding-left: 10%">
           <span>no result</span>
    </div>
    <?php
        }
        ?>



<footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>
