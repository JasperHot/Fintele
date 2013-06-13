<?php
include ('LogsClass.Class.php'); 
$dir="logs/".date("Y/m/d",time());
$filename=date("His",time()).".log";
$logs=new Logs($dir,$filename);
?>