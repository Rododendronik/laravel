<?php
define('DB_SERVER', 'containers-us-west-56.railway.app');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'YUFufvjA6WN8yP3xV8KO');
define('DB_NAME', 'railway');
define('DB_PORT', 6035);
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>