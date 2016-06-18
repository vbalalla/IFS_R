<?php

require_once("Sql.php");
$s = new Sql();
$connectValue = $s->connectToDatabase('recruit');

if($_GET['type'] == 'candidate'){
    if(!empty($_POST['check_list'])){
// Loop to store and display values of individual checked checkbox.
        foreach($_POST['check_list'] as $selected){
            mysqli_query($connectValue, "DELETE FROM candidate WHERE CandID = '$selected'");
            echo "$selected deleted </br>";
        }
    }
}

if($_GET['type'] == 'session'){
    if(!empty($_POST['check_list'])){
// Loop to store and display values of individual checked checkbox.
        foreach($_POST['check_list'] as $selected){
            mysqli_query($connectValue, "DELETE FROM recruitmentsession WHERE RSID = '$selected'");
            echo "$selected deleted </br>";
        }
    }
}

if($_GET['type'] == 'interview'){
    if(!empty($_POST['check_list'])){
// Loop to store and display values of individual checked checkbox.
        foreach($_POST['check_list'] as $selected){
            mysqli_query($connectValue, "DELETE FROM interview WHERE IntID = '$selected'");
            echo "$selected deleted </br>";
        }
    }
}

header("Location: searchAndDelete.php");


?>