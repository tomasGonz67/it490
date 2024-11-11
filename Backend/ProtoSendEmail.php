#!/usr/bin/php
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require('PHPMailer/PHPMailerAutoload.php');
require('vendor/autoload.php');

$to      = 'lad5@njit.edu';
$subject = 'rnt real';
$body = 'hello! hope this works';

$headers = [
    'From' => 'Webmaster <webmaster@rntreal.me>',
    'X-Sender' => 'Webmaster <webmaster@rntreal.me>',
    'X-Mailer' => 'PHP/' . phpversion(),
    'X-Priority' => '1',
    'Return-Path' => 'webmaster@rntreal.me',
    'MIME-Version' => '1.0',
    'Content-Type' => 'text/html; charset=iso-8859-1'
];

$body = wordwrap($body, 70, "\r\n");
$res = mail($to, $subject, $body, $headers);
if ($res) {
    echo("Email sent to $to");
} else {
    echo("Email send failed to $to");
}
echo "\n"

?>