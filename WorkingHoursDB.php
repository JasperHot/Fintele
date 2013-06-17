<?php
// define WorkingHours DB

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

echo "database ".$dbname." connected </br></br>";

/* Tables already created on June 1st

//Create Table User
$query = "CREATE TABLE User
(
ID char(50) not null primary key,
pseudo char(50),
weekbalance float(2,2),
monthbalance float(2,2)
)";

$result = $db->query($query);
if (!$result)
{
	echo 'Error creating table User !';
	exit;
}

//Create Table Record
$query = "CREATE TABLE Record
(
ID char(50) not null,
datetime datetime not null,
inorout int,
primary key (ID, datetime)
)";

$result = $db->query($query);
if (!$result)
{
	echo 'Error creating table Record !';
	exit;
}
*/


//insert item
/*$query = "insert into User values
(
'xxx',
'yyy',
1.1,
2.2
)";

$result = $db->query($query);
if (!$result)
{
	echo 'Error insert User !</br>';
}*/

//select item
/*$query = "select ID from User where ID='xxx'";

$result = $db->query($query);
if (!$result)
{
	echo 'Error select User !</br>';
}
else
{
    $num_results = $result->num_rows;
    echo "select in User ".$dbname.":</br></br>";
    if($num_results==0)
    {echo "empty</br>";}
    for ($i=0;$i<$num_results;$i++)
    {
	    $row = $result->fetch_row();
	    echo $row[0];
	    echo '</br>';
    }
}*/
//delete item
/*$query = "delete from User where id='xxx'";

$result = $db->query($query);
if (!$result)
{
	echo 'Error delete User !</br>';
}*/

//update item
/*$queryUP = "update Record set inorout=2 where inorout=0";

$result = $db->query($queryUP);
if (!$result)
{
echo 'Error update Record !</br>';
}*/
//search
/*$queryCI = "select ID,datetime from Record where TO_DAYS(datetime)=TO_DAYS('2013-06-12') and ID='o5M22jqVr_u4ofsVCGiUeUoZ9qYM' order by datetime limit 0,1";

$resultCI = $db->query($queryCI);
echo "Show ID & datetime in Record ".$dbname.":</br></br>";
if(!$resultCI){
	echo "Error select</br>";
}else{
    if($resultCI->num_rows==0){
    	echo "empty select</br>";
    }
    for ($i=0;$i<$resultCI->num_rows;$i++){
	    $row = $resultCI->fetch_row();
	    //echo $row[0];
	    echo $row[1];
	    $timestampCI=strtotime($row[1]);
	    $hour=date("H",$timestampCI);
	    $minute=date("i",$timestampCI);
	    echo $timestampCI."-".$hour."-".$minute.'</br>';
    }
    $resultCI->close();
}*/
//set mysql time zone
/*$querySET = "SET time_zone = '+8:00'";
$resultSET = $db->query($querySET);
if(!$resultSET)
	$logs->setLog("checkin:set time_zone failed:");*/

//get milli second
/*$time = explode ( " ", microtime () );
$time2 = explode ( ".", ($time [0] * 1000) );
$timestamp = $time2 [0];
echo $time[0]."-".$time[1]."-".$time2[0]."-".$time2[1]."-".$timestamp."</br>";*/
//check db
$query = "show tables";
$result = $db->query($query);
$num_results = $result->num_rows;
echo "Show tables in database ".$dbname.":</br></br>";
for ($i=0;$i<$num_results;$i++)
{
	$row = $result->fetch_row();
	echo $row[0];
	echo '</br>';
}

//check user
$query = "select id from User";
$result = $db->query($query);
$num_results = $result->num_rows;
echo "Show all ID in User ".$dbname.":</br></br>";
if($num_results==0)
{echo "empty</br>";}
for ($i=0;$i<$num_results;$i++)
{
	$row = $result->fetch_row();
	echo $row[0];
	echo '</br>';
}
$db->close();
?>