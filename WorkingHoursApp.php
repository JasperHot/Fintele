<?php

//set timezone for PRC
date_default_timezone_set('PRC');

include ('wxTextMsgClass.php');

$wechatObj = new wechatCallbackapiTest();
$wechatObj->getPOST();
//$wechatObj->responseMsg();
//$wechatObj->keywordDetection();
$wechatObj->WorkingHoursProcess();
$wechatObj->sendResult();
?>