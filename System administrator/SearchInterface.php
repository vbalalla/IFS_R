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
    <?php include_once('Search.php')?>
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
                <a href="systemAdministratorHome.php" class="navHome"> Home</a>
                <a href="../UserManual/UserManual.html" class="navHelp">Help </a>
            </form>
        </aside>
        <aside class="asideLeft"></aside>
    </header>

    <aside class="asideLeftIcons" style="padding-left: 80px">
        <nav>
            <a href="index.php" class="aBack">Back</a>
            <a href="recruitmentSessionHelp.php" class="aHelp">Help</a>
        </nav>
    </aside>

    <table>
        <tbody>
        <tr>
            <td>
                <?php
                $s = new Search();
                if(isset($_GET['Search'])){
                    $s->fullSearch($_GET["Search"]);
                }

                ?>
            </td>
        </tr>

        </tbody>
    </table>


    <footer>Copyright 2015 &copy;</footer>
</div>


</body>
</html>

