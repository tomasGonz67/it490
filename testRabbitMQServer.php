#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($username,$password)
{

    //return false if not valid
}


function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "insertFightersDMZ":
      require_once 'mysqlconnect.php';
      return insertFighters($request['fighters']);
    case "getFightersDMZ":
      require_once 'DMZ.php';
      return getFighters();
    case "addFighter":
      require_once 'mysqlconnect.php';
      return addFighter($request['session'], $request['name']);
    case "joinLeague":
      require_once 'mysqlconnect.php';
      return joinLeague($request['username'], $request['password'], $request['id']);
    case "createLeague":
      require_once 'mysqlconnect.php';
      return createLeague($request['session']);
    case "sendMessage":
      require_once 'mysqlconnect.php';
      return getMessage($request['name'], $request['message']);
    case "getFighters":
      require_once 'mysqlconnect.php';
      return getFighters();
    case "logout":
      require_once 'mysqlconnect.php';
      return logout($request['session']);
    case "login":
      $id=uniqid();
      $hash = md5($id);
      require_once 'mysqlconnect.php';
      return checkLogin($request['username'],$request['password'], $hash);

    case "register":
      require_once 'mysqlconnect.php';
      return register($request['username'],$request['password'], $request['email']);

    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

