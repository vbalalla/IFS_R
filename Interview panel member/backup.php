

<?php
$toDay = date('d-m-Y');

$dbhost =   "localhost";
$dbuser =   "root";
$dbpass =   "";
$dbname =   "test";

exec("mysqldump --user=$dbuser --password='$dbpass' --host=$dbhost $dbname > backups/".$toDay."_DB.sql");

header('Location: /IFS/createBackup.php?id=1');

?>

