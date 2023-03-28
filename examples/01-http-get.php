<?php
/*
 * This is the simplest example of how to perform an HTTP GET request using Curl
 */
use Gt\Curl\Curl;

require(__DIR__ . "/../vendor/autoload.php");

$curl = new Curl("https://ipapi.co/country_name");
$curl->exec();
echo "Your country is: ";
echo $curl->output(), PHP_EOL;

/* Example output:
Your country is: United Kingdom
*/
