#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');



function register($username, $password){
	$mydb = new mysqli('127.0.0.1','testUser','12345','testdb');
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
	$mydb = new mysqli('127.0.0.1','testUser','12345','testdb');
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	$session= str_replace('"', '', $sess);
	echo("log".$session);
	
	$query = "UPDATE users SET session_key = NULL WHERE session_key ='$session'";
	$response = $mydb->query($query);

	if ($response){
		return "user logged out $sess";
	}
	else {
		return "error";
	}
}

function setHash($username, $hash){
	$mydb = new mysqli('127.0.0.1','testUser','12345','testdb');
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

	$mydb = new mysqli('127.0.0.1','testUser','12345','testdb');
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
