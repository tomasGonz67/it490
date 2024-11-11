#!/usr/bin/php
<?php

// this space left intentionally blank

$to      = 'example@example.com';
$subject = 'rnt real';
$message = 'hello! hope this works';
$headers = 'From: webmaster@hashbeep.me'       . "\r\n" .
                'Reply-To: webmaster@hashbeep.me' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

$message = wordwrap($message, 70, "\r\n");
$res = mail($to, $subject, $message, $headers);
echo $res

// https://github.com/PHPMailer/PHPMailer

?>