﻿<?php

//header("Content-Type: text/html; charset=utf-8");
//header(“Content-Type: text/html; charset=utf-8");


include ('WorkingHoursClass.php');

// wechat class definition
class wechatCallbackapiTest
{
	public $postStr; //post data string as attribute
	public $postObj; //post data as attribute
	/*
	<xml>
	<ToUserName><![CDATA[toUser]]></ToUserName>
	<FromUserName><![CDATA[fromUser]]></FromUserName>
	<CreateTime>1348831860</CreateTime>
	<MsgType><![CDATA[text]]></MsgType>
	<Content><![CDATA[this is a test]]></Content>
	<MsgId>1234567890123456</MsgId>
	</xml>
	*/
	public $resultStr = ""; //returned data string to Weixin server

	// define return data format according to Weixin XML data below
	/*
	<xml>
	<ToUserName><![CDATA[toUser]]></ToUserName>
	<FromUserName><![CDATA[fromUser]]></FromUserName>
	<CreateTime>12345678</CreateTime>
	<MsgType><![CDATA[text]]></MsgType>
	<Content><![CDATA[content]]></Content>
	<FuncFlag>0</FuncFlag>
	</xml>
	*/
	public $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";

	public function WorkingHoursProcess ()
	{
		global $logs;
		//define WorkingHours object
		$whObj = new WorkingHours();
		$whObj->userID = $this->postObj->FromUserName;
		
		//excute actions with DB
		$actionCommand = trim($this->postObj->Content);

		$logs->setLogW(__FILE__, __LINE__,$this->postObj->ToUserName.";".
				$this->postObj->FromUserName.";".
				$this->postObj->CreateTime.";".
				$this->postObj->MsgType.";".
				$this->postObj->Content.";".
				$this->postObj->FuncFlag.";");
		switch ($actionCommand)
		{
			case "注册" :
				$whObj->Register();
				$this->resultStr = $whObj->outputStr;
				break;
			case "上班" :
				$whObj->CheckIn();
				$this->resultStr = $whObj->outputStr;
				break;
			case "下班" :
				$whObj->CheckOut();
				$this->resultStr = $whObj->outputStr;
				break;
			case "查询" :
				$whObj->CheckBalance();
				$this->resultStr = $whObj->outputStr;
				break;
			case "请假半天" :
			    $whObj->requestHalfDayOff();
				$this->resultStr = $whObj->outputStr;
				break;
			case "请假全天" :
				$whObj->requestOneDayOff();
				$this->resultStr = $whObj->outputStr;
				break;
			case "记录" :
				$whObj->showRecord();
				$this->resultStr = $whObj->outputStr;
				break;
				
			case "帮助" :
			default :
				$helpMsg = "Fintele可以帮你通过微信管理工作时间，输入下面的关键字，就能体验多种功能：\n\n".
							"- 注册：注册之后即可记录并查询时间\n\n".
							"- 上班：记录每天上班时间，并建议下班时间。只记录最早的一次上班时间\n\n".
							"- 下班：记录每天下班时间，并显示当天工作时间。记录最晚的一次下班时间，可以多次更新下班时间\n\n".
							"- 查询：查询本周结余和本月结余。本周结余周末自动归零，本月结余月末自动归零\n\n".
							"- 请假半天：请半天假。本天自动补半天工作时间\n\n".
							"- 请假全天：请全天假。本天自动补一天工作时间\n\n".
							"- 记录：查询最近20次上下班和请假记录\n\n".
							"- 帮助：现在正在查看的就是哦\n\n".
							"更多功能，敬请期待！如有建议，欢迎反馈 ^_^";
				$this->resultStr = $helpMsg;
				//$whObj->CheckRegister();
				//$this->resultStr = $whObj->outputStr;
				break;		
		}
	}
	
	
	public function sendResult()
	{   
		global $logs;
		//extract post data
		$fromUsername = $this->postObj->FromUserName;
		$toUsername = $this->postObj->ToUserName;
		$textContent = trim($this->postObj->Content);
		$time_sender = (int)$this->postObj->CreateTime;
		$messageType = $this->postObj->MsgType;
		//form response
		$time = time();
		$msgType = "text";
		$resultXMLStr = sprintf($this->textTpl, $fromUsername, $toUsername, $time, $msgType, $this->resultStr);
		//send response
		$logs->setLogW(__FILE__, __LINE__,$resultXMLStr);
		echo $resultXMLStr;
	}

	public function getPOST() //get post data
	{
		//get post string, May be due to the different environments
		$this->postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		
		//get post data into XML object, and to DataArray
		if (!empty($this->postStr))
			$this->postObj = simplexml_load_string($this->postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		else
			$this->postObj = 0;
	}

	public function responseMsg()
	{
		if($this->postObj)
		{
			//extract NECESSARY post data
			$fromUsername = $this->postObj->FromUserName;
			$textContent = trim($this->postObj->Content);
			$messageType = $this->postObj->MsgType;
			$time_sender = (int)$this->postObj->CreateTime;
			$time = time();
			//form content string
			if(!empty( $textContent ))
				$contentStr = $fromUsername." sent ".$messageType.": ".$textContent."\n \nSent ".date('Y-n-j H:i:s', $time_sender)."\n \nReplied ".date('Y-n-j H:i:s', $time)."\n\n";
			else
				$contentStr = "Input something...\n";
			//append content string to result string
			$this->resultStr = $this->resultStr.$contentStr;
		}
		else
			exit;
	}

	public function keywordDetection()
	{
		//define keyword which is to be detected
		$keyword = "haha";

		if ($this->postObj)
		{
			//extract post data
			$textContent = trim($this->postObj->Content);
			//form response
			if(strstr($textContent,$keyword))
				$contentStr = "Keyword is correct :)\n\n";
			else
				$contentStr = "Keyword is incorrect :(\nTry again :)\n\n";
			//append content string to result string
			$this->resultStr = $this->resultStr.$contentStr;
		}
		else
			exit;
	}


	public function valid()
	{
		$echoStr = $_GET["echostr"];

		//valid signature , option
		if($this->checkSignature()){
			echo $echoStr;
			exit;
		}
	}

	private function checkSignature()
	{
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];

		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );

		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>