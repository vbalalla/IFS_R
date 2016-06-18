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
    <title>Recruitment Session</title>

    <script type="text/javascript" src="js/sortable.js"></script>
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <script src="jquery/jquery.js"></script>
    <script src="jquery-ui-1.11.4/jquery-ui.js"></script>
    <script src="jquery-ui-1.11.4/jquery-ui.css"></script>
    <script src="jquery-ui-1.11.4/jquery.min.js"></script>
    <script src="jquery-ui-1.11.4/jquery-ui.min.js"></script>

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

    <?php
    require_once("GlobalVariables.php");
    global $db, $user, $pass;

    try {
        $dbh= new PDO($db,$user,$pass);

        $sql = $dbh->prepare("SELECT * FROM employee");
        $loadStatus = $dbh->prepare("SELECT * FROM interviewpanelmemberdetails");

        if($sql->execute()) {
            $sql->setFetchMode(PDO::FETCH_ASSOC);
        }
    }
    catch(Exception $error) {
        echo '<p>', $error->getMessage(), '</p>';


    }



    ?>

    <!--javascript for the table-->
    <script type="text/javascript">



    </script>


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

    <aside class="asideLeftIcons">
        <nav>
            <a href="systemAdministratorHome.php" class="aBack">Back</a>
            <a href="registration.php" class="aCreateNew">Create New</a>
            <a href="" class="aHelp">Help</a>
        </nav>
    </aside>

    <table width="83%" border="1" align="center" cellpadding="5" class="sortable" id="existingSessionsTable">
        <thead>
        <tr>
            <th width="10%" scope="col">User ID</th>
            <th width="25%" scope="col">User Name</th>
            <th width="20%" scope="col">email</th>
            <th width="20%" scope="col">User Privileges</th>
        </tr>
        </thead>
        <tbody>
        <?php while($row = $sql->fetch()) { ?>
            <script> var id = <?php echo(json_encode($row['EmpID'])); ?>;</script>
            <tr>
                <td id=<?php echo $row['EmpID']?> href=""><?php echo $row['EmpID']?></td>
                <td id=<?php echo $row['EmpID']?> href=""><?php echo $row['FirstName'] . " " . $row['LastName']?></td>
                <td id=<?php echo $row['EmpID']?> href=""><?php echo $row['email']?></td>
                <td id=<?php echo $row['EmpID']?> href="">
                    <select id="status" name="d8" class="formSelect">
                        <option value="<?php echo $row['EmpID']?>">Administrative officer</option>
                        <option value="<?php echo $row['EmpID']?>">Interview panel member</option>
                    </select>
                </td>
                <td style="width: 10%; padding-left: 25px"><button onclick="window.location.href='removeUser.php?id=<?php echo $row['EmpID']?>'" style="background-color: violet" type="submit">remove</button></td>
            </tr>
        <?php }?>
        </tbody>
    </table>

    <?php
        while($data = $loadStatus->fetch()){
            echo $data['UserID'];
        }
    ?>
    <footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>
