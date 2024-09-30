<?php
header('Content-Type: application/json');

// Check if the POST request is empty
if (empty($_POST)) {
    $response = ["message" => "No POST message set, politely fuck off"];
    echo json_encode($response);
    exit(0);
}

$request = $_POST;
$response = ["message" => "Unsupported request type, politely FUCK OFF"]; // Use an associative array for the response

// Check the type of request
if (isset($request["type"])) {
    switch ($request["type"]) {
        case "login":
            $response = ["message" => "Login, yeah we can do that"]; // Set the response as an associative array
            break;
    }
}

// Return the response as JSON
echo json_encode($response);
exit(0);
?>
