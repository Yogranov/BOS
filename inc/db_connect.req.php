<?php

$connect_error = 'There was a problem. Please try again later..';

$mysql_host = 'localhost';
$mysql_username = 'bug';
$mysql_password = '';
$mysql_db_name = 'bug';

    
  
$db_connect = mysqli_connect($mysql_host, $mysql_username, $mysql_password, $mysql_db_name);
mysqli_query($db_connect, "SET NAMES 'utf8'");

?>