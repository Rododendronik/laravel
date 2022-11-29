<?php
/* Attempt to connect to MySQL database */
$link = mysqli_init();
if (!mysqli_real_connect($link, 'localhost', 'xbudai01', '3moparge', 'xbudai01', 0, '/var/run/mysql/mysql.sock')) {
	die('cannot connect '.mysqli_connect_error());
} 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>