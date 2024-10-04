#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function sendToRabbit($uname, $pword){
$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
$creds = [$uname,$pword];
$response = $client->send_request($creds);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

}

