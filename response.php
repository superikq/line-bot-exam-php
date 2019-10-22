<?php 
	define('UPLOAD_DIR', 'tmp_image/');
	/*Get Data From POST Http Request*/
	$datas = file_get_contents('php://input');
	/*Decode Json From LINE Data Body*/
	$deCode = json_decode($datas,true);

	file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);

// -- เก็บ รูป ที่ส่งมา
  	$LINEDatas['token'] = "oqwqIM9MVlZg/Df6fJ2r9kYAXTLRcStAPltdM6oXo0D7YYkIn6Mf/Omn1OIcmlelvrai3OvTCnnlbwDLc0OnCQubmcvPEbFrXvILkmGFc8c2+1XHqLwc3ysScRJGLXRTo18Wq6hrBN8MIeQpbi3ERwdB04t89/1O/w1cDnyilFU=";
	$messageType = $deCode['events'][0]['message']['type'];
	if($messageType == 'image'){
		$LINEDatas['messageId'] = $deCode['events'][0]['message']['id'];
		$results = getContent($LINEDatas);
		if($results['result'] == 'S'){
		$file = UPLOAD_DIR . uniqid() . '.png';
		$success = file_put_contents($file, $results['response']);
		}
	}
	
// -- เก็บ User Profile & Log
	$replyToken = $deCode['events'][0]['replyToken'];
	$userId = $deCode['events'][0]['source']['userId'];

	$LINEDatas['url'] = "https://api.line.me/v2/bot/profile/".$userId;
  	$LINEDatas['token'] = "oqwqIM9MVlZg/Df6fJ2r9kYAXTLRcStAPltdM6oXo0D7YYkIn6Mf/Omn1OIcmlelvrai3OvTCnnlbwDLc0OnCQubmcvPEbFrXvILkmGFc8c2+1XHqLwc3ysScRJGLXRTo18Wq6hrBN8MIeQpbi3ERwdB04t89/1O/w1cDnyilFU=";
	$results = getLINEProfile($LINEDatas);
	file_put_contents('log-profile.txt', $results['message'] . PHP_EOL, FILE_APPEND);	
	$deProfile = json_decode($results['message'],true);
	$displayName = $deProfile['displayName'];

// --- ตอบกลับ	
	$messages = [];
	$messages['replyToken'] = $replyToken;
	$messages['messages'][0] = getFormatTextMessage("ยินดีต้อนรับสู่ระบบ Auto FCP reports คุณ " . $displayName);
	$encodeJson = json_encode($messages);
	$LINEDatas['url'] = "https://api.line.me/v2/bot/message/reply";
  	$LINEDatas['token'] = "oqwqIM9MVlZg/Df6fJ2r9kYAXTLRcStAPltdM6oXo0D7YYkIn6Mf/Omn1OIcmlelvrai3OvTCnnlbwDLc0OnCQubmcvPEbFrXvILkmGFc8c2+1XHqLwc3ysScRJGLXRTo18Wq6hrBN8MIeQpbi3ERwdB04t89/1O/w1cDnyilFU=";
  	$results = sentMessage($encodeJson,$LINEDatas);
	
	/*Return HTTP Request 200*/
	http_response_code(200);

	function getFormatTextMessage($text)
	{
		$datas = [];
		$datas['type'] = 'text';
		$datas['text'] = $text;
		return $datas;
	}

	function sentMessage($encodeJson,$datas)
	{
		$datasReturn = [];
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $datas['url'],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $encodeJson,
			CURLOPT_HTTPHEADER => array(
				"authorization: Bearer ".$datas['token'],
				"cache-control: no-cache",
				"content-type: application/json; charset=UTF-8",
			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
		    $datasReturn['result'] = 'E';
		    $datasReturn['message'] = $err;
		} else {
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

	function getLINEProfile($datas)
	{
		$datasReturn = [];
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $datas['url'],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer ".$datas['token'],
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

	function getContent($datas)
	{
		$datasReturn = [];
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.line.me/v2/bot/message/".$datas['messageId']."/content",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_POSTFIELDS => "",
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer ".$datas['token'],
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
			$datasReturn['result'] = 'S';
			$datasReturn['message'] = 'Success';
			$datasReturn['response'] = $response;
		}
		return $datasReturn;
	}
	
?>
