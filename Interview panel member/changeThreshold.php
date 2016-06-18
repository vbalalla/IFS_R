<?php
require_once("threshold.php");
if(isset($_POST['years'])){
echo $_POST['years'];
$s=new Threshold();
$s->setThresholdPeriod($_POST['years']/12);
}
?>