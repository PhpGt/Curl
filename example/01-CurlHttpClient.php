<?php
use Gt\Curl\CurlHttpClient;
use Gt\Http\Request;
use Gt\Http\Uri;

require(__DIR__ . "/../vendor/autoload.php");

$request = new Request(
	"GET",
	new Uri("https://postman-echo.com/get?org=PhpGt&repo=Curl")
);

$client = new CurlHttpClient();

echo "Sending request...", PHP_EOL;
$response = $client->sendRequest($request);
$json = json_decode($response->getBody());

echo "Arguments received: ", PHP_EOL;
foreach($json->args as $key => $value) {
	echo "$key => $value", PHP_EOL;
}