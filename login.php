<?php
header('Content-Type: application/json');

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
	require 'testRabbitMQClient.php';
	sendToRabbit($request["uname"], $request["pword"]);
	break;
}

function finishLogin($message){
	echo json_encode($message);
	exit(0);
}


?>
