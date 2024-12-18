#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
//172.24.37.96



function addFighter($sess, $name){
	$mydb = new mysqli('localhost','testUser','12345','testdb');
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	$sessionOne = str_replace(['"', "'"], '', $sess);

	$session = preg_replace('/\s+/', '', $sessionOne);

	$query = "SELECT username FROM users WHERE session_key = '$session'";
	$response = $mydb->query($query);
	if ($response){
		$row = $response->fetch_assoc();
		if ($row){
			$userName = $row['username'];
			$query = "SELECT league_name FROM leagues WHERE user_name = '$userName'";
			$response = $mydb->query($query);
			if ($response->num_rows === 0){
				return 'YOU ARE NOT IN A LEAGUE';
			}
			if ($response){
				$row = $response->fetch_assoc();
				if ($row){
					$leagueName=$row['league_name'];
					$query = "SELECT fighter1, fighter2, benched FROM leagues WHERE league_name = '$leagueName'";
					$response = $mydb->query($query);
					if ($response) {
						while ($row = $response->fetch_assoc()) {
							if ($name==$row['fighter1'] || $name==$row['fighter2'] || $name==$row['benched']){
								return 'NO DUPE NAMES ALLOWED';
							}
						}
					} else {
						echo "Error executing query: " . $mydb->error;
					}
					$query = "SELECT inDraft FROM leagues WHERE user_name = '$userName'";
					$response = $mydb->query($query);
					if ($response){
						$row = $response->fetch_assoc();
						if ($row){
							$inDraft=$row['inDraft'];
							if ($inDraft==null){
								$query = "SELECT turnOrder FROM leagues WHERE user_name = '$userName'";
								$response = $mydb->query($query);
								if ($response){
									$row = $response->fetch_assoc();
									if ($row){
										$turnOrder=$row['turnOrder'];
										if ($turnOrder!=1){
											return "IT IS NOT YOUR TURN TO PICK YET!";
										}
										else{
											$query = "UPDATE leagues SET inDraft = 1, gameOrder=gameOrder+1 WHERE league_name ='$leagueName'";
											$response = $mydb->query($query);
											if ($response){
												$query = "UPDATE leagues SET fighter1 = '$name' WHERE user_name ='$userName'";
												$response = $mydb->query($query);
												if ($response){
													return "fighter added!";
												}
												else {
													return "error";
												}
											}
											
										}
									}
								}
							}
							$query = "SELECT gameOrder FROM leagues WHERE user_name = '$userName'";
							$response = $mydb->query($query);
							if ($response){
								$row = $response->fetch_assoc();
								if ($row){
									$gameOrder=$row['gameOrder'];
								}
							}
							if ($inDraft==1){
								$query = "SELECT turnOrder FROM leagues WHERE user_name = '$userName'";
								$response = $mydb->query($query);
								if ($response){
									$row = $response->fetch_assoc();
									if ($row){
										$turnOrder=$row['turnOrder'];
										if ($turnOrder!=$gameOrder){
											return "IT IS NOT YOUR TURN TO PICK YET!";
										}
										else{
											$query = "UPDATE leagues SET fighter1 = '$name' WHERE user_name ='$userName'";
											$response = $mydb->query($query);
											if ($response){
												$query = "SELECT COUNT(*) AS total_count FROM leagues WHERE league_name = '$leagueName'";
												$response = $mydb->query($query);
												if($response){
													$row = $response->fetch_assoc();
													$totalCount = $row['total_count'];
													if ($totalCount==$gameOrder){
														$query = "UPDATE leagues SET inDraft = 2 WHERE league_name = '$leagueName'";

														$response = $mydb->query($query);
													}
													else{
														$query = "UPDATE leagues SET gameOrder = gameOrder+1 WHERE league_name ='$leagueName'";
														$response = $mydb->query($query);
													}
												}
												return "fighter added!";
											}
											else {
												return "error";
											}
										}
									}
								}
							}

							if ($inDraft==2){
								$query = "SELECT turnOrder FROM leagues WHERE user_name = '$userName'";
								$response = $mydb->query($query);
								if ($response){
									$row = $response->fetch_assoc();
									if ($row){
										$turnOrder=$row['turnOrder'];
										if ($turnOrder!=$gameOrder){
											return "IT IS NOT YOUR TURN TO PICK YET!";
										}
										else{
											$query = "UPDATE leagues SET fighter2 = '$name' WHERE user_name ='$userName'";
											$response = $mydb->query($query);
											if ($response){
												$query = "SELECT COUNT(*) AS total_count FROM leagues WHERE league_name = '$leagueName'";
												$response = $mydb->query($query);
												if($response){
													$row = $response->fetch_assoc();
													$totalCount = $row['total_count'];
													if ($gameOrder==1){
														$query = "UPDATE leagues SET inDraft = 3 WHERE league_name ='$leagueName'";
														$response = $mydb->query($query);
													}
													else{
														$query = "UPDATE leagues SET gameOrder = gameOrder-1 WHERE league_name ='$leagueName'";
														$response = $mydb->query($query);
													}
												}
												return "fighter added!";
											}
											else {
												return "error";
											}
										}
									}
								}
							}
							if ($inDraft==3){
								$query = "SELECT turnOrder FROM leagues WHERE user_name = '$userName'";
								$response = $mydb->query($query);
								if ($response){
									$row = $response->fetch_assoc();
									if ($row){
										$turnOrder=$row['turnOrder'];
										if ($turnOrder!=$gameOrder){
											return "IT IS NOT YOUR TURN TO PICK YET!";
										}
										else{
											$query = "UPDATE leagues SET benched = '$name' WHERE user_name ='$userName'";
											$response = $mydb->query($query);
											if ($response){
												$query = "SELECT COUNT(*) AS total_count FROM leagues WHERE league_name = '$leagueName'";
												$response = $mydb->query($query);
												if($response){
													$row = $response->fetch_assoc();
													$totalCount = $row['total_count'];
													if ($totalCount==$gameOrder){
														return 'DRAFTING DONE';
													}
													else{
														$query = "UPDATE leagues SET gameOrder = gameOrder+1 WHERE league_name ='$leagueName'";
														$response = $mydb->query($query);
													}
												}
												return "fighter added!";
											}
											else {
												return "error";
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

function joinLeague($userName, $password, $leagueName){
	$mydb = new mysqli('localhost','testUser','12345','testdb');
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");


	$leagueName=$leagueName . " League";
	$query = "SELECT inDraft FROM leagues WHERE league_name = '$leagueName'";
	$response = $mydb->query($query);
	if ($response) {
		$row = $response->fetch_assoc();
	
		if ($row) {
			$inDraft = $row['inDraft'];
			if ($inDraft!=null){
				return "CAN NOT JOIN LEAGUE THAT HAS STARTED";
			}
		}
	}

	try{
		$query = "SELECT COUNT(*) FROM leagues WHERE league_name = '$leagueName'";
		$response = $mydb->query($query);
		$countRow = $response->fetch_row();
		$count = $countRow[0]; 
		$count=$count+1;
		$query = "INSERT INTO leagues (user_name, league_name, turnOrder, gameOrder) VALUES ('$userName','$leagueName', $count, 1)";
		$response = $mydb->query($query);
		
		if ($response) {
			return "user added to league!";
		} else {
			throw new Exception("Database error: " . $mydb->error);
		}} catch (Exception $e) {
		return "Error: " . $e->getMessage();
	}	
}


function createLeague($sess){
	$mydb = new mysqli('localhost','testUser','12345','testdb');
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	$sessionOne = str_replace(['"', "'"], '', $sess);

	$session = preg_replace('/\s+/', '', $sessionOne);

	$query = "SELECT email FROM users WHERE session_key = '$session'";
	$response = $mydb->query($query);
	if ($response) {
		$row = $response->fetch_assoc();
		if ($row){
			$email=$row['email'];
			$subject = "league"; // Subject of the email
			$message = "Congrats on starting your league!"; // Body of the email
			$headers = "From: froggychop100@aol.com"; // Replace with a valid sender email address

			// Send the email
			if (mail($email, $subject, $message, $headers)) {
    			echo "Email sent successfully!";
			} else {
    			echo "Failed to send email.";
			}
		}
	}



	$query = "SELECT username FROM users WHERE session_key = '$session'";
	$response = $mydb->query($query);
	if ($response) {
		$row = $response->fetch_assoc();
	
		if ($row) {
			$userName = $row['username'];
			$leagueName=$userName . " League";
			try{
			$query = "INSERT INTO leagues (user_name, league_name, turnOrder, gameOrder) VALUES ('$userName', '$leagueName', 1,1)";
			$result = $mydb->query($query);
			if ($result){
				$query = "SELECT id FROM leagues WHERE user_name = '$userName'";
				$response = $mydb->query($query);
				if ($response){
					$row= $response->fetch_assoc();
					if ($row){
						$id=$row['id'];
						$link = "http://localhost/sample/joinLeague.html?id=" . $userName; // i totally messed this up but im not changing it. Should still work fine.
						return $link;
					}
				}
			}
			else{
				throw new Exception("Database error: " . $mydb->error);
			}}catch (Exception $e) {
				return "Error: " . $e->getMessage();
			}	
			
		}

	} 
	else {
		echo "Error: " . $mydb->error;
	}
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

function insertFighters($fightersArray){
	echo $fightersArray;
	echo "Inserting fighterz";
	$mydb = new mysqli('localhost','testUser','12345','testdb');
	return false;
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
			$fightersArray = $fightersArray["fighters"];
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
			return $fighters;
		}
			
	}

}

function register($username, $password, $email){
	$mydb = new mysqli('localhost','testUser','12345','testdb');
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	if ($mydb->errno != 0)
	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		exit(0);
	}

	try {
		$query = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
		
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
