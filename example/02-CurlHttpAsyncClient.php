<?php
use Gt\Curl\CurlHttpClient;
use Gt\Http\Request;
use Gt\Http\Response;
use Gt\Http\Uri;

require(__DIR__ . "/../vendor/autoload.php");

$request1 = new Request(
	"GET",
	new Uri("https://postman-echo.com/get?requestNumber=1")
);
$request2 = new Request(
	"GET",
	new Uri("https://postman-echo.com/get?requestNumber=2")
);
$request3 = new Request(
	"GET",
	new Uri("https://postman-echo.com/get?requestNumber=3")
);

$bodyList = [];

function outputResponse(Response $response, int $requestNumber) {
	global $bodyList;
	echo "Received response from request number $requestNumber.", PHP_EOL;
	$bodyList[$requestNumber] = $response->getBody();
}

$client = new CurlHttpClient();
$client->sendAsyncRequest($request1)->then(function(Response $response) {
	call_user_func("outputResponse", $response, 1);
});
$client->sendAsyncRequest($request2)->then(function(Response $response) {
	call_user_func("outputResponse", $response, 2);
});
$client->sendAsyncRequest($request3)->then(function(Response $response) {
	call_user_func("outputResponse", $response, 3);
});

echo "Executing concurrent requests...", PHP_EOL;

do {
	$active = $client->processAsync();
	echo ".";
	usleep(100_000);
}
while($active > 0);

foreach($bodyList as $i => $body) {
	echo "Body $i: ", $body, PHP_EOL;
}

echo "Done!", PHP_EOL;
