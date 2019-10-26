<?php
require "vendor/autoload.php";

$AccessToken = $_POST["AccessToken"];
$ChannelSecret = $_POST["ChannelSecret"];
$LineID = $_POST["LineID"];
$MessageType = $_POST["MessageType"];
$altText = $_POST["altText"];
$Message = $_POST["Message"];

switch ($MessageType) {
case "text":
	$emoji_prefix = '0x100';
	while (strpos($Message,$emoji_prefix)!==false) {
		$emoji_position = strpos($Message,$emoji_prefix);
		$emoji_code = substr($Message,$emoji_position,8);
		$emoji_code2 = substr($emoji_code,0,1) . '0' . substr($emoji_code,2,6);
		$bin = hex2bin($emoji_code2);
		$emoji_id = mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');
		$Message = str_replace($emoji_code,$emoji_id,$Message);
	}
	$Message = str_replace(PHP_EOL,'\n',$Message);
	$Message = str_replace('"','\"',$Message);
	$Message = '{ "type": "text", "text": "' . $Message . '"}';
	break;
case "image":
	$Message = '{ "type": "image", "originalContentUrl": "' . $Message . '", "previewImageUrl": "' . $Message . '" }';
	break;
case "video":
	$Message = '{ "type": "video", "originalContentUrl": "' . $Message . '", "previewImageUrl": "' . $Message . '" }';
	break;
case "audio":
	$Message = '{ "type": "audio", "originalContentUrl": "' . $Message . '", "duration": 60000 }';
	break;
case "location":
	break;
case "flex":
	$Message = '{ "type": "flex", "altText": "' . $altText . '", "contents": ' . $Message . ' }';
	break;
}

$API_URL = 'https://api.line.me/v2/bot/message/push';
$post_header = array('Content-Type: application/json', 'Authorization: Bearer ' . $AccessToken);
$json_string = '{ "to": "' . $LineID . '", "messages": [' . $Message . ']}';

var_dump($json_string);

$ch = curl_init($API_URL);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($ch);
curl_close($ch);
echo $result . "\r\n";

?>

