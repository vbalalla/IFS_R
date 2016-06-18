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
    <table>
        <tbody>
        <tr>
            <td style="width: 100%">
                <nav><ul class="navDownList">
                        <li><a href="backupCV.php" class="backupAdminHome">Backup CVs</a></li>
                        <li><a href="backup.php" class="backupAdminHome"> Backup Database</a></li>
                    </ul>
                </nav>
            </td>
            <td>
            </td>
        </tr>

        </tbody>
    </table>


    <footer>Copyright 2015 &copy;</footer>
</div>

</body>

<?php
    if (isset($_GET["id"])) {
        if($_GET["id"]==0){
    ?>
            <script type="text/javascript">
                swal("Done", "CVs are backed up", "success")
            </script>
    <?php
        }
    ?>
    <?php
        if ($_GET["id"]==1){
    ?>
            <script type="text/javascript">
                swal("Done", "Database is backed up", "success")
            </script>

    <?php
        }
    }
?>
</html>

