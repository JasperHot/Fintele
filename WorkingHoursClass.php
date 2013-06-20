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
				$this->outputStr = "已注册,无需重复注册.可输入\"上班\",\"下班\"或\"查询\".";
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
			$this->outputStr = "上班失败";
			$logs->setLogW(__FILE__, __LINE__,"checkin:select failed:".$db->error);
		}
		else/*if it registered*/
		{
			//registered
			$logs->setLogW(__FILE__, __LINE__,"checkin:select sucessful");
			
			if($result->num_rows==0)
			{
				$this->outputStr = "请先注册，再上班";
			}
			else
			{
				
				$queryCI = "select datetime from Record where TO_DAYS(datetime)=TO_DAYS('".date("Y-m-d",time())."') "
						."and ID='".$this->userID."' and inorout=1 order by datetime limit 0,1";
				$logs->setLogW(__FILE__, __LINE__,"checkin:select:".$queryCI);
				
				$resultCI = $db->query($queryCI);
				if(!$resultCI){
					$logs->setLogW(__FILE__, __LINE__,"checkout:select failed:".$db->error);
				}else{
					if($resultCI->num_rows==0){
						
						$timestamp=time();
						$hour=date("H",$timestamp);
						$minute=date("i",$timestamp);
						$logs->setLogW(__FILE__, __LINE__,"checkin:".$hour.":".$minute);
						$query2 = "insert into Record values ('".$this->userID."', '".date("Y-m-d H:i:s",$timestamp)."', 1)";/*1-in*/
						$logs->setLogW(__FILE__, __LINE__,"checkin:".$query2);
						$result2 = $db->query($query2);
						if ($result2){
							$hour+=9;
						    if($hour>24){$hour-=24;}
							$this->outputStr = "上班成功,时间".date("Y-m-d H:i:s",$timestamp).". 预计下班时间: ".$hour."点".$minute."分";
							$logs->setLogW(__FILE__, __LINE__,"checkin:insert sucessful");
						}
						else {
							$this->outputStr = "上班失败 :(";
							$logs->setLogW(__FILE__, __LINE__,"checkin:insert failed:".$db->error);
						}
						
					}
					else{
					    for ($i=0;$i<$resultCI->num_rows;$i++){
						    $row = $resultCI->fetch_row();
							
						    $timestampCI=strtotime($row[0]);
						    $hourCI=date("H",$timestampCI)+9;
						    if($hourCI>24){$hourCI-=24;}
						    $minuteCI=date("i",$timestampCI);
						    $this->outputStr = "今天已上班,时间".date("Y-m-d H:i:s",$timestampCI).". 预计下班时间: ".$hourCI."点".$minuteCI."分";
						    $logs->setLogW(__FILE__, __LINE__,"checkin:select sucessful");
					    }
					}
				}
				$resultCI->close();
				
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
			$this->outputStr = "下班失败";
			$logs->setLogW(__FILE__, __LINE__,"checkout:select failed:".$db->error);
		}
		else/*if it registered*/
		{
			//registered
			$logs->setLogW(__FILE__, __LINE__,"checkout:select sucessful");
				
			if($result->num_rows==0)
			{
				$this->outputStr = "请先注册，再下班";
			}
			else
			{
				$timestamp=time();
				$hour=date("H",$timestamp);
				$minute=date("i",$timestamp);
				$hourNow=$hour;
				$minuteNow=$minute;
				$logs->setLogW(__FILE__, __LINE__,"checkout:".$hour.":".$minute);
				

				$queryCI = "select datetime from Record where TO_DAYS(datetime)=TO_DAYS('".date("Y-m-d",$timestamp)."') "
							."and ID='".$this->userID."' and inorout=1 order by datetime limit 0,1";
				$logs->setLogW(__FILE__, __LINE__,"checkout:select:".$queryCI);
					
				$resultCI = $db->query($queryCI);
				if(!$resultCI){
					$this->outputStr = "下班失败";
			        $logs->setLogW(__FILE__, __LINE__,"checkout:select failed:".$db->error);
				}else{
					if($resultCI->num_rows==0){
						$this->outputStr = "请先上班，再下班";
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
                        
                        $queryDC = "select datetime from Record where TO_DAYS(datetime)=TO_DAYS('".date("Y-m-d",$timestamp)."') "
							."and ID='".$this->userID."' and inorout=2 order by datetime limit 0,1";
                        $logs->setLogW(__FILE__, __LINE__,"checkout:".$queryDC);
                        $resultDC = $db->query($queryDC);
                        if(!$resultDC){
                        	$this->outputStr = "下班失败";
                        	$logs->setLogW(__FILE__, __LINE__,"checkout:select failed:".$db->error);
                        }else{
                        	if($resultDC->num_rows==0){
                        		//if no today record found, insert
                        		$query2 = "insert into Record values ('".$this->userID."', '".date("Y-m-d H:i:s",$timestamp)."', 2)";/*2-out*/
                        		$logs->setLogW(__FILE__, __LINE__,"checkout:".$query2);
                        		$result2 = $db->query($query2);
                        		if ($result2){
                        			$this->outputStr = "下班成功,时间".date("Y-m-d H:i:s",$timestamp).". 今日工作".$hour."小时".$minute."分钟";
                        			$logs->setLogW(__FILE__, __LINE__,"checkout:insert sucessful");
                        			 
                        			//update weekbalance & monthbalance
                        			$queryBL = "select weekbalance,monthbalance from User where ID='".$this->userID."' limit 0,1";
                        			$logs->setLogW(__FILE__, __LINE__,"checkout:select:".$queryBL);
                        		
                        			$resultBL = $db->query($queryBL);
                        			if(!$resultBL){
                        				$logs->setLogW(__FILE__, __LINE__,"checkout:select failed:".$db->error);
                        			}else{
                        				if($resultBL->num_rows==0){
                        					$logs->setLogW(__FILE__, __LINE__,"checkout:select failed:".$db->error);
                        				}
                        				for ($i=0;$i<$resultBL->num_rows;$i++){
                        					$row = $resultBL->fetch_row();
                        					 
                        					$row[0]+=($hour+$minute/60)-9;
                        					$row[1]+=($hour+$minute/60)-9;
                        		
                        					$query3 = "update User set weekbalance=".$row[0].", monthbalance=".$row[1]." where ID='".$this->userID."'";
                        					$logs->setLogW(__FILE__, __LINE__,"checkout:".$query3);
                        					$result3 = $db->query($query3);
                        					if (!$result3){
                        						$logs->setLogW(__FILE__, __LINE__,"checkout:update failed:".$db->error);
                        					}
                        				}
                        				$resultBL->close();
                        			}
                        			 
                        		}
                        		else {
                        			$this->outputStr = "下班失败 :(";
                        			$logs->setLogW(__FILE__, __LINE__,"checkout:insert failed:".$db->error);
                        		}
                        		
                        	}
                        	else{
                        		$row = $resultDC->fetch_row();
                        		$hourUP=date('H', strtotime($row[0]));
                        		$minuteUP=date('i', strtotime($row[0]));
                        		$logs->setLogW(__FILE__, __LINE__,"checkout:select sucessful. before checkout H:".$hourUP." M:".$minuteUP);
                        		//if today record is found, update
                        		$query2 = "update Record set  datetime='".date("Y-m-d H:i:s",$timestamp)."' where TO_DAYS(datetime)=TO_DAYS('".date("Y-m-d",$timestamp)."') "
                        				."and ID='".$this->userID."' and inorout=2 ";
                        		$logs->setLogW(__FILE__, __LINE__,"checkout:".$query2);
                        		$result2 = $db->query($query2);
                        		if ($result2){
                        			$this->outputStr = "下班成功,时间".date("Y-m-d H:i:s",$timestamp).". 今日工作".$hour."小时".$minute."分钟";
                        			$logs->setLogW(__FILE__, __LINE__,"checkout:update scessful");
                        		
                        			//update weekbalance & monthbalance
                        			$queryBL = "select weekbalance,monthbalance from User where ID='".$this->userID."' limit 0,1";
                        			$logs->setLogW(__FILE__, __LINE__,"checkout:select:".$queryBL);
                        		
                        			$resultBL = $db->query($queryBL);
                        			if(!$resultBL){
                        				$logs->setLogW(__FILE__, __LINE__,"checkout:select failed:".$db->error);
                        			}else{
                        				if($resultBL->num_rows==0){
                        					$logs->setLogW(__FILE__, __LINE__,"checkout:select failed:".$db->error);
                        				}
                        				for ($i=0;$i<$resultBL->num_rows;$i++){
                        					$row = $resultBL->fetch_row();
                        					$logs->setLogW(__FILE__, __LINE__,"checkout:select sucessful. now checkout H:".$hour." M:".$minute);
                        					if($minuteNow>=$minuteUP){
                        						$minute=$minuteNow-$minuteUP;
                        						$hour=$hourNow-$hourUP;
                        					}
                        					else {
                        						$minute=$minuteNow+60-$minuteUP;
                        						$hour=$hourNow-1-$hourUP;
                        					}
                        					
                        					$row[0]+=($hour+$minute/60);
                        					$row[1]+=($hour+$minute/60);
                        		
                        					$query3 = "update User set weekbalance=".$row[0].", monthbalance=".$row[1]." where ID='".$this->userID."'";
                        					$logs->setLogW(__FILE__, __LINE__,"checkout:".$query3);
                        					$result3 = $db->query($query3);
                        					if (!$result3){
                        						$logs->setLogW(__FILE__, __LINE__,"checkout:update failed:".$db->error);
                        					}
                        				}
                        				$resultBL->close();
                        			}
                        		
                        		}
                        		else {
                        			$this->outputStr = "下班失败 :(";
                        			$logs->setLogW(__FILE__, __LINE__,"checkout:update failed:".$db->error);
                        		}
                        	}
                        	$resultDC->close();
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
		global $logs;
		//connect to DB
		@ $db = new mysqli($this->hostname, $this->username, $this->password, $this->dbname);
		if (mysqli_connect_errno())
			exit;
		//find if it is inserted
		$query = "select weekbalance,monthbalance from User where ID='".$this->userID."'";
		$logs->setLogW(__FILE__, __LINE__,"checkbl:select:".$query);
		
		$result = $db->query($query);
		
		if(!$result)
		{
			$this->outputStr = "查询失败";
			$logs->setLogW(__FILE__, __LINE__,"checkbl:select failed:".$db->error);
		}
		else//if select sucessful
		{
			$logs->setLogW(__FILE__, __LINE__,"checkbl:select sucessful");
				
			if($result->num_rows==0)
			{
				$this->outputStr = "请先注册，上班/下班后，再查询";
			}
			else//if it registered
			{
				for ($i=0;$i<$result->num_rows;$i++){
					$row = $result->fetch_row();
					$weekbl=explode ( ".",$row[0]);	
					if($weekbl[0]>0){
						$weekblH=$weekbl[0];
						$weekblM=explode ( ".",$weekbl[1]/10000*60);
						$this->outputStr = "本周结余".$weekblH."小时".$weekblM[0]."分钟.";
					}
					else{
						$weekblH=$weekbl[0]*(-1);
						$weekblM=explode ( ".",$weekbl[1]/10000*60);
						$this->outputStr = "本周缺席".$weekblH."小时".$weekblM[0]."分钟.";
					}
					$this->outputStr .="\n\r";
					$monthbl=explode ( ".",$row[1]);
					if($monthbl[0]>0){
						$monthblH=$monthbl[0];
						$monthblM=explode ( ".",$monthbl[1]/10000*60);
						$this->outputStr .= "本月结余".$monthblH."小时".$monthblM[0]."分钟.";
					}
					else{
						$monthblH=$monthbl[0]*(-1);
						$monthblM=explode ( ".",$monthbl[1]/10000*60);
						$this->outputStr .= "本月缺席".$monthblH."小时".$monthblM[0]."分钟.";
					}
					$logs->setLogW(__FILE__, __LINE__,"checkbl:query week H:".$weekbl[0]." M:".$weekblM[0]
							.".  month H:".$monthbl[0]." M:".$monthblM[0]);
				}
				$result->close();
			}
			
		}
		$db->close();
		//search and notify week balance and month balance in DB User_table
	}
	
	public function CheckRegister ()
	{
		global $logs;
		//connect to DB
		@ $db = new mysqli($this->hostname, $this->username, $this->password, $this->dbname);
		if (mysqli_connect_errno())
			exit;
		//find if it is inserted
		$query = "select ID from User where ID='".$this->userID."'";
		$logs->setLogW(__FILE__, __LINE__,"checkbl:select:".$query);
		
		$result = $db->query($query);
		
		if(!$result)
		{
			$this->outputStr = "查询失败";
			$logs->setLogW(__FILE__, __LINE__,"checkbl:select failed:".$db->error);
		}
		else//if select sucessful
		{
			$logs->setLogW(__FILE__, __LINE__,"checkbl:select sucessful");
			
			if($result->num_rows==0)
			{
				$this->outputStr = "请先注册。";
			}
			else//if it registered
			{
				$this->outputStr = "请输入\"上班\",\"下班\"或\"查询\".";
			}
		}
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