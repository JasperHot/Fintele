<?php
//set timezone for PRC
date_default_timezone_set('PRC');

//include ('WorkingHoursClass.php');
//Variables for connecting to your database.
//These variable values come from your hosting account.
$hostname = "workinghours.db.10736184.hostedresource.com";
$username = "workinghours";
$dbname = "workinghours";

//These variable values need to be changed by you before deploying
$password = "Hours9to5@FT";

//Connecting to your database

@ $db = new mysqli($hostname, $username, $password, $dbname);

if (mysqli_connect_errno())
{
	echo 'Error connecting database !';
	exit;
}

/* DB tables
 * User (ID, Pseudo, WeekBalance, MonthBalance)
* Record (ID, DateTime, InOrOut) //InOrOut: 1 = In, 2 = Out
*/

//echo "database ".$dbname." connected </br></br>";
include ('Logs.php');


	$textTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<FuncFlag>0</FuncFlag>
				</xml>";
	{
		global $logs;

		$toUsername = "o5M22jqVr_u4ofsVCGiUeUoZ9qYM";
		$fromUsername = "gh_b7e3597ab71b";
		$textContent = "你好！";
		$time = time();
		$msgType = "text";
		$resultXMLStr = sprintf($textTpl, $toUsername, $fromUsername, $time, $msgType, $textContent);
		//send response
		$logs->setLog($resultXMLStr);
		echo $resultXMLStr;
	}
//check user
/*$query = "select id from User";
$result = $db->query($query);
$num_results = $result->num_rows;

if($num_results==0){echo "empty</br>";}
for ($i=0;$i<$num_results;$i++)
{
	$row = $result->fetch_row();
    $wechatObj->postObj->ToUserName=$row[0];
    $wechatObj->sendResult();
}*/
$db->close();
?>