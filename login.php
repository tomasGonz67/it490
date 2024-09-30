<?php
header('Content-Type: application/json'); // Set the content type to JSON

// Check if the POST request is empty
if (empty($_POST)) {
    $response = ["message" => "No POST message set, politely fuck off"];
    echo json_encode($response); // Send the JSON response
    exit(0);
}

$request = $_POST;
$response = ["message" => "Unsupported request type, politely FUCK OFF"]; // Default response

// Check the type of request
if (isset($request["type"])) {
    switch ($request["type"]) {
        case "login":
            // Here, we can assume login logic or success response
            $response = ["message" => "Login successful!"]; // Example response for a successful login
            break;
    }
}

// Return the response as JSON
echo json_encode($response); // Encode and send the response
exit(0);
?>
