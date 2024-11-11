#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('DMZFunctions.php');

function requestProcessor($request) {
	echo "received request".PHP_EOL;
	var_dump($request);
	if(!isset($request['type'])) {
	  return "ERROR: unsupported message type";
	}

	switch ($request['type']) {
		case "getFightersDMZ":
			$response_msg = getFightersServerside();
			break;
		default:
			$response_msg = array("returnCode" => '0', 'message'=>"Server received request and processed");
			break;
	}
	return $response_msg;
	// return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

function getFightersServerside() {
	// Sends an API call to get fighters. named differently to avoid the DBMachine accidentally running the DMZMachine's functions
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://ufc-api-theta.vercel.app/mma-api/fighters");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($curl);
	// echo $response;

	curl_close($curl);
	$fightersArray = json_decode($response, true);
	$fightersArray=$fightersArray["fighters"];
	$request = [
		'type' => 'insertFightersDMZ',
		'fighters' => $fightersArray
	];
	return $fightersArray;
}

$server = new rabbitMQServer("rabbitConfig.ini", "DMZ");

echo "DMZ SERVER BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "DMZ SERVER END".PHP_EOL;

?>
