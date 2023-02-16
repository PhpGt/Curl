<?php
/*
 * In the previous curl-multi example, all response data is buffered, but under
 * some circumstances it is useful to get the response data as soon as it's
 * received, even if the response hasn't completed yet.
 *
 * With CurlMulti, we can stream the data byte for byte as it's received. This
 * is especially useful for processing data on slow or long-running responses.
 *
 * Notice how the dots are emitted every 100 microseconds, and the content is
 * streamed during the execution (within the while loop).
 */

use Gt\Curl\Curl;
use Gt\Curl\CurlMulti;

require(__DIR__ . "/../vendor/autoload.php");

$urlArray = [
	"https://catfact.ninja/fact",
	"https://api.ipify.org/?format=json",
	"https://this-domain-name-does-not-exist.example.com/nothing.json",
];

$multi = new CurlMulti();

foreach($urlArray as $url) {
	$curl = new Curl($url);
	$curl->setOpt(CURLOPT_HEADERFUNCTION, function ($ch, string $rawHeader):int {
		echo "HEADER: $rawHeader";
		return strlen($rawHeader);
	});
	$curl->setOpt(CURLOPT_WRITEFUNCTION, function ($ch, string $rawBody):int {
		echo "BODY: $rawBody\n";
		return strlen($rawBody);
	});
	$curl->setOpt(CURLOPT_TIMEOUT, 10);
	$multi->add($curl);
}

$stillRunning = 0;
do {
	$multi->exec($stillRunning);
	usleep(10_000);
	echo ".";
}
while($stillRunning > 0);

echo PHP_EOL;
