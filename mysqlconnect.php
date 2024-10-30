#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
//172.24.37.96


function createLeague($sess){
	$mydb = new mysqli('localhost','testUser','12345','testdb');
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	$sessionOne = str_replace(['"', "'"], '', $sess);

	$session = preg_replace('/\s+/', '', $sessionOne);

	$query = "SELECT username FROM users WHERE session_key = '$session'";
	$response = $mydb->query($query);
	if ($response) {
		$row = $response->fetch_assoc();
	
		if ($row) {
			$userName = $row['username'];
			$leagueName=$userName . " League";
			$query = "INSERT INTO leagues (user_name, league_name) VALUES ('$userName', '$leagueName')";
			$result = $mydb->query($query);
			return "link";
		}

	} 
	else {
		// Handle query error
		echo "Error: " . $mydb->error;
	}
    //$leagueName = $userName . " League";
	
		
	
}


function getMessage($name, $message){
	$mydb = new mysqli('localhost','testUser','12345','testdb');
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	try{
		$query = "INSERT INTO userMessages (name, message) VALUES ('$name','$message')";
		$response = $mydb->query($query);
		
		if ($response) {
			return "Message Recieved!";
		} else {
			throw new Exception("Database error: " . $mydb->error);
		}} catch (Exception $e) {
		return "Error: " . $e->getMessage();
	}	
}

function getFighters(){
	$mydb = new mysqli('localhost','testUser','12345','testdb');
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	if ($mydb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		exit(0);
	}

	$query = "select * from fighters;";
	$response = $mydb->query($query);
	
	if ($response){

		if ($response->num_rows > 0){
			$fighters =[];
			while($row = $response->fetch_assoc()) {
				$fighters[] = $row;
			}
			return $fighters;
		}

		else{
			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, "https://ufc-api-theta.vercel.app/mma-api/fighters");
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$response = curl_exec($curl);
			curl_close($curl);
			$fightersArray = json_decode($response, true);
			$fightersArray=$fightersArray["fighters"];
			$fighters=[];
			foreach ($fightersArray as $fighter) {
				$fighter_id = $mydb->real_escape_string($fighter['fighter_id']);
				$name = $mydb->real_escape_string($fighter['name']);
				$height = $mydb->real_escape_string($fighter['height']);
				$weight = $mydb->real_escape_string($fighter['weight']);
				$n_win = $fighter['n_win']; 
				$n_loss = $fighter['n_loss']; 
				$n_draw = $fighter['n_draw'];
			
				$query = "INSERT INTO fighters (fighter_id, name, height, weight, n_win, n_loss, n_draw) 
						  VALUES ('$fighter_id', '$name', '$height', '$weight', $n_win, $n_loss, $n_draw)";
			
				$result = $mydb->query($query);
			
				if (!$result) {
					echo "Error inserting fighter: " . $mydb->error;
				}
			}
			$query = "select * from fighters;";
			$result = $mydb->query($query);
			while($row = $result->fetch_assoc()) {
				$fighters[] = $row;
			}
			var_dump($fighters);
			return $fighters;
		}
			
	}

}

function register($username, $password){
	$mydb = new mysqli('localhost','testUser','12345','testdb');
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	if ($mydb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		exit(0);
	}

	try {
		$query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
		
		$response = $mydb->query($query);
		
		if ($response) {
			echo "User added";
			return "User added to database";
		} else {
			echo "user not added";
			throw new Exception("Database error: " . $mydb->error);
		}
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
		return "Error: " . $e->getMessage();
	}

}

function logout($sess){
	$mydb = new mysqli('localhost','testUser','12345','testdb');
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	$sessionOne = str_replace(['"', "'"], '', $sess);

	$session = preg_replace('/\s+/', '', $sessionOne);
	echo "pls".$session;
	$query = "UPDATE users SET session_key = NULL WHERE session_key ='$session'";
	$response = $mydb->query($query);

	if ($response){
		return "loggedOut";
	}
	else {
		return "error";
	}
}

function setHash($username, $hash){
	$mydb = new mysqli('localhost','testUser','12345','testdb');
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	$query = "UPDATE users SET session_key = '$hash' WHERE username = '$username'";
	$response = $mydb->query($query);

	if ($response){
		return $hash;
	}
	else {
		return "error";
	}
}

function checkLogin($username, $password, $hash){

	$mydb = new mysqli('localhost','testUser','12345','testdb');
$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

if ($mydb->errno != 0)
{
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	exit(0);
}



	$query = "select * from users;";

	$response = $mydb->query($query);
	
	if ($response){
		while ($row=$response->fetch_assoc()){
			if ($username==$row['username'] && $password==$row['password']){
				return setHash($username, $hash);
				exit(0);
			}
			
		}
		return 'Login FAILED';
		exit(0);
	}

	if ($mydb->errno != 0)
{
	echo "failed to execute query:".PHP_EOL;
	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
	exit(0);
}
}





?>
