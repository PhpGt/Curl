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

/** Example output:
.................................HEADER: HTTP/2 200
HEADER: server: nginx
HEADER: date: Tue, 28 Mar 2023 11:26:46 GMT
HEADER: content-type: application/json
HEADER: vary: Accept-Encoding
HEADER: cache-control: no-cache, private
HEADER: access-control-allow-origin: *
HEADER: set-cookie: XSRF-TOKEN=eyJpdiI6I; expires=Tue, 28-Mar-2023 13:26:46 GMT; path=/; samesite=lax
HEADER: set-cookie: catfacts_session=eyJpdiI6InUzdFY; expires=Tue, 28-Mar-2023 13:26:46 GMT; path=/; httponly; samesite=lax
HEADER: x-frame-options: SAMEORIGIN
HEADER: x-xss-protection: 1; mode=block
HEADER: x-content-type-options: nosniff
HEADER:
BODY: {"fact":"Edward Lear, author of \\The Owl and the Pussycat\\\"\", is said to have had his new house in San Remo built to exactly the same specification as his previous residence, so that his much-loved tabby, Foss, would immediately feel at home.\"\"\"","length":236}
...............HEADER: HTTP/2 200
HEADER: content-type: application/json
HEADER: date: Tue, 28 Mar 2023 11:26:46 GMT
HEADER: vary: Origin
HEADER: content-length: 20
HEADER:
BODY: {"ip":"82.4.210.105"}
.
*/
