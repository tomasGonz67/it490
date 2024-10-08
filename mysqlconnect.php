#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');




function checkLogin($username, $password){

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
				return 'Login successful';
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
