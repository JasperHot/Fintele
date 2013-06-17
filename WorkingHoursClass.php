﻿<?php

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
		$logs->setLogW(__FILE__, __LINE__,"register:select:".$query);

		$result = $db->query($query);

        
		if(!$result)
		{
			$this->outputStr = "注册失败";
			$logs->setLogW(__FILE__, __LINE__,"register:select failed:".$db->error);
		}
		else
		{
			//registered
			$logs->setLogW(__FILE__, __LINE__,"register:select sucessful");

			if($result->num_rows==0)
			{
				//insert into DB User_table, not insert if duplicate
				$query2 = "insert into User values ('".$this->userID."', NULL, 0.0, 0.0)";
				$result2 = $db->query($query2);
				if ($result2){
					$this->outputStr = "注册成功 :)";
					$logs->setLogW(__FILE__, __LINE__,"register:insert sucessful");
				}
				else {
					$this->outputStr = "注册失败 :(";
					$logs->setLogW(__FILE__, __LINE__,"register:insert failed:".$db->error);
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
		$logs->setLogW(__FILE__, __LINE__,"checkin:select:".$query);
		
		$result = $db->query($query);
		
		if(!$result)
		{
			$this->outputStr = "登入失败";
			$logs->setLogW(__FILE__, __LINE__,"checkin:select failed:".$db->error);
		}
		else/*if it registered*/
		{
			//registered
			$logs->setLogW(__FILE__, __LINE__,"checkin:select sucessful");
			
			if($result->num_rows==0)
			{
				$this->outputStr = "请先注册，再登入";
			}
			else
			{
				$timestamp=time();
				$hour=date("H",$timestamp);
				$minute=date("i",$timestamp);
				$logs->setLogW(__FILE__, __LINE__,"checkin:".$hour.":".$minute);
			    $query2 = "insert into Record values ('".$this->userID."', '".date("Y-m-d H:i:s",$timestamp)."', 1)";/*1-in*/
			    $logs->setLogW(__FILE__, __LINE__,"checkin:".$query2);
				$result2 = $db->query($query2);
				if ($result2){
					
					$queryCI = "select datetime from Record where TO_DAYS(datetime)=TO_DAYS('".date("Y-m-d",$timestamp)."') "
							."and ID='".$this->userID."' and inorout=1 order by datetime limit 0,1";
					$logs->setLogW(__FILE__, __LINE__,"checkin:select:".$queryCI);
						
					$resultCI = $db->query($queryCI);
					if(!$resultCI){
						$logs->setLogW(__FILE__, __LINE__,"checkout:select failed:".$db->error);
					}else{
						if($resultCI->num_rows==0){
							$logs->setLogW(__FILE__, __LINE__,"checkin:select failed: can't find insert before");
						}
						for ($i=0;$i<$resultCI->num_rows;$i++){
							$row = $resultCI->fetch_row();
					
							$timestampCI=strtotime($row[0]);
							$hourCI=date("H",$timestampCI)+9;
							if($hourCI>24){$hourCI-=24;}
							$minuteCI=date("i",$timestampCI);
						}
					}
					$resultCI->close();
					$this->outputStr = "登入成功,时间".date("Y-m-d H:i:s",$timestamp).". 预计下班时间: ".$hourCI."点".$minuteCI."分";
					$logs->setLogW(__FILE__, __LINE__,"checkin:insert sucessful");
				}
				else {
					$this->outputStr = "登入失败 :(";
					$logs->setLogW(__FILE__, __LINE__,"checkin:insert failed:".$db->error);
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
		$logs->setLogW(__FILE__, __LINE__,"checkout:select:".$query);
		
		$result = $db->query($query);
		
		if(!$result)
		{
			$this->outputStr = "登出失败";
			$logs->setLogW(__FILE__, __LINE__,"checkout:select failed:".$db->error);
		}
		else/*if it registered*/
		{
			//registered
			$logs->setLogW(__FILE__, __LINE__,"checkout:select sucessful");
				
			if($result->num_rows==0)
			{
				$this->outputStr = "请先注册，再登出";
			}
			else
			{
				$timestamp=time();
				$hour=date("H",$timestamp);
				$minute=date("i",$timestamp);
				$logs->setLogW(__FILE__, __LINE__,"checkout:".$hour.":".$minute);
				

				$queryCI = "select datetime from Record where TO_DAYS(datetime)=TO_DAYS('".date("Y-m-d",$timestamp)."') "
							."and ID='".$this->userID."' and inorout=1 order by datetime limit 0,1";
				$logs->setLogW(__FILE__, __LINE__,"checkout:select:".$queryCI);
					
				$resultCI = $db->query($queryCI);
				if(!$resultCI){
					$this->outputStr = "登出失败";
			        $logs->setLogW(__FILE__, __LINE__,"checkout:select failed:".$db->error);
				}else{
					if($resultCI->num_rows==0){
						$this->outputStr = "请先登入，再登出";
					}
					for ($i=0;$i<$resultCI->num_rows;$i++){
						$row = $resultCI->fetch_row();

						$timestampCI=strtotime($row[0]);
						$hourCI=date("H",$timestampCI);
						$minuteCI=date("i",$timestampCI);
                        if($minute>=$minuteCI){
                            $minute-=$minuteCI;
                            if($hour>=$hourCI){
                            	$hour-=$hourCI;
                            }
                            else{
                            	$minute=0;
                            	$logs->setLogW(__FILE__, __LINE__,"checkout:caculate hour error! hourCI=".
                            				$hourCI.", minuteCI=".$minuteCI.", hour=".$hour.",minute=".$minute);
                            }
                        }
                        else{
                            if($hour>$hourCI){
                            	$minute=$minute+60-$minuteCI;
                            	$hour=$hour-1-$hourCI;
                            }
                            else{
                            	$minute=0;
                            	$logs->setLogW(__FILE__, __LINE__,"checkout:caculate hour error! hourCI=".
                            				$hourCI.", minuteCI=".$minuteCI.", hour=".$hour.",minute=".$minute);
                            }
                        }
                        
                        $query2 = "insert into Record values ('".$this->userID."', '".date("Y-m-d H:i:s",$timestamp)."', 2)";/*2-out*/
                        $logs->setLogW(__FILE__, __LINE__,"checkout:".$query2);
                        $result2 = $db->query($query2);
                        if ($result2){
                        	$this->outputStr = "登出成功,时间".date("Y-m-d H:i:s",$timestamp).". 今日工作".$hour."小时".$minute."分钟";
                        	$logs->setLogW(__FILE__, __LINE__,"checkout:insert sucessful");
                        }
                        else {
                        	$this->outputStr = "登出失败 :(";
                        	$logs->setLogW(__FILE__, __LINE__,"checkout:insert failed:".$db->error);
                        }
					}
					$resultCI->close();
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