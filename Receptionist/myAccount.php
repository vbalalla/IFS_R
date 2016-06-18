<?php
     include_once('../phpSessions.php');
  if($_SESSION['type']=='admin_officer')
    header("location: ../Administrative officer/index.php");
  
  if($_SESSION['type']=='interview_panel')
    header("location: ../Interview panel member/index.php");
  
	if($_SESSION['type']=='system_admin')
    header("location: ../System administrator/systemAdministratorHome.php");
    ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>IFS Resume Trekker</title>
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">
    input[type=submit] {
	padding-left: 40%;
	padding-right: 5px;
	color: #281A2B;
	background-image: url(images/recruitmentSession/submit.png);
	background-repeat: no-repeat;
	padding-top: 11px;
	padding-bottom: 24px;
	margin-right: 2%;
	font-family: "OpenSans Regular";
	border-style: none;
	border-color: #E9E1E1;
	background-color: #E9E1E1;
	font-weight: 600;
	font-size: 1.2em;
	margin-left: 1%;
	margin-top: 5%;
	-webkit-transition: all 0.3s ease;
	-o-transition: all 0.3s ease;
	transition: all 0.3s ease;
	margin-bottom: 10%;
	}
	input[type=submit]:hover {
		background-image: url(images/recruitmentSession/submit2.png);
	}
	</style>

    
</head>

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
        <a href="recruitmentSessionInterface.php" class="navHome"> Home</a>
        <a href="../UserManual/UserManual.html" class="navHelp">Help </a>
      </form>

    </aside>
    <aside class="asideLeft"></aside>
  </header>
<div>
    <h3>My Details</h3>
    User Name : <span style="color: purple"><?php echo $_SESSION["login_user"]?></span><br>
    <h3>change my password</h3>
    <?php
    $remarks="";
    $error="";
    if(isset($_GET['id'])){

        $id=$_GET['id'];
        if ($id==0)
        {
            if(isset($_POST['oldpass'])){
                $old = $_POST['oldpass'];
                $new = $_POST['newpass'];
                $usrid = $_SESSION['usrid'];
                require_once("Sql.php");
                $s = new Sql();
                $connectValue = $s->connectToDatabase('recruit');

                $val = mysqli_query($connectValue, "SELECT password FROM login WHERE usrid='$usrid' ");
                if(mysqli_fetch_row($val)[0]==$old){
                    mysqli_query($connectValue, "UPDATE login SET password='$new' WHERE usrid='$usrid' ");
                    mysqli_close($connectValue);
                    ?>
                    <h3 style="color: green">
                        <?php
                        echo 'Password has been changed!';
                        ?>
                    </h3>
                <?php
                }
                else{
                    mysqli_close($connectValue);
                    header("location: myAccount.php?id=1");
                }


            }

        }

        if ($id==1)
        {
                $error = 'Wrong password!';
        }
    }
    ?>
    <form action="myAccount.php?id=0" method="POST">
        <table>
            <tr>
                <td>Old Password: </td>
                <td><input name="oldpass" type="password" /></td>
                <td><span style="color:red; font-size: small "><?php echo $error; ?></span></td>
            </tr>
            <tr>
                <td>New Password: </td>
                <td><input name="newpass" type="password" /></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" value="Save">
                </td>
            </tr>
        </table>
    </form>
</div>

            <footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>
