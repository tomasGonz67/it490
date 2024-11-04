#!/usr/bin/php
<?php
require_once('rabbitMQLib.inc');
function requestProcessor($req) {}
function getFighters(){
			$curl = curl_init();
	                $client = new rabbitMQClient("testRabbitMQ.ini","DMZ");
			curl_setopt($curl, CURLOPT_URL, "https://ufc-api-theta.vercel.app/mma-api/fighters");
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$response = curl_exec($curl);
			curl_close($curl);
			$fightersArray = json_decode($response, true);
			$fightersArray=$fightersArray["fighters"];
            $request = [
                'type' => 'insertFightersDMZ',
                'fighters' => $fightersArray
            ];
            return $response = $client->send_request($request);
        }

$server = new rabbitMQServer("testRabbitMQ.ini", "DMZ");

echo "DMZ SERVER BEGIN".PHP_EOL;
$server->process_requests('getFighters');
echo "DMZ SERVER END".PHP_EOL;

?>
