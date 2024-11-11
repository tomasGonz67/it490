#!/usr/bin/php
<?php

// https://www.textmagic.com/docs/api/php/
// https://www.textmagic.com/docs/api/
// https://www.textmagic.com/docs/api/start/

// MAKE SURE YOU'RE IN THE BACKEND FOLDER BEFORE RUNNING THIS
// composer require textmagic/sdk dev-master
require('vendor/autoload.php');

$client = new TextmagicRestClient('<USERNAME>', '<APIV2_KEY>');
$result = ' ';
try {
    $result = $client->messages->create(
        array(
            'text' => 'Hello from Textmagic PHP',
            'phones' => implode(', ', array('99900000'))
        )
    );
}
catch (\Exception $e) {
    if ($e instanceof RestException) {
        print '[ERROR] ' . $e->getMessage() . "\n";
        foreach ($e->getErrors() as $key => $value) {
            print '[' . $key . '] ' . implode(',', $value) . "\n";
        }
    } else {
        print '[ERROR] ' . $e->getMessage() . "\n";
    }
    return;
}
echo $result['id'];


?>