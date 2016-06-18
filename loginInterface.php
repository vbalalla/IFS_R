<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>IFS Resume Trekker</title>
    <link href="css/style.css" rel="stylesheet" type="text/css">


    <?php
include('login.php'); // Includes Login Script

if(isset($_SESSION['login_user'])){
    if($_SESSION['type']=='admin_officer'){
        header("location: Administrative officer/index.php");
    }
    if($_SESSION['type']=='system_admin'){
        header("location: System administrator/systemAdministratorHome.php");
    }
    if($_SESSION['type']=='receptionist'){
        header("location: Receptionist/recruitmentSessionInterface.php");
    }
    if($_SESSION['type']=='interview_panel'){
        header("location: Interview panel member/index.php");
    }

}
?>
</head>
<body>
<div>
    <header>
        <aside class="asideRight">
        </aside>
        <aside class="asideLeft"></aside>
    </header>


<div id="main">
    <h1></h1>
    <div id="login">
        <h2>Login</h2>
        <form action="" method="post" style="padding-bottom: 100px">
            <table>
                <tr>
                    <td>
                        <label>UserName :</label>
                    </td>
                    <td>
                        <input id="name" name="username" placeholder="username" type="text"><br>
                    </td>
                    <td>
                        <span style="color:red; font-size: small "><?php echo $usrerror; ?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Password :</label>
                    </td>
                    <td>
                        <input id="password" name="password" placeholder="**********" type="password"><br>
                    </td>
                    <td>
                        <span style="color:red; font-size: small"><?php echo $error; ?></span>
                        <span style="color:red; font-size: small"><?php echo $passerror; ?></span>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input id="login" name="submit" type="image" value="Login" src="images/login.png" >
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<footer>Copyright 2015 &copy;</footer>
</div>
</body>
</html>