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

<body

<div>
    <header>
        <aside class="asideRight">
            <input type="search" class="searchbox"><img src="images/searchIcon.png" width="15" height="15" alt=""/>

            <a href="systemAdministratorHome.php" class="navHome"> Home</a>
            <a href="help.html" class="navHelp">Help </a></aside>

        <aside class="asideLeft"></aside>
    </header>
    <table>
        <tbody>
        <tr>
            <td style="width: 100%">
                <nav><ul class="navDownList">
                        <li><a href="backupCV.php" class="backupAdminHome">Backup CVs</a></li>
                        <li><a href="backup.php" class="backupAdminHome"> Backup Database</a></li>
                        <li><a href="searchRecords.php" class="backupAdminHome"> Automatic Backup Setings</a></li>
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

