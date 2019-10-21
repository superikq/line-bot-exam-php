<?php



require "vendor/autoload.php";

$access_token = 'oqwqIM9MVlZg/Df6fJ2r9kYAXTLRcStAPltdM6oXo0D7YYkIn6Mf/Omn1OIcmlelvrai3OvTCnnlbwDLc0OnCQubmcvPEbFrXvILkmGFc8c2+1XHqLwc3ysScRJGLXRTo18Wq6hrBN8MIeQpbi3ERwdB04t89/1O/w1cDnyilFU=';

$channelSecret = 'ac5b35088b8ce7153d9649559e5b3eb3';

$pushID = 'Ude928b040ac88f570c6b964ee22c6d99';

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret]);

$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello world');
$response = $bot->pushMessage($pushID, $textMessageBuilder);

echo $response->getHTTPStatus() . ' ' . $response->getRawBody();







