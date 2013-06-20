<?php
include ('LogsClass.Class.php'); 
$dir="logs/users/".date("Y/m/d",time());
$filename=date("His",time()).".log";
$logs=new Logs($dir,$filename);
?>