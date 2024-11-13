<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

echo ("asd");
if (isset($_GET['type'])) {
    switch ($_GET['type']) {
        case 'getFighters':
            $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
            $request = [
                'type' => 'getFighters'
            ];
            $response = $client->send_request($request);
            echo json_encode($response);
            break;
    }
}


if (!isset($_POST) || !isset($_GET))
{
	$msg = "NO POST MESSAGE SET, POLITELY FUCK OFF";
	echo json_encode($msg);
	exit(0);
}
$request = $_POST;
$response = "unsupported request type, politely FUCK OFF";
switch ($request["type"])
{

	case "addFighter":
		$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
		$request = [
			'type' =>'addFighter',
			'session'=>$request['sess'],
			'name'=>$request['name']
		];
		$response = $client->send_request($request);
		echo json_encode($response);
		break;

	case "joinLeague":
		$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
		$request = [
			'username' => $request['uname'],
			'password' => $request['pword'],
			'id'=> $request['id'],
			'type' =>'joinLeague'
		];
		$response = $client->send_request($request);
		echo json_encode($response);
		break;

	case "createLeague":
		$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
		$request = [
			'type' =>'createLeague',
			'session'=>$request['sess']
		];
		$response = $client->send_request($request);
		echo json_encode($response);
		break;

	case "sendMessage":
		$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
		$request = [
			'name' => $request['name'],
			'message' => $request['message'],
			'type' =>'sendMessage',
		];
		$response = $client->send_request($request);
		echo json_encode($response);
		break;

	case "login":
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	$request = [
		'username' => $request['uname'],
		'password' => $request['pword'],
		'type' =>'login',
	];
	$response = $client->send_request($request);
	echo json_encode($response);
	break;

	case "register":
		$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
		$request = [
			'username' => $request['uname'],
			'password' => $request['pword'],
			'email' => $request['email'],
			'type' =>'register',
		];
		$response = $client->send_request($request);
		echo json_encode($response);
		break;

	case "logout":
		$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
		$request = [
			'type' =>'logout',
			'session'=>$request['sess']
		];
		$response = $client->send_request($request);
		echo json_encode($response);
		break;


}

exit(0);
?>
