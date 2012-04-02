<?php //login.php

error_reporting(E_ERROR | E_PARSE);

$db_hostname = 'localhost';
$db_database = 'loggerheadtest';
$db_username = 'jermsthekao';
$db_password = 'password';

mysql_connect($db_hostname, $db_username, $db_password)
	or die ('Unable to connect to database: ' . mysql_error());
mysql_select_db($db_database)
	or die ('Unable to select database: ' . mysql_error());

date_default_timezone_set('America/Los_Angeles');
$todayDate = date('mdy');
//$todayDate = '91411';
?>