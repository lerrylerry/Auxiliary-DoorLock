<?php


define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'u553122496_root');
// define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'nedzlerry4B');
// define('DB_PASSWORD', '');
define('DB_DATABASE', 'u553122496_dbauxsys');
// define('DB_DATABASE', 'dbauxsys');

$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
