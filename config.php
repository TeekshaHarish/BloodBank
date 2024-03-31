<?php

define('DB_SERVER','localhost');
define('DB_USERNAME','root');
define('DB_PASSWORD','');
define('DB_NAME','BloodBank');

$con=mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);
if(!$con){
    die("Connection to database failed due to ".mysqli_connect_error());
}
?>