<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
if (!isset($_POST))
{
	$msg = "NO POST MESSAGE SET, POLITELY FUCK OFF";
	echo json_encode($msg);
	exit(0);
}
$request = $_POST;
$response = "unsupported request type, politely FUCK OFF";
switch ($request["type"])
{
	
	case "login":
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	$request = [
		'username' => $request['uname'],
		'password' => $request['pword'],
		'type' =>'login',
		'sessionId'=>"four"
	];
	$response = $client->send_request($request);
	echo json_encode($response);
	break;

	case "register":
		$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
		$request = [
			'username' => $request['uname'],
			'password' => $request['pword'],
			'type' =>'register',
		];
		$response = $client->send_request($request);
		echo json_encode($response);
		break;
}

exit(0);
?>
