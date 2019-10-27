<?php
// Get Data From POST Http Request
$datas = file_get_contents('php://input');
$deCode = json_decode($datas,true);
file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);
	
$type = $deCode['events'][0]['type'];
$replyToken = $deCode['events'][0]['replyToken'];
$userId = $deCode['events'][0]['source']['userId'];

// Get user profile
$url = "https://api.line.me/v2/bot/profile/".$userId;
$AccessToken = "oqwqIM9MVlZg/Df6fJ2r9kYAXTLRcStAPltdM6oXo0D7YYkIn6Mf/Omn1OIcmlelvrai3OvTCnnlbwDLc0OnCQubmcvPEbFrXvILkmGFc8c2+1XHqLwc3ysScRJGLXRTo18Wq6hrBN8MIeQpbi3ERwdB04t89/1O/w1cDnyilFU=";
$results = getLINEProfile($url, $AccessToken);
file_put_contents('log-profile.txt', $results['message'] . PHP_EOL, FILE_APPEND);	
$deProfile = json_decode($results['message'],true);
$displayName = $deProfile['displayName'];

$url = "https://api.line.me/v2/bot/message/reply";
$AccessToken = "oqwqIM9MVlZg/Df6fJ2r9kYAXTLRcStAPltdM6oXo0D7YYkIn6Mf/Omn1OIcmlelvrai3OvTCnnlbwDLc0OnCQubmcvPEbFrXvILkmGFc8c2+1XHqLwc3ysScRJGLXRTo18Wq6hrBN8MIeQpbi3ERwdB04t89/1O/w1cDnyilFU=";
switch ($type) {
case "join":
	$Message = '{ "type": "text", "text": "สวัสดีครับทุกคน"}';
	$results = sendLineMessage($url, $AccessToken, $replyToken, $Message);
	echo "Result: " . $result . "\r\n";
	break;
case "message":
	$Message = '{ "type": "text", "text": "ขอบคุณครับ คุณ  ' . $displayName . '\n( ' . $userId . ' )"}';
	$results = sendLineMessage($url, $AccessToken, $replyToken, $Message);
	echo "Result: " . $result . "\r\n";
	break;
case "leave":
	break;
}

function sendLineMessage ($url, $AccessToken, $replyToken, $Message)
{
	$post_header = array('Content-Type: application/json', 'Authorization: Bearer ' . $AccessToken);
	$post_body = '{ "replyToken": "' . $replyToken . '", "messages": [' . $Message . ']}';

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}

function getLINEProfile ($url, $AccessToken)
{
	$datasReturn = [];
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
			"Authorization: Bearer ".$AccessToken,
			"cache-control: no-cache"
		),
	));
	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	if($err){
		$datasReturn['result'] = 'E';
		$datasReturn['message'] = $err;
	}else{
		if($response == "{}"){
			$datasReturn['result'] = 'S';
			$datasReturn['message'] = 'Success';
		}else{
			$datasReturn['result'] = 'E';
			$datasReturn['message'] = $response;
		}
	}
	return $datasReturn;
}	

?>
