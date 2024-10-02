#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  if (!isset($_POST)){
	  $msg = "NO POST MESSAGE SET, POLITELY FUCK OFF";
	  //echo json_encode($msg);
	  exit(0);
  }
}
else
{
  $msg = "test message";
}

$request = $_POST;
$response = "unsupported request type, politely FUCK OFF";
switch ($request["type"])
{
	case "login":
	  $request['type'] = "Login";
    $request['message'] = $msg;
	break;
}

$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

