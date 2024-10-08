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
      return register($request['username'],$request['password']);

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

