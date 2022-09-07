<?php
/*
 * This example shows how more than one Curl request can be executed
 * simultaneously by adding the Curl object to a CurlMulti object. This means
 * the total time waiting for N responses is equal to the slowest response,
 * rather than the sum of all responses.
 */
use Gt\Curl\Curl;
use Gt\Curl\CurlMulti;

require(__DIR__ . "/../vendor/autoload.php");

$curlCat = new Curl("https://catfact.ninja/fact");
$curlIp = new Curl("https://api.ipify.org/?format=json");
$curlTimeout = new Curl("https://this-domain-name-does-not-exist.example.com/nothing.json");

$multi = new CurlMulti();
$multi->add($curlCat);
$multi->add($curlIp);
$multi->add($curlTimeout);

$stillRunning = 0;
do {
	$multi->exec($stillRunning);
	usleep(100_000);
	echo ".";
}
while($stillRunning > 0);

echo PHP_EOL;
echo "Cat API response: " . $multi->getContent($curlCat) . PHP_EOL;
echo "IP API response: " . $multi->getContent($curlIp) . PHP_EOL;
echo "Timeout API response: " . $multi->getContent($curlTimeout) . PHP_EOL;
