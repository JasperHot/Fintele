<?php

class WorkingHours
{
	public $userID = ""; //wexin user open ID
	public $outputStr = ""; //result to output to weixin
	//DB info
	public $hostname = "workinghours.db.10736184.hostedresource.com";
	public $username = "workinghours";
	public $dbname = "workinghours";
	public $password = "Hours9to5@FT";
	//public $tableUser = "User";
	//public $tableRecord = "Record"
		
	public function Register ()
	{
		global $logs;
		//connect to DB
		@ $db = new mysqli($this->hostname, $this->username, $this->password, $this->dbname);
		if (mysqli_connect_errno())
			exit;
		//find if it is inserted
		$query = "select ID from User where ID='".$this->userID."'";
		$logs->setLog("register:select:".$query);

		$result = $db->query($query);

        
		if(!$result)
		{
			$this->outputStr = "注册失败";
			$logs->setLog("register:select failed:".$db->error);
		}
		else
		{
			//registered
			$logs->setLog("register:select sucessful");

			if($result->num_rows==0)
			{
				//insert into DB User_table, not insert if duplicate
				$query2 = "insert into User values ('".$this->userID."', NULL, 0.0, 0.0)";
				$result2 = $db->query($query2);
				if ($result2){
					$this->outputStr = "注册成功 :)";
					$logs->setLog("register:insert sucessful");
				}
				else {
					$this->outputStr = "注册失败 :(";
					$logs->setLog("register:insert failed:".$db->error);
				}
			}
			else
			{
				$this->outputStr = "已注册，无需重复注册。可输入\"登入\"或\"登出\"查询。";
			}

			$result->close();
		}
		
		$db->close();
	}
	
	public function SetPseudo ()
	{
		//set Pseudo to DB User_table
	}
	
	public function CheckIn ()
	{ 
		global $logs;
		//connect to DB
		@ $db = new mysqli($this->hostname, $this->username, $this->password, $this->dbname);
		if (mysqli_connect_errno())
			exit;
		//find if it is inserted
		$query = "select ID from User where ID='".$this->userID."'";
		$logs->setLog("checkin:select:".$query);
		
		$result = $db->query($query);
		
		if(!$result)
		{
			$this->outputStr = "登入失败";
			$logs->setLog("checkin:select failed:".$db->error);
		}
		else/*if it registered*/
		{
			//registered
			$logs->setLog("checkin:select sucessful");
			
			if($result->num_rows==0)
			{
				$this->outputStr = "请先注册，再登入";
			}
			else
			{
				$timestamp=time();
				$hour=date("H",$timestamp);
				$minute=date("i",$timestamp);
				$logs->setLog("checkin:".$hour.":".$minute);
			    $query2 = "insert into Record values ('".$this->userID."', '".date("Y-m-d H:i:s",$timestamp)."', 1)";/*1-in*/
			    $logs->setLog("checkin:".$query2);
				$result2 = $db->query($query2);
				if ($result2){
					$this->outputStr = "登入成功,时间".date("Y-m-d H:i:s",$timestamp);
					$logs->setLog("checkin:insert sucessful");
				}
				else {
					$this->outputStr = "登入失败 :(";
					$logs->setLog("checkin:insert failed:".$db->error);
				}
			}
			
			$result->close();

		}
		$db->close();
		//reset week balance if 1st time of week in DB User_table
		
		//reset month balance if 1st time of month in DB User_table
		
		//check if CheckedIn today in DB Record_table
		
		//set CheckIn time to DB Record_table
		
		//calculate and notify CheckOut time proposal
	}
	
	public function CheckOut ()
	{
		global $logs;
		//connect to DB
		@ $db = new mysqli($this->hostname, $this->username, $this->password, $this->dbname);
		if (mysqli_connect_errno())
			exit;
		//find if it is inserted
		$query = "select ID from User where ID='".$this->userID."'";
		$logs->setLog("checkout:select:".$query);
		
		$result = $db->query($query);
		
		if(!$result)
		{
			$this->outputStr = "登出失败";
			$logs->setLog("checkout:select failed:".$db->error);
		}
		else/*if it registered*/
		{
			//registered
			$logs->setLog("checkout:select sucessful");
				
			if($result->num_rows==0)
			{
				$this->outputStr = "请先注册，再登出";
			}
			else
			{
				$timestamp=time();
				$hour=date("H",$timestamp);
				$minute=date("i",$timestamp);
				$logs->setLog("checkout:".$hour.":".$minute);
				$query2 = "insert into Record values ('".$this->userID."', '".date("Y-m-d H:i:s",$timestamp)."', 0)";/*0-out*/
				$logs->setLog("checkout:".$query2);
				$result2 = $db->query($query2);
				if ($result2){
					$this->outputStr = "登出成功,时间".date("Y-m-d H:i:s",$timestamp);
					$logs->setLog("checkout:insert sucessful");
				}
				else {
					$this->outputStr = "登出失败 :(";
					$logs->setLog("checkout:insert failed:".$db->error);
				}
			}
				
			$result->close();

		}
		$db->close();
		//check if CheckedOut today in DB Record_table
		
		//set or update CheckOut time to DB Record_table
		
		//calculate and notify day balance
		
		//calculate, notify and update week balance and month balance to DB User_table
	}
	
	public function CheckBalance ()
	{
		//search and notify week balance and month balance in DB User_table
	}
	
	public function CheckRecord ()
	{
		//search and notify recods in DB Record_table
	}
	
	public function ResetBalance ()
	{
		//update week balance and month balance in DB User_table
	}
	
	public function DeleteRecord ()
	{
		//delete all records in DB Record_table
	}
	
	public function HalfDayOff ()
	{
		
	}
	
	public function DayOff ()
	{
		
	}
}
?>