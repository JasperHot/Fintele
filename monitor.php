<?php

date_default_timezone_set('PRC');
$hostname = "workinghours.db.10736184.hostedresource.com";
$username = "workinghours";
$dbname = "workinghours";

//These variable values need to be changed by you before deploying
$password = "Hours9to5@FT";

//Connecting to your database

include ('LogsClass.Class.php');



//while(1)
{
	sleep(180);
	$timestamp=time();
	$dir="logs/monitor/".date("Y/m/d",$timestamp);
	$filename=date("His",$timestamp).".log";
	$logs=new Logs($dir,$filename);
	
	/*weekbalance Mon 0:00-1:00*/
	if((1==date("w",$timestamp)) and (0==date("G",$timestamp)))
	{
	    @ $db = new mysqli($hostname, $username, $password, $dbname);

        if (mysqli_connect_errno())
        {
	        $logs->setLogW(__FILE__, __LINE__,"connect failed:".$db->error);
        }
        else
        {
            $query = "update User set  weekbalance=0 ";
            $logs->setLogW(__FILE__, __LINE__,"update:".$query);
            $result = $db->query($query);
            if ($result){
        	    $logs->setLogW(__FILE__, __LINE__,"update successful ".date("Y-m-d H:i:s",$timestamp));
            }
            else{
        	    $logs->setLogW(__FILE__, __LINE__,"update failed:".$db->error);
            }
            $db->close();
        }
	}
	
	/*monthbalance 1th 0:00-1:00*/
	if((1==date("j",$timestamp)) and (0==date("G",$timestamp)))
	{
		@ $db = new mysqli($hostname, $username, $password, $dbname);

        if (mysqli_connect_errno())
        {
	        $logs->setLogW(__FILE__, __LINE__,"connect failed:".$db->error);
        }
        else
        {
            $query = "update User set  monthbalance=0 ";
            $logs->setLogW(__FILE__, __LINE__,"update:".$query);
            $result = $db->query($query);
            if ($result){
        	    $logs->setLogW(__FILE__, __LINE__,"update successful ".date("Y-m-d H:i:s",$timestamp));
            }
            else{
                $logs->setLogW(__FILE__, __LINE__,"update failed:".$db->error);
            }
            $db->close();
        }
	}
	////$logs->close();
	//sleep(1620);
	
}